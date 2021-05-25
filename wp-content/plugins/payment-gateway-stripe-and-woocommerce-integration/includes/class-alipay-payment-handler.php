<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles and process alipay orders and payments
 *
 */
class EH_Alipay_Payment_Handler extends WC_Payment_Gateway_CC{

	public function __construct() {

		add_action( 'wp', array( $this, 'eh_redirect_alipay_order' ),1 );

		\Stripe\Stripe::setApiKey(EH_Stripe_Payment::get_stripe_api_key());
	}

	/**
	 *Gets order id of alipay orders after redirection to authorisation page.
	 *
	 */
	public function eh_redirect_alipay_order() {

		if ( ! is_order_received_page() || empty( $_GET['client_secret'] ) || empty( $_GET['source'] ) ) {
			return;
		}

		$order_id = intval( $_GET['order_id'] );

		$this->eh_alipay_payment( $order_id );
	}


	/**
	 * Process alipay payment requests that are redirected.
	 */
	public function eh_alipay_payment( $order_id, $retry = true, $error = false ) {
		
	
		try {
			$source = sanitize_text_field( $_GET['source'] );

			if ( empty( $source ) || empty( $order_id ) ) {
				return;
			}

			$order = wc_get_order( $order_id );

			if ( ! is_object( $order ) ) {
				return;
			}

			if ( 'processing' === $order->get_status() || 'completed' === $order->get_status() || 'on-hold' === $order->get_status() ) {
				return;
			}
			
            $retrive_source = \Stripe\Source::retrieve($source);

			if ( 'failed' === $retrive_source->status || 'canceled' === $retrive_source->status ) {
				throw new Exception ( __( 'Unable to process this payment, please try again.', 'payment-gateway-stripe-and-woocommerce-integration' ));
			}

			if ( 'consumed' === $retrive_source->status ) {
				return;
			}

			if ( 'chargeable' !== $retrive_source->status ) {
				return;
			}

			// Prep source object.
			$eh_source_object           = new stdClass();
			$eh_source_object->token_id = '';
			$eh_source_object->source   = $retrive_source->id;
			$eh_source_object->status   = 'chargeable';

			$response = \Stripe\Charge::create($this->eh_make_charge_params( $order, $eh_source_object ));




			$this->eh_process_payment_response( $response, $order );

		} catch ( Exception $e ) {

			$order->update_status( 'failed', sprintf( __( 'Stripe payment failed: %s', 'payment-gateway-stripe-and-woocommerce-integration' ),$e->getMessage() ) );
			
			wc_add_notice( $e->getMessage(), 'error' );
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
	}
	

	/**    
	 * gets required parameters for creating stripe charge.
	 *
	 */
	public function eh_make_charge_params( $order, $prepared_source ) {

		$stripe_settings                 = get_option( 'woocommerce_eh_stripe_pay_settings' );

		$post_data                       =  array();
		$currency                        =  $order->get_currency();
		$post_data['currency']           =  strtolower( $currency);
		$post_data['amount']             =  EH_Stripe_Payment::get_stripe_amount( $order->get_total(), $currency );
		$post_data['description']        =  sprintf( __( '%1$s - Order %2$s', 'payment-gateway-stripe-and-woocommerce-integration' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );
		$billing_email                   =  (WC()->version < '2.7.0') ? $order->billing_email      : $order->get_billing_email();
		$billing_first_name              =  (WC()->version < '2.7.0') ? $order->billing_first_name : $order->get_billing_first_name();
		$billing_last_name               =  (WC()->version < '2.7.0') ? $order->billing_last_name  : $order->get_billing_last_name();
		
		$post_data['shipping']['name']   =  $billing_first_name . ' ' . $billing_last_name;
		$post_data['shipping']['phone']  =  (WC()->version < '2.7.0') ? $order->billing_phone : $order->get_billing_phone();

		$post_data['shipping']['address']['line1']       = (WC()->version < '2.7.0') ? $order->shipping_address_1 : $order->get_shipping_address_1();
		$post_data['shipping']['address']['line2']       = (WC()->version < '2.7.0') ? $order->shipping_address_2 : $order->get_shipping_address_2();
		$post_data['shipping']['address']['state']       = (WC()->version < '2.7.0') ? $order->shipping_state     : $order->get_shipping_state();
		$post_data['shipping']['address']['city']        = (WC()->version < '2.7.0') ? $order->shipping_city      : $order->get_shipping_city();
		$post_data['shipping']['address']['postal_code'] = (WC()->version < '2.7.0') ? $order->shipping_postcode  : $order->get_shipping_postcode();
		$post_data['shipping']['address']['country']     = (WC()->version < '2.7.0') ? $order->shipping_country   : $order->get_shipping_country();
		
		$post_data['metadata']  = array(
			__( 'customer_name', 'payment-gateway-stripe-and-woocommerce-integration' ) => sanitize_text_field( $billing_first_name ) . ' ' . sanitize_text_field( $billing_last_name ),
			__( 'customer_email', 'payment-gateway-stripe-and-woocommerce-integration' ) => sanitize_email( $billing_email ),
			'order_id' => $order->get_order_number(),
		);
		
		if ( $prepared_source->source ) {
			$post_data['source'] = $prepared_source->source;
		}
		
		return apply_filters( 'eh_alipay_generate_charge_request', $post_data, $order, $prepared_source );
	}
	
	
	/**
	 * Store extra meta data for an order and adds order notes for orders.
	 */
	public function eh_process_payment_response( $response, $order ) {
		
		$order_id = $order->get_order_number();
        
        // Stores charge data.
        $obj1 = new EH_Stripe_Payment();
        $charge_param = $obj1->make_charge_params($response, $order_id);
        add_post_meta($order_id, '_eh_stripe_payment_charge', $charge_param);
		
		$order_id  = version_compare(WC_VERSION, '2.7.0', '<') ? $order->id : $order->get_id();
		$captured = ( isset( $response->captured )) ? 'Captured' : 'Uncaptured';
        
		// Stores charge capture data.
		if ( version_compare(WC_VERSION, '2.7.0', '<') ) {
            update_post_meta( $order_id, '_eh_alipay_charge_captured', $captured );
        } else {
            $order->update_meta_data( '_eh_alipay_charge_captured', $captured );
		}
		
		$order_time = date('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600); 

		if ( 'Captured' === $captured ) {
			
			if ( 'pending' === $response->status ) {
				$order_stock_reduced = $order->get_meta( '_order_stock_reduced', true );

				if ( ! $order_stock_reduced ) {
				    wc_reduce_stock_levels( $order_id );
				}

				$order->set_transaction_id( $response->id );
				$order->update_status( 'on-hold');
				$order->add_order_note( __('Payment Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($response->status) .' [ ' . $order_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $response->source->type . '. ' . __('Charge Status :', 'payment-gateway-stripe-and-woocommerce-integration') . $captured . (is_null($response->balance_transaction) ? '' :'. Transaction ID : ' . $response->balance_transaction) );
			}
			
			if ( 'succeeded' === $response->status ) {
				$order->payment_complete( $response->id );

				$order->add_order_note( __('Payment Status : ', 'payment-gateway-stripe-and-woocommerce-integration') . ucfirst($response->status) .' [ ' . $order_time . ' ] . ' . __('Source : ', 'payment-gateway-stripe-and-woocommerce-integration') . $response->source->type . '. ' . __('Charge Status :', 'payment-gateway-stripe-and-woocommerce-integration') . $captured . (is_null($response->balance_transaction) ? '' :'. Transaction ID : ' . $response->balance_transaction) );
			}

		} else {
			 $order->set_transaction_id( $response->id );

			if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
			    wc_reduce_stock_levels( $order_id );
			}

			$order->update_status( 'on-hold', sprintf( __( 'Stripe alipay order meta (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'payment-gateway-stripe-and-woocommerce-integration' ), $response->id) );
		}
		return $response;
	}

}
 
new EH_Alipay_Payment_Handler();