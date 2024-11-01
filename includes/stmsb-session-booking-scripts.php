<?php
//add admin scripts
function stmsb_add_admin_scripts($hook_suffix)
{
    if ('toplevel_page_sm-session-management' === $hook_suffix || 'session-mgmt_page_session_slots' === $hook_suffix || 'session-mgmt_page_view_appointments' === $hook_suffix || 'session-management_page_view_appointments' === $hook_suffix || 'session-mgmt_page_manual_bookings' === $hook_suffix || 'session-mgmt_page_session_log' === $hook_suffix || 'session-mgmt_page_general_settings' === $hook_suffix || 'admin_page_change_bookings' === $hook_suffix) {
        //list of css
        wp_enqueue_style('stmsb-bootstraps-style', STMSB_SESSION_BOOKING_URL . '/css/bootstrap.min.css');
        wp_enqueue_style('stmsb-bootstraps-time-picker-style',
            STMSB_SESSION_BOOKING_URL . '/css/bootstrap-date-timepicker.min.css');
        wp_enqueue_style('stmsb-full-calendar-style', STMSB_SESSION_BOOKING_URL . '/css/fullcalendar.min.css');
        wp_enqueue_style('stmsb-style', STMSB_SESSION_BOOKING_URL . '/css/style-admin.css');


        //list of scripts
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-validate', STMSB_SESSION_BOOKING_URL. 'js/jquery.validate.min.js', array('jquery'), false, true);
        wp_enqueue_script('stmsb-moment-script', STMSB_SESSION_BOOKING_URL . '/js/moment.min.js');
        wp_enqueue_script('stmsb-moment-with-timezone-script',
            STMSB_SESSION_BOOKING_URL . '/js/moment-timezone-with-data.min.js');
        wp_enqueue_script('stmsb-bootstraps-script', STMSB_SESSION_BOOKING_URL . '/js/bootstrap.min.js');

        wp_enqueue_script('stmsb-time-picker-script',
            STMSB_SESSION_BOOKING_URL . '/js/bootstrap-datetimepicker.min.js');
        wp_enqueue_script('stmsb-full-calendar-script', STMSB_SESSION_BOOKING_URL . '/js/fullcalendar.min.js');
        wp_enqueue_script('stmsb-admin-script', STMSB_SESSION_BOOKING_URL . '/js/main-admin.js', array('jquery'));
        if (isset($_GET['page'])):
            if ($_GET['page'] === 'view_appointments' AND is_admin()) {
                wp_enqueue_script('stmsb-fullcalender-admin-script',
                    STMSB_SESSION_BOOKING_URL . '/js/main-admin-fullcalendar.js', array('jquery'), '', true);
            }
        endif;
        $stmsb_js_param = array(
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'editMgmtUrl'       => admin_url() . 'admin.php?page=sm-session-management',
            'changeBookingUrl'  => admin_url() . 'admin.php?page=change_bookings',
            'displayDateFormat' => stmsb_convert_php_to_moment_format(STMSB_DISPLAY_DATE_FORMAT),
            'displayTimeFormat' => stmsb_convert_php_to_moment_format(STMSB_DISPLAY_TIME_FORMAT),
            'now'               => date(DATE_ISO8601, time())
        );
        wp_localize_script('stmsb-admin-script', 'stmsb_php_vars', $stmsb_js_param);
    }
}

add_action('admin_enqueue_scripts', 'stmsb_add_admin_scripts');


//Add scripts
function stmsb_add_scripts()
{
    if(is_woocommerce() ||is_checkout() ||is_cart()) {
        //list of css
        wp_enqueue_style('jquery-ui-css', STMSB_SESSION_BOOKING_URL . '/css/jquery.ui.css');
        wp_enqueue_style('stmsb-style', STMSB_SESSION_BOOKING_URL . '/css/style.css');

        //list of scripts
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('stmsb-moment-script', STMSB_SESSION_BOOKING_URL . '/js/moment.min.js');
        wp_enqueue_script('stmsb-public-script', STMSB_SESSION_BOOKING_URL . '/js/main.js', array('jquery'));
        $stmsb_js_param = array(
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'displayTimeFormat' => stmsb_convert_php_to_moment_format(STMSB_DISPLAY_TIME_FORMAT),
            'storeDateTimeFormat' => stmsb_convert_php_to_moment_format(STMSB_STORE_DATE_TIME_FORMAT)
        );
        wp_localize_script('stmsb-public-script', 'stmsb_php_vars', $stmsb_js_param);
    }
}


add_action('wp_enqueue_scripts', 'stmsb_add_scripts');

function stmsb_convert_php_to_moment_format($format)
{
    $replacements = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '',
        'L' => '',
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '',
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz',
        'I' => '',
        'O' => '',
        'P' => '',
        'T' => '',
        'Z' => '',
        'c' => '',
        'r' => '',
        'U' => 'X',
    ];
    $momentFormat = strtr($format, $replacements);
    return $momentFormat;
}