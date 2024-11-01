<?php
function stmsb_edit_session_type()
{
    global $wpdb;
    $deleteId = sanitize_text_field($_POST['editId']);
    $sqlToGetInfo = "select session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_type_id = '$deleteId' ";
    $getInfo = $wpdb->get_row($sqlToGetInfo);
    if ($getInfo != null) {
        wp_send_json(true);
    } else {
        $wpdb->delete(STMSB_SESSION_SESSION_INFO_TABLE, array('session_type_id' => $deleteId));
        wp_send_json(false);
    }
}

add_action('wp_ajax_stmsb_edit_session_type', 'stmsb_edit_session_type');
add_action('wp_ajax_nopriv_stmsb_edit_session_type', 'stmsb_edit_session_type');


// Session Slot Ajax calls
function stmsb_delete_session_slot()
{
    global $wpdb;
    $orderCheck = 0;
    $deleteId = sanitize_text_field($_POST['deleteid']);
    $getInfo = $wpdb->get_row("select admin_booking, order_id, session_date_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_id = '$deleteId' ");
    $toDeleteOrderId = $getInfo->order_id;
    $toDeleteAdminBooking = $getInfo->admin_booking;
    $todeleteSessionDateId = $getInfo->session_date_id;
    if ($toDeleteOrderId > 0 || $toDeleteAdminBooking == 1) {
        $orderCheck = 1;

    }
    if ($orderCheck == 1) {
        wp_send_json(true);
    } else {
        if (stmsb_is_final_session($deleteId) == true) {
            $wpdb->delete(STMSB_SESSION_DATE_TABLE, array('session_date_id' => $todeleteSessionDateId));
        }
        $wpdb->delete(STMSB_SESSION_PRODUCT_TABLE, array('session_id' => $deleteId));
        wp_send_json(false);
    }
}

add_action('wp_ajax_stmsb_delete_session_slot', 'stmsb_delete_session_slot');
add_action('wp_ajax_nopriv_stmsb_delete_session_slot', 'stmsb_delete_session_slot');


// Returns Available dates based on session type picked
function stmsb_show_date_method()
{
    global $wpdb;
    $type = sanitize_text_field($_POST['typePicked']);
    $sqlToGetSessionType = $wpdb->get_row("select session_type_id from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type = '$type'");
    $sessionTypeArray = $sqlToGetSessionType->session_type_id;
    $sqlToGetSessionDates = $wpdb->get_results("select session_date_start,session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_type_id = '$sessionTypeArray' ORDER BY session_date_start DESC ");
    ?>
    <option disabled selected hidden value="default">----- Please Select Date -----</option><?php
    foreach ($sqlToGetSessionDates as $sqlToGetSessionDate) {
        $sessionDate = null;
        $sessionDateId = $sqlToGetSessionDate->session_date_id;
        $sqlToCheckOrders = $wpdb->get_results("select session_start,order_id,admin_booking from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id='$sessionDateId' ");
        $countSessionForDay = count($sqlToCheckOrders);
        $countBookedSessionForDay = 0;
        foreach ($sqlToCheckOrders as $sqlToCheckOrder) {
            $sessionDate = $sqlToCheckOrder->session_start;
            $order_id = $sqlToCheckOrder->order_id;
            $admin_booking = $sqlToCheckOrder->admin_booking;
            if ($order_id > 0 || $admin_booking == 1 || $order_id == -1) {
                $countBookedSessionForDay = $countBookedSessionForDay + 1;
            }
        }
        //check for date that are in future and shows only them.
        $unixDBDate = strtotime($sessionDate);
        $unixCurrentDate = strtotime(gmdate('Y-m-d H:i:s'));

        if ($countSessionForDay > $countBookedSessionForDay) {
            if ($unixDBDate >= $unixCurrentDate) {

                ?>
                <option value="<?php echo esc_html($sessionDateId); ?>"><?php
                    $sessionDateinAus = stmsb_convert_into_local($sessionDate);
                    echo esc_html(date(STMSB_DISPLAY_DATE_FORMAT, strtotime(stmsb_show_date_only($sessionDateinAus)))); ?></option> <?php
            }
        }
    }
}

add_action('wp_ajax_stmsb_show_date', 'stmsb_show_date_method');
add_action('wp_ajax_nopriv_stmsb_show_date', 'stmsb_show_date_method');

// Returns Available time based on date picked
function stmsb_show_available_time()
{
    global $wpdb;
    $dateId = sanitize_text_field($_POST['dateIdPicked']);
    $sqlToGetSessionTimes = $wpdb->get_results("select session_start, order_id, admin_booking from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$dateId'");
    ?>
    <option disabled selected hidden value="default">----- Please Select Time -----</option><?php
    foreach ($sqlToGetSessionTimes as $sqlToGetSessionTime) {
        $sessionOrderId = $sqlToGetSessionTime->order_id;
        $sessionAdminId = $sqlToGetSessionTime->admin_booking;
        $sessionStartTime = $sqlToGetSessionTime->session_start;
        if ($sessionOrderId == 0 && $sessionAdminId == 0) {
            ?>
            <option value="<?php echo esc_html($sessionStartTime); ?>"><?php
                $sessionTimeinAus = stmsb_convert_into_local($sessionStartTime);
                $sessionTime = date(STMSB_DISPLAY_TIME_FORMAT, strtotime(stmsb_show_time_only($sessionTimeinAus)));
                echo esc_html($sessionTime); ?></option> <?php
        }
    }
}

add_action('wp_ajax_stmsb_show_manual_time', 'stmsb_show_available_time');
add_action('wp_ajax_nopriv_stmsb_show_manual_time', 'stmsb_show_available_time');

function stmsb_show_details()
{
    $dateInTake = sanitize_text_field($_POST['id']);
    global $wpdb;
    $getDatesSql = "Select id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date = '$dateInTake' ";
    $getDates = $wpdb->get_results($getDatesSql);
    foreach ($getDates as $getDate) {
        $id = $getDate->id;
        $getProductsSql = "Select session_start,session_end,customer_name from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$id' ";
        $getProducts = $wpdb->get_results($getProductsSql);
        foreach ($getProducts as $getProduct) {
            $name = $getProduct->customer_name;
            $startTime = $getProduct->session_start;
            $endTime = $getProduct->session_end;
            echo esc_html($dateInTake);
            echo esc_html($name);
            echo esc_html($startTime);
            echo esc_html($endTime);
        }
    }
}

add_action('wp_ajax_stmsb_show_details', 'stmsb_show_details');
add_action('wp_ajax_nopriv_stmsb_show_details', 'stmsb_show_details');