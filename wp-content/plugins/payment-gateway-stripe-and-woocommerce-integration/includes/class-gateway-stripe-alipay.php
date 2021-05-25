<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles Alipay payment method.
 *
 * @extends WC_Payment_Gateway
 *
 */
class EH_Alipay_Stripe_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		$this->id                 = 'eh_alipay_stripe';
		$this->method_title       = __( 'Alipay', 'payment-gateway-stripe-and-woocommerce-integration' );
		$this->method_description = sprintf( __( 'Accepts Alipay payments via Stripe. Supports currencies CNY, AUD, CAD, EUR, GBP, HKD, JPY, MYR, NZD, SGD, USD. <p> <a target="_blank" href="https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/#alipay"> Read documentation </a></p> ', 'payment-gateway-stripe-and-woocommerce-integration' ), admin_url( 'admin.php?page=wt_stripe_menu' ) );
		$this->supports           = array(
			'products',
			'refunds',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
        
		$stripe_settings               = get_option( 'woocommerce_eh_stripe_pay_settings' );
		
		$this->title                   = $this->get_option( 'eh_stripe_alipay_title' );
		$this->description             = $this->get_option( 'eh_stripe_alipay_description' );
		$this->enabled                 = $this->get_option( 'enabled' );
		$this->eh_order_button         = $this->get_option( 'eh_stripe_alipay_order_button');
        $this->order_button_text       = __($this->eh_order_button, 'payment-gateway-stripe-and-woocommerce-integration');


		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

        // Set stripe API key.
       
        \Stripe\Stripe::setApiKey(EH_Stripe_Payment::get_stripe_api_key());
        
	}


	/**
	 * Initialize form fields in alipay payment settings page.
	 */
	public function init_form_fields() {

		$stripe_settings   = get_option( 'woocommerce_eh_stripe_pay_settings' );
        
		$this->form_fields = array(

			'eh_stripe_alipay_form_title'   => array(
                'type'        => 'title',
                'class'       => 'eh-css-class',
            ),
			'enabled'                       => array(
				'title'       => __('Alipay','payment-gateway-stripe-and-woocommerce-integration'),
				'label'       => __('Enable','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'checkbox',
				'default'     => isset($stripe_settings['eh_stripe_alipay']) ? $stripe_settings['eh_stripe_alipay'] : 'no',
				'desc_tip'    => __('Enable to accept Alipay payments through Stripe.','payment-gateway-stripe-and-woocommerce-integration'),
			),
			'eh_stripe_alipay_title'         => array(
				'title'       => __('Title','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'text',
				'description' =>  __('Input title for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
				'default'     =>isset($stripe_settings['eh_stripe_alipay_title']) ? $stripe_settings['eh_stripe_alipay_title'] : __('Alipay', 'payment-gateway-stripe-and-woocommerce-integration'),
				'desc_tip'    => true,
			),
			'eh_stripe_alipay_description'     => array(
				'title'       => __('Description','payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'textarea',
				'css'         => 'width:25em',
				'description' => __('Input texts for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
			 	'default'     =>isset($stripe_settings['eh_stripe_alipay_description']) ? $stripe_settings['eh_stripe_alipay_description'] : __('Secure payment via Alipay.', 'payment-gateway-stripe-and-woocommerce-integration'),
			 	'desc_tip'    => true
			),

			'eh_stripe_alipay_order_button'    => array(
				'title'       => __('Order button text', 'payment-gateway-stripe-and-woocommerce-integration'),
				'type'        => 'text',
				'description' => __('Input a text that will appear on the order button to place order at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
				'default'     => isset($stripe_settings['eh_stripe_alipay_order_button']) ? $stripe_settings['eh_stripe_alipay_order_button'] :__('Pay via Alipay', 'payment-gateway-stripe-and-woocommerce-integration'),
				'desc_tip'    => true
			)
		);   
    }
    
	/**
	 * Get Alipay icon.
	 *
	 */
	public function get_icon() {
		$style = version_compare(WC()->version, '2.6', '>=') ? 'style="margin-left: 0.3em"' : '';
        $icon = '';
        
		$icon .= '<img src="' . WC_HTTPS::force_https_url(EH_STRIPE_MAIN_URL_PATH . 'assets/img/alipay.png') . '" alt="Alipay" width="52" title="Alipay" ' . $style . ' />';
		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}

	/**
     *Makes gateway available for only alipay supported currencies.
     */
    public function is_available() {

		$stripe_settings   = get_option( 'woocommerce_eh_stripe_pay_settings' );

		if ('yes' === $this->enabled) {
    	    $alipay_cur  = array('CNY','AUD', 'CAD', 'EUR', 'GBP', 'HKD', 'JPY','MYR', 'NZD', 'SGD', 'USD');
        
            if (! in_array( get_woocommerce_currency(), $alipay_cur ) ) {
		
				return false; 
			}
			if ('test' === $stripe_settings['eh_stripe_mode']) {
                if (! $stripe_settings['eh_stripe_test_publishable_key'] || ! $stripe_settings['eh_stripe_test_secret_key']) {
                    return false;
                }
            } else {
                if (!$stripe_settings['eh_stripe_live_secret_key'] || !$stripe_settings['eh_stripe_live_publishable_key']) {
                    return false;
                }
            }

			return true;
	    }
        return false; 
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
	 * Payment form on checkout page
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
	 * Builds the return URL from redirects.
	 *
	 */
	public function get_stripe_return_url( $order = null, $id = null ) {
		if ( is_object( $order ) ) {
			if ( empty( $id ) ) {
				$id = uniqid();
			}
			
			$order_id  = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();

			$args = array(
				'utm_nooverride' => '1',
				'order_id'       => $order_id,
			);

			return esc_url_raw( add_query_arg( $args, $this->get_return_url( $order ) ) );
		}

		return esc_url_raw( add_query_arg( array( 'utm_nooverride' => '1' ), $this->get_return_url() ) );
	}


	/**
	 * gets parameters for alipay source.
	 *
	 */
	public function eh_create_source( $order ) {
		$currency              =  $order->get_currency();
		$order_id              =  version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();
		$return_url            =  $this->get_stripe_return_url( $order );
		$post_data             =  array();
		$billing_first_name    =  (WC()->version < '2.7.0') ? $order->billing_first_name : $order->get_billing_first_name();
		$billing_last_name     =  (WC()->version < '2.7.0') ? $order->billing_last_name  : $order->get_billing_last_name();
		$name                  =  $billing_first_name . ' ' . $billing_last_name;
		$email                 =  (WC()->version < '2.7.0') ? $order->billing_email      : $order->get_billing_email();
		$phone                 =  (WC()->version < '2.7.0') ? $order->billing_phone      : $order->get_billing_phone();
		$post_data['owner']    =  array();
		if ( ! empty( $phone ) ) {
			$post_data['owner']['phone'] = $phone;
		}

		if ( ! empty( $name ) ) {
			$post_data['owner']['name'] = $name;
		}

		if ( ! empty( $email ) ) {
			$post_data['owner']['email'] = $email;
		}

		$post_data['amount']   = EH_Stripe_Payment::get_stripe_amount( $order->get_total(), $currency );
		$post_data['currency'] = strtolower( $currency );
		$post_data['type']     = 'alipay';
		

		$post_data['owner']['address']['line1']       = (WC()->version < '2.7.0') ? $order->billing_address_1  : $order->get_billing_address_1();
		$post_data['owner']['address']['line2']       = (WC()->version < '2.7.0') ? $order->billing_address_2  : $order->get_billing_address_2();
		$post_data['owner']['address']['state']       = (WC()->version < '2.7.0') ? $order->billing_state      : $order->get_billing_state();
		$post_data['owner']['address']['city']        = (WC()->version < '2.7.0') ? $order->billing_city       : $order->get_billing_city();
		$post_data['owner']['address']['postal_code'] = (WC()->version < '2.7.0') ? $order->billing_postcode   : $order->get_billing_postcode();
		$post_data['owner']['address']['country']     = (WC()->version < '2.7.0') ? $order->billing_country    : $order->get_billing_country();
		

		$post_data['redirect'] = array( 'return_url' => $return_url );

        return apply_filters( 'eh_stripe_alipay_source', $post_data, $order );
	}

	/**
	 * Process the payment
	 *
	 */
	public function process_payment( $order_id, $retry = true, $force_save_save = false ) {
		$order = wc_get_order( $order_id );
		$currency =  $order->get_currency();
		
		$response = \Stripe\Source::create($this->eh_create_source( $order ));
		
		if ( version_compare(WC_VERSION, '2.7.0', '<') ) {
            update_post_meta( $order_id, '_eh_stripe_source_id', $response->id );
        } else {
            $order->update_meta_data( '_eh_stripe_source_id', $response->id );
		}
		
		$order->save();
			
		
		if ( ! empty( $response->error ) ) {
			$order->add_order_note( $response->error->message );
			$order->save();
		}
			
		return array(
			'result'   => 'success',
		'redirect'     => esc_url_raw( $response->redirect->url ),
		);
	}

	/**
     *Process alipay refund process.
	 */
	public function process_refund($order_id, $amount = NULL, $reason = '') {
    
		$client = $this->get_clients_details();
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
						$obj = new EH_Stripe_Payment();
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