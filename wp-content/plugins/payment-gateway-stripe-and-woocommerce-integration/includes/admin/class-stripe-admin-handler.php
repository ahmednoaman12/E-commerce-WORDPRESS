<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles admin side.
 * @since 3.3.4
 * 
 */
class Eh_Stripe_Admin_Handler  {

    /**
	 * Constructor
	 */
	public function __construct() {
      
        add_action('admin_menu', array($this,'register_stripe_menu_page'));
        add_filter( 'woocommerce_screen_ids', array($this,'add_eh_screen_id' ));
        add_action('admin_enqueue_scripts', array($this,'register_admin_scripts'));
        add_action( 'admin_notices', array( $this, 'eh_menu_admin_notices' ) );
        add_action('init', 'add_thickbox' );
       
    }

    /**
	 * register admin scripts.
     * @since 3.3.6
	 */
    function register_admin_scripts(){
       
        wp_register_style('eh-stripe-admin-style', EH_STRIPE_MAIN_URL_PATH . '/assets/css/eh-admin.css',array(),EH_STRIPE_VERSION);
        wp_enqueue_style('eh-stripe-admin-style');

    }
  
    /**
	 * register menu options.
     * @since 3.3.4
	 */
    function register_stripe_menu_page() {
        
        add_menu_page(
            __( 'WebToffee Stripe','payment-gateway-stripe-and-woocommerce-integration' ), 
            __( 'WebToffee Stripe','payment-gateway-stripe-and-woocommerce-integration' ), 
            'manage_options', 
            'wt_stripe_menu',
            null,
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 40 40">
            <path fill="black" d="M19.5996094 7.33078125C19.6834375 7.33078125 19.7680469 7.31859375 19.8502344 7.29171875 21.1832813 6.87101562 22.5951563 6.66671875 24.1666406 6.66671875 25.5948437 6.66671875 26.8847656 6.83195312 28.1095312 7.17210937 28.5164063 7.286875 28.9713281 6.98898437 29.1365625 6.60976562 29.8103906 5.06921875 30.4459375 3.615 30.4459375 2.64820312 30.4459375 1.32984375 29.6272656.000078125 27.7970312.000078125 26.5714063.000078125 26.1157031.578671875 25.8675 1.07507812 25.8544531 1.06039062 25.8414844 1.045 25.8292188 1.03109375 25.4882031.6453125 24.9177344-7.10542736e-15 23.5611719-7.10542736e-15 22.0344531-7.10542736e-15 21.0432812 1.33140625 20.5639062 2.42023437 18.8809375 2.50328125 17.8873437 3.254375 17.8873437 4.46367187 17.8873437 5.15539062 18.270625 5.92117187 18.8883594 6.93195312 19.0429688 7.18515625 19.315625 7.33078125 19.5996094 7.33078125zM15.0382812 22.5774219C16.5714844 22.7458594 17.7857031 23.2846094 19.003125 23.8721875 19.94875 24.3278906 20.9261719 24.7991406 22.1639062 25.0505469 23.9990625 25.4224219 27.4145312 26.115 29.0503125 28.1153125 29.2114844 28.3122656 29.4490625 28.4213281 29.695625 28.4213281 29.7647656 28.4213281 29.8339844 28.4132031 29.903125 28.3953125 32.3347656 27.7710937 34.1316406 27.4065625 35.2001562 27.5139844L35.4426562 27.5327344C35.4614062 27.534375 35.4792969 27.5335156 35.4971875 27.5335156 35.8845312 27.5709375 36.3467969 27.1664844 36.3467969 26.7001562 36.3467969 26.5357812 36.29875 26.3819531 36.2165625 26.2525781 35.5492187 24.0732031 34.3260937 21.9833594 33.9208594 21.3225781 33.8875 13.5157812 32.1809375 8.33351562 24.1666406 8.33351562 16.1514844 8.33351562 14.4457812 13.5166406 14.4124219 21.3250781 14.27 21.5659375 14.2578125 21.8621875 14.3807031 22.1144531 14.5060156 22.3714844 14.7542187 22.545625 15.0382812 22.5774219zM24.1666406 19.1667188C22.7880469 19.1667188 21.6666406 18.0453125 21.6666406 16.6667188 21.6666406 15.5817188 22.3653906 14.6653906 23.3332813 14.3202344L23.3332813 14.1667188C23.3332813 13.7060938 23.7060156 13.3333594 24.1666406 13.3333594 24.6272656 13.3333594 25 13.7060938 25 14.1667188L25.8333594 14.1667188C26.2939844 14.1667188 26.6667187 14.5394531 26.6667187 15.0000781 26.6667187 15.4607031 26.2939844 15.8334375 25.8333594 15.8334375L24.1667187 15.8334375C23.7069531 15.8334375 23.3333594 16.2069531 23.3333594 16.6667969 23.3333594 17.1265625 23.706875 17.5001563 24.1667187 17.5001563 25.5453125 17.5001563 26.6667187 18.6215625 26.6667187 20.0001563 26.6667187 21.0851562 25.9679687 22.0014844 25.0000781 22.3466406L25.0000781 22.5001563C25.0000781 22.9607813 24.6273437 23.3335156 24.1667187 23.3335156 23.7060937 23.3335156 23.3333594 22.9607812 23.3333594 22.5001563L22.5 22.5001563C22.039375 22.5001563 21.6666406 22.1274219 21.6666406 21.6667969 21.6666406 21.2061719 22.039375 20.8334375 22.5 20.8334375L24.1666406 20.8334375C24.6264062 20.8334375 25 20.4599219 25 20.0000781 25 19.5403125 24.6264844 19.1667188 24.1666406 19.1667188zM7.78320312 23.0403125L.99609375 21.6828906C.75359375 21.6340625.498046875 21.6975781.304375 21.8553906.112265625 22.0140625 0 22.2500781 0 22.4999219L0 35.8332812C0 36.2939063.372734375 36.6666406.833359375 36.6666406L6.0546875 36.6666406C7.29164062 36.6666406 8.35609375 35.74375 8.5303125 34.520625L9.76890625 25.8454687C9.95609375 24.5319531 9.0853125 23.2998437 7.78320312 23.0403125z"/>
            <path fill="black" d="M35,29.1665625 C33.9595312,29.1665625 32.2047656,29.5375 30.6270312,29.9333594 C30.2407031,30.0303125 29.9907031,30.385 29.9994531,30.783125 C29.9998437,30.7997656 30,30.8164062 30,30.8332031 C30,32.09375 29.445,33.2005469 28.4391406,33.9517187 C27.4642188,34.6792969 26.2125781,35.0185938 24.5003125,35.0185938 C22.2578125,35.0185938 19.2059375,34.4378906 15.1692969,33.2441406 C14.7526562,33.1209375 14.4582031,32.7067969 14.5396875,32.28 C14.6335937,31.7878125 15.1303125,31.4930469 15.5957031,31.6322656 C19.4466406,32.7732031 22.4414062,33.3517969 24.5003125,33.3517969 C26.2532813,33.3517969 27.0198437,32.931875 27.4430469,32.6153125 C28.0257031,32.1807031 28.3333594,31.5646875 28.3333594,30.8330469 C28.3333594,30.6932812 28.3078906,30.5674219 28.2902344,30.4371094 C27.9747656,28.1364062 24.9453906,27.3149219 21.8327344,26.6826562 C20.3874219,26.3896875 19.3115625,25.8713281 18.2715625,25.3691406 C16.9889844,24.750625 15.7780469,24.1663281 14.1667188,24.1663281 C13.5278906,24.1663281 12.8532031,24.2128125 12.160625,24.3042187 C11.7232813,24.361875 11.4076563,24.7540625 11.440625,25.1939062 C11.4648438,25.5169531 11.4582813,25.8067969 11.4176563,26.0811719 L10.1790625,34.75875 C10.1676563,34.836875 10.1448438,34.9101562 10.1253125,34.9841406 C10.1236719,34.9914844 10.0829688,35.1444531 10.0813281,35.1509375 C9.9853125,35.5423437 10.1822656,35.946875 10.5500781,36.11125 C14.5149219,37.8853125 19.8078906,39.9996094 22.4999219,39.9996094 C25.7323437,39.9996094 32.3876562,36.728125 36.7903125,34.5634375 C37.9267187,34.0049219 38.8684375,33.5413281 39.4840625,33.2707812 C39.7823438,33.1397656 39.99625,32.8557812 39.9999308,32.53 C40.0211719,30.5511719 37.9595312,29.1665625 35,29.1665625 Z"/>
            </svg>'),57
        );
        add_submenu_page( 
            'wt_stripe_menu',
            __( 'General Settings','payment-gateway-stripe-and-woocommerce-integration' ), 
            __( 'General Settings','payment-gateway-stripe-and-woocommerce-integration' ),
            'manage_options', 
            'wt_stripe_menu', 
            array( $this, 'eh_stripe_menu_page' )
        );
       
        $eh_stripe = get_option("woocommerce_eh_stripe_pay_settings");
        
        if(isset($eh_stripe['overview'])){
            if ('yes' === $eh_stripe['overview']) {

                add_submenu_page('wt_stripe_menu',
                    __( 'Stripe Overview','payment-gateway-stripe-and-woocommerce-integration' ),
                    __( 'Stripe Overview','payment-gateway-stripe-and-woocommerce-integration' ),
                    'manage_options', 'eh-stripe-overview',
                    array('EH_Stripe_Overview','eh_stripe_template_display')
                );
            }
        }
       
    }
    /**
	 * Adds admin notice with useful links when plugin is not enabled.
     * @since 3.3.6
	 */
    function eh_menu_admin_notices(){

        //makes admin notice dismissible
        $dismiss_notice = filter_input( INPUT_GET, 'dismiss_notice', FILTER_SANITIZE_NUMBER_INT );
        
        $eh_stripe = get_option("woocommerce_eh_stripe_pay_settings");

		if ( $dismiss_notice ) {
			update_option( 'notice_dismissed', true );
        }
        
        $notice_dismissed = get_option( 'notice_dismissed' );
        if(! $notice_dismissed){
      
            
            if(! empty($eh_stripe)){
                if ( ('' != $eh_stripe['eh_stripe_test_secret_key'] || '' != $eh_stripe['eh_stripe_live_secret_key'] ) && ('' != $eh_stripe['eh_stripe_test_publishable_key'] || '' != $eh_stripe['eh_stripe_live_publishable_key'] )) {
                    return;
                }
            }
           
            $msg = sprintf('<p style="font-weight: 600;">'. __('Some useful links : ', 'payment-gateway-stripe-and-woocommerce-integration').'</p><table class="eh-admin-notice-table"><tr></tr><tr>
                <td><span>'. __('Stripe is available for businesses in over 30 countries', 'payment-gateway-stripe-and-woocommerce-integration').'</span></td><td> - </td>
                <td><a href="https://stripe.com/global" target="_blank">'. __('Country list', 'payment-gateway-stripe-and-woocommerce-integration').'</a></td></tr><tr>
                <td><span>'. __('If you don\'t have a Stripe Account, get it for free', 'payment-gateway-stripe-and-woocommerce-integration').'</span></td><td> - </td>
                <td><a href="https://dashboard.stripe.com/register" target="_blank">'. __('Create a Stripe account', 'payment-gateway-stripe-and-woocommerce-integration').'</a></td></tr><tr>
                <td><span>'.__('Get the access keys(publishable and secret) from your Stripe dashboard ', 'payment-gateway-stripe-and-woocommerce-integration').'</span></td><td> - </td>
                <td><a href="https://dashboard.stripe.com/account/apikeys" target="_blank">'. __('Get API keys', 'payment-gateway-stripe-and-woocommerce-integration').'</a></td></tr></table>');
            
            $dismiss_url = add_query_arg( 'dismiss_notice', '1');
        
            echo "<div class='notice notice-info'><p><a href='" . $dismiss_url . "' class='woocommerce-message-close notice-dismiss' style='position:relative;float:right;padding:0px;text-decoration:none;'></a>$msg</p></div>";
        }

        // adds saved changes message when form data is submitted
        if(isset($_GET['msg']) && $_GET['msg']==1){
            echo  '<div class="notice notice-success"> <p>'.__( "Your settings have been saved.", "payment-gateway-stripe-and-woocommerce-integration" ).'</p> </div>';
        }

        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ( 'wt_stripe_menu' != $page && 'wc-settings' != $page ){
            return;

        }
        if('wc-settings' == $page){
            $tab = (isset($_GET['tab'])) ? esc_attr($_GET['tab']) : false;
            $section = (isset($_GET['section'])) ? esc_attr($_GET['section']) : false;
            if( ($tab != 'checkout') || !$section){
                return;
            }
            if( ($section != 'eh_stripe_pay') && ($section != 'eh_alipay_stripe') && ($section != 'eh_stripe_checkout') )
            {
                return;
            }
        }

        if(!empty($eh_stripe)){

            if(isset($eh_stripe['eh_stripe_mode']) && ('test' === $eh_stripe['eh_stripe_mode'])){
                if (!$eh_stripe['eh_stripe_test_secret_key'] || !$eh_stripe['eh_stripe_test_publishable_key']) {
                    $setting_link = admin_url('admin.php?page=wt_stripe_menu');
                    $message = '<strong>Stripe Payment Gateway for WooCommerce: </strong>API keys missing <a href="'.$setting_link.'" >Please set your API keys here.</a>';
                    echo "<div class='notice notice-error is-dismissible' style='background-color: #ffd5d6;'><p>$message</p></div>";
                }
            } 

            if(isset($eh_stripe['eh_stripe_mode']) && ('test' === $eh_stripe['eh_stripe_mode'])){
                if ( ! empty( $eh_stripe['eh_stripe_test_secret_key'] ) && ! preg_match( '/^[rs]k_test_/', $eh_stripe['eh_stripe_test_secret_key'] ) ) {
                    $message = __('The "Test Secret Key" should start with "sk_test" or "rk_test", enter the correct key.', 'payment-gateway-stripe-and-woocommerce-integration');
                    echo "<div class='notice notice-error is-dismissible'><p>$message</p></div>";
                }
                if ( ! empty( $eh_stripe['eh_stripe_test_publishable_key'] ) && ! preg_match( '/^pk_test_/', $eh_stripe['eh_stripe_test_publishable_key'] ) ) {
                    $message = __('The "Test Publishable Key" should start with "pk_test", enter the correct key.', 'payment-gateway-stripe-and-woocommerce-integration');
                    echo "<div class='notice notice-error is-dismissible'><p>$message</p></div>";
                }
            } elseif(isset($eh_stripe['eh_stripe_mode']) && ('live' === $eh_stripe['eh_stripe_mode'])){
                if ( ! empty( $eh_stripe['eh_stripe_live_secret_key'] ) && ! preg_match( '/^[rs]k_live_/', $eh_stripe['eh_stripe_live_secret_key'] ) ) {
                    $message = __('The "Live Secret Key" should start with "sk_live" or "rk_live', 'payment-gateway-stripe-and-woocommerce-integration');
                    echo "<div class='notice notice-error is-dismissible'><p>$message</p></div>";
                }
                if ( ! empty( $eh_stripe['eh_stripe_live_publishable_key'] ) && ! preg_match( '/^pk_live_/', $eh_stripe['eh_stripe_live_publishable_key'] ) ) {
                    $message = __('The "Live Publishable Key" should start with "pk_live", enter the correct key.', 'payment-gateway-stripe-and-woocommerce-integration');
                    echo "<div class='notice notice-error is-dismissible'><p>$message</p></div>";
                }
            }
        }
        
    }
    
    /**
	 * Renders stripe settings page
     * @since 3.3.6
	 */
    function eh_stripe_menu_page(){

        ?>

        <div class="wrap">

            <h2><?php _e('WebToffee Stripe','payment-gateway-stripe-and-woocommerce-integration'); ?></h2>

            <?php
            if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab' ];
            } else{
                $active_tab = 'general_settings';
            }
            ?>
            <div class="eh_settings_left">
                <h2 class="nav-tab-wrapper">
                <a href="?page=wt_stripe_menu&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('General Settings','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=credit_card" class="nav-tab <?php echo $active_tab == 'credit_card' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Credit/Debit Cards','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=applepay" class="nav-tab <?php echo $active_tab == 'applepay' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Apple Pay','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=payment_request" class="nav-tab <?php echo $active_tab == 'payment_request' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Payment Request Button','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=alipay" class="nav-tab <?php echo $active_tab == 'alipay' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Alipay','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=checkout" class="nav-tab <?php echo $active_tab == 'checkout' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Stripe Checkout','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                    <a href="?page=wt_stripe_menu&tab=help_tab" class="nav-tab <?php echo $active_tab == 'help_tab' ? 'eh-nav-tab-active' : ''; ?>"><?php  _e('Help Guide','payment-gateway-stripe-and-woocommerce-integration'); ?></a>
                </h2>

                <?php
                    if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
                    else $tab = 'general_settings';
                    
                    switch ( $tab ){
                        case 'general_settings' :
                            ?>

                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages(); 
                                    $obj = new EH_Stripe_General_Settings();
                                    if( ! empty( $_POST ) ) {
                                    
                                        $obj->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    $obj->admin_options();
                                    
                                    wc_enqueue_js("
                                        $('.description').css({'font-style':'normal'});
                                        $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                    
                                        jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_test_secret_key, #woocommerce_eh_stripe_pay_eh_stripe_live_secret_key ' ).attr('autocomplete','new-password');
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

                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                        break;

                        case 'credit_card' :
                            ?>

                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages(); 
                                    if( ! empty( $_POST ) ) {
                                    
                                        $gateways = WC()->payment_gateways()->payment_gateways();
                                        $gateways[ 'eh_stripe_pay']->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    
                                    $obj = new EH_Stripe_Payment();
                                    
                                    $obj->admin_options();
                                    
                                    wc_enqueue_js("
                                        $('.description').css({'font-style':'normal'});
                                        $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                        jQuery( '#woocommerce_eh_stripe_pay_eh_stripe_statement_descriptor, #woocommerce_eh_stripe_pay_eh_stripe_live_secret_key ' ).attr('maxlength','22');
                                    ");

                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                        break;

                        case 'applepay' :
                            ?>
    
                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages();
                                    $obj = new EH_Stripe_Applepay(); 
                                    if( ! empty( $_POST ) ) {
                                       
                                        $obj->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    
                                    $obj->admin_options();
                                    
                                    wc_enqueue_js("
                                        $('.description').css({'font-style':'normal'});
                                        $('.eh-desp-class').css({'font-weight': '400','font-size': '12px','width':'100%','margin-top': '10px'});
                                        $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                   ");

                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                             
                        break;   

                        case 'payment_request' :
                            ?>
    
                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages();
                                    $obj = new EH_Stripe_Payment_Request(); 
                                    if( ! empty( $_POST ) ) {
                                       
                                        $obj->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    
                                    $obj->admin_options();
                                    
                                    wc_enqueue_js("
                                        $('.description').css({'font-style':'normal'});
                                        $('.eh-desp-class').css({'font-style': 'italic','font-weight': '400','font-size': '12px','width':'100%','margin-top': '10px'});
                                        $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                   ");

                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                             
                        break; 

                        case 'alipay' :
                            ?>

                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages(); 
                                    if( ! empty( $_POST ) ) {
                                    
                                        $gateways = WC()->payment_gateways()->payment_gateways();
                                        $gateways[ 'eh_alipay_stripe']->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    
                                    $obj = new EH_Alipay_Stripe_Gateway();
                                    
                                    $obj->admin_options();

                                    wc_enqueue_js("
                                        $('.description').css({'font-style':'normal'});
                                        $('.eh-desp-class').css({'font-style': 'italic','font-weight': '400','font-size': '12px','width':'100%','margin-top': '10px'});
                                        $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                   ");
                                    
                                    
                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                            
                        break;   
                        case 'checkout' :
                            ?>

                                <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">
                                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
                                    
                                    <?php
                                    WC_Admin_Settings::get_settings_pages(); 
                                    if( ! empty( $_POST ) ) {
                                    
                                        $gateways = WC()->payment_gateways()->payment_gateways();
                                        $gateways[ 'eh_stripe_checkout']->process_admin_options();
                                        wp_redirect($_SERVER['REQUEST_URI'].'&msg=1'); exit();
                                    }
                                    $obj = new Eh_Stripe_Checkout();
                                    
                                    $obj->admin_options();
                                    wc_enqueue_js("
                                    $('.description').css({'font-style':'normal'});
                                    $('.eh-desp-class').css({'font-style': 'italic','font-weight': '400','font-size': '12px','width':'100%','margin-top': '10px'});
                                    $('.eh-css-class').css({'border-top': 'dashed 1px #ccc','padding-top': '5px','width': '95%'}); 
                                    ");
                                    
                                    ?>
                                    <p class="submit">
                                        <?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
                                            <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'payment-gateway-stripe-and-woocommerce-integration' ); ?>" />
                                        <?php endif; ?>
                                        
                                    </p>
                                </form>
                            <?php
                        break; 
                        case 'help_tab' :
                            ?>
                                 <form method="post" class="eh_mainform" action="" enctype="multipart/form-data">   
                                     <div class="eh-tab-content">
                        
                                         <div class="eh_sub_tab_container">		
                                             <div class="eh_sub_tab_content" data-id="help-links" style="display:block;">
                                                 <h3><?php _e('Help Links','payment-gateway-stripe-and-woocommerce-integration'); ?></h3>
                                                 <ul class="eh-help-links">
                                                     <li>
                                                         <img src="<?php echo EH_STRIPE_MAIN_URL_PATH;?>assets/img/documentation.png">
                                                         <h3><?php _e('Documentation','payment-gateway-stripe-and-woocommerce-integration'); ?></h3>
                                                         <p><?php _e('Refer to our documentation to set up and get started.','payment-gateway-stripe-and-woocommerce-integration'); ?></p>
                                                         <a target="_blank" href="https://www.webtoffee.com/setting-up-woocommerce-stripe-payment-gateway-plugin/" class="button button-primary">
                                                             <?php _e('Documentation','payment-gateway-stripe-and-woocommerce-integration'); ?>        
                                                         </a>
                                                     </li>
                                                     <li>
                                                         <img src="<?php echo EH_STRIPE_MAIN_URL_PATH;?>assets/img/support.png">
                                                         <h3><?php _e('Support','payment-gateway-stripe-and-woocommerce-integration'); ?></h3>
                                                         <p><?php _e('We would love to help you on any queries or issues.','payment-gateway-stripe-and-woocommerce-integration'); ?></p>
                                                         <a target="_blank" href="https://www.webtoffee.com/support/" class="button button-primary">
                                                             <?php _e('Contact us','payment-gateway-stripe-and-woocommerce-integration'); ?>
                                                         </a>
                                                     </li>               
                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                 </form>
                             <?php
                        break;
                    }
                ?>
            </div>
            <div class="eh_settings_right">
                <?php include(EH_STRIPE_MAIN_PATH . "includes/eh-goto-pro.php"); ?>
            </div>
        </div>
        <?php    
    }
    
    
    /**
	 * includes screen ids of settings pages with woocommerce screen ids
     * @since 3.3.6
	 */
    public function add_eh_screen_id( $screen_ids ) {
        $screen_ids[] = 'toplevel_page_wt_stripe_menu';
        $screen_ids[] = 'webtoffee-stripe_page_stripe_alipay_menu';
        $screen_ids[] = 'webtoffee-stripe_page_stripe_checkout_menu';
        return $screen_ids;

    }

}