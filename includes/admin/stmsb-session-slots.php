<?php

class STMSB_Session_Slots
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
            'Session Slots',
            'manage_options',
            'session_slots',
            array($this, 'stmsb_session_slots')
        );
    }

    function stmsb_session_slots()
    {
        stmsb_check_woocommerce();
        global $wpdb;
        $page = !empty(sanitize_text_field($_GET['page-no'])) ? (int)sanitize_text_field($_GET['page-no']) : 1;
        $per_page = STMSB_DISPLAY_ROW;
        $total_count = stmsb_count_all_from_table(STMSB_SESSION_PRODUCT_TABLE);
        $pagination = new STMSB_Pagination($page, $per_page, $total_count);
        $sessionName = stmsb_get_session_name_from_session_id(1);
        $dateCheck = null;
        //Code for adding new session to new date
        if (isset($_POST['save'])) {
            $sessionDate = sanitize_text_field($_POST['sessionDates']);
            $sessionTime = sanitize_text_field($_POST['startTime']);
            if ($this->stmsb_validate_input($sessionDate, $sessionTime)) {
                $sessionTypeId = 1;
                stmsb_create_session($sessionTypeId, $sessionDate, $sessionTime, $adminEmail = '', $adminCustomer = '');
                $prevType = $wpdb->get_results("select session_type from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id ={$sessionTypeId} ",
                    OBJECT);
                $sessionName = $prevType[0]->session_type;
            }
        }
        ?>
        <!--suppress HtmlFormInputWithoutLabel -->
        <div class="wrap">
            <?php include_once(dirname(__FILE__) . '/stmsb-header.php') ?>
            <div id="sm-message-alert"></div>
            <h4>Session Slots</h4>
            <p><?php esc_html_e('Create Session for a date and time of user choice. Multiple session type can be set for a date', 'stmsb_session_booking') ?></p>
            <?php
            $sql = "Select session_id, session_date_id, session_start, YEAR(session_start) as sortYear ,customer_name, session_end from `" . STMSB_SESSION_PRODUCT_TABLE . "` ORDER BY session_start DESC LIMIT {$per_page} OFFSET {$pagination->stmsb_offset()} ";
            $getSessions = $wpdb->get_results($sql);
            ?>
            <div class='sessionTypeList'><!--Start of output UI display-->
                <div class='innerWrapper'>
                    <h4 class="marginBot"><?php esc_html_e('Available Sessions Slots', 'stmsb_session_booking') ?></h4>
                    <h6><?php esc_html_e('Note: All displayed date and time are in ' . STMSB_USER_TIMEZONE, 'stmsb_session_booking') ?></h6>
                    <table class='widefat fixed marginTop25' id="tableSlot" cellspacing="o">
                        <thead>
                        <tr>
                            <th style="width:23% ;">Date</th>
                            <th>Session Type</th>
                            <th>Start Time</th>
                            <th style="width: 18%;">End Time</th>
                            <th style="width: 15%;">Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($getSessions as $getSession) {
                            $mainId = $getSession->session_id;
                            $adminDateId = $getSession->session_date_id;
                            $arrays = $wpdb->get_row("Select session_date_start, session_type_id from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_id = '$adminDateId' ");
                            $sqlAdminSessionType = $arrays->session_type_id;
                            $sessionType = $wpdb->get_row("Select session_type from `" . STMSB_SESSION_SESSION_INFO_TABLE . "` where session_type_id = '$sqlAdminSessionType' ");
                            $gmtDisplayStartTime = $getSession->session_start;
                            $gmtDisplayEndTime = $getSession->session_end;
                            $ausDisplayStartTime = stmsb_convert_into_local($gmtDisplayStartTime);
                            $ausDisplayEndTime = stmsb_convert_into_local($gmtDisplayEndTime);
                            $DisplayDate = stmsb_show_date_only($ausDisplayStartTime);
                            $DisplayStartTime = stmsb_show_time_only($ausDisplayStartTime);
                            $DisplayEndTime = stmsb_show_time_only($ausDisplayEndTime);
                            $typeSession = $sessionType->session_type;
                            ?>
                            <tr class="alternate">
                            <td class="column-sessionDate">
                                <strong><?php echo esc_html(date(STMSB_DISPLAY_DATE_FORMAT, strtotime($DisplayDate))); ?>  </strong>
                            </td>
                            <td class="column-sessionDate">
                                <strong><?php echo esc_html($typeSession); ?>  </strong></td>
                            <td class="column-sessionType">
                                <strong><?php
                                    echo esc_html(date(STMSB_DISPLAY_TIME_FORMAT, strtotime($DisplayStartTime))); ?> </strong>
                            </td>
                            <td class="column-sessionType">
                                <strong><?php
                                    echo esc_html(date(STMSB_DISPLAY_TIME_FORMAT, strtotime($DisplayEndTime))); ?> </strong></td>
                            <td class="column-sessionDate">
                                <button class="btnSlotDelete button button-primary button-large"
                                        data-id="<?php echo esc_html($mainId); ?>"> Delete
                                </button>
                            </td>
                            </tr><?php
                        } ?>


                        </tbody>
                    </table>
                    <div class="slotPagination" style="display: inline-block; width:100%">
                        <?php
                        if ($pagination->stmsb_total_page() > 1) {
                            if ($pagination->stmsb_has_previous_page()) { ?>
                                <ul class="pager" style="float:left; margin-right: 24%">
                                    <li class="previous">
                                        <?php  printf('<a href=%1$sadmin.php?page=session_slots&page-no=%2$s> << Previous </a>', esc_html(admin_url()), esc_html($pagination->stmsb_previous_page())); ?>
                                    </li>
                                </ul>
                            <?php } else { ?>
                                <ul class="pager" style="float:left; margin-right: 24%">
                                    <li class="previous disabled">
                                        <?php echo "<span> << Previous </span>"; ?>
                                    </li>
                                </ul>
                            <?php } ?>
                            <ul class="pagination" style="float:left;">
                                <?php for ($i = 1; $i <= $pagination->stmsb_total_page(); $i++) { ?>
                                    <?php if ($i == $page) { ?>
                                        <li class="disabled"><span><?php echo esc_html($i); ?></span></li>
                                    <?php } else { ?>
                                        <li class="active">
                                            <?php  printf('<a href=%1$sadmin.php?page=session_slots&page-no=%2$s>%3$s</a>', esc_html(admin_url()), esc_html($i),esc_html($i)); ?>
                                        </li>
                                    <?php } ?>
                                 <?php } ?>
                            </ul>
                            <?php if ($pagination->stmsb_has_next_page()) { ?>
                                <ul class="pager" style="float:right">
                                    <li class="next">
                                        <?php  printf('<a href=%1$sadmin.php?page=session_slots&page-no=%2$s> Next >> </a>', esc_html(admin_url()), esc_html($pagination->stmsb_next_page())); ?>
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
            </div><!-- End of output UI display -->

            <div class='sessionTypeForm'><!-- Start of input UI display -->
                <div class='innerWrapper'>
                    <form method="post" class="bookSessionDate" id="bookSessionDate">
                        <h4 class="marginBot"><?php esc_html_e('Add Session Slots for ' . $sessionName, 'stmsb_session_booking'); ?></h4>
                        <?php
                        if (empty($sessionName))
                            esc_html_e('Note: Please add the session type from "Session Mgmt" to continue.', 'stmsb_session_booking');
                        ?>
                        <div class="row marginTop">
                            <fieldset class="column">
                                <label for="sessionDates"><Strong><?php esc_html_e('Session Date:', 'stmsb_session_booking'); ?></Strong></label>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input autocomplete="off" id="datetimepicker12" class="datepicker"
                                                   type="text" style="width: 100%"
                                                   name="sessionDates"
                                                   <?php if (isset($sessionDate)): ?>value="<?php echo esc_html($sessionDate); ?>"<?php endif;
                                            ?>>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="column">
                                <label for="startTime"><Strong><?php esc_html_e('Start Time:', 'stmsb_session_booking'); ?></Strong></label>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input autocomplete="off" id="datetimepicker2" class="startTime"
                                                   name="startTime" style="width: 100%">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <fieldset class="floatLeft">
                            <button name="save" type="submit" class="button button-primary button-large" id="publish"
                            ><span class="glyphicon glyphicon-save"></span> Add Session
                            </button>
                        </fieldset>
                        <div class="clear"></div>

                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    function stmsb_validate_input($sessionTypeDate, $sessionTime)
    {
        if ($this->stmsb_is_real_date($sessionTypeDate)) {
            stmsb_show_warning('Session Date is not valid! Please enter a valid date to continue.');

            return false;
        }

        if (is_null($sessionTypeDate) || $sessionTypeDate == '') {
            stmsb_show_warning('Session Date cannot be Blank! Please select a date to continue.');

            return false;
        }
        if (is_null($sessionTime) || $sessionTime == '') {
            stmsb_show_warning('Session Time cannot be blank! Please enter a time to continue.');

            return false;
        }

        if ($this->stmsb_is_empty_session()) {
            stmsb_show_warning('Session Type has not been created yet. Please go to "Session Mgmt" to create it.');

            return false;
        }

        return true;
    }

    function stmsb_is_empty_session()
    {
        return empty(stmsb_get_session_type_from_session_type_id(1));
    }

    function stmsb_is_real_date($date)
    {
        if (false === strtotime($date)) {
            return false;
        }
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year);
    }

}