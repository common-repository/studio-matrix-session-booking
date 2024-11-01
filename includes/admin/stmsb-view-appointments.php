<?php

class STMSB_View_Appointments
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'stmsb_add_submenu_to_page'));
    }

    public function stmsb_add_submenu_to_page()
    {
        add_submenu_page(
            _x('sm-session-management', 'stmsb_session_booking'),
            _x('Session Management', 'stmsb_session_booking'),
            'View Appointments',
            'manage_options',
            'view_appointments',
            array($this, 'stmsb_view_appointments')
        );
    }

    function stmsb_view_appointments()
    {
        stmsb_check_woocommerce();

        global $wpdb, $jason_time;
        $jason_time = array();
        $getDates   = $wpdb->get_results("Select * from `" . STMSB_SESSION_DATE_TABLE . "`");
        foreach ($getDates as $getDate) {
            $id          = $getDate->session_date_id;
            $typeid      = $getDate->session_type_id;
            $typeName    = stmsb_get_session_name_from_session_id($typeid);
            $getProducts = $wpdb->get_results("Select session_id,session_start,session_end,customer_name,order_id,admin_booking from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$id'");
            foreach ($getProducts as $getProduct) {
                $sessionId    = $getProduct->session_id;
                $title        = $getProduct->customer_name;
                $orderId      = $getProduct->order_id;
                $adminBooking = $getProduct->admin_booking;
                if ($adminBooking == 1) {
                    $from = "Manual Booking";
                } else {
                    $from = "System";
                }
                if ($orderId == -1) {
                    continue;
                }
                if ($title == null) {
                    $title = 'No Booking';
                }
                $dateTimeStart = $getProduct->session_start;
                $dateTimeEnd   = $getProduct->session_end;
                $paid          = ($getProduct->order_id == 0) ? false : true;
                $jason_time[]  = array(
                    'title' => sanitize_text_field($title),
                    'start' => sanitize_text_field(date(DATE_ISO8601, strtotime(stmsb_convert_into_local($dateTimeStart)))),
                    'end'   => sanitize_text_field(date(DATE_ISO8601, strtotime(stmsb_convert_into_local($dateTimeEnd)))),
                    'id'    => sanitize_text_field($sessionId),
                    'type'  => sanitize_text_field($typeName),
                    'from'  => sanitize_text_field($from),
                    'paid'  => sanitize_text_field($paid)
                );
            }
        }

        ?>
        <!--suppress JSUnusedLocalSymbols -->
        <script type="text/javascript">
            var toShowDates = <?php echo json_encode($jason_time); ?>;
        </script>
        <div class="wrap">
            <?php include_once(dirname(__FILE__) . '/stmsb-header.php') ?>
            <h4>View Appointments</h4>
            <p><?php esc_html_e('View all you sessions here. All displayed date and time are in ' . STMSB_USER_TIMEZONE,
                    'stmsb_session_booking') ?></p>
            <!-- The Modal -->
            <div id="myModal" class="modal">
                <!-- Modal content -->
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h4>Event Details</h4>
                    <table class='widefat fixed' id="sessionList" cellspacing="o">
                        <thead>
                        <tr>
                            <th id="sessionCustomerName" class="manage-column column-sessionCustomerName "
                                scope="col"> Name
                            </th>
                            <th id="sessionType" class="manage-column column-sessionDate " scope="col">Session
                                Type
                            </th>
                            <th style="width: 12%;" id="sessionVia" class="manage-column column-sessionDate "
                                scope="col">Via
                            </th>
                            <th style="width: 12%;" id="sessionDate" class="manage-column column-sessionDate "
                                scope="col">Date
                            </th>
                            <th style="width: 9%;" id="sessionStart" class="manage-column column-sessionStart"
                                scope="col">Start
                                Time
                            </th>
                            <th style="width: 9%;" id="sessionEnd" class="manage-column column-sessionType" scope="col">
                                End Time
                            </th>
                        </tr>
                        </thead>
                        <tbody class="alternate">
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="width: 100%"><!-- Start of input UI display -->
                <div class='innerWrapper'>
                    <div class="calendar"></div>
                </div>
            </div>
            <div class="dayClickWindow"></div>
        <?php
    }
}