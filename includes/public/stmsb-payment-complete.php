<?php

// use woocommerce_payment_complete_order_status for local testing
// use woocommerce_payment_complete for live
add_action('woocommerce_payment_complete', 'stmsb_after_payment_order');

function stmsb_after_payment_order()
{
    global $wpdb;
    // check for the product type to run the plugin
    $productId = null;
    $arrays    = $wpdb->get_results("Select * from `" . STMSB_SESSION_SESSION_INFO_TABLE . "`", ARRAY_A);
    $flag      = false; // to check the product type
    if ( ! empty(WC()->cart)) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            foreach ($arrays as $array) {
                if ($cart_item['product_id'] == $array['session_type_product']) {
                    $flag = true;
                    break;
                }
            }
        }
    }

    if ($flag) {
        $keyDateTime   = WC()->session->get('keyGmtDateTime');
        $userDateTime  = WC()->session->get('UserChooseTime');
        $order_id      = WC()->session->get('orderId');
        WC()->session->__unset( 'keyGmtDateTime' );
        WC()->session->__unset( 'UserChooseTime' );
        WC()->session->__unset( 'orderId' );
        $order         = new WC_Order($order_id);
        $name          = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $customerEmail = $order->get_billing_email();

        if ($keyDateTime != null) {
            $getEndTime     = $wpdb->get_row("select session_id, session_end,session_date_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_start = '$keyDateTime' ");
            $sessionId       = $getEndTime->session_id;

            //adding to data base
            $wpdb->update(STMSB_SESSION_PRODUCT_TABLE,
                array('order_id' => $order_id, 'customer_name' => $name, 'customer_email' => $customerEmail),
                array('session_id' => $sessionId));
        } else {
            for ($i = 0; $i < count($userDateTime); $i++) {
                $getEndTime       = $wpdb->get_row("select session_start, session_end, session_date_id, session_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_start = '$userDateTime[$i]' ");
                $mailGmtEndTime   = $getEndTime->session_end;
                $id               = $getEndTime->session_date_id;
                $sessionId        = $getEndTime->session_id;
                $sessionStartTime = $getEndTime->session_start;

                //adding data to database
                $wpdb->query("UPDATE `" . STMSB_SESSION_PRODUCT_TABLE . "` Set order_id = '$order_id', customer_name = '$name', customer_email = '$customerEmail' where session_id = $sessionId ");
                // making the same time slots unavailable for other products
                stmsb_check_for_conflicting_session_ids($id, $sessionStartTime, $mailGmtEndTime);
            }
        }
    }
}