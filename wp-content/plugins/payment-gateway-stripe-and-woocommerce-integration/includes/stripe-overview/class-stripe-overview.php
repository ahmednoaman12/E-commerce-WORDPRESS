<?php
if (!defined('ABSPATH')) {
    exit;
}
class EH_Stripe_Overview
{
    function __construct() {
       
        add_action('admin_menu', array($this,'eh_stripe_overview_menu_add'));  
        add_action('wp_ajax_eh_spg_stripe_refund_payment', array($this, 'eh_stripe_refund_payment')); 
        add_action('wp_ajax_eh_spg_capture_payment', array($this, 'eh_capture_payment'));
        add_action('wp_ajax_eh_spg_refund_payment', array($this, 'eh_refund_payment'));          
    }

    /**
    *Processes full refund from stripe overview transaction details table.
    */
    public  function eh_stripe_refund_payment() {

        if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
        {
            die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
        }
        $order_id = intval($_POST['order_id']);
        $obj = new EH_Stripe_Payment();
        $client = $obj->get_clients_details();
        $reason = __('Manual Refund Status:', 'payment-gateway-stripe-and-woocommerce-integration');
        $data = get_post_meta($order_id, '_eh_stripe_payment_charge', true);
        $status = $data['captured'];
        $charge_id = $data['id'];
        if ('Captured' === $status) {
            $balance_data = get_post_meta($order_id, '_eh_stripe_payment_balance', true);
            $amount = $balance_data['balance_amount'];
            $currency = $balance_data['currency'];
            $wc_order = new WC_Order($order_id);
            $remaining_amount = $wc_order->get_remaining_refund_amount();
            $refund_params = array(
                'amount' => $obj->get_stripe_amount($amount, $currency),
                'reason' => 'requested_by_customer',
                'metadata' => array(
                    'order_id' => $wc_order->get_order_number(),
                    'Total Tax' => $wc_order->get_total_tax(),
                    'Total Shipping' => (WC()->version < '2.7.0') ? $wc_order->get_total_shipping() : $wc_order->get_shipping_total(),
                    'Customer IP' => $client['IP'],
                    'Agent' => $client['Agent'],
                    'Referer' => $client['Referer'],
                    'Reason for Refund' => __('Stripe Overview refund', 'payment-gateway-stripe-and-woocommerce-integration')
                )
            );

            try {
                $charge_response = \Stripe\Charge::retrieve($charge_id);
                $refund_response = $charge_response->refunds->create($refund_params);
                if ($refund_response) {
                    $refund = wc_create_refund(array(
                        'amount' => $remaining_amount,
                        'reason' => 'Refunded using Stripe',
                        'order_id' => $order_id,
                        'line_items' => array(),
                    ));
                    do_action('woocommerce_refund_processed', $refund, true);
                    $refund_id = (WC()->version < '2.7.0') ? $refund->id : $refund->get_id();
                    if ($wc_order->get_remaining_refund_amount() > 0 || ( $wc_order->has_free_item() && $wc_order->get_remaining_refund_items() > 0 )) {
                        /**
                         * woocommerce_order_partially_refunded.
                         *
                         * @since 2.4.0
                         * Note: 3rd arg was added in err. Kept for bw compat. 2.4.3.
                         */
                        do_action('woocommerce_order_partially_refunded', $order_id, $refund_id, $refund_id);
                    } else {
                        do_action('woocommerce_order_fully_refunded', $order_id, $refund_id);

                        $wc_order->update_status(apply_filters('woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund_id));
                        $response_data['status'] = 'fully_refunded';
                    }

                    do_action('woocommerce_order_refunded', $order_id, $refund_id);

                    // Clear transients
                    wc_delete_shop_order_transients($order_id);
                    $refund_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
                    $data = $obj->make_refund_params($refund_response, $remaining_amount, ((WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency()), $order_id);
                    add_post_meta($order_id, '_eh_stripe_payment_refund', $data);
                    $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . (($data['status'] === 'succeeded') ? 'Success' : 'Failed') . ' [ ' . $refund_time . ' ] ' . (is_null($data['transaction_id']) ? '' : '<br>' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
                    EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Refund - Order #' . $wc_order->get_order_number());
                    $message = $remaining_amount . ' refund ' . $data['status'] . ' at ' . $refund_time . (is_null($data['transaction_id']) ? '' : '. Transaction Id - ' . $data['transaction_id']);
                    wp_send_json($message);
                } else {
                    EH_Stripe_Log::log_update('dead', $refund_response, get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                    $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __(' Status : Failed ', 'payment-gateway-stripe-and-woocommerce-integration'));
                    die($refund_response->message);
                }
            } catch (Exception $error) {
                $oops = $error->getJsonBody();
                EH_Stripe_Log::log_update('dead', $oops['error'], get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . $oops['error']['message']);
                die($oops['error']['message']);
            }
        } else {
            die('Uncaptured Amount cannot be refunded');
        }
    }

    /**
     *Captures uncaptured payment order from stripe overview page.
     */
    public  function eh_capture_payment() {

        if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
        {
            die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
        }
        $order_id = intval($_POST['order_id']);
        $order_data = get_post_meta($order_id, '_eh_stripe_payment_charge', true);
        $intent_id  = get_post_meta($order_id, '_eh_stripe_payment_intent', true);
        $charge_id = $order_data['id'];
        if (class_exists('EH_Stripe_Payment')) {
            $eh_stripe_this = new EH_Stripe_Payment();
            if ('test' == $eh_stripe_this->eh_stripe_mode) {
                \Stripe\Stripe::setApiKey($eh_stripe_this->eh_stripe_test_secret_key);
            } else {
                \Stripe\Stripe::setApiKey($eh_stripe_this->eh_stripe_live_secret_key);
            }
        }

        try {
            $eh_stripe_this = new EH_Stripe_Payment();
            $wc_order = new WC_Order($order_id);
            $intent = \Stripe\PaymentIntent::retrieve($intent_id);
            $intent->capture();
            $charge_response =  end($intent->charges->data);

            $data = $eh_stripe_this->make_charge_params($charge_response, $order_id);

            if ('Captured' == $data['captured'] && 'Paid' == $data['paid']) {
                $capture_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
                $wc_order->update_status('processing');
                update_post_meta($order_id, '_eh_stripe_payment_charge', $data);
                EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Capture - Order #' . $wc_order->get_order_number());
                $wc_order->add_order_note(__('Capture Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($data['status']) . ' [ ' . $capture_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['source_type'] . '. ' . __('Charge Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['captured'] . (is_null($data['transaction_id']) ? '' : '. ' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
                die('Capture ' . $data['status'] . ' at ' . $capture_time . ', via ' . $data['source_type']);
            }
        } catch (Exception $error) {
            $user = wp_get_current_user();
            $user_detail = array(
                'name' => get_user_meta($user->ID, 'first_name', true),
                'email' => $user->user_email,
                'phone' => get_user_meta($user->ID, 'billing_phone', true),
            );
            $oops = $error->getJsonBody();
            $wc_order->add_order_note($capture_response->status . ' ' . $error->getMessage());
            EH_Stripe_Log::log_update('dead', array_merge($user_detail, $oops), get_bloginfo('blogname') . ' - Charge - Order #' . $wc_order->get_order_number());
            die($error->getMessage());
        }
    }
    
    /**
     *Process refund payment from stripe overview.
     */
    public function eh_refund_payment() {

        if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
        {
            die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
        }
        $amount = wc_format_decimal($_POST['refund_amount']); // SFRWDF-224 Cannot refund amount that comes after decimal point
        $mode = sanitize_text_field($_POST['refund_mode']);
        $order_id = intval($_POST['order_id']);
        $obj = new EH_Stripe_Payment();
        $client = $obj->get_clients_details();
        $data = get_post_meta($order_id, '_eh_stripe_payment_charge', true);
        $status = $data['captured'];
        $reason = __('Manual Refund Status:', 'payment-gateway-stripe-and-woocommerce-integration');
        if ('Captured' === $status) {
            $charge_id = $data['id'];
            $currency = $data['currency'];
            $total_amount = $data['amount'];
            $wc_order = new WC_Order($order_id);
            if ($mode === 'full') {
                $refund_amount = $wc_order->get_remaining_refund_amount();
                $div = $wc_order->get_remaining_refund_amount() * ($total_amount / ((WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total()));
            } else {
                $refund_amount = $amount;
                $div = $amount * ($total_amount / ((WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total()));
            }
            $refund_params = array(
                'amount' => $obj->get_stripe_amount($div, $currency),
                'reason' => 'requested_by_customer',
                'metadata' => array(
                    'order_id' => $wc_order->get_order_number(),
                    'Total Tax' => $wc_order->get_total_tax(),
                    'Total Shipping' => (WC()->version < '2.7.0') ? $wc_order->get_total_shipping() : $wc_order->get_shipping_total(),
                    'Customer IP' => $client['IP'],
                    'Agent' => $client['Agent'],
                    'Referer' => $client['Referer'],
                    'Reason for Refund' => __('Refund through Stripe Overview Page', 'payment-gateway-stripe-and-woocommerce-integration')
                )
            );
           
            try {
                $charge_response = \Stripe\Charge::retrieve($charge_id);
                $refund_response = $charge_response->refunds->create($refund_params);
                if ($refund_response) {
                    $refund = wc_create_refund(array(
                        'amount' => $refund_amount,
                        'reason' => 'Refunded using Stripe',
                        'order_id' => $order_id,
                        'line_items' => array(),
                    ));
                    do_action('woocommerce_refund_processed', $refund, true);
                    $refund_id = (WC()->version < '2.7.0') ? $refund->id : $refund->get_id();
                    if ($wc_order->get_remaining_refund_amount() > 0 || ( $wc_order->has_free_item() && $wc_order->get_remaining_refund_items() > 0 )) {
                        /**
                         * woocommerce_order_partially_refunded.
                         *
                         * @since 2.4.0
                         * Note: 3rd arg was added in err. Kept for bw compat. 2.4.3.
                         */
                        do_action('woocommerce_order_partially_refunded', $order_id, $refund_id, $refund_id);
                    } else {
                        do_action('woocommerce_order_fully_refunded', $order_id, $refund_id);

                        $wc_order->update_status(apply_filters('woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund_id));
                        $response_data['status'] = 'fully_refunded';
                    }

                    do_action('woocommerce_order_refunded', $order_id, $refund_id);

                    // Clear transients
                    wc_delete_shop_order_transients($order_id);
                    $refund_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
                    $data = $obj->make_refund_params($refund_response, $refund_amount, ((WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency()), $order_id);
                    add_post_meta($order_id, '_eh_stripe_payment_refund', $data);
                    $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . (($data['status'] === 'succeeded') ? 'Success' : 'Failed') . ' [ ' . $refund_time . ' ] ' . (is_null($data['transaction_id']) ? '' : '<br>' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
                    EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Refund - Order #' . $wc_order->get_order_number());
                    $message = $refund_amount . ' refund ' . $data['status'] . ' at ' . $refund_time . (is_null($data['transaction_id']) ? '' : '. Transaction Id - ' . $data['transaction_id']);
                    wp_send_json($message);
                } else {
                    EH_Stripe_Log::log_update('dead', $refund_response, get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                    $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __(' Status : Failed ', 'payment-gateway-stripe-and-woocommerce-integration'));
                    die($refund_response->message);
                }
            } catch (Exception $error) {
                $oops = $error->getJsonBody();
                EH_Stripe_Log::log_update('dead', $oops['error'], get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . $oops['error']['message']);
                die($oops['error']['message']);
            }
        } else {
            die('Uncaptured Amount cannot be refunded');
        }
    }

    /**
     *Adds stripe overview submenu under woocommerce main menu in dashboard.
     */
    public function eh_stripe_overview_menu_add()
    {

        add_action( 'admin_head', array( $this, 'menu_order_count' ) );
        add_action('admin_init', array(
            $this,
            'eh_stripe_register_plugin_styles_scripts'
        ));
        add_action('wp_default_scripts', function($scripts)
        {
            if (!empty($scripts->registered['jquery'])) {
                $jquery_dependencies                 = $scripts->registered['jquery']->deps;
                $scripts->registered['jquery']->deps = array_diff($jquery_dependencies, array(
                    'jquery-migrate'
                ));
            }
        });
    }

    /**
     * gets position of stripe overview submenu.
     */
    public function menu_order_count() 
    {
        global $submenu;

        if ( isset( $submenu['wt_stripe_menu'] ) ) {
         
          // Add count if user has access
          if ( apply_filters( 'woocommerce_include_processing_order_count_in_menu', true ) && ( $order_count = $this->eh_spg_uncaptured_count() ) ) {
            foreach ( $submenu['wt_stripe_menu'] as $key => $menu_item ) {
              if ( 0 === strpos( $menu_item[0], _x( 'Stripe Overview', 'Admin menu name', 'wt_stripe_menu' ) ) ) {
                $submenu['wt_stripe_menu'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . $order_count . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>';
                break;
              }
            }
          }
        }
    }

    /**
     * gets uncaptured order count.
     */
    public function eh_spg_uncaptured_count()
    {
        $count=0;
        $id=  eh_stripe_overview_get_order_ids();
        for($i=0;$i<count($id);$i++)
        {
            $data=get_post_meta( $id[$i] ,'_eh_stripe_payment_charge',true);
            if(isset($data['captured']) && $data['captured']=='Uncaptured')
            {
                $count++;
            }
        }
        return $count;
    }

    //Register styles and scripts for stripe overview page.
    public function eh_stripe_register_plugin_styles_scripts()
    {   
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ('eh-stripe-overview' != $page)
            return;
        
        global $woocommerce;
        $woocommerce_version = function_exists('WC') ? WC()->version : $woocommerce->version;
        wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version);
        wp_register_style('eh-boot-style', EH_STRIPE_MAIN_URL_PATH.'assets/css/boot.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-boot-style');

        wp_register_style('eh-xcharts.min-style', EH_STRIPE_MAIN_URL_PATH.'assets/css/xcharts.min.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-xcharts.min-style');
        wp_register_style('eh-style-style', EH_STRIPE_MAIN_URL_PATH.'assets/css/style.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-style-style');
        
        //xchart includes
        wp_register_script('eh-xhart-lib-script', '//cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-xhart-lib-script');
        wp_register_script('eh-xcharts.min', EH_STRIPE_MAIN_URL_PATH .'assets/js/xcharts.min.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-xcharts.min');
        //date picker
        wp_register_script('eh-sugar.min', EH_STRIPE_MAIN_URL_PATH .'assets/js/sugar.min.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-sugar.min');

        // our chart init file
        wp_register_script('eh-custom-chart', EH_STRIPE_MAIN_URL_PATH .'assets/js/script.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-custom-chart');
        wp_register_script('eh-custom', EH_STRIPE_MAIN_URL_PATH .'assets/js/eh-stripe-custom.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-custom');
        
        wp_register_style('eh-alert-style', EH_STRIPE_MAIN_URL_PATH.'/assets/css/sweetalert2.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-alert-style');
        wp_register_script('eh-alert-jquery', EH_STRIPE_MAIN_URL_PATH.'/assets/js/sweetalert2.min.js',array(),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-alert-jquery');

        wp_register_style('eh-daterangepicker_style', EH_STRIPE_MAIN_URL_PATH.'/assets/css/daterangepicker.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-daterangepicker_style');

        wp_register_script('eh-moment-jquery', EH_STRIPE_MAIN_URL_PATH.'assets/js/moment.min.js',array('jquery'),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-moment-jquery');

        wp_register_script('eh-picker-jquery', EH_STRIPE_MAIN_URL_PATH.'assets/js/daterangepicker.min.js',array('jquery'),EH_STRIPE_VERSION,true);
        wp_enqueue_script('eh-picker-jquery');

    }

    //gets front main page of stripe overview page 
    public static function eh_stripe_template_display()
    {
        include (EH_STRIPE_MAIN_PATH."templates/template-frontend-main.php");
    }
}
new EH_Stripe_Overview();