<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class='wrapper' id='eh_stripe_overview'>
    <div style='height: 50%;position:relative' id='analytics'>
        
        <div style="width:30%;" class="top-analytics">
            <div class="loader" style="padding-top: 400px">
                <span style="position: absolute; width: 100%; vertical-align: middle; text-align: center; margin: -120px 0px;">
                    <h2>
                        <?php _e('Please Wait ...', 'payment-gateway-stripe-and-woocommerce-integration'); ?>
                    </h2>
                </span>
            </div>  
            <div class="status-box status-box-main">


                <h3 style="float: left;"><?php _e( 'Overview of Stripe','payment-gateway-stripe-and-woocommerce-integration' ); ?> </h3><h4 style="float: left;padding-left: 5px;"> ( <a href="<?php echo admin_url( 'admin.php?page=wt_stripe_menu'); ?>"><?php _e( 'Stripe Settings','payment-gateway-stripe-and-woocommerce-integration' ); ?></a> ) </h4>
                <input type="hidden" id="eh_currency_field" value="<?php echo get_woocommerce_currency();?>">
                <ul class="list-group">
                    
                    <li class="list-group-item" id="captured_status">
                        <span class="tag tag-default tag-pill pull-xs-right" id="eh_capture_total"></span>
                        <span class="button eh_graph_button" data-field= "Captured" data-title="<?php echo __('Captured Payments','payment-gateway-stripe-and-woocommerce-integration');?>" style="background: #c6e1c6;color: #5b841b;width: 30%;text-align: center;"><?php _e( 'Captured','payment-gateway-stripe-and-woocommerce-integration' ); ?></span>

                    </li>
                    <li class="list-group-item"id="uncaptured_status">
                        <span class="tag tag-default tag-pill pull-xs-right" id="eh_uncapture_total"> </span>
                        <span class="button eh_graph_button" data-field= "Uncaptured" data-title="<?php echo __('Uncaptured Payments','payment-gateway-stripe-and-woocommerce-integration');?>" style="color: #94660c;background: #f8dda7;width: 30%;text-align: center;"><?php _e( 'Uncaptured ','payment-gateway-stripe-and-woocommerce-integration' ); ?></span>
                    </li>
                    <li class="list-group-item" id="refund_status">
                        <span class="tag tag-default tag-pill pull-xs-right" id="eh_refunded_total"> </span>
                        <span class="button eh_graph_button" data-field= "succeeded" data-title="<?php echo __('Refunded Payments','payment-gateway-stripe-and-woocommerce-integration');?>" style="background: #e5e5e5;color: #777;width: 30%;text-align: center;"><?php _e( 'Refund ','payment-gateway-stripe-and-woocommerce-integration' ); ?></span>
                    </li>
                </ul>
                <div class= "eh-graph-hidden-field">
                    
                </div>
                <center>
                    <form class="form-horizontal">
                        <fieldset>
                            <div class="eh_inptgrp">
                            </div>
                            <div class="eh_date_range_picker">
                                <input type="text" id="eh_date_picker" name="eh_date_picker" style="width:200px !important;">
                                <p style="font-style:italic; font-weight: 400; font-size:12px; width:100%; "> <?php _e( 'Provide date range for displaying overview.','payment-gateway-stripe-and-woocommerce-integration' ); ?></p>
                            </div>
                        </fieldset>
                    </form>
                </center>
            </div>
        </div>
        <div style="width:63%;" class="top-analytics">
            <div class="status-box status-box-main">

                <div class="eh_graph_title_class"> <span  id="eh-graph-title" name="eh-graph-title"> <?php _e( 'Captured Payments ','payment-gateway-stripe-and-woocommerce-integration' ); ?></span></div>
                <div id='chart' style="height:95%; width:99%;"></div>

            </div>
        </div>
    </div>
</div>
   
<div>
    <?php
    
    function eh_stripe_overview_page_tabs($current = 'orders') {
        $tabs = array(
            'orders'   => __("Order Details", 'payment-gateway-stripe-and-woocommerce-integration'), 
            'stripe'  => __("Transaction Details", 'payment-gateway-stripe-and-woocommerce-integration')
        );
        $html =  '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ($tab == $current) ? 'nav-tab-active' : '';
            $html .=  '<a class="nav-tab ' . $class . '" href="?page=eh-stripe-overview&tab=' . $tab . '">' . $name . '</a>';
        }
        $html .= '</h2>';
        echo $html;
    }
    $tab = (!empty($_GET['tab']))? esc_attr($_GET['tab']) : 'orders';
    eh_stripe_overview_page_tabs($tab);

    wp_nonce_field('ajax-eh-spg-nonce', '_ajax_eh_spg_nonce');

    if($tab == 'orders' ) {
        ?>
        <div class="table-box table-box-main" id='order_section'>
            <div class="loader">
            </div>
            <?php include 'template-order-overview.php'; ?>
        </div>
        <?php
    }
    else {
        ?>
        <div class="table-box table-box-main" id='stripe_section'>
            <div class="loader">
            </div>
            <?php include 'template-stripe-overview.php'; ?>
        </div>    
        <?php
    }
    // Code after the tabs (outside)
    ?>
</div>