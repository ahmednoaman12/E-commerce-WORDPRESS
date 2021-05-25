<?php
if (!defined('ABSPATH')) {
    exit;
}
function eh_stripe_analytics()
{
    
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }

    $start      = sanitize_text_field($_POST['start']);
    $end        = sanitize_text_field($_POST['end']);
    $order_id   = eh_stripe_overview_get_order_ids();
    $temp_json  = array();
    $temp_json2 = array();
    $temp_json3 = array();

    for($i=0,$j=0;$i<count($order_id);$i++)
    {
        $id_data = get_post_meta($order_id[$i],'_eh_stripe_payment_charge',true);

        if(isset($id_data['captured']) && ($id_data['captured'] === 'Captured' ))
        {
            $id_date = date('Y-m-d',strtotime($id_data['created']));
            if(strtotime($id_date) >= strtotime($start) && strtotime($id_date) <= strtotime($end))
            {
                $temp_json[$j]['label'] = $id_date;
                $temp_json[$j]['value'] = floatval($id_data['order_amount']);
                $j++;
            }
        }
        if(isset($id_data['captured']) && ($id_data['captured'] === 'Uncaptured' ))
        { 
            
            $id_date = date('Y-m-d',strtotime($id_data['created']));
            if(strtotime($id_date) >= strtotime($start) && strtotime($id_date) <= strtotime($end))
            {
                $temp_json2[$j]['label2'] = $id_date;
                $temp_json2[$j]['value2'] = floatval($id_data['order_amount']);
                $j++;
            }
        }
    }

    $id = eh_stripe_overview_get_order_ids();

    for($i=0,$k=0;$i<count($id);$i++)
    {
       
        $data = get_post_meta( $id[$i] , '_eh_stripe_payment_refund');

        if($data!=='')
        {
            for($j=0;$j<count($data);$j++)
            {           

                if('succeeded' === $data[$j]['status'])
                
                {
                    $id_date = date('Y-m-d',strtotime($data[$j]['created']));
                 
                    if(strtotime($id_date) >= strtotime($start) && strtotime($id_date) <= strtotime($end))
                    {
                        $temp_json3[$k]['label3'] = $id_date;
                        $temp_json3[$k]['value3'] = floatval($data[$j]['order_amount']);
                        $k++;
                    }
                    
                }
            }
        }
    }
    $a[0]=array(
        'label' =>date('Y-m-d', strtotime('-1 day', strtotime($start))),
        'value' =>0
    );
    $c[0]=array(
        'label' =>date('Y-m-d', strtotime('+1 day', strtotime($end))),
        'value' =>0
    );
    $sum = array_reduce($temp_json, function ($a, $b) {
        isset($a[$b['label']]) ? $a[$b['label']]['value'] += $b['value'] : $a[$b['label']] = $b;  
        return $a;
    });
    if($sum!=null){
        $b = array_values($sum);         
    }
    else{
        $b = array();
    }
        
    if($b!=null)
    {
        $charge = array_merge_recursive($a,$b,$c);
    }
    else 
    {
        $charge = array_merge_recursive($a,$c);
    }


    $a2[0]=array(
        'label2' =>date('Y-m-d', strtotime('-1 day', strtotime($start))),
        'value2' =>0
    );
    $c2[0]=array(
        'label2' =>date('Y-m-d', strtotime('+1 day', strtotime($end))),
        'value2' =>0
    );
     
    $sum2 = array_reduce($temp_json2, function ($a2, $b2) {
        isset($a2[$b2['label2']]) ? $a2[$b2['label2']]['value2'] += $b2['value2'] : $a2[$b2['label2']] = $b2;  
        return $a2;
    });
    if($sum2!=null){
        $b2 = array_values($sum2);         
     }
     else{
        $b2 = array();
     }
     if($b2!=null)
    {
        $charge2 = array_merge_recursive($a2,$b2,$c2);
    }
    else 
    {
        $charge2 = array_merge_recursive($a2,$c2);
    }


    $a3[0]=array(
        'label3' =>date('Y-m-d', strtotime('-1 day', strtotime($start))),
        'value3' =>0
    );
    $c3[0]=array(
        'label3' =>date('Y-m-d', strtotime('+1 day', strtotime($end))),
        'value3' =>0
    );

    $sum3 = array_reduce($temp_json3, function ($a3, $b3) {
        isset($a3[$b3['label3']]) ? $a3[$b3['label3']]['value3'] += $b3['value3'] : $a3[$b3['label3']] = $b3;  
        return $a3;
    });
     if($sum3!=null){
        $b3 = array_values($sum3);         
     }
     else{
         $b3 = array();
     }
     if($b3!=null)
    {
        $charge3 = array_merge_recursive($a3,$b3,$c3);
    }
    else 
    {
        $charge3 = array_merge_recursive($a3,$c3);
    }

    die(json_encode(array(array_values($charge),array_values($charge2),array_values($charge3))));

}
function eh_order_status_update_callback()
{
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }

    $ids = sanitize_text_field($_POST['order_id']); //$_POST['order_id] is a string value of order ids seperated by ' , '
    $order_id = ($ids != '') ? explode(',', $ids) : '';
    $order_action = sanitize_text_field( $_POST['order_action'] );
    if(count($order_id)!=1)
    {
        for($i=0;$i<count($order_id);$i++)
        {
            $wc_order=  wc_get_order($order_id[$i]);
            switch ($order_action)
            {
                case 'processing':
                    $wc_order->update_status('processing');
                    break;
                case 'completed' :
                    $wc_order->update_status('completed');
                    break;
                case 'on-hold' :
                    $wc_order->update_status('on-hold');
                    break;
            }
        }
    }
    else
    {
        $wc_order=  wc_get_order($order_id[0]);
        switch ($order_action)
        {
            case 'processing':
                $wc_order->update_status('processing');
                break;
            case 'completed' :
                $wc_order->update_status('completed');
                break;
                //included order action on-hold , if any order is checked for bulk action for on-hold
            case 'on-hold' :   
                $wc_order->update_status('on-hold');
                break;
                
        }
    }    
    die('sucesss');
}
function eh_spg_list_order_all_callback()
{
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }
    $page = intval($_POST['paged']);
    $obj  = new Eh_Stripe_Order_Datatables();
    $obj->input();
    $obj->ajax_response($page);
 //  wp_enqueue_script('eh-custom');
}
function eh_spg_list_stripe_all_callback()
{
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }
    $page = intval($_POST['paged']);
    $obj  = new Eh_Stripe_Datatables();
    $obj->input();
    $obj->ajax_response($page);
//    wp_enqueue_script('eh-custom');
}
function eh_order_display_count_callback()
{
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }
    $value=  intval($_POST['row_count']);
    update_option('eh_order_table_row', $value);
    die('success');
}
function eh_stripe_display_count_callback()
{
    if(!EH_Helper_Class::check_write_access(EH_STRIPE_PLUGIN_NAME, 'ajax-eh-spg-nonce'))
    {
       die(_e('You are not allowed to view this page.', 'payment-gateway-stripe-and-woocommerce-integration'));
    }
    $value=  intval($_POST['row_count']);
    update_option('eh_stripe_table_row', $value);
    die('success');
}
add_action('wp_ajax_eh_spg_analytics', 'eh_stripe_analytics');
add_action('wp_ajax_eh_order_display_count', 'eh_order_display_count_callback');
add_action('wp_ajax_eh_stripe_display_count', 'eh_stripe_display_count_callback');
add_action('wp_ajax_eh_order_status_update', 'eh_order_status_update_callback');
add_action('wp_ajax_eh_spg_get_all_order', 'eh_spg_list_order_all_callback');
add_action('wp_ajax_eh_spg_get_all_stripe', 'eh_spg_list_stripe_all_callback');

