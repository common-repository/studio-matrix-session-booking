<?php
add_action('woocommerce_before_add_to_cart_button', 'stmsb_add_date_time_field_cart_form');

function stmsb_add_date_time_field_cart_form()
{
    echo '<input id="selectedDateTime" name="selectedDateTime" type="hidden">';
}

add_action('woocommerce_before_add_to_cart_form', 'stmsb_take_date_time');

function stmsb_take_date_time()
{
    global $wpdb, $product;
    /** @noinspection PhpUndefinedMethodInspection */
    $currentProductId = $product->get_id();
    $key              = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : null;
    WC()->session->set('key', $key);
    if ($key != null) {
        $dateIdArray1   = $wpdb->get_row("select session_start from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_key = '$key'");
        $sessionKeyTime = $dateIdArray1->session_start;
        WC()->session->set('keyGmtDateTime', $sessionKeyTime);
    }
    $availableDates = array();

    //get information(date and time) for manual booked session
    //extraction of date and time
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

    //hides the 'Add to cart button if product type matches'
    if ($flag == true) {
        ?>
        <!--suppress HtmlFormInputWithoutLabel -->
        <script type="text/javascript">
            var $j = jQuery;
            $j(document).ready(function () {
                (function () {
                    $j(".single_add_to_cart_button").hide();
                    $j(".quantity").hide();
                })();
            });
        </script>
        <?php
        // shows the 'Add to Cart' button form start for manual booking as date and time are already set
        if ($key != null) {
            ?>
            <script type="text/javascript">
                var $j = jQuery;
                $j(document).ready(function () {
                    (function () {
                        $j(".single_add_to_cart_button").show();
                        $j(".quantity").hide();
                    })();
                });
            </script>
            <?php
        }

        ?>
        <div class='wrap' id="contentTimeDate">
            <div class='innerWrapper'>
                <form action="#" method="post" class="frontDate">
                    <h4><?php esc_html_e('Choose the date and time', 'stmsb_session_booking'); ?></h4>
                    <fieldset>
                        <p><label for="frontSessionDate"><Strong><?php esc_html_e('Session Date: ', 'stmsb_session_booking'); ?></Strong></label></p>
                        <?php if ($key != null) { ?>
                            <input type="text" name="frontSessionDate" readonly="readonly" value="<?php
                            if(isset($sessionKeyTime)):
                            $audDateTime = stmsb_convert_into_local($sessionKeyTime);
                            $onlyDate    = date(STMSB_DISPLAY_DATE_FORMAT,strtotime(stmsb_show_date_only($audDateTime)));
                            echo esc_html($onlyDate);
                            endif;
                            ?>">
                            <?php
                        } else { ?>
                            <input class="datepicker" type="text" name="frontSessionDate" id="frontDatePicker">
                            <?php
                            //Code for displaying available dates only
                            $storedDate    = '';
                            $sessionTypeId = stmsb_check_session_type_id_for_product($productId);
                            $allDateIds    = stmsb_get_date_ids($sessionTypeId);
                            foreach ($allDateIds as $allDateId) {
                                //check condition for not repeating the checked dates
                                if ($storedDate == $allDateId) {
                                    continue;
                                }
                                $setDate      = stmsb_get_date_end_from_date_id($allDateId);
                                $arrays       = "Select `session_date_id`,session_end ,`order_id` from `" . STMSB_SESSION_PRODUCT_TABLE . "` where session_date_id = '$allDateId' AND order_id = '0' AND admin_booking = '0' AND session_date_id = '$allDateId' ";
                                $getTimeSlots = $wpdb->get_results($arrays);

                                if ($getTimeSlots != null) {
                                    $FormatDate       = date_create($setDate);
                                    $FormatedDate     = date_format($FormatDate, "n/j/Y");
                                    $availableDates[] = (string)(strtotime($FormatedDate));
                                }
                                $storedDate = $allDateId;
                            }
                        }

                        ?>
                        <!--suppress JSUnusedLocalSymbols -->
                        <script type="text/javascript">
                            var unbookedDates = <?php echo json_encode($availableDates); ?>;
                            var currentProductId = "<?php echo json_encode($currentProductId); ?>";
                        </script>
                    </fieldset>
                    <fieldset>
                        <p><label for="startTime"><Strong><?php esc_html_e('Start Time: ','stmsb_session_booking'); ?></Strong></label></p>
                        <?php if ($key != null) : ?>
                            <input class="text" type="text" name="frontSessionTime" id="timeDivFront"
                                   readonly="readonly"
                                   value="<?php
                                   if(isset($sessionKeyTime)):
                                   $audDateTime = stmsb_convert_into_local($sessionKeyTime);
                                   $onlyTime    = date(STMSB_DISPLAY_TIME_FORMAT,strtotime(stmsb_show_time_only($audDateTime)));
                                   echo esc_html($onlyTime);
                                   endif;
                                   ?>">
                        <?php else: ?>
                            <select class="wide" name="frontSessionTime" id="FrontDivTime"></select>
                        <?php endif; ?>
                    </fieldset>
                    <h6><?php esc_html_e('The appearing time is in ' . STMSB_USER_TIMEZONE, 'stmsb_session_booking') ?></h6>
                </form>
            </div>
        </div>
        <?php
    }
}

add_action('woocommerce_add_to_cart', 'stmsb_add_to_session_product');
function stmsb_add_to_session_product($cart_item_key)
{
    if(empty( isset($_POST['selectedDateTime']) && $_POST['selectedDateTime'])){
        // Removing this freshly added cart item
        WC()->cart->remove_cart_item($cart_item_key);
    }else{
        $time  = isset($_POST['selectedDateTime']) && ! empty(($_POST['selectedDateTime'])) ? sanitize_text_field($_POST['selectedDateTime']) : null;
        $array = [];
        array_push($array, $time);
        WC()->session->set('UserChooseTime', $array);
    }

}

function stmsb_get_date_end_from_date_id($dateId)
{
    global $wpdb;
    $dateString = $wpdb->get_row("select session_date_end from `" . STMSB_SESSION_DATE_TABLE . "` where session_date_id = '$dateId' ");
    $dateTime   = $dateString->session_date_end;
    $audDate    = stmsb_convert_into_local($dateTime);
    $date       = stmsb_show_date_only($audDate);

    return $date;
}