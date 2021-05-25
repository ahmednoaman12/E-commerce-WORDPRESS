<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles stripe checkout payment method.
 *
 * @extends WC_Payment_Gateway
 *
 */
class Eh_Stripe_Checkout extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		$this->id                 = 'eh_stripe_checkout';
		$this->method_title       = __( 'Stripe Checkout', 'payment-gateway-stripe-and-woocommerce-integration' );
		$this->method_description = sprintf( __( 'Pay with Stripe Checkout', 'payment-gateway-stripe-and-woocommerce-integration' ) );
		$this->supports           = array(
			'products',
			'refunds',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
        
        $this->eh_stripe_option        = get_option("woocommerce_eh_stripe_pay_settings");
		$this->title                   = $this->get_option( 'eh_stripe_checkout_title' );
        $this->description             = $this->get_option( 'eh_stripe_checkout_description' );
        $this->method_description      = __( '<p style="max-width: 97%;"> Stripe Checkout redirects users to a secure, Stripe-hosted payment page to accept payment. You will have to specify an account name in Stripe <a href="https://dashboard.stripe.com/account" target="_blank">Dashboard</a> prior to configuring the settings. <a class="thickbox" href="'.EH_STRIPE_MAIN_URL_PATH . 'assets/img/stripe_checkout.gif?TB_iframe=true&width=100&height=100" >Preview</a></p><p><a target="_blank" href="https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/#stripe_checkout"> Read documentation </a>  </p>', 'payment-gateway-stripe-and-woocommerce-integration' );
        
        $this->enabled                 = $this->get_option( 'enabled' );
        $this->eh_order_button         = $this->get_option( 'eh_stripe_checkout_order_button');
        $this->order_button_text       = __($this->eh_order_button, 'payment-gateway-stripe-and-woocommerce-integration');
        $this->stripe_checkout_page_locale = $this->get_option( 'eh_stripe_checkout_page_locale');

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

        add_action( 'wc_ajax_eh_spg_stripe_checkout_order', array( $this, 'eh_spg_stripe_checkout_order_callback' ) );
        add_action( 'wc_ajax_eh_spg_stripe_cancel_order', array( $this, 'eh_spg_stripe_cancel_order' ) );
        add_action( 'woocommerce_available_payment_gateways',array($this, 'eh_disable_gateway_for_order_pay' ));
        add_action( 'set_logged_in_cookie', array( $this, 'eh_set_cookie_on_current_request' ) );
 
        // Set stripe API key.
        \Stripe\Stripe::setApiKey(EH_Stripe_Payment::get_stripe_api_key());
        
	}
    
	/**
	 * Initialize form fields in stripe checkout payment settings page.
     * @since 3.3.4
	 */
	public function init_form_fields() {


		$this->form_fields = array(

            'eh_stripe_checkout_form_title' => array(
                'type' => 'title',
                'class'=> 'eh-css-class',
            ),

			'enabled'      => array(
				'title'       => __('Stripe Checkout','payment-gateway-stripe-and-woocommerce-integration'),
				'label'       => __('Enable','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'checkbox',
                'default'     => 'no',
                'desc_tip'    => __('Enable to accept Stripe Checkout payments.','payment-gateway-stripe-and-woocommerce-integration')
			),
			'eh_stripe_checkout_title'         => array(
				'title'       => __('Title','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'text',
				'description' =>  __('Input title for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
				'default'     => __('Stripe Checkout', 'payment-gateway-stripe-and-woocommerce-integration'),
				'desc_tip'    => true,
			),
			'eh_stripe_checkout_description'     => array(
				'title'       => __('Description','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'textarea',
				'css'         => 'width:25em',
				'description' => __('Input texts for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
			 	'default'     => __('Secure payment via Stripe Checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
			 	'desc_tip'    => true
			),

			'eh_stripe_checkout_order_button'    => array(
				'title'       => __('Order button text', 'payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'text',
				'description' => __('Input a text that will appear on the order button to place order at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
				'default'     => __('Pay via Stripe Checkout', 'payment-gateway-stripe-and-woocommerce-integration'),
				'desc_tip'    => true
            ),
            'eh_stripe_checkout_page_locale' => array(
                'title'      => __( 'Locale', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'       => 'select',
                'class'      => 'wc-enhanced-select',
                'options'    => array(
                    
                    'auto' => __('Browser\'s locale', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'bg'  => __('Bulgarian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'cs'  => __('Czech', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'da'  => __('Danish', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'nl'  => __('Dutch', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'en'  => __('English', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'et'  => __('Estonian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'fi'  => __('Finnish', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'fr'  => __('French', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'de'  => __('German', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'el'  => __('Greek', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'hu'  => __('Hungarian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'it'  => __('Italian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'ja'  => __('Japanese', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'lv'  => __('Latvian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'lt'  => __('Lithuanian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'ms'  => __('Malay', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'mt'  => __('Maltese', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'nb'  => __('Norwegian Bokmål', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'pl'  => __('Polish', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'pt'  => __('Portuguese', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'ro'  => __('Romanian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'ru'  => __('Russian', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'zh'  => __('Simplified Chinese', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'sl'  => __('Slovak', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'es'  => __('Spanish', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'sv'  => __('Swedish', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'tr'  => __('Turkish', 'payment-gateway-stripe-and-woocommerce-integration'),
                ),
                'description' => sprintf(__('Choose a desired locale code from the drop down (Languages supported by Stripe Checkout are listed)', 'payment-gateway-stripe-and-woocommerce-integration')),
                'default'     => 'auto',
                'desc_tip'    => true,
            ),
		);   
    }
    
	/**
     * Checks if gateway should be available to use.
     * @since 3.3.4
     */
	public function is_available() {

        if ('yes' === $this->enabled) {
           
            if ('test' === $this->eh_stripe_option['eh_stripe_mode']) {
                if (! $this->eh_stripe_option['eh_stripe_test_publishable_key'] || ! $this->eh_stripe_option['eh_stripe_test_secret_key']) {
                    return false;
                }
            } else {
                if (!$this->eh_stripe_option['eh_stripe_live_secret_key'] || !$this->eh_stripe_option['eh_stripe_live_publishable_key']) {
                    return false;
                }
            }

            return true;
        }
        return false; 
    }
	

	/**
	 * Payment form on checkout page
     * @since 3.3.4
	 */
	public function payment_fields() {
		$user        = wp_get_current_user();
		$total       = WC()->cart->total;
		$description = $this->get_description();
		echo '<div class="status-box">';
        
        if ($this->description) {
            echo apply_filters('eh_stripe_desc', wpautop(wp_kses_post("<span>" . $this->description . "</span>")));
        }
        echo "</div>";
	}
    
    /**
     * loads stripe checkout scripts.
     * @since 3.3.4
     */
    public function payment_scripts(){
        
        if(is_checkout()){ 
            wp_enqueue_script('eh_checkout_script', EH_STRIPE_MAIN_URL_PATH . 'assets/js/eh-checkout.js',array('stripe_v3_js','jquery'),EH_STRIPE_VERSION ,true);

            if(isset($this->eh_stripe_option['eh_stripe_mode'])){

                if ('test' == $this->eh_stripe_option['eh_stripe_mode']) {
                    $public_key = $this->eh_stripe_option['eh_stripe_test_publishable_key'];
                    $secret_key = $this->eh_stripe_option['eh_stripe_test_secret_key'];
                } else {
                    $public_key = $this->eh_stripe_option['eh_stripe_live_publishable_key'];
                    $secret_key = $this->eh_stripe_option['eh_stripe_live_secret_key'];
                }
            }

            $eh_stripe_checkout_params = array(
                'key'                                           => isset($public_key) ? $public_key : '',
                'wp_ajaxurl'                                    => admin_url("admin-ajax.php"),
                'wc_ajaxurl'                                    => WC_AJAX::get_endpoint( '%%change_end%%' ),
            );
            wp_localize_script( 'eh_checkout_script', 'eh_stripe_checkout_params', $eh_stripe_checkout_params);
        }
    }

    /**
	 * Proceed with current request using new login session (to ensure consistent nonce).
	 */
	public function eh_set_cookie_on_current_request( $cookie ) {
		$_COOKIE[ LOGGED_IN_COOKIE ] = $cookie;
	}

    /**
	 * Process the payment
	 *
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
        $currency =  $order->get_currency();
        
        $total = EH_Stripe_Payment::get_stripe_amount( $order->get_total(), get_woocommerce_currency());
        
        $email = (WC()->version < '2.7.0') ? $order->billing_email : $order->get_billing_email();
        
        $images    = array();
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            
            $quantity_label =  0 < $cart_item['quantity'] ?  $cart_item['quantity']  : '';
            $_product       =  wc_get_product( $cart_item['data']->get_id()); 

            $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($_product->get_id()));
            if(isset($featured_image[0])){
                $images[] = $featured_image[0];
            }
           
        }

        $capture_method = 'automatic';
        if(isset($this->eh_stripe_option['eh_stripe_capture'])){

            if ('no' == $this->eh_stripe_option['eh_stripe_capture']) {
                $capture_method = 'manual';
            }
        }

        $session_data = array(

            'payment_method_types' => ['card'],
            
            'payment_intent_data' => [
                'description' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' Order #' . $order->get_order_number(),
                'capture_method' => $capture_method,
            ],
            'success_url'=> home_url().'/?wc-ajax=eh_spg_stripe_checkout_order'.'&sessionid={CHECKOUT_SESSION_ID}'.'&order_id='.$order_id.'&_wpnonce='.wp_create_nonce('eh_checkout_nonce'),
            'cancel_url' => home_url().'/?wc-ajax=eh_spg_stripe_cancel_order'.'&_wpnonce='.wp_create_nonce('eh_checkout_nonce').'&order_id='.$order_id,

        );
        //if there is image for the product add image to the line items 
        $count = 0;

        foreach ($images as $key => $value) {
            
            if( !empty($value)) {

               $count = 1;
            }
        }

        if( $count == 1 ) { 
            $session_data['line_items'] = array(
                [
                    'name'     => esc_html( __( 'Total', 'payment-gateway-stripe-and-woocommerce-integration' ) ),
                    'amount'   => $total,
                    'currency' => strtolower(get_woocommerce_currency()),
                    'quantity' => 1,
                    'images'   => array($images),
                ]
            );
        }else{
            $session_data['line_items'] = array(
                [
                    'name'     => esc_html( __( 'Total', 'payment-gateway-stripe-and-woocommerce-integration' ) ),
                    'amount'   => $total,
                    'currency' => strtolower(get_woocommerce_currency()),
                    'quantity' => 1,
                ]
            );
        }

        if(!empty($email)){
            $session_data['customer_email'] = $email;
        }

        $session_data['locale'] = $this->stripe_checkout_page_locale;

        $session = \Stripe\Checkout\Session::create($session_data);
        
        return  array(
            'result'   => 'success',
            'redirect' => $this->get_payment_session_checkout_url( $session->id, $order ),
        );
    }

    function get_payment_session_checkout_url($session_id, $order){

        return sprintf(
            '#response=%s',
            base64_encode(
                wp_json_encode(
                    array(
                        'session_id' => $session_id,
                        'order_id'      => (WC()->version < '2.7.0') ? $order->id : $order->get_id(),
                        'time'          => rand(
                            0,
                            999999
                        ),
                    )
                )
            )
        );

    }

	/**
     * creates order after checkout session is completed.
     * @since 3.3.4
     */
    public function eh_spg_stripe_checkout_order_callback() {
        
        if(!EH_Helper_Class::verify_nonce(EH_STRIPE_PLUGIN_NAME, 'eh_checkout_nonce'))
        {
            die(_e('Access Denied', 'payment-gateway-stripe-and-woocommerce-integration'));
        }
      
        $session_id = sanitize_text_field( $_GET['sessionid'] );
        $order_id = intval( $_GET['order_id'] );
        $order = wc_get_order($order_id);

        $obj  = new EH_Stripe_Payment();
            
        $order_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
        
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        $payment_intent_id = $session->payment_intent;

        add_post_meta( $order_id, '_eh_stripe_payment_intent', $payment_intent_id); 

        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
        $charge_details = $payment_intent->charges['data'];
    
        foreach($charge_details as $charge){

            $charge_response = $charge;  
        }

        $data = $obj->make_charge_params($charge_response, $order_id);
        
        if ($charge_response->paid == true) {

            if($charge_response->captured == true){
                $order->payment_complete($data['id']);
            }

            if (!$charge_response->captured) {
                $order->update_status('on-hold');
            }

            $order->set_transaction_id( $data['transaction_id'] );

            $order->add_order_note(__('Payment Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($data['status']) . ' [ ' . $order_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['source_type'] . '. ' . __('Charge Status :', 'payment-gateway-stripe-and-woocommerce-integration') . $data['captured'] . (is_null($data['transaction_id']) ? '' : '.'.__('Transaction ID : ','payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
            WC()->cart->empty_cart();
            add_post_meta($order_id, '_eh_stripe_payment_charge', $data);
            EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Charge - Order #' . $order_id);
            
            // Return thank you page redirect.
            $result =  array(
                'result'    => 'success',
                'redirect'  => $obj->get_return_url($order),
            );
        
            wp_safe_redirect($result['redirect']);
            exit;
            
        } else {
            wc_add_notice($data['status'], $notice_type = 'error');
            EH_Stripe_Log::log_update('dead', $charge_response, get_bloginfo('blogname') . ' - Charge - Order #' . $order_id);
        }
       
    }
    
    /**
     * Disable stripe checkout gateway for order-pay page 
     * @since 3.3.6
     */ 
    function eh_disable_gateway_for_order_pay( $available_gateways ) {
        if ( is_wc_endpoint_url( 'order-pay' ) ) {
            
           unset( $available_gateways['eh_stripe_checkout'] );
        }
        return $available_gateways;
    }


    public function eh_spg_stripe_cancel_order(){
        
        if(!EH_Helper_Class::verify_nonce(EH_STRIPE_PLUGIN_NAME, 'eh_checkout_nonce'))
        {
            die(_e('Access Denied', 'payment-gateway-stripe-and-woocommerce-integration'));
        }

        $order_id = intval( $_GET['order_id'] );
        $order = wc_get_order($order_id);
        
        if(isset($_GET['createaccount']) && absint($_GET['createaccount'])==1) 
		{
			$userID = (WC()->version < '2.7.0') ? $order->user_id : $order->get_user_id();
			wc_set_customer_auth_cookie( $userID );
        }
        
        wc_add_notice(__('You have cancelled Stripe Checkout Session. Please try to process your order again.', 'payment-gateway-stripe-and-woocommerce-integration'), 'notice');
        wp_redirect(wc_get_checkout_url());
        exit;
    }

    /**
     * process order refund
     * @since 3.3.4
     */
    public function process_refund($order_id, $amount = NULL, $reason = '') {
        
        $obj = new EH_Stripe_Payment();
		$client = $obj->get_clients_details();
		if ($amount > 0) {
			
			$data = get_post_meta($order_id, '_eh_stripe_payment_charge', true);
            $status = $data['captured'];

			if ('Captured' === $status) {
				$charge_id = $data['id'];
				$currency = $data['currency'];
				$total_amount = $data['amount'];
						
				$wc_order = new WC_Order($order_id);
				$div = $amount * ($total_amount / ((WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total()));
				$refund_params = array(
					'amount' => EH_Stripe_Payment::get_stripe_amount($div, $currency),
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
					$refund_response = $charge_response->refunds->create($refund_params);
					if ($refund_response) {
										
						$refund_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);
						
						$data = $obj->make_refund_params($refund_response, $amount, ((WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency()), $order_id);
						add_post_meta($order_id, '_eh_stripe_payment_refund', $data);
						$wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __('Status : refunded ', 'payment-gateway-stripe-and-woocommerce-integration') . ' [ ' . $refund_time . ' ] ' . (is_null($data['transaction_id']) ? '' : '<br>' . __('Transaction ID : ', 'payment-gateway-stripe-and-woocommerce-integration') . $data['transaction_id']));
						EH_Stripe_Log::log_update('live', $data, get_bloginfo('blogname') . ' - Refund - Order #' . $wc_order->get_order_number());
						return true;
					} else {
						EH_Stripe_Log::log_update('dead', $data, get_bloginfo('blogname') . ' - Refund Error - Order #' . $wc_order->get_order_number());
						$wc_order->add_order_note(__('Reason : ', 'payment-gateway-stripe-and-woocommerce-integration') . $reason . '.<br>' . __('Amount : ', 'payment-gateway-stripe-and-woocommerce-integration') . get_woocommerce_currency_symbol() . $amount . '.<br>' . __(' Status : Failed ', 'payment-gateway-stripe-and-woocommerce-integration'));
						return new WP_Error('error', $data->message);
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

}