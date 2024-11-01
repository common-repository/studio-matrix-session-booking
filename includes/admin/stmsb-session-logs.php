<?php

class STMSB_Session_Log
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
            'Session Log',
            'manage_options',
            'session_log',
            array($this, 'stmsb_session_log')
        );
    }

    function stmsb_session_log()
    {
        stmsb_check_woocommerce();
        $page = !empty(sanitize_text_field($_GET['page-no'])) ? (int)sanitize_text_field($_GET['page-no']) : 1;
        $per_page = STMSB_DISPLAY_ROW;
        $total_count = stmsb_count_all_from_table(STMSB_SESSION_PRODUCT_TABLE);
        $pagination = new STMSB_Pagination($page, $per_page, $total_count);

        ?>
        <div class="wrap">
        <?php include_once(dirname(__FILE__) . '/stmsb-header.php') ?>
        <div id="sm-message-alert"></div>
        <h4>Session Logs</h4>
        <p><?php esc_html_e('List of all Session slots and its details. If session slot has not been booked it will display blank customer name and email address', 'stmsb_session_booking') ?></p>
        <div class='sessionLog'>
            <?php
            global $wpdb;
            $dataArray = $wpdb->get_results("select * from `" . STMSB_SESSION_PRODUCT_TABLE . "` ORDER BY session_start DESC LIMIT {$per_page} OFFSET {$pagination->stmsb_offset()}");
            ?>
            <div class='innerWrapper'>
                <p><?php esc_html_e('Note: All displayed date and time are in ' . STMSB_USER_TIMEZONE, 'stmsb_session_booking') ?></p>
                <table class='widefat fixed marginTop' id="tableLog" cellspacing="o">
                    <thead>
                    <tr>
                        <th style="width: 13%" scope="col">Date</th>
                        <th style="width: 20%" scope="col">Name</th>
                        <th style="width: 7%" scope="col">Via</th>
                        <th scope="col">Email</th>
                        <th style="width: 9%" scope="col">Time</th>
                        <th style="width: 11%" scope="col">Session Type</th>
                        <th style="width: 10%" scope="col"> Payment</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php


                    $payment = null;
                    foreach ($dataArray as $data) {
                        $sessionDateId = $data->session_date_id;
                        $orderId = $data->order_id;
                        $sessionStart = $data->session_start;
                        $date = stmsb_show_date_only(stmsb_convert_into_local($sessionStart));
                        $time = stmsb_show_time_only(stmsb_convert_into_local($sessionStart));
                        $type = stmsb_get_session_type_from_date_id($sessionDateId);
                        $name = $data->customer_name;
                        $email = $data->customer_email;
                        if ($orderId > 0)
                            $payment = "Paid";
                        elseif ($orderId == -99)
                            $payment = "Paid By Cash";
                        else {
                            if ($name == '' && $email == '')
                                $payment = "-";
                            else
                                $payment = "Pending";
                        }


                        $adminBooking = $data->admin_booking;
                        if ($adminBooking == 1) {
                            $from = "Manual";
                        } else {
                            $from = "System";
                        }


                        ?>
                        <tr class="alternate">
                            <td class="column-sessionType">
                                <strong> <?php echo esc_html(date(STMSB_DISPLAY_DATE_FORMAT, strtotime($date))); ?> </strong></td>
                            <td class="column-sessionDuration" align="left"><strong><?php
                                    if ($name == null)
                                        echo "-";
                                    else
                                        echo esc_html($name); ?> </strong></td>
                            <td class="column-sessionDuration" align="left"><strong><?php echo esc_html($from); ?> </strong></td>
                            <td class="column-sessionDuration" align="left"><strong><?php
                                    if ($email == null)
                                        echo "-";
                                    else
                                        echo esc_html($email); ?> </strong></td>
                            <td class="column-sessionDuration" align="left">
                                <strong><?php echo esc_html(date(STMSB_DISPLAY_TIME_FORMAT, strtotime($time))); ?> </strong></td>
                            <td class="column-sessionDuration" align="left"><strong><?php echo esc_html($type); ?> </strong></td>
                            <td class="column-sessionType" align="left"><strong> <?php echo esc_html($payment); ?>  </strong></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="logPagination" style="display: inline-block; width: 100%">
                    <?php
                    if ($pagination->stmsb_total_page() > 1) {
                        if ($pagination->stmsb_has_previous_page()) { ?>
                            <ul class="pager" style="float:left; margin-right: 30%">
                                <li class="previous">
                                    <?php  printf('<a href=%1$sadmin.php?page=session_log&page-no=%2$s> << Previous </a>', esc_html(admin_url()), esc_html($pagination->stmsb_previous_page())); ?>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <ul class="pager" style="float:left; margin-right: 30%">
                                <li class="previous disabled">
                                    <?php echo "<span> << Previous </span>"; ?>
                                </li>
                            </ul>
                        <?php } ?>
                        <ul class="pagination" style="float:left;">
                            <?php for ($i = 1; $i <= $pagination->stmsb_total_page(); $i++) { ?>
                                <?php if ($i == $page) { ?>
                                    <li class="disabled"><span><?php echo $i ?></span></li>
                                <?php } else { ?>
                                    <li class="active">
                                        <?php  printf('<a href=%1$sadmin.php?page=session_log&page-no=%2$s>%3$s</a>', esc_html(admin_url()), esc_html($i),esc_html($i)); ?>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <?php if ($pagination->stmsb_has_next_page()) { ?>
                            <ul class="pager" style="float:right">
                                <li class="next">
                                    <?php  printf('<a href=%1$sadmin.php?page=session_log&page-no=%2$s> Next >> </a>', esc_html(admin_url()), esc_html($pagination->stmsb_next_page())); ?>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <ul class="pager" style="float:right">
                                <li class="next disabled">
                                    <?php echo "<span> Next >> </span>"; ?>
                                </li>
                            </ul>
                        <?php }
                    }
                    ?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <?php
    }
}