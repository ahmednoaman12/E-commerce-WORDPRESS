<?php

if (!defined('ABSPATH')) {
    exit;
}  

/**
 * EH_Stripe_Payment_Request class.
 *
 * @extends EH_Stripe_Payment
 */
class EH_Stripe_Payment_Request extends EH_Stripe_Payment {

    public function __construct() {
		$this->id        = 'eh_stripe_pay';
        $this->init_form_fields();
        $this->init_settings();
	}

    public function init_form_fields() {
        
        $this->form_fields = array(
            'eh_payment_request_form_title' => array(
                'title' => sprintf('<span style="font-weight: bold; font-size: 15px; color:#23282d;">'.__( 'Payment Request Button','payment-gateway-stripe-and-woocommerce-integration' ).'<span>'),
                'type' => 'title',
                'description' => '<p style="max-width: 97%;">'.__( 'Accepts payments via Google Pay or chrome payment methods. It works if the customer has set up Google Pay on a device or have cards saved on a supporting browser.</p><p> <a target="_blank" href="https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/#google_pay"> Read documentation </a></p> ','payment-gateway-stripe-and-woocommerce-integration' ),
            ),
            'eh_payment_request_title' => array(
                'class'=> 'eh-css-class',
                'type' => 'title',
            ),
            'eh_payment_request' => array(
                'title'       => __( 'Payment Request Button', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'label'       => __( 'Enable', 'payment-gateway-stripe-and-woocommerce-integration' ), 
                'type'        => 'checkbox',
                'desc_tip'    => __( 'Enable to accept payments via Google Pay or chrome payment methods.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => 'no',
            ),
            'eh_payment_request_button_enable_options' => array(
                'title' => __('Show on page', 'payment-gateway-stripe-and-woocommerce-integration'),
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'css' => 'width: 350px;',
                'desc_tip' => __('Payment Request Button will be shown on selected pages.', 'payment-gateway-stripe-and-woocommerce-integration'),
                'options' => array(
                    'product' => 'Product page',
                    'cart' => 'Cart page',
                    'checkout' => 'Checkout page',
                ),
                'default' => array(
                    'product',
                    'cart',
                    'checkout'
                )
            ),
            'eh_payment_request_style_title' => array(
                'type' => 'title',
                'class'=> 'eh-table-css-class',
                'title' => sprintf('<span style="font-weight: bold; font-size: 15px; color:#23282d;">'.__( 'Button Settings','payment-gateway-stripe-and-woocommerce-integration' ).'<span> <a  class="thickbox" href="'.EH_STRIPE_MAIN_URL_PATH . 'assets/img/googlepay_preview.png?TB_iframe=true&width=100&height=100"> <small> [Preview] </small> </a>'),
            ),

            'eh_payment_request_button_type' => array(
                'title'       => __( 'Type', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Displays the chosen button type at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => 'buy',
                'desc_tip'    => true,
                'options'     => array(
                    'default' => __( 'Pay', 'payment-gateway-stripe-and-woocommerce-integration' ),
                    'buy'     => __( 'Buy', 'payment-gateway-stripe-and-woocommerce-integration' ),
                    'donate'  => __( 'Donate', 'payment-gateway-stripe-and-woocommerce-integration' ),
                ),
            ),
            'eh_payment_request_button_theme' => array(
                'title'       => __( 'Theme', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Displays the chosen color scheme at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => 'default',
                'desc_tip'    => true,
                'options'     => array(
                    'dark'          => __( 'Dark', 'payment-gateway-stripe-and-woocommerce-integration' ),
                    'light'         => __( 'Light', 'payment-gateway-stripe-and-woocommerce-integration' ),
                    'light-outline' => __( 'Light-Outline', 'payment-gateway-stripe-and-woocommerce-integration' ),
                ),
            ),
            'eh_payment_request_button_height' => array(
                'title'       => __( 'Height', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'text',
                'description' => __( ' Enter the button height in pixels. Width is set to 100%.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => '44',
                'desc_tip'    => true,
            ),
           
        );
    }
    public function admin_options() {
    
        parent::admin_options();
    }

    public function process_admin_options(){
        
        parent::process_admin_options();
    }

}