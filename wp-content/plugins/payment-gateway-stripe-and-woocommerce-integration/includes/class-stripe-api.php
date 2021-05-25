<?php

if (!defined('ABSPATH')) {
    exit;
}

class EH_Stripe_Payment extends WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'eh_stripe_pay';
        $this->method_title = __('Credit/Debit Cards', 'payment-gateway-stripe-and-woocommerce-integration');
        $this->has_fields = true;
        $this->supports = array(
            'products',
            'refunds',
        );
        $this->init_form_fields();
        $this->init_settings();
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->eh_stripe_order_button = $this->get_option('eh_stripe_order_button');
        $this->eh_stripe_mode = $this->get_option('eh_stripe_mode');
        $this->eh_stripe_test_secret_key = $this->get_option('eh_stripe_test_secret_key');
        $this->eh_stripe_test_publishable_key = $this->get_option('eh_stripe_test_publishable_key');
        $this->eh_stripe_live_secret_key = $this->get_option('eh_stripe_live_secret_key');
        $this->eh_stripe_live_publishable_key = $this->get_option('eh_stripe_live_publishable_key');
        $this->eh_stripe_capture = 'yes' === $this->get_option('eh_stripe_capture', 'yes');
        $this->eh_stripe_checkout_cards = $this->get_option('eh_stripe_checkout_cards') ? $this->get_option('eh_stripe_checkout_cards') : array();
        $this->eh_stripe_enforce_cards = 'yes' === $this->get_option('eh_stripe_enforce_cards', 'yes');
        $this->eh_stripe_email_receipt = 'yes' === $this->get_option('eh_stripe_email_receipt', 'yes');
        $this->eh_stripe_apple_pay = 'yes' === $this->get_option('eh_stripe_apple_pay', 'yes');
        $this->eh_stripe_apple_color = $this->get_option('eh_stripe_apple_color');
        $this->eh_stripe_form_description = $this->get_option('eh_stripe_form_description');
        $this->order_button_text = __($this->eh_stripe_order_button, 'payment-gateway-stripe-and-woocommerce-integration');
        $this->eh_stripe_inline_form = 'yes' === $this->get_option('eh_stripe_inline_form', 'yes');
        $this->eh_stripe_enable_inline_form  = true;

        $this->method_description = sprintf(__("Accepts Stripe payments via credit or debit card.", 'payment-gateway-stripe-and-woocommerce-integration')." <p><a target='_blank' href='https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/#credit_debit'>  ".__('Read documentation', 'payment-gateway-stripe-and-woocommerce-integration')." </a> </p> ");

        if ('test' === $this->eh_stripe_mode) {
            $this->description = $this->description . sprintf('<br>' . '<strong>' . __('Stripe TEST MODE Enabled: ', 'payment-gateway-stripe-and-woocommerce-integration') . '</strong>' . __(' Use these ', 'payment-gateway-stripe-and-woocommerce-integration') . '<a href="https://stripe.com/docs/testing" target="_blank">' . __(' Test Card Details ', 'payment-gateway-stripe-and-woocommerce-integration') . '</a>' . __(' for Testing.', 'payment-gateway-stripe-and-woocommerce-integration'));
            $this->description = trim($this->description);
        }

         // Set stripe API key.
        \Stripe\Stripe::setApiKey( self::get_stripe_api_key() );

        if (is_admin()) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        
        // Hooks
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

        // update the redirection URL for payment intent verififcation
        add_filter( 'woocommerce_payment_successful_result', array( $this, 'modify_successful_payment_result' ), 99999, 2 );
        
        add_action('before_woocommerce_pay',array( $this, 'verify_payment_intent_verification_in_order_pay' ));
        add_action( 'set_logged_in_cookie', array( $this, 'eh_set_cookie_on_current_request' ) );
       
    }
   
    /**
     * function to get stripe api key.
     */
    public static function get_stripe_api_key(){

        $stripe_settings  = get_option( 'woocommerce_eh_stripe_pay_settings' );
        if(isset($stripe_settings['eh_stripe_mode'])){
            $mode = $stripe_settings['eh_stripe_mode'];

            if ('test' === $mode) {
                
                return $stripe_settings['eh_stripe_test_secret_key'];
            } else {
                return $stripe_settings['eh_stripe_live_secret_key'];
            }
        }
    }

    /**
     * Processes and saves options.
     * @since 3.4.2
     */
    public function process_admin_options(){

        parent::process_admin_options();

        $eh_stripe = get_option("woocommerce_eh_stripe_pay_settings");
       
        if(isset($eh_stripe['eh_stripe_enforce_cards']) && ($eh_stripe['eh_stripe_enforce_cards'] === 'no')){

           $eh_stripe['eh_stripe_checkout_cards'] = array( 'mastercard','visa','diners','discover','amex', 'jcb');
           $eh_stripe['eh_stripe_enforce_cards'] = '';

        }
        update_option('woocommerce_eh_stripe_pay_settings',$eh_stripe);
    }
    
    /**
     * function to add Licence activation window.
     */
    public function admin_options() {
      
        $plugin_name = 'stripepaymentgateway';
        parent::admin_options();
    }
    
    /**
     * Get stripe activated payment cards icon.
     */
    public function get_icon() {
        $ext = version_compare(WC()->version, '2.6', '>=') ? '.svg' : '.png';
        $style = version_compare(WC()->version, '2.6', '>=') ? 'style="margin-left: 0.3em"' : '';
        $icon = '';

        if ((in_array('visa', $this->eh_stripe_checkout_cards)) || (in_array('Visa', $this->eh_stripe_checkout_cards))) {
            $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/visa' . $ext) . '" alt="Visa" width="32" title="VISA" ' . $style . ' />';
        }
        if ((in_array('mastercard', $this->eh_stripe_checkout_cards)) || (in_array('MasterCard', $this->eh_stripe_checkout_cards))){
            $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard' . $ext) . '" alt="Mastercard" width="32" title="Master Card" ' . $style . ' />';
        }
        if ((in_array('amex', $this->eh_stripe_checkout_cards)) || (in_array('American Express', $this->eh_stripe_checkout_cards))){
            $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/amex' . $ext) . '" alt="Amex" width="32" title="American Express" ' . $style . ' />';
        }
        if ('USD' === get_woocommerce_currency()) {
            if ((in_array('discover', $this->eh_stripe_checkout_cards)) || (in_array('Discover', $this->eh_stripe_checkout_cards))){
                $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/discover' . $ext) . '" alt="Discover" width="32" title="Discover" ' . $style . ' />';
            }
            if ((in_array('jcb', $this->eh_stripe_checkout_cards)) || (in_array('JCB', $this->eh_stripe_checkout_cards))){
                $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/jcb' . $ext) . '" alt="JCB" width="32" title="JCB" ' . $style . ' />';
            }
            if ((in_array('diners', $this->eh_stripe_checkout_cards)) || (in_array('Diners Club', $this->eh_stripe_checkout_cards))){
                $icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/diners' . $ext) . '" alt="Diners" width="32" title="Diners Club" ' . $style . ' />';
            }
        }
        if ($this->eh_stripe_apple_pay) {
            $icon .= '<img src="' . WC_HTTPS::force_https_url(EH_STRIPE_MAIN_URL_PATH . 'assets/img/apple-pay.png') . '" alt="Apple Pay" width="32" title="Apple Pay" ' . $style . ' />';
        }
        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }
    
    /**
     * Checks if gateway should be available to use.
     */
    public function is_available() {
        if ('yes' === $this->enabled) {
            if (!$this->eh_stripe_mode && is_checkout()) {
                return false;
            }
            if ('test' === $this->eh_stripe_mode) {
                if (!$this->eh_stripe_test_secret_key || !$this->eh_stripe_test_publishable_key) {
                    return false;
                }
            } else {
                if (!$this->eh_stripe_live_secret_key || !$this->eh_stripe_live_publishable_key) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Initialize form fields.
     */
    public function init_form_fields() {
        $this->form_fields = include( 'eh-stripe-settings-page.php' );
       
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        $tab = (isset($_GET['tab'])) ? esc_attr($_GET['tab']) : false;
        $section = (isset($_GET['section'])) ? esc_attr($_GET['section']) : false;
        if ('wc-settings' != $page && 'checkout' != $tab && 'eh_stripe_pay' != $section)
            return;

        wp_enqueue_media();
        
        wc_enqueue_js("
                    $('.description').css({'font-style':'normal'});
                    $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '65%'}); 
                    jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_test_secret_key, #woocommerce_eh_stripe_pay_eh_stripe_live_secret_key ' ).attr('autocomplete','new-password');
                    jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_statement_descriptor, #woocommerce_eh_stripe_pay_eh_stripe_live_secret_key ' ).attr('maxlength','22');
                    jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_mode' ).on( 'change', function() {
                                    var test    = jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_test_publishable_key, #woocommerce_eh_stripe_pay_eh_stripe_test_secret_key' ).closest( 'tr' ),
                                    live = jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_live_publishable_key, #woocommerce_eh_stripe_pay_eh_stripe_live_secret_key' ).closest( 'tr' );

                                    if ('test' === jQuery( this ).val()) {
                                            test.show();
                                            live.hide();
                                    } else {
                                            test.hide();
                                            live.show();
                                    }
                            }).change();
                    
            ");
    }

    /**
     * Outputs scripts used for stripe payment.
     */
    public function payment_scripts() {

        if ( (is_checkout() || is_product() || is_cart())  && !is_order_received_page()) {
            wp_register_script('stripe_v3_js', 'https://js.stripe.com/v3/');

           // wp_enqueue_script('stripe_checkout', 'https://checkout.stripe.com/checkout.js');
            wp_enqueue_script('eh_stripe_checkout', plugins_url('assets/js/eh_stripe_checkout.js', EH_STRIPE_MAIN_FILE), array('stripe_v3_js','jquery'),EH_STRIPE_VERSION, true);
            if ('test' == $this->eh_stripe_mode) {
                $public_key = $this->eh_stripe_test_publishable_key;
            } else {
                $public_key = $this->eh_stripe_live_publishable_key;
            }

            $show_zip_code = apply_filters('eh_stripe_ccshow_zipcode',true);
            $stripe_params = array(
                'key' => $public_key,
                'show_zip_code' => $show_zip_code,
                'i18n_terms' => __('Please accept the terms and conditions first', 'payment-gateway-stripe-and-woocommerce-integration'),
                'i18n_required_fields' => __('Please fill in required checkout fields first', 'payment-gateway-stripe-and-woocommerce-integration'),
            );
            $stripe_params['sepa_elements_options']                   = apply_filters(
                'eh_stripe_sepa_elements_options',
                array(
                    'supportedCountries' => array( 'SEPA' ),
                    'placeholderCountry' => WC()->countries->get_base_country(),
                    'style'              => array( 'base' => array( 'fontSize' => '15px' ) ),
                )
            );

            $stripe_params['elements_options']                        = apply_filters( 'eh_stripe_elements_options', array() );
            $stripe_params['stripe_enable_inline_form']               = $this->eh_stripe_enable_inline_form; 
            $stripe_params['is_checkout']                             = ( is_checkout() && empty( $_GET['pay_for_order'] ) ) ? 'yes' : 'no';
            $stripe_params['enabled_inline_form']                     = $this->eh_stripe_inline_form ? 'yes' : 'no';
            $stripe_params['inline_postalcode']                       = apply_filters('hide_inline_postal_code', true);

            wp_localize_script('eh_stripe_checkout', 'eh_stripe_val', apply_filters('eh_stripe_val', $stripe_params));
        }
    }

    /**
     *Payment form on checkout page.
     */
    public function payment_fields() {
        $user = wp_get_current_user();
        if ($user->ID) {
            $user_email = get_user_meta($user->ID, 'billing_email', true);
            $user_email = $user_email ? $user_email : $user->user_email;
        } else {
            $user_email = '';
        }
        echo '<div class="status-box">';

        if ($this->description) {
            echo apply_filters('eh_stripe_desc', wpautop(wp_kses_post("<span>" . $this->description . "</span>")));
        }
        echo "</div>";
        $pay_button_text = __('Pay', 'payment-gateway-stripe-and-woocommerce-integration');
        if (is_checkout_pay_page()) {
            $order_id = get_query_var('order-pay');
            $order = wc_get_order($order_id);
            $email = (WC()->version < '2.7.0') ? $order->billing_email : $order->get_billing_email();
            echo '<div
                id="eh-stripe-pay-data"
                data-panel-label="' . esc_attr($pay_button_text) . '"
                data-description="' . esc_attr($this->eh_stripe_form_description) . '"
                data-email="' . esc_attr(($email !== '') ? $email : get_bloginfo('name', 'display')) . '"
                data-amount="' . esc_attr($this->get_stripe_amount(((WC()->version < '2.7.0') ? $order->order_total : $order->get_total()))) . '"
                data-name="' . esc_attr(sprintf(get_bloginfo('name', 'display'))) . '"
                data-currency="' . esc_attr(((WC()->version < '2.7.0') ? $order->order_currency : $order->get_currency())) . '">';

            echo $this->elements_form();
            echo '</div>';

        } else {
            echo '<div
                id="eh-stripe-pay-data"
                data-panel-label="' . esc_attr($pay_button_text) . '"
                data-description="' . esc_attr($this->eh_stripe_form_description) . '"
                data-email="' . esc_attr($user_email) . '"
                data-amount="' . esc_attr($this->get_stripe_amount(WC()->cart->total)) . '"
                data-name="' . esc_attr(sprintf(get_bloginfo('name', 'display'))) . '"
                data-currency="' . esc_attr(strtolower(get_woocommerce_currency())) . '">';

            echo $this->elements_form();
           
            echo '</div>';
        }
    }

    /**
     *Renders stripe elements on payment form.
     */
    public function elements_form() {
        ?>
        <fieldset id="eh-<?php echo esc_attr( $this->id ); ?>-cc-form" class="eh-credit-card-form eh-payment-form" style="background:transparent;">
            <?php do_action( 'eh_woocommerce_credit_card_form_start', $this->id ); ?>

            <?php if($this->eh_stripe_inline_form){ ?>

                <div id="eh-stripe-card-element" class="eh-stripe-elements-field" style="padding:5px 10px;">
                    <!-- a Stripe Element will be inserted here. -->
                </div>

            <?php }else{ ?>
            
                <div class="form-row form-row-wide">
                    <label for="eh-stripe-card-element"><?php esc_html_e( 'Card Number', 'payment-gateway-stripe-and-woocommerce-integration' ); ?> <span class="required">*</span></label>
                    <div class="stripe-card-group">
                        <div id="eh-stripe-card-element" class="eh-stripe-elements-field">
                        <!-- a Stripe Element will be inserted here. -->
                        </div>

                        <i class="eh-stripe-credit-card-brand eh-stripe-card-brand" alt="Credit Card"></i>
                    </div>
                </div>

                <div class="form-row form-row-first">
                    <label for="eh-stripe-exp-element"><?php esc_html_e( 'Expiry Date', 'payment-gateway-stripe-and-woocommerce-integration' ); ?> <span class="required">*</span></label>

                    <div id="eh-stripe-exp-element" class="eh-stripe-elements-field">
                    <!-- a Stripe Element will be inserted here. -->
                    </div>
                </div>

                <div class="form-row form-row-last">
                    <label for="eh-stripe-cvc-element"><?php esc_html_e( 'Card Code (CVC)','payment-gateway-stripe-and-woocommerce-integration' ); ?> <span class="required">*</span></label>
                <div id="eh-stripe-cvc-element" class="eh-stripe-elements-field">
                <!-- a Stripe Element will be inserted here. -->
                </div>
                </div>
                <?php } ?>
                <div class="clear"></div>

            <!-- Used to display form errors -->
            <div class="stripe-source-errors" role="alert" style="color:#ff0000"></div>
            <br />
            <?php do_action( 'eh_woocommerce_credit_card_form_end', $this->id ); ?>
            <div class="clear"></div>
        </fieldset>
        <?php
    }

    /**
     *List of zero decimal currencies supported by stripe.
     */
    public static function zerocurrency()  {
        return array("BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "VUV", "XAF", "XOF", "XPF", "VND");
    }
    
    /**
     *Gets stripe amount.
     */
    public  static function get_stripe_amount($total, $currency = '') {
        if (!$currency) {
            $currency = get_woocommerce_currency();
        }
        if (in_array(strtoupper($currency), self::zerocurrency())) {
            // Zero decimal currencies
            $total = absint($total);
        } else {
            $total = round($total, 2) * 100; // In cents
        }
        return $total;
    }

    /**
     *Reset stripe amount after charge response.
     */
    public function reset_stripe_amount($total, $currency = '') {
        if (!$currency) {
            $currency = get_woocommerce_currency();
        }
        if (in_array(strtoupper($currency), self::zerocurrency())) {
            // Zero decimal currencies
            $total = absint($total);
        } else {
            $total = round($total, 2) / 100; // In cents
        }
        return $total;
    }
    
     /**
     *Gets client details.
     */
    public function get_clients_details() {
        return array(
            'IP' => $_SERVER['REMOTE_ADDR'],
            'Agent' => $_SERVER['HTTP_USER_AGENT'],
            'Referer' => $_SERVER['HTTP_REFERER']
        );
    }
    
   
     /**
     *Gets details for stripe charge creation.
     */
    public function get_charge_details( $wc_order, $token, $order_type, $client, $card_brand, $currency, $amount,$customer = '' ) {
        $product_name = array();
        foreach ($wc_order->get_items() as $item) {
            array_push($product_name, $item['name']);
        }

        $charge = array(
            'payment_method' => $token,
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => array(
                'order_id' => $wc_order->get_order_number(),
                'Total Tax' => $wc_order->get_total_tax(),
                'Total Shipping' => $wc_order->get_total_shipping(),
                'Customer IP' => $client['IP'],
                'Agent' => $client['Agent'],
                'Referer' => $client['Referer'],
                'WP customer #' => (WC()->version < '2.7.0') ? $wc_order->user_id : $wc_order->get_user_id(),
                'Billing Email' => (WC()->version < '2.7.0') ? $wc_order->billing_email : $wc_order->get_billing_email()
            ),
            'description' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' Order #' . $wc_order->get_order_number(),
        );
        
        if( $this->get_option('eh_stripe_statement_descriptor') ) {
            
            $statement_descriptor = $this->get_option('eh_stripe_statement_descriptor');
            $statement_descriptor =  ( strlen( $statement_descriptor < 22 ) ) ? $statement_descriptor : substr( $statement_descriptor ,0,22);            
            $charge['statement_descriptor'] = $statement_descriptor;
        }
        $charge['customer'] = $customer;

        $product_list = implode(' | ', $product_name);

        $charge['metadata']['Products'] = substr($product_list, 0, 499);
        
        
        $show_items_details = apply_filters('eh_stripe_show_items_in_payment_description', false);
                
        if($show_items_details){
            
            $charge['description']=$charge['metadata']['Products'] .' '.wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' Order #' . $wc_order->get_order_number();
        }
        $charge['confirm'] =  true ;

        if ('other' != $card_brand) { 
            $charge['capture_method'] = $this->eh_stripe_capture ? 'automatic' : 'manual'; 
        }
        if ($this->eh_stripe_email_receipt) {
            $charge['receipt_email'] = (WC()->version < '2.7.0') ? $wc_order->billing_email : $wc_order->get_billing_email();
        }
        if (!is_checkout_pay_page()) {
            $charge['shipping'] = array(
                'address' => array(
                    'line1' => (WC()->version < '2.7.0') ? $wc_order->shipping_address_1 : $wc_order->get_shipping_address_1(),
                    'line2' => (WC()->version < '2.7.0') ? $wc_order->shipping_address_2 : $wc_order->get_shipping_address_2(),
                    'city' => (WC()->version < '2.7.0') ? $wc_order->shipping_city : $wc_order->get_shipping_city(),
                    'state' => (WC()->version < '2.7.0') ? $wc_order->shipping_state : $wc_order->get_shipping_state(),
                    'country' => (WC()->version < '2.7.0') ? $wc_order->shipping_country : $wc_order->get_shipping_country(),
                    'postal_code' => (WC()->version < '2.7.0') ? $wc_order->shipping_postcode : $wc_order->get_shipping_postcode()
                ),
                'name' => ((WC()->version < '2.7.0') ? $wc_order->shipping_first_name : $wc_order->get_shipping_first_name()) . ' ' . ((WC()->version < '2.7.0') ? $wc_order->shipping_last_name : $wc_order->get_shipping_last_name()),
                'phone' => (WC()->version < '2.7.0') ? $wc_order->billing_phone : $wc_order->get_billing_phone(),
            );
        }
        
       return apply_filters('eh_stripe_payment_intent_args', $charge);
    }
    
     /**
     *Creates charge parameters from charge response.
     */
    public function make_charge_params($charge_value, $order_id) {
        $wc_order = wc_get_order($order_id);
        $charge_data = json_decode(json_encode($charge_value));
        $origin_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
        $charge_parsed = array(
            "id" => $charge_data->id,
            "amount" => $this->reset_stripe_amount($charge_data->amount, $charge_data->currency),
            "amount_refunded" => $this->reset_stripe_amount($charge_data->amount_refunded, $charge_data->currency),
            "currency" => strtoupper($charge_data->currency),
            "order_amount" => (WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total(),
            "order_currency" => (WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency(),
            "captured" => $charge_data->captured ? "Captured" : "Uncaptured",
            "transaction_id" => $charge_data->balance_transaction,
            "mode" => (false == $charge_data->livemode) ? 'Test' : 'Live',
            "metadata" => $charge_data->metadata,
            "created" => date('Y-m-d H:i:s', $charge_data->created),
            "paid" => $charge_data->paid ? 'Paid' : 'Not Paid',
            "receiptemail" => (null == $charge_data->receipt_email) ? 'Receipt not send' : $charge_data->receipt_email,
            "receiptnumber" => (null == $charge_data->receipt_number) ? 'No Receipt Number' : $charge_data->receipt_number,
            "source_type" => ('card' == $charge_data->payment_method_details->type ) ? ($charge_data->payment_method_details->card->brand . "( " . $charge_data->payment_method_details->card->funding . " )") : (( 'alipay' == $charge_data->source->type ) ? 'Alipay' : 'Undefined'),
            "status" => $charge_data->status,
            "origin_time" => $origin_time
        );
        $trans_time = date('Y-m-d H:i:s', time() + ((get_option('gmt_offset') * 3600) + 10));
        $tranaction_data = array(
            "id" => $charge_data->id,
            "total_amount" => $charge_parsed['amount'],
            "currency" => $charge_parsed['currency'],
            "balance_amount" => 0,
            "origin_time" => $trans_time
        );
        if (0 === count(get_post_meta($order_id, '_eh_stripe_payment_balance'))) {
            if ($charge_parsed['captured'] === 'Captured') {
                $tranaction_data['balance_amount'] = $charge_parsed['amount'];
            }
            add_post_meta($order_id, '_eh_stripe_payment_balance', $tranaction_data);
        } else {
            $tranaction_data['balance_amount'] = $charge_parsed['amount'];
            update_post_meta($order_id, '_eh_stripe_payment_balance', $tranaction_data);
        }
        return $charge_parsed;
    }
    
     /**
     *Creates refund parameters from refund response .
     */
    public function make_refund_params($refund_value, $amount, $currency, $order_id) {
        $refund_data = json_decode(json_encode($refund_value));
        $origin_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
        $refund_parsed = array(
            "id" => $refund_data->id,
            "object" => $refund_data->object,
            "amount" => $this->reset_stripe_amount($refund_data->amount, $refund_data->currency),
            "transaction_id" => $refund_data->balance_transaction,
            "currency" => strtoupper($refund_data->currency),
            "order_amount" => $amount,
            "order_currency" => $currency,
            "metadata" => $refund_data->metadata,
            "created" => date('Y-m-d H:i:s', $refund_data->created + get_option('gmt_offset') * 3600),
            "charge_id" => $refund_data->charge,
            "receiptnumber" => (null == $refund_data->receipt_number) ? 'No Receipt Number' : $refund_data->receipt_number,
            "reason" => $refund_data->reason,
            "status" => $refund_data->status,
            "origin_time" => $origin_time
        );
        $trans_time = date('Y-m-d H:i:s', time() + ((get_option('gmt_offset') * 3600) + 10));
        $transaction_data = get_post_meta($order_id, '_eh_stripe_payment_balance', true);
        $balance = floatval($transaction_data['balance_amount']) - floatval($refund_parsed['amount']);
        $transaction_data['balance_amount'] = $balance;
        $transaction_data['origin_time'] = $trans_time;
        update_post_meta($order_id, '_eh_stripe_payment_balance', $transaction_data);
        return $refund_parsed;
    }
    

    public function create_stripe_customer($token, $order_id, $user_email = false) {

        $order = wc_get_order($order_id);

        $response = \Stripe\Customer::create(array(
                    "description" => "Customer for Order #" . $order_id,
                    "email" => $user_email,
                    "payment_method" => $token,
                    "address" => array( //sending name and billing address to stripe to support indian exports
                        'city' => method_exists($order, 'get_billing_city') ? $order->get_billing_city() : $order->billing_city,
                        'country' => method_exists($order, 'get_billing_country') ? $order->get_billing_country() : $order->billing_country,
                        'line1' => method_exists($order, 'get_billing_address_1') ? $order->get_billing_address_1() : $order->billing_address_1,
                        'line2' => method_exists($order, 'get_billing_address_2') ? $order->get_billing_address_2() : $order->billing_address_2,
                        'postal_code' => method_exists($order, 'get_billing_postcode') ? $order->get_billing_postcode() : $order->billing_postcode,
                        'state' => method_exists($order, 'get_billing_state') ? $order->get_billing_state() : $order->billing_state,
                    ),
                    "name" => (method_exists($order, 'get_billing_first_name') ? $order->get_billing_first_name() : $order->billing_first_name) . (method_exists($order, 'get_billing_last_name')  ? $order->get_billing_last_name()  : $order->billing_last_name),
        ));

        if (empty($response->id)) {
            return false;
        }

        return $response;
    }

    /**
	 * Proceed with current request using new login session (to ensure consistent nonce).
	 */
	public function eh_set_cookie_on_current_request( $cookie ) {
		$_COOKIE[ LOGGED_IN_COOKIE ] = $cookie;
	}
    
    /**
     *Process stripe payment.
     */
    public function process_payment($order_id) {
        $wc_order = wc_get_order($order_id);
        try {
            $token = sanitize_text_field($_POST['eh_stripe_pay_token']);
            $payment_method = sanitize_text_field($_POST['eh_stripe_pay_token']);


            $card_brand =  isset( $_POST['eh_stripe_card_type'] )? sanitize_text_field($_POST['eh_stripe_card_type']) : 'other';
            $currency = isset($_POST['eh_stripe_pay_currency']) ? sanitize_text_field($_POST['eh_stripe_pay_currency']) : get_woocommerce_currency();
            $amount = isset( $_POST['eh_stripe_pay_amount'] )? sanitize_text_field($_POST['eh_stripe_pay_amount']) : self::get_stripe_amount(((WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total())) ;
            $client = $this->get_clients_details();

            $process_auth = true;

            
            if (!in_array($card_brand, $this->eh_stripe_checkout_cards)) {

                $process_auth = false; 

                if('visa' === $card_brand){
                    $card_brand = 'Visa';
                }
                if('mastercard' === $card_brand){
                    $card_brand = 'MasterCard';
                }
                if('amex' === $card_brand){
                    $card_brand = 'American Express';
                }
                if('jcb' === $card_brand){
                    $card_brand = 'JCB';
                }
                if('diners' === $card_brand){
                    $card_brand = 'Diners Club';
                }
                if('discover' === $card_brand){
                    $card_brand = 'Discover';
                }
                if(in_array($card_brand, $this->eh_stripe_checkout_cards)){
                $process_auth = true; 
                }
            }

            //checks if restrict cards option was unchecked before, if uncheked allows all cards to process.
            $eh_stripe = get_option("woocommerce_eh_stripe_pay_settings");
            if(isset($eh_stripe['eh_stripe_enforce_cards']) && ($eh_stripe['eh_stripe_enforce_cards'] === 'no')){
                $process_auth = true; 
            }
                   
            if ($process_auth) {
                $order_type = 'card';
                $customer = $this->create_stripe_customer( $payment_method, $order_id, ((WC()->version < '2.7.0') ? $wc_order->billing_email : $wc_order->get_billing_email()) );
                try {
                    $payment_intent_args  = $this->get_charge_details($wc_order, $token, $order_type, $client, $card_brand, $currency, $amount,$customer);
                        
                    $intent = \Stripe\PaymentIntent::create( $payment_intent_args );
                   
                    $this->save_payment_intent_to_order( $wc_order, $intent );
                    
                    add_post_meta( $order_id, '_eh_stripe_payment_intent', $intent->id); 

                    if ($intent->status == 'requires_action' &&
                        $intent->next_action->type == 'use_stripe_sdk') {
                        # Tell the client to handle the action
                        $this->unlock_order_payment( $wc_order );
                        if ( is_wc_endpoint_url( 'order-pay' ) ) {
                            $redirect_url = add_query_arg( 'eh-stripe-confirmation', 1, $wc_order->get_checkout_payment_url( false ) );

                            return array(
                                'result'   => 'success',
                                'redirect' => $redirect_url,
                            );
                        } else {

                            /**
                             * This URL contains only a hash, which will be sent to `checkout.js` where it will be set like this:
                             * `window.location = result.redirect`
                             * Once this redirect is sent to JS, the `onHashChange` function will execute `handleCardPayment`.
                             */

                            return array(
                                'result'        => 'success',
                                'redirect'      => $this->get_return_url( $wc_order ),
                                'intent_secret' => $intent->client_secret,
                                'createaccount' => (int) ! empty( $_POST['createaccount'] ), // WPCS: input var ok, CSRF ok.
                            );
                        }
                    }    
                     else {
                          return $this->process_order(   end( $intent->charges->data ),$wc_order );
                     }

                }
                catch (\Stripe\Error\Card $e) {
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $this->process_error(  $wc_order,$err['param'], $err, 'failed' );
                    return;
                }
                
                
            } else {
                $user = wp_get_current_user();
                $enforce_detail = array (
                    'name' => get_user_meta($user->ID, 'first_name', true),
                    'email' => $user->user_email,
                    'phone' => get_user_meta($user->ID, 'billing_phone', true),
                    'type' => "card_error",
                    'card' => $card_brand,
                    'token' => $token,
                    'message' => __("Admin declined the payment due to Card Restriction.", 'payment-gateway-stripe-and-woocommerce-integration')
                );
              
                $eh_brands = implode(",",$this->eh_stripe_checkout_cards);
                wc_add_notice(__('Card brand', 'payment-gateway-stripe-and-woocommerce-integration').' (' . $card_brand . ") " . __('has been restricted for payment by the seller. Please reload and try with another card from the accepted list', 'payment-gateway-stripe-and-woocommerce-integration').' ('.__($eh_brands,'payment-gateway-stripe-and-woocommerce-integration').')', $notice_type = 'error');
                EH_Stripe_Log::log_update('dead', $enforce_detail, get_bloginfo('blogname') . ' - Charge - Order #' . $wc_order->get_order_number());
                return array (
                    'result' => 'failure'
                );
            }
        } catch (Exception $error) {
            $user = wp_get_current_user();
            $user_detail = array(
                'name' => get_user_meta($user->ID, 'first_name', true),
                'email' => $user->user_email,
                'phone' => get_user_meta($user->ID, 'billing_phone', true),
            );

            if (method_exists($error, 'getJsonBody')) {
                $oops = $error->getJsonBody();
                $error_message = $oops['error']['message'];
            } else {
                $oops = array('message' => $error->getMessage());
                $error_message = $error->getMessage();
            }

            wc_add_notice(__('Payment Failed ', 'payment-gateway-stripe-and-woocommerce-integration') . "( " . $error_message . " )." . __('Refresh and try again', 'payment-gateway-stripe-and-woocommerce-integration'), $notice_type = 'error');
            EH_Stripe_Log::log_update('dead', array_merge($user_detail, $oops), get_bloginfo('blogname') . ' - Charge - Order #' . $wc_order->get_order_number());
            return array (
                'result' => 'failure'
            );
        }
    }


    /**
     * Change order based on charge responce
     * @since 3.3.0
     * get the code from processs_payment() method
     */
    function process_order( $charge_response ,$wc_order ) {
        
        $order_id = (WC()->version < '2.7.0') ? $wc_order->id : $wc_order->get_id();
        
        $data = $this->make_charge_params($charge_response, $order_id);
        $order_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
        if ($charge_response->paid == true) {

            if($charge_response->captured == true){
                $wc_order->payment_complete($data['id']);
            }
            if (!$charge_response->captured) {
                $wc_order->update_status('on-hold');
            }
            $wc_order->add_order_note(__('Payment Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($data['status']) . ' [ ' . $order_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['source_type'] . '. ' . __('Charge Status :', 'payment-gateway-stripe-and-woocommerce-integration') . $data['captured'] . (is_null($data['transaction_id']) ? '' : '. <br>'.__('Transaction ID : ','payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
            WC()->cart->empty_cart();
            add_post_meta($order_id, '_eh_stripe_payment_charge', $data);
            EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Charge - Order #' . $wc_order->get_order_number());
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($wc_order),
            );
        } else {
            wc_add_notice($data['status'], $notice_type = 'error');
            EH_Stripe_Log::log_update('dead', $charge_response, get_bloginfo('blogname') . ' - Charge - Order #' . $wc_order->get_order_number());
        }
    }
    
    
    //update errors occuring during different processes
    private function process_error($order, $title, $message, $status = 'failed') {
        $order->add_order_note($message['message']);
        $order->update_status($status);

        $error_arr = array('message' => $message);
        EH_Stripe_Log::log_update('dead', $error_arr, get_bloginfo('blogname') . " - $title - Order #" . $order->get_order_number());
    }
    
    /**
     * Process refund process.
     * ( check need to update with payment intent ).
     */
    public function process_refund($order_id, $amount = NULL, $reason = '') {
    
        $client = $this->get_clients_details();
        if ($amount > 0) {
            $data = get_post_meta($order_id, '_eh_stripe_payment_charge', true);
            $intent_id = get_post_meta($order_id, 'eh_stripe_intent_id', true);
            $status = $data['captured'];
            if ('Captured' === $status) {
                $charge_id = $data['id'];
                $currency = $data['currency'];
                $total_amount = $data['amount'];
                $wc_order = new WC_Order($order_id);
                $div = $amount * ($total_amount / ((WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total()));
                $refund_params = array(
                    'amount' => self::get_stripe_amount($div, $currency),
                    'reason' => 'requested_by_customer',
                    'metadata' => array(
                        'order_id' => $wc_order->get_order_number(),
                        'Total Tax' => $wc_order->get_total_tax(),
                        'Total Shipping' => (WC()->version < '2.7.0') ? $wc_order->get_total_shipping() : $wc_order->get_shipping_total(),
                        'Customer IP' => $client['IP'],
                        'Agent' => $client['Agent'],
                        'Referer' => $client['Referer'],
                        'Reaon for Refund' => $reason
                    )
                );
                
                try {

                    $charge_response = \Stripe\Charge::retrieve($charge_id);
                    $refund_response = $charge_response->refunds->create( $refund_params );
                    

                    if ( $refund_response ) {
                        
                        $refund_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
                        $data = $this->make_refund_params($refund_response, $amount, ((WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency()), $order_id);
                        add_post_meta($order_id, '_eh_stripe_payment_refund', $data);
                        $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . (($data['status'] === 'succeeded') ? 'Success' : 'Failed') . ' [ ' . $refund_time . ' ] ' . (is_null($data['transaction_id']) ? '' : '<br>' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
                        EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Refund - Order #' . $wc_order->get_order_number());
                        return true;
                    } else {
                        EH_Stripe_Log::log_update('dead', $refund_response, get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                        $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __(' Status : Failed ', 'payment-gateway-stripe-and-woocommerce-integration'));
                        return new WP_Error('error', $refund_response->message);
                    }
                } catch (Exception $error) {
                    $oops = $error->getJsonBody();
                    EH_Stripe_Log::log_update('dead', $oops['error'], get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
                    $wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . $oops['error']['message']);
                    return new WP_Error('error', $oops['error']['message']);
                }
            } else {
                return new WP_Error('error', __('Uncaptured Amount cannot be refunded', 'payment-gateway-stripe-and-woocommerce-integration'));
            }
        } else {
            return false;
        }
    }
    
    //gets file size of log files in units of bytes
    public function file_size($bytes) {
        $result = 0;
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ".", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

     /**
     * Change payment rediret URL when "requires_action"
     * @since 3.3.0
     */
    public function modify_successful_payment_result( $result, $order_id ) {
      
        if ( ! isset( $result['intent_secret'] ) ) {
          
            return $result;
        }

        // Put the final thank you page redirect into the verification URL.
        $verification_url = add_query_arg(
            array(
                'order'       => $order_id,
                '_wpnonce'    => wp_create_nonce( 'eh_stripe_confirm_payment_intent' ),
                'redirect_to' => rawurlencode( $result['redirect'] ),
            ),
            WC_AJAX::get_endpoint( 'eh_stripe_verify_payment_intent' )
        );

        // Combine into a hash.
        $redirect = sprintf( '#confirm-pi-%s:%s', $result['intent_secret'], rawurlencode( $verification_url ) );

        return array(
            'result'   => 'success',
            'redirect' => $redirect,
        );
    }
    
    /**
     * Save intent details with order
     * @since 3.3.0
     */
    public function save_payment_intent_to_order( $order, $intent ) {
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        
        if ( version_compare(WC_VERSION, '2.7.0', '<') ) {
            update_post_meta( $order_id, 'eh_stripe_intent_id', $intent->id );
        } else {
            $order->update_meta_data( 'eh_stripe_intent_id', $intent->id );
        }

        if ( is_callable( array( $order, 'save' ) ) ) {
            $order->save();
        }
    }
    
    /**
     * Retreve the payment intent detials from order
     * @since 3.3.0
     */
    public function get_payment_intent_from_order( $order ) {
        $order_id = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();

        if ( version_compare(WC_VERSION, '2.7.0', '<') ) {
            $intent_id = get_post_meta( $order_id, 'eh_stripe_intent_id', true );
        } else {
            $intent_id = $order->get_meta( 'eh_stripe_intent_id' );
        }

        if ( ! $intent_id ) {
            return false;
        }

        return \Stripe\PaymentIntent::retrieve( $intent_id );
    }
    
    /**
     * Check intent status for declaring payment status
     * @since 3.3.0
     */
    public function verify_payment_intent_after_checkout( $order ) {
        
        $payment_method = version_compare(WC_VERSION, '3.0.0', '<') ? $order->payment_method : $order->get_payment_method();
        if ( $payment_method !== $this->id ) {
            // If this is not the payment method, an intent would not be available.
            return;
        }

        $intent = $this->get_payment_intent_from_order( $order );

        if ( ! $intent ) {
            // No intent, redirect to the order received page for further actions.
            return;
        }
        
        clean_post_cache( $order->get_id() );
        $order = wc_get_order( $order->get_id() );

        if ( 'pending' !== $order->get_status() && 'failed' !== $order->get_status() ) { // Check payment already completed.
            return;
        }

        if ( $this->lock_order_payment( $order, $intent ) ) {
            return;
        }

        if ( 'succeeded' === $intent->status || 'requires_capture' === $intent->status ) {
        
            // Proceed with the payment completion.
            return $this->process_order(   end( $intent->charges->data ),$order );

        } else if ( 'requires_payment_method' === $intent->status ) {
            // `requires_payment_method` means that SCA got denied for the current payment method.
            $this->failed_sca_authenticate( $order, $intent );
        }

        $this->unlock_order_payment( $order );
    }
    /**
     * Puase processing order if same intent already being handled
     * @since 3.3.0
     */
    public function lock_order_payment( $order, $intent ) {
        $order_id       = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();
        $transient_name = 'eh_stripe_processing_intent_' . $order_id;
        $processing     = get_transient( $transient_name );
        
        if ( $processing === $intent->id ) {
            return true;
        }
        
        set_transient( $transient_name, $intent->id, 5 * MINUTE_IN_SECONDS );

        return false;
    }
    /**
     * Release the order after processing order.
     * @since 3.3.0
     */
    public function unlock_order_payment( $order ) {
        $order_id       = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();
        delete_transient( 'eh_stripe_processing_intent_' . $order_id );
    }
    /**
     *  change order status on fail SCA authentication
     * @since 3.3.0
     */
    public function failed_sca_authenticate( $order, $intent ) {
        if ( 'failed' === $order->get_status() ) { // Chack order already failed.
            return;
        }
        
        
        $status_message = ( $intent->last_payment_error )
            ? sprintf( __( 'Stripe SCA authentication failed. Reason: %s', 'payment-gateway-stripe-and-woocommerce-integration' ), $intent->last_payment_error->message )
            : __( 'Stripe SCA authentication failed.', 'payment-gateway-stripe-and-woocommerce-integration' );
        $order->update_status( 'failed', $status_message ); 
    }

    /**
     * Order Pay page
     * @since 3.3.0
     */
    public function verify_payment_intent_verification_in_order_pay(  ) {

        if ( ( ! is_wc_endpoint_url( 'order-pay' ) || ! isset( $_GET['eh-stripe-confirmation'] ) ) ) {

            return ;
        }
       
        add_filter( 'woocommerce_checkout_show_terms', '__return_false' );
        add_filter( 'woocommerce_pay_order_button_html', '__return_false' );
        add_filter( 'woocommerce_available_payment_gateways', array( $this, '__return_empty_array' ) );
        add_filter( 'woocommerce_no_available_payment_methods_message', array( $this, 'change_no_available_methods_message' ) );
        add_action( 'woocommerce_pay_order_after_submit', array( $this, 'add_payment_intent_hidden_items' ) );
        
        
        
        return array();
    }


    function __return_empty_array() {
        return array();
    }
    public function change_no_available_methods_message() {
        return wpautop( __( "Need to autherise the payment.", 'payment-gateway-stripe-and-woocommerce-integration' ) );
    }
    /**
     * Add payment intent fields for order pay page 
     * @since 3.3.0
     */
    public function add_payment_intent_hidden_items() {

        $order     = wc_get_order( absint( get_query_var( 'order-pay' ) ) );
        
        $intent    = $this->get_payment_intent_from_order( $order );
        $order_id  = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();
        
        add_post_meta( $order_id, '_eh_stripe_payment_intent', $intent->id); 

        $query_args  = array(
            'order'            => $order_id,
            '_wpnonce'         => wp_create_nonce( 'eh_stripe_confirm_payment_intent' ),
            'redirect_to'      => rawurlencode( $this->get_return_url( $order ) ),
            'is_pay_for_order' => true,
        );
       
        $verification_url = add_query_arg(
            $query_args,
            WC_AJAX::get_endpoint( 'eh_stripe_verify_payment_intent' )
        );
         
        echo '<input type="hidden" id="eh-stripe-intent-id" value="' . esc_attr( $intent->client_secret ) . '" />';
        echo '<input type="hidden" id="eh-stripe-intent-return" value="' . esc_attr( $verification_url ) . '" />';
    }

}


