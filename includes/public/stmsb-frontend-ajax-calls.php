<?php
function stmsb_show_front_time() {
    $date             = sanitize_text_field($_POST['datePicked']);
    $currentProductId = sanitize_text_field($_POST['currentProductId']);
    $gmtStartDayTime  = sanitize_text_field(date(STMSB_STORE_DATE_TIME_FORMAT,strtotime(stmsb_get_gmt_for_user_timezone_day_start($date ))));
    global $wpdb;
    $getSessionTypeId = $wpdb->get_row( "Select session_type_id from `".STMSB_SESSION_SESSION_INFO_TABLE."` where session_type_product ='$currentProductId'" );
    $sessionTypeId    = $getSessionTypeId->session_type_id;
    $getSessionDateId = $wpdb->get_row( "Select session_date_id from `".STMSB_SESSION_DATE_TABLE."` where session_type_id ='$sessionTypeId'  and session_date_start ='$gmtStartDayTime'" );
    $getDateId        = $getSessionDateId->session_date_id;
    $sql              = "Select session_start, order_id, admin_booking from `".STMSB_SESSION_PRODUCT_TABLE."` where session_date_id = '$getDateId ' ";
    $getSessionStarts = $wpdb->get_results( $sql );
    ?>
    <option value="default">----- Please Select Time -----</option><?php

    foreach ( $getSessionStarts as $getSessionStart ) {
        $utcTime      = $getSessionStart->session_start;
        $adminBooking = $getSessionStart->admin_booking;
        $order        = $getSessionStart->order_id;
        if ( $order > 0 || $adminBooking > 0 || $order == - 1 ) {
            continue;
        }
        ?>
        <option class="withDates" value="<?= $utcTime ?>"><?php echo date(STMSB_DISPLAY_TIME_FORMAT,strtotime(stmsb_show_time_only(stmsb_convert_into_local($utcTime)))); ?> </option>
        <?php
    }
    exit;
}

add_action( 'wp_ajax_stmsb_show_front_time', 'stmsb_show_front_time' );
add_action( 'wp_ajax_nopriv_stmsb_show_front_time', 'stmsb_show_front_time' );
