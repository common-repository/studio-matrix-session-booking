<?php

add_filter('woocommerce_loop_add_to_cart_link', 'stmsb_remove_add_to_cart', 10, 2);

function stmsb_remove_add_to_cart($args, $product)
{
    global $wpdb;
    // check for the product type to run the plugin
    $sql       = "Select * from `" . STMSB_SESSION_SESSION_INFO_TABLE . "`";
    $arrays    = $wpdb->get_results($sql, ARRAY_A);
    $flag      = false; // to check the product type
    $productId = get_the_ID();
    foreach ($arrays as $array) {
        $tableProductId = $array['session_type_product'];
        if ($productId == $tableProductId) {
            $flag = true;
            break;
        }
    }
    if ($flag) {
        /** @noinspection PhpUndefinedMethodInspection */
        return "<a href='" . $product->get_permalink() . "'>View Product</a>";
    } else {
        return $args;
    }

}