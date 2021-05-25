<?php

if (!defined('ABSPATH')) {
    exit;
}  

/**
 * EH_Stripe_Applepay class.
 *
 * @extends EH_Stripe_Payment
 */
class EH_Stripe_Applepay extends EH_Stripe_Payment {

    public function __construct() {
		$this->id        = 'eh_stripe_pay';
        $this->init_form_fields();
        $this->init_settings();
	}

    public function init_form_fields() {
        
        $this->form_fields = array(

            'eh_stripe_apple_form_title' => array(
                'title' => sprintf('<span style="font-weight: bold; font-size: 15px; color:#23282d;">'.__( 'Apple Pay','payment-gateway-stripe-and-woocommerce-integration' ).'<span>'),
                'type' => 'title',
                'description' => __('Accepts Apple Pay payment via Stripe.', 'payment-gateway-stripe-and-woocommerce-integration') .' <div class="wt_info_div"><p> '.__('To use Apple Pay, you need to register all of your web domains in Stripe dashboard.', 'payment-gateway-stripe-and-woocommerce-integration').' </p> <p>'.__('Registration steps:', 'payment-gateway-stripe-and-woocommerce-integration').'</p><ul class="wt_notice_bar_style"><li> '.__('Download the <a href="https://stripe.com/files/apple-pay/apple-developer-merchantid-domain-association"> domain association file </a> and host it at /.well-known/apple-developer-merchantid-domain-association on your site.', 'payment-gateway-stripe-and-woocommerce-integration').' </li> <li>'.__('For example, if you’re registering https://example.com, make that file available at https://example.com/.well-known/apple-developer-merchantid-domain-association.', 'payment-gateway-stripe-and-woocommerce-integration').'</li> <li>'.__('Next, register your domain with Apple. Go to the <a href="https://dashboard.stripe.com/account/payments/apple_pay" target="_blank">Apple Pay tab </a> in the account settings of your Dashboard and add your domain. All domains, whether in production or testing, must be registered.', 'payment-gateway-stripe-and-woocommerce-integration').'</li></ul> <p> '.__('We recommend you to test the configuration with  a payment method saved in the Apple Wallet on a', 'payment-gateway-stripe-and-woocommerce-integration').' <a href="https://support.apple.com/en-us/HT208531" target="_blank">'.__('compatible device.', 'payment-gateway-stripe-and-woocommerce-integration').'</a></p> </div><p> <a target="_blank" href="https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/#apple_pay"> '.__('Read documentation', 'payment-gateway-stripe-and-woocommerce-integration').' </a></p>',
            ),
            'eh_stripe_apple_pay_title' => array(
                'class'=> 'eh-css-class',
                'type' => 'title',
            ),
            'eh_stripe_apple_pay' => array(
                'title' => __('Apple Pay', 'payment-gateway-stripe-and-woocommerce-integration'),
                'label' => __('Enable', 'payment-gateway-stripe-and-woocommerce-integration'),
                'type' => 'checkbox',
                'description' => __('Enable to accept payment via Apple Pay.', 'payment-gateway-stripe-and-woocommerce-integration'),
                'default' => 'no',
                'desc_tip' => true
            ),

            'eh_stripe_apple_pay_options' => array(
                'title' => __('Show on pages', 'payment-gateway-stripe-and-woocommerce-integration'),
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'css' => 'width: 350px;',
                'desc_tip' => __('Apple Pay button will be shown on selected pages.', 'payment-gateway-stripe-and-woocommerce-integration'),
                'options' => array(
                    'cart' => 'Cart page',
                    'checkout' => 'Checkout page',
                    'product' => 'Product page'
                ),
                'default' => array(
                    'checkout',
                    'cart',
                    'product'
                )
            ),

            'eh_stripe_apple_pay_spiliter' => array(
                'title'       => __('Separator', 'payment-gateway-stripe-and-woocommerce-integration'),
                'description' => __( 'Separator will be displayed between the Apple Pay button and the order button at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'text',
                'default'     => '--OR--',
                'desc_tip'    => true,
            ),
            'eh_stripe_apple_pay_description' => array(
                'title' => __('Description', 'payment-gateway-stripe-and-woocommerce-integration'),
                'type' => 'textarea',
                'css' => 'width:25em',
                'description' => __('Description will be displayed between the Apple Pay button and the separator at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
                'default' => __('Pay via Apple Pay', 'payment-gateway-stripe-and-woocommerce-integration'),
                'desc_tip' => true
            ),
            'eh_stripe_apple_pay_style_title' => array(
                'type' => 'title',
                'class'=> 'eh-table-css-class',
                'title' => sprintf('<span style="font-weight: bold; font-size: 15px; color:#23282d;">'.__( 'Button Settings ','payment-gateway-stripe-and-woocommerce-integration' ).'</span> <a  class="thickbox" href="'.EH_STRIPE_MAIN_URL_PATH . 'assets/img/applepay_preview.png?TB_iframe=true&width=100&height=100"> <small> [Preview] </small> </a>'),
            ),
            'eh_stripe_apple_pay_position_checkout' => array(
                'title' => __('Position', 'payment-gateway-stripe-and-woocommerce-integration'),
                'type' => 'select',
                'class'       => 'wc-enhanced-select',
                'options' => array(
                    'above' => __('Above', 'payment-gateway-stripe-and-woocommerce-integration'),
                    'below' => __('Below', 'payment-gateway-stripe-and-woocommerce-integration')
                ),
                'description' => sprintf(__('Choose to position the Apple Pay button above or below the order button in the checkout page.', 'payment-gateway-stripe-and-woocommerce-integration')),
                'default' => 'below',
                'desc_tip' =>true
            ),
            'eh_stripe_apple_color' => array(
                'title'       => __( 'Color', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Choose the button color from the standard  white or black for it to appear accordingly at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => 'black',
                'desc_tip'    => true,
                'options'     => array(
                        'white' => __( 'White', 'payment-gateway-stripe-and-woocommerce-integration' ),
                        'black' => __( 'Black', 'payment-gateway-stripe-and-woocommerce-integration' ),
                )
            ),
            'eh_stripe_apple_pay_type' => array(
                'title'       => __( 'Text', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Choose an appropriate button text from the drop down which will appear at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration' ),
                'default'     => 'pay',
                'desc_tip'    => true,
                'options'     => array(
                        'pay'       => __( 'Apple Pay', 'payment-gateway-stripe-and-woocommerce-integration' ),
                        'buy'       => __( 'Buy with Apple Pay', 'payment-gateway-stripe-and-woocommerce-integration' ),
                        'set-up'    => __( 'Set up Apple Pay', 'payment-gateway-stripe-and-woocommerce-integration' ),
                )
            ),
            
            'eh_stripe_apple_pay_language' => array(
                'title'       => __('Text language', 'payment-gateway-stripe-and-woocommerce-integration'),
                'description' => sprintf(__( '<span class="eh-desp-class"> Input language code for Apple Pay button text. Defaulted to ‘en’. <a href="http://www.mathguide.de/info/tools/languagecode.html" target="_blank">Available codes</a></span>', 'payment-gateway-stripe-and-woocommerce-integration' )),
                'type'        => 'text',
                'default'     => 'en',
                'desc_tip'    => __(' Displays Apple Pay button text in selected language.', 'payment-gateway-stripe-and-woocommerce-integration')
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