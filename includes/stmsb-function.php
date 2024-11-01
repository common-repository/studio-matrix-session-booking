<?php

function stmsb_show_error($message)
{
    $class = 'notice notice-error is-dismissible';
    $message = __($message, 'stmsb_session_booking');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

function stmsb_show_warning($message)
{
    $class = 'notice notice-warning is-dismissible';
    $message = __($message, 'stmsb_session_booking');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}


function stmsb_show_success($message)
{
    $class = 'notice notice-success is-dismissible';
    $message = __($message, 'stmsb_session_booking');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

function stmsb_get_year($date)
{
    if (($timestamp = strtotime($date)) !== false) {
        return date("Y", $timestamp);
    }
    return false;
}

function stmsb_check_woocommerce()
{
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        esc_html_e('This Plugin is an extension of woocommerce. Please install woocommerce to continue', 'stmsb_session_booking');
        return;
    }
}

function stmsb_is_final_session($sessionSlotId)
{
    global $wpdb;
    $count = 0;
    $getDateId = $wpdb->get_row("select session_date_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_id = '$sessionSlotId'");
    $dateId = $getDateId->session_date_id;
    $getDates = $wpdb->get_results("select session_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$dateId'");
    /** @noinspection PhpUnusedLocalVariableInspection */
    foreach ($getDates as $getDate) {
        $count = $count + 1;
    }
    if ($count == 1) {
        return true;
    } else {
        return false;
    }
}


function stmsb_check_for_conflicting_session_ids($sessionDateId, $sessionStartTime, $sessionEndTime)
{
    $conflictIdArray = stmsb_different_session_in_same_time_range($sessionDateId);
    for ($i = 0; $i < count($conflictIdArray); $i++) {
        $startTimeHolder = stmsb_get_product_start_time_for_date_id($conflictIdArray[$i]);
        $endTimeHolder = stmsb_get_product_end_time_for_date_id($conflictIdArray[$i]);
        $toCheckStartTime = strtotime($sessionStartTime);
        $toCheckEndTime = strtotime($sessionEndTime);
        for ($j = 0; $j < count($startTimeHolder); $j++) {
            if (($toCheckStartTime >= strtotime($startTimeHolder[$j]->session_start) && $toCheckStartTime < strtotime($endTimeHolder[$j]->session_end))
                || ($toCheckEndTime >= strtotime($startTimeHolder[$j]->session_start) && $toCheckEndTime < strtotime($endTimeHolder[$j]->session_end))) {
                stmsb_block_conflict_session_id($conflictIdArray[$i], $startTimeHolder[$j]->session_start,
                    $endTimeHolder[$j]->session_end);
            }
        }
    }
}

function stmsb_restore_conflicting_session_ids($sessionDateId, $sessionStartTime, $sessionEndTime)
{
    $conflictIdArray = stmsb_different_session_in_same_time_range($sessionDateId);
    for ($i = 0; $i < count($conflictIdArray); $i++) {
        $startTimeHolder = stmsb_get_product_start_time_for_date_id($conflictIdArray[$i]);
        $endTimeHolder = stmsb_get_product_end_time_for_date_id($conflictIdArray[$i]);
        $toCheckStartTime = strtotime($sessionStartTime);
        $toCheckEndTime = strtotime($sessionEndTime);
        for ($j = 0; $j < count($startTimeHolder); $j++) {
            if (($toCheckStartTime >= strtotime($startTimeHolder[$j]->session_start) && $toCheckStartTime < strtotime($endTimeHolder[$j]->session_end)) || ($toCheckEndTime >= strtotime($startTimeHolder[$j]->session_start) && $toCheckEndTime < strtotime($endTimeHolder[$j]->session_end))) {
                stmsb_restore_conflict_session_id($conflictIdArray[$i], $startTimeHolder[$j]->session_start,
                    $endTimeHolder[$j]->session_end);
            }
        }
    }
}

function stmsb_restore_conflict_session_id($sessionDateId, $startTime, $endTime)
{
    global $wpdb;
    $getDateId = $wpdb->get_row("select session_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId' and session_start ='$startTime' and session_end='$endTime'");
    $sessionId = $getDateId->session_id;
    $wpdb->query("UPDATE `" . STMSB_SESSION_PRODUCT_TABLE . "` Set order_id = '0' where session_id = $sessionId ");
}


function stmsb_block_conflict_session_id($sessionDateId, $startTime, $endTime)
{
    global $wpdb;
    $getDateId = $wpdb->get_row("select session_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId' and session_start ='$startTime' and session_end='$endTime'");
    $sessionId = $getDateId->session_id;
    $wpdb->query("UPDATE `" . STMSB_SESSION_PRODUCT_TABLE . "` Set order_id = '-1' where session_id = $sessionId ");
}

function stmsb_get_product_start_time_for_date_id_for_year($sessionDateId)
{
    global $wpdb;
    $dateStrings = $wpdb->get_results("select session_start from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId'");

    return $dateStrings;
}


function stmsb_get_product_start_time_for_date_id($sessionDateId)
{
    global $wpdb;
    $dateStrings = $wpdb->get_results("select session_start from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId' AND customer_name=' '");

    return $dateStrings;
}

function stmsb_get_product_end_time_for_date_id($sessionDateId)
{
    global $wpdb;
    $dateStrings = $wpdb->get_results("select session_end from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId' AND customer_name=' '");

    return $dateStrings;
}

function stmsb_different_session_in_same_time_range($sessionDateId)
{
    global $wpdb;
    $sessionDayStart = stmsb_get_session_day_start($sessionDateId);
    $dateStrings = $wpdb->get_results("select session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_start = '$sessionDayStart' ");
    $id = array();
    foreach ($dateStrings as $datestring) {
        $id[] = $datestring->session_date_id;
    }
    $value = stmsb_remove_array_item($id, $sessionDateId);

    return $value;
}

function stmsb_remove_array_item($array, $item)
{
    $index = array_search($item, $array);
    if ($index !== false) {
        unset($array[$index]);
        $array = array_values($array);
    }

    return $array;
}

function stmsb_get_session_day_start($sessionDateId)
{
    global $wpdb;
    $dateString = $wpdb->get_row("select session_date_start from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_id = '$sessionDateId' ");
    $dateTime = $dateString->session_date_start;

    return $dateTime;
}

function stmsb_convert_into_gmt($dateTime)
{
    $defaultTimeZone = date_default_timezone_get();
    date_default_timezone_set(STMSB_USER_TIMEZONE);
    $localTime = strtotime($dateTime);
    $dateTimeInGMT = gmstrftime('%d-%m-%Y %I:%M %p', $localTime);
    date_default_timezone_set($defaultTimeZone);

    return $dateTimeInGMT;
}

function stmsb_convert_into_local($gmtTime)
{
    $defaultTimeZone = date_default_timezone_get();
    date_default_timezone_set('GMT');
    $time = strtotime($gmtTime);
    date_default_timezone_set(STMSB_USER_TIMEZONE);
    $dateInLocal = date("Y-m-d H:i:s", $time);
    date_default_timezone_set($defaultTimeZone);

    return $dateInLocal;
}


function stmsb_get_gmt_for_user_timezone_day_start($date)
{
    $defaultTimeZone = date_default_timezone_get();
    $time = "00:00:00";
    $dateTime = $date . " " . $time;
    date_default_timezone_set(STMSB_USER_TIMEZONE);
    $localDayStartTime = strtotime($dateTime);
    $GmtDayStartTime = gmstrftime('%d-%m-%Y %I:%M:%S', $localDayStartTime);
    date_default_timezone_set($defaultTimeZone);
    return $GmtDayStartTime;
}

function stmsb_show_date_only($dateTime)
{
    $splitArray = explode(" ", $dateTime);
    return $splitArray[0];
}

function stmsb_show_time_only($dateTime)
{
    $splitArray = explode(" ", $dateTime);
    return $splitArray[1];
}

function stmsb_get_gmt_for_user_timezone_day_end($date)
{
    $defaultTimeZone = date_default_timezone_get();
    $time = "23:59:59";
    $dateTime = $date . " " . $time;
    date_default_timezone_set(STMSB_USER_TIMEZONE);
    $localDayStartEnd = strtotime($dateTime);
    $GmtDayStartEnd = gmstrftime('%d-%m-%Y %I:%M:%S', $localDayStartEnd);
    date_default_timezone_set($defaultTimeZone);

    return $GmtDayStartEnd;
}

function stmsb_get_data_from_session_table($typeId)
{
    global $wpdb;
    $dataArray = $wpdb->get_row("select * from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$typeId'");

    return $dataArray;
}

function stmsb_get_data_from_product_table($sessionId)
{
    global $wpdb;
    $dataArray = $wpdb->get_row("select * from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_id = '$sessionId'");

    return $dataArray;
}

function stmsb_get_session_type_from_session_type_id($sessionTypeId)
{
    global $wpdb;
    $dataArray = $wpdb->get_row("select session_type from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$sessionTypeId'");

    return $dataArray->session_type;
}

function stmsb_get_date_ids($sessionTypeId)
{
    global $wpdb;
    $values = array();
    $dataArray = $wpdb->get_results("select session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_type_id = '$sessionTypeId'");
    foreach ($dataArray as $data) {
        array_push($values, $data->session_date_id);
    }
    return $values;
}

function stmsb_get_date_id_from_start_date_and_type_id($sessionDateStart, $sessionTypeId)
{
    global $wpdb;
    $dataId = $wpdb->get_row("select session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_start = '$sessionDateStart' and session_type_id = '$sessionTypeId'");

    return $dataId->session_date_id;
}

function stmsb_get_date_id_from_session_type_id($sessionDateStart, $sessionTypeId)
{
    global $wpdb;
    $dataId = $wpdb->get_row("select session_date_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_start = '$sessionDateStart' and session_type_id ='$sessionTypeId'");
    return $dataId->session_date_id;
}

function stmsb_get_ending_time($sessionTypeId, $gmtStartDateTime)
{
    $dateSessionTable = stmsb_get_data_from_session_table($sessionTypeId);
    $duration = $dateSessionTable->session_duration;
    $durationInhr = intval($duration) / 60;
    if ($durationInhr == 1) {
        $plus = 60 * 60;
    } else {
        $durationFrac = $durationInhr - 1;
        $durationMin = $durationFrac * 60;
        $plus = (60 * 60) + ($durationMin * 60);
    }
    $time = strtotime($gmtStartDateTime) + $plus;
    $gmtEndDateTime = date("d-m-Y h:i A", $time);

    return $gmtEndDateTime;
}

function stmsb_get_count_of_product_for_day($sessionDateId)
{
    global $wpdb;
    $productInfo = $wpdb->get_results("select * from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId'");
    $count = count($productInfo);

    return $count;
}

function stmsb_get_products_info_for_date($sessionDateId, $slot)
{
    global $wpdb;
    $productInfo = $wpdb->get_results("select * from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId'");

    return $productInfo[$slot];
}

function stmsb_check_booked_time_slot($sessionDateId, $gmtStartDateTime)
{
    for ($i = 0; $i < stmsb_get_count_of_product_for_day($sessionDateId); $i++) {
        $productStartTime = strtotime(stmsb_get_products_info_for_date($sessionDateId, $i)->session_start);
        $productEndTime = strtotime(stmsb_get_products_info_for_date($sessionDateId, $i)->session_end);
        $selectedStartTime = strtotime($gmtStartDateTime);
        if ($selectedStartTime >= $productStartTime && $selectedStartTime < $productEndTime) {
            return true;
        }
    }

    return false;
}

function stmsb_get_session_name_from_session_id($sessionId)
{
    global $wpdb;
    $getData = $wpdb->get_row("select session_type from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$sessionId'");

    return $getData->session_type;
}

function stmsb_get_session_type_from_date_id($sessionDateId)
{
    global $wpdb;
    $sessionTypeIdArray = $wpdb->get_row("select session_type_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_id = '$sessionDateId'");
    $sessionTypeId = $sessionTypeIdArray->session_type_id;
    $sessionTypeNameArray = $wpdb->get_row("select session_type from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$sessionTypeId'");
    $sessionTypeName = $sessionTypeNameArray->session_type;

    return $sessionTypeName;
}

function stmsb_get_session_type_id_from_date_id($sessionDateId)
{
    global $wpdb;
    $sessionTypeIdArray = $wpdb->get_row("select session_type_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_id = '$sessionDateId'");
    $sessionTypeId = $sessionTypeIdArray->session_type_id;

    return $sessionTypeId;
}

function stmsb_check_session_type_id_for_product($productId)
{
    global $wpdb;
    $getData = $wpdb->get_row("select session_type_id from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_product = '$productId'");
    if ($getData != null) {
        return $getData->session_type_id;
    } else {
        return false;
    }
}

function stmsb_get_session_id_from_session_date_id_and_session_start($sessionDateId, $sessionStart)
{
    global $wpdb;
    $getData = $wpdb->get_row("select session_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$sessionDateId' and session_start='$sessionStart' ");

    return $getData->session_id;


}

function stmsb_create_session($sessionTypeId, $sessionDate, $sessionTime, $adminEmail = '', $adminCustomer = '', $returnHash = false)
{
    global $wpdb;
    $dateCheck = 0;
    $adminBooking = null;
    $localTime = $sessionDate . " " . $sessionTime;
    $gmtStartDateTime = stmsb_convert_into_gmt($localTime);
    if ($adminEmail != '' && $adminCustomer != '') {
        $adminBooking = 1;
    }

    //Checking if date has been booked previously
    $gmtStartTimeForUserGivenDay = date(STMSB_STORE_DATE_TIME_FORMAT, strtotime(stmsb_get_gmt_for_user_timezone_day_start($sessionDate)));
    $gmtEndTimeForUserGivenDay = date(STMSB_STORE_DATE_TIME_FORMAT, strtotime(stmsb_get_gmt_for_user_timezone_day_end($sessionDate)));
    //Check for dual session type in a day
    $databaseDateArray = $wpdb->get_results("select * from `" . STMSB_SESSION_DATE_TABLE . "`");
    foreach ($databaseDateArray as $databaseDate) {
        $databaseStartDateTime = $databaseDate->session_date_start;
        $databaseEndDateTime = $databaseDate->session_date_end;
        $databaseSessionTypeId = $databaseDate->session_type_id;
        if ($databaseStartDateTime == $gmtStartTimeForUserGivenDay && $databaseEndDateTime == $gmtEndTimeForUserGivenDay && $databaseSessionTypeId == $sessionTypeId) {
            $dateCheck = 1; // already exists in database
        }
    }
    if ($dateCheck == 0) {
        //insert into Database msb_date_info
        $wpdb->query($wpdb->prepare("INSERT into `" . STMSB_SESSION_DATE_TABLE . "` VALUE ( %d,%d,%s,%s)", '',
            $sessionTypeId, date(STMSB_STORE_DATE_TIME_FORMAT, strtotime($gmtStartTimeForUserGivenDay)), date(STMSB_STORE_DATE_TIME_FORMAT, strtotime($gmtEndTimeForUserGivenDay))));
    }

    //Runs the time check if there is on conflict in date and session type
    if ($dateCheck != 2) {
        //creation of hash tag
        $hash = esc_attr(bin2hex(random_bytes(32)));

        //Calculation of ending time
        $gmtEndDateTime = stmsb_get_ending_time($sessionTypeId, $gmtStartDateTime);//
        $sessionDateId = stmsb_get_date_id_from_session_type_id(date(STMSB_STORE_DATE_TIME_FORMAT, strtotime($gmtStartTimeForUserGivenDay)), $sessionTypeId);

        //Checking if time is booked previously
        $timeCheck = stmsb_check_booked_time_slot($sessionDateId, $gmtStartDateTime);

        if ($timeCheck == true) {
            stmsb_show_error('Time Slot is already booked');
        } else {
            //Format->
            // || session_id | session_type_id | session_start | session_end | order_id | customer_name | customer_email | session_key | admin_booking | admin_email ||
            $sql = $wpdb->prepare("INSERT into `" . STMSB_SESSION_PRODUCT_TABLE . "` VALUE ( %d,%d,%s,%s,%d,%s,%s,%s,%d,%s)",
                '', $sessionDateId, date(STMSB_STORE_DATE_TIME_FORMAT, strtotime($gmtStartDateTime)), date(STMSB_STORE_DATE_TIME_FORMAT, strtotime($gmtEndDateTime)), '', $adminBooking == 1 ? $adminCustomer : '', $adminBooking == 1 ? $adminEmail : '', $hash, $adminBooking == 1 ? 1 : '', $adminBooking == 1 ? $adminEmail : '');
            $wpdb->query($sql);
            stmsb_show_success('Session slot has been added!');
        }
        if ($returnHash) {
            return $hash;
        }
    }
    return false;
}

function stmsb_count_all_from_table($tableName)
{
    global $wpdb;
    $var = $wpdb->get_row("SELECT COUNT(*) AS num FROM {$tableName}");
    return ($var->num);
}

function stmsb_count_manual_bookings()
{
    global $wpdb;
    $var = $wpdb->get_row("SELECT COUNT(*) AS num FROM " . STMSB_SESSION_PRODUCT_TABLE . " WHERE admin_booking=1");
    return ($var->num);
}

function stmsb_get_product_id_from_type_id($sessionTypeId)
{
    global $wpdb;
    $sqlGetProductId = "Select session_type_product from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$sessionTypeId'";
    $GetProductId = $wpdb->get_row($sqlGetProductId);
    return $GetProductId->session_type_product;

}

function stmsb_get_product_id_from_session_id($sessionId)
{
    global $wpdb;
    $sqlGetProductId = "Select session_date_id from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_id = '$sessionId'";
    $GetProductId = $wpdb->get_row($sqlGetProductId);
    return stmsb_get_product_id_from_type_id(stmsb_get_session_type_id_from_date_id($GetProductId->session_date_id));
}

function stmsb_get_product_url($productId)
{
    return get_permalink($productId);
}

function stmsb_write_custom_css($customCss)
{
    $my_file = STMSB_SESSION_BOOKING_FILE_PATH . '/css/style.css';
    $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
    fwrite($handle, $customCss);
}

function stmsb_read_custom_css()
{
    $my_file = STMSB_SESSION_BOOKING_FILE_PATH . '/css/style.css';
    $handle = fopen($my_file, 'r');
    $data = fread($handle, filesize($my_file));
    return $data;
}