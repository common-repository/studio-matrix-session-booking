<?php

add_action('woocommerce_checkout_update_order_meta', 'stmsb_checkout_process');

function stmsb_checkout_process($order_id)
{
    WC()->session->set('orderId', $order_id);
}