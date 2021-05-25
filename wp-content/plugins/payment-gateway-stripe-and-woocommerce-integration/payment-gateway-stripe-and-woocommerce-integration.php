<?php
/*
 * Plugin Name: Stripe Payment Gateway for WooCommerce ( Basic )
 * Plugin URI: https://wordpress.org/plugins/payment-gateway-stripe-and-woocommerce-integration/
 * Description: Accept payments from your WooCommerce store via Credit/Debit Cards, Apple Pay, Google Pay, Alipay and Stripe Checkout using Stripe.
 * Author: WebToffee
 * Author URI: https://www.webtoffee.com/product/woocommerce-stripe-payment-gateway/
 * Version: 3.5.8
 * WC tested up to: 5.2.2
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: payment-gateway-stripe-and-woocommerce-integration
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!defined('EH_STRIPE_MAIN_URL_PATH')) {
    define('EH_STRIPE_MAIN_URL_PATH', plugin_dir_url(__FILE__));
}
if (!defined('EH_STRIPE_MAIN_PATH')) {
    define('EH_STRIPE_MAIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('EH_STRIPE_VERSION')) {
    define('EH_STRIPE_VERSION', '3.5.8');
}
if (!defined('EH_STRIPE_MAIN_FILE')) {
    define('EH_STRIPE_MAIN_FILE', __FILE__);
}
if (!defined('EH_STRIPE_INSTALLED_VERSION')) { 
    define('EH_STRIPE_INSTALLED_VERSION', 'BASIC');
}
if (!defined('EH_STRIPE_PLUGIN_NAME')) { 
    define('EH_STRIPE_PLUGIN_NAME', 'payment_gateway_stripe_and_woocommerce_integration');
}

if (!class_exists('Stripe\Stripe')) { //fix for SFRWDF-184
    include(EH_STRIPE_MAIN_PATH . "vendor/autoload.php");
}

require_once(ABSPATH . "wp-admin/includes/plugin.php");

if(is_plugin_active('eh-stripe-payment-gateway/stripe-payment-gateway.php')){ 
    

    if( defined('EH_STRIPE_INSTALLED_VERSION') && EH_STRIPE_INSTALLED_VERSION == 'PREMIUM' ) {
				
        deactivate_plugins( plugin_basename(__FILE__) );
        wp_die(__("Oops! PREMIUM Version of this Plugin Installed. Please deactivate the PREMIUM Version before activating BASIC.", 'payment-gateway-stripe-and-woocommerce-integration'), "", array('back_link' => 1));
        
    }

    return;
} else {
    
    add_action('plugins_loaded', 'eh_stripe_check', 99);

    function eh_stripe_check() {

        if ( class_exists( 'WooCommerce' ) ){ 
            register_activation_hook(__FILE__, 'eh_stripe_init_log');
            include(EH_STRIPE_MAIN_PATH . "includes/log.php");

           
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eh_stripe_plugin_action_links');
            eh_stripe_init();
        } else{

            deactivate_plugins( plugin_basename(__FILE__) );
            add_action('admin_notices', 'eh_stripe_wc_admin_notices', 99);
        }

    }

    function eh_stripe_wc_admin_notices() {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain) {
                    $old = array(
                        "Plugin <strong>deactivated</strong>.",
                        "Selected plugins <strong>deactivated</strong>.",
                        "Plugin deactivated.",
                        "Selected plugins deactivated.",
                        "Plugin <strong>activated</strong>.",
                        "Selected plugins <strong>activated</strong>.",
                        "Plugin activated.",
                        "Selected plugins activated."
                    );
                    $new = "<span style='color:red'>Stripe Payment for Woocommerce ( BASIC ) (WebToffee)-</span> Plugin Needs WooCommerce to Work.";
                    if (in_array($untranslated_text, $old, true)) {
                        $translated_text = $new;
                    }
                    return $translated_text;
                }, 99, 3);
    }

    function eh_stripe_plugin_action_links($links) {
        $setting_link = admin_url('admin.php?page=wt_stripe_menu');
        $plugin_links = array(
            '<a href="' . $setting_link . '">' . __('Settings', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>',
            '<a href="https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/" target="_blank">' . __('Documentation', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>',
            '<a href="https://www.webtoffee.com/product/woocommerce-stripe-payment-gateway/?utm_source=free_plugin_sidebar&utm_medium=Stripe_basic&utm_campaign=Stripe&utm_content='.EH_STRIPE_VERSION.'" target="_blank" style="color:#3db634;">' . __('Premium Upgrade', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>',
            '<a href="https://wordpress.org/support/plugin/payment-gateway-stripe-and-woocommerce-integration/" target="_blank">' . __('Support', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>',
            // '<a href="https://wordpress.org/support/plugin/payment-gateway-stripe-and-woocommerce-integration/reviews/" target="_blank">' . __('Review', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>',     
                    
        );
        if (array_key_exists('deactivate', $links)) {
            $links['deactivate'] = str_replace('<a', '<a class="ehstripe-deactivate-link"', $links['deactivate']);
        }
        return array_merge($plugin_links, $links);
    }
   
    function eh_stripe_init() {
        add_action('init', 'eh_stripe_lang_loader');
        
         //adds payment gateways
        function eh_section_add_stripe_gateway($methods) {
            $methods[] = 'EH_Stripe_Payment';
            $methods[] = 'EH_Alipay_Stripe_Gateway';
            $methods[] = 'Eh_Stripe_Checkout';
            return $methods;
        }
        

        function eh_stripe_lang_loader() {
            load_plugin_textdomain('payment-gateway-stripe-and-woocommerce-integration', false, dirname(plugin_basename(__FILE__)) . '/lang');
        }
        
        //includes neccessary payment method files
        add_filter('woocommerce_payment_gateways', 'eh_section_add_stripe_gateway');
        if (!class_exists('EH_Stripe_Payment')) {
            include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-api.php");
            include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-intent-manager.php");
            include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-checkout.php");
            include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-payment-request-button.php");
            
            new  Eh_Stripe_Payment_Request_Class();
            new  Eh_Stripe_Checkout();

            $eh_stripe = get_option("woocommerce_eh_stripe_pay_settings");
            if(isset($eh_stripe['overview'])){
                if ('yes' === $eh_stripe['overview']) {
                    include(EH_STRIPE_MAIN_PATH . "includes/stripe-overview/class-overview-table-data.php");
                    include(EH_STRIPE_MAIN_PATH . "includes/stripe-overview/class-stripe-overview.php");
                    include(EH_STRIPE_MAIN_PATH . "includes/stripe-overview/include-ajax-functions.php");
                }
            }
        }
        
        include(EH_STRIPE_MAIN_PATH . "includes/class-alipay-payment-handler.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-gateway-stripe-alipay.php");
        include(EH_STRIPE_MAIN_PATH . "includes/admin/class-stripe-admin-handler.php");
        new Eh_Stripe_Admin_Handler();
        include(EH_STRIPE_MAIN_PATH . "includes/class-eh-stripe-uninstall-feedback.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-eh-security-helper.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-eh-stripe-review-request.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-general-settings.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-apple-pay.php");
        include(EH_STRIPE_MAIN_PATH . "includes/class-stripe-payment-request-tab.php");
    }
    
    
    //initialises log file
    function eh_stripe_init_log() {
        if (WC()->version >= '2.7.0') {
            $logger = wc_get_logger();
            $live_context = array('source' => 'eh_stripe_pay_live');
            $init_msg = EH_STRIPE_LOG::init_live_log();
            $logger->log("debug", $init_msg, $live_context);
            $dead_context = array('source' => 'eh_stripe_pay_dead');
            $init_msg = EH_STRIPE_LOG::init_dead_log();
            $logger->log("debug", $init_msg, $dead_context);
        } else {
            $log = new WC_Logger();
            $init_msg = EH_STRIPE_LOG::init_live_log();
            $log->add("eh_stripe_pay_live", $init_msg);
            $init_msg = EH_STRIPE_LOG::init_dead_log();
            $log->add("eh_stripe_pay_dead", $init_msg);
        }
    }

    //adds styles to card elements in checkout page
    add_action( 'wp_enqueue_scripts', 'add_eh_stripe_gateway_styles' );
    function add_eh_stripe_gateway_styles() {
        wp_register_style( 'eh-style', plugins_url( 'assets/css/eh-style.css', __FILE__ ),array(),EH_STRIPE_VERSION );
        wp_enqueue_style( 'eh-style' );
    }

    //gets order ids of all payment methods for stripe overview page
    function eh_stripe_overview_get_order_ids() {
        $args = array(
            'post_type' => 'shop_order',
            'fields' => 'ids',
            'numberposts' => -1,
            'post_status' => array('wc-processing', 'wc-on-hold', 'wc-completed', 'wc-refunded')
        );
        $id = get_posts($args);
        $order_all_id = array();
        for ($i = 0, $count = 0; $i < count($id); $i++) {
            if (('eh_stripe_pay' === get_post_meta($id[$i], '_payment_method', true)) || ('eh_alipay_stripe' === get_post_meta($id[$i], '_payment_method', true)) || ('eh_stripe_checkout' === get_post_meta($id[$i], '_payment_method', true))) {
                $order_all_id[$count] = $id[$i];
                $count++;
            }
        }
        return $order_all_id;
    }

    //add action to capture stripe payment in order meta box
    add_action('woocommerce_order_actions', 'add_order_meta_box_actions', 2, 1);

    function add_order_meta_box_actions($actions) {
        global $post;
        $data = get_post_meta($post->ID, '_eh_stripe_payment_charge', true);
        $charge_capture = isset($data['captured']) ? $data['captured'] : '';
        if ($charge_capture == 'Uncaptured') {
            $actions['eh_stripe_capture'] = __('Capture Stripe Payment', 'payment-gateway-stripe-and-woocommerce-integration');
            return $actions;
        }
        return $actions;
    }

    add_action('woocommerce_order_action_eh_stripe_capture', 'process_order_meta_box_actions');

    //process stripe payment when capture action is given from order meta box
    function process_order_meta_box_actions() {
        global $post;
        $post_data = get_post_meta($post->ID, '_eh_stripe_payment_charge', true);
        $intent_id  = get_post_meta($post->ID, '_eh_stripe_payment_intent', true);
        $charge_id = $post_data['id'];

        try {
            $eh_stripe_this = new EH_Stripe_Payment();
            $wc_order = new WC_Order($post->ID);
            $intent = \Stripe\PaymentIntent::retrieve($intent_id);
                $intent->capture();
                $charge_response = end($intent->charges->data);

                
            $data = $eh_stripe_this->make_charge_params($charge_response, $post->ID);

            if ('Captured' == $data['captured'] && 'Paid' == $data['paid']) {

                $capture_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
                $wc_order->update_status('processing');
                update_post_meta($post_data['metadata']->order_id, '_eh_stripe_payment_charge', $data);
                EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Capture - Order #' . $wc_order->get_order_number());
                $wc_order->add_order_note(__('Capture Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($data['status']) . ' [ ' . $capture_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['source_type'] . '. ' . __('Charge Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['captured'] . (is_null($data['transaction_id']) ? '' : '. ' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
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
        }
    }

}




/*
 *  Displays update information for a plugin. 
 */
function eh_stripe_payment_gateway_for_woocommerce_update_message( $data, $response )
{

    if(isset( $data['upgrade_notice']))
        {
            add_action( 'admin_print_footer_scripts','eh_stripe_payment_gateway_for_woocommerce_plugin_screen_update_js');
            $msg=str_replace(array('<p>','</p>'),array('<div>','</div>'),$data['upgrade_notice']);
            echo '<style type="text/css">
            #payment-gateway-stripe-and-woocommerce-integration-update .update-message p:last-child{ display:none;}     
            #payment-gateway-stripe-and-woocommerce-integration-update ul{ list-style:disc; margin-left:30px;}
            .wt-update-message{ padding-left:30px;}
            </style>
            <div class="update-message wt-update-message">'. wpautop($msg).'</div>';
        }

}
add_action( 'in_plugin_update_message-payment-gateway-stripe-and-woocommerce-integration/payment-gateway-stripe-and-woocommerce-integration.php', 'eh_stripe_payment_gateway_for_woocommerce_update_message', 10, 2 );

if(!function_exists('eh_stripe_payment_gateway_for_woocommerce_plugin_screen_update_js'))
{
    function eh_stripe_payment_gateway_for_woocommerce_plugin_screen_update_js()
    {
        ?>
            <script>
                ( function( $ ){
                    var update_dv=$( '#payment-gateway-stripe-and-woocommerce-integration-update');
                    update_dv.find('.wt-update-message').next('p').remove();
                    update_dv.find('a.update-link:eq(0)').click(function(){
                        $('.wt-update-message').remove();
                    });
                })( jQuery );
            </script>
        <?php
    }
}