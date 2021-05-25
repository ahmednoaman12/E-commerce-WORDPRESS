<?php

class eh_Stripe_Intent_Manager {
    protected $gateway;

    public function __construct() {
      
		add_action( 'wc_ajax_eh_stripe_verify_payment_intent', array( $this, 'verify_intent' ) );
    }
    

    /**
     * verify payment intent
     */

    function verify_intent() {
		
        global $woocommerce;
        $gateway = new EH_Stripe_Payment();

        try {
			$order = $this->get_order_from_request();
		} catch ( Exception $e ) {
			/* translators: Error message text */
			$message = $e->getMessage();
			wc_add_notice( esc_html( $message ), 'error' );

			$redirect_url = $woocommerce->cart->is_empty()
				? get_permalink( woocommerce_get_page_id( 'shop' ) )
				: wc_get_checkout_url();

            $this->handle_error( $e, $redirect_url );

        }

        try {

			$gateway->verify_payment_intent_after_checkout( $order );

			if ( ! isset( $_GET['is_ajax'] ) ) {
                

               
				$redirect_url = isset( $_GET['redirect_to'] )
					? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) )
                    : $gateway->get_return_url( $order );
				wp_safe_redirect( $redirect_url );
            }
            
			exit;
		} catch ( Exception $e ) {

			$this->handle_error( $e, $gateway->get_return_url( $order ) );
		}
        




     }

     protected function handle_error( $e, $redirect_url ) {
		// `is_ajax` is only used for PI error reporting, a response is not expected.
		if ( isset( $_GET['is_ajax'] ) ) {
			exit;
		}

		wp_safe_redirect( $redirect_url );
		exit;
    }



    protected function get_order_from_request() {
		if(! EH_Helper_Class::verify_nonce(EH_STRIPE_PLUGIN_NAME, 'eh_stripe_confirm_payment_intent')) {
			throw new Exception( __( 'Something went wrong. Please try again.', 'payment-gateway-stripe-and-woocommerce-integration' ) );
		}

		// Load the order ID.
		$order_id = null;
		if ( isset( $_GET['order'] ) && absint( $_GET['order'] ) ) {
			$order_id = absint( $_GET['order'] );
		}

		// Retrieve the order.
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			throw new Exception( __( 'Payment verification error: Missing order ID for payment confirmation', 'payment-gateway-stripe-and-woocommerce-integration' ) );
		}

		return $order;
	}

}

$intent_manager = new eh_Stripe_Intent_Manager();