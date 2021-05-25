<?php
if (!defined('ABSPATH')) {
    exit;
}

  
$file_size_live=(file_exists(wc_get_log_file_path('eh_stripe_pay_live'))?$this->file_size(filesize(wc_get_log_file_path('eh_stripe_pay_live'))):'');
$file_size_dead=(file_exists(wc_get_log_file_path('eh_stripe_pay_dead'))?$this->file_size(filesize(wc_get_log_file_path('eh_stripe_pay_dead'))):'');
return array(

    'eh_stripe_settings_title' => array(
        'class'=> 'eh-css-class',
        'title' => sprintf('<span style="font-weight: bold; font-size: 15px; color:#23282d;">'.__( 'Settings','payment-gateway-stripe-and-woocommerce-integration' ).'<span>'),
        'type' => 'title'
    ),

    'enabled' => array(
        'title' => __('Credit/debit cards', 'payment-gateway-stripe-and-woocommerce-integration'),
        'label' => __('Enable', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'checkbox',
        'default' => 'no',
        'desc_tip' => __('Enable to accept credit/debit card payments through Stripe.', 'payment-gateway-stripe-and-woocommerce-integration'),
    ),
   
    'title' => array(
        'title' => __('Title', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'text',
        'description' => __('Input title for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'default' => __('Stripe', 'payment-gateway-stripe-and-woocommerce-integration'),
        'desc_tip' => true
    ),
    'description' => array(
        'title' => __('Description', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'textarea',
        'css' => 'width:25em',
        'description' => __('Input texts for the payment gateway displayed at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'default' => __('Secure payment via Stripe.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'desc_tip' => true
    ),
    'eh_stripe_order_button' => array(
        'title' => __('Order button text', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'text',
        'description' => __('Input a text that will appear on the order button to place order at the checkout.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'default' => __('Pay via Stripe', 'payment-gateway-stripe-and-woocommerce-integration'),
        'desc_tip' => true
    ),
    'eh_stripe_checkout_cards' => array(
        'title' => __('Allowed cards', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'multiselect',
        'class' => 'chosen_select',
        'css' => 'width: 350px;',
        'desc_tip' => __('Accepts payments using selected cards. Icon of the chosen cards will be displayed at the checkout. Discover, Diners Club, and JCB cards are supported only for USD.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'options' => array(
            'mastercard' => 'MasterCard',
            'visa' => 'Visa',
            'amex' => 'American Express',
            'discover' => 'Discover',
            'jcb' => 'JCB',
            'diners' => 'Diners Club'
        ),
        'default' => array(
            'mastercard',
            'visa',
            'diners',
            'discover',
            'amex',
            'jcb'
        )
    ),
   
    'eh_stripe_pay_actions_title' => array(
        'type' => 'title',
        'class'=> 'eh-css-class',
    ),

    'eh_stripe_inline_form' => array(
        'title' => __('Card fields in a row ', 'payment-gateway-stripe-and-woocommerce-integration').'<a  class="thickbox" href="'.EH_STRIPE_MAIN_URL_PATH . 'assets/img/card_fields_preview.png?TB_iframe=true&width=100&height=100"> [Preview] </a>',
        'label' => __('Enable', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'checkbox',
        'description' => __('Enable to have a single field for card number, expiration, and CVV.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'default' => 'no',
    ),    
    'eh_stripe_email_receipt' => array(
        'title' => __('Email transaction receipt', 'payment-gateway-stripe-and-woocommerce-integration'),
        'label' => __('Enable ', 'payment-gateway-stripe-and-woocommerce-integration'),
        'type' => 'checkbox',
        'description' => __('Enable to send transaction receipt via email to customers.', 'payment-gateway-stripe-and-woocommerce-integration'),
        'default' => 'no',
        'desc_tip' => true
    ),
    'eh_stripe_statement_descriptor' => array(
        'title' => __('Statement descriptor', 'payment-gateway-stripe-and-woocommerce-integration'),
        'description' => __('Enter a statement descriptor which will appear on customer\'s bank statements. Max 22 characters. ', 'payment-gateway-stripe-and-woocommerce-integration').'<a href="https://stripe.com/docs/statement-descriptors" target="_blank">'.__('Learn more','payment-gateway-stripe-and-woocommerce-integration').'</a>', 
        'type' => 'text',
    ),
);
