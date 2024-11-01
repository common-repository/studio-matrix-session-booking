<?php

class STMSB_Session_Management
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'stmsb_add_menu_to_page'));
    }

    public function stmsb_add_menu_to_page()
    {
        add_menu_page(
            _x('Session Management for Products', 'In the title tag of the page', 'stmsb_session_booking'),
            _x('Session Mgmt', 'Menu title', 'stmsb_session_booking'),
            'manage_options',
            'sm-session-management', array($this, 'stmsb_session_management_contents'),
            'dashicons-calendar-alt',
            '55.999'
        );
    }


    public function stmsb_session_management_contents()
    {
        stmsb_check_woocommerce();
        global $wpdb;
        $error         = null;
        $edit          = null;
        $delete        = null;
        $editTypeName  = null;
        $editDuration  = null;
        $editProductId = null;
        $editId        = null;
        $getSessions = $wpdb->get_row('Select * from ' . STMSB_SESSION_SESSION_INFO_TABLE);

        if (isset($_POST['save'])) {
            $sessionType     = sanitize_text_field($_POST['sessionTypeName']);
            $sessionDuration = sanitize_text_field($_POST['sessionTypeDuration']);
            $sessionProduct  = sanitize_text_field($_POST['sessionTypeProduct']);

            if ($this->stmsb_validate_mgmt_input($sessionType, $sessionDuration, $sessionProduct)) {
                $typeCheck       = 0;


                $sqlToCheckDoubleEntry = $wpdb->get_results("Select * from " . STMSB_SESSION_SESSION_INFO_TABLE . " WHERE session_type='{$sessionType}' and session_type_product='{$sessionProduct}'");
                if ( !empty($sqlToCheckDoubleEntry)) {
                    $typeCheck = 1;
                }

                if ($typeCheck == 1) {
                    stmsb_show_warning('Session Type has already been set for the selected product!');
                }
                if ($typeCheck == 0) {
                    $wpdb->query("UPDATE `" . STMSB_SESSION_SESSION_INFO_TABLE . "` Set session_type ='$sessionType' ,session_duration = '$sessionDuration', session_type_product = '$sessionProduct' where session_type_id = 1 ");
                    stmsb_show_success('Session Type has been added!');?>
                    <!--suppress JSUnresolvedVariable -->
                    <script>
                     window.setTimeout(function () {
                         window.location.href = stmsb_php_vars.editMgmtUrl;
                     }, 1000);
                    </script>
                    <?php
                }
            }
        }
        ?>
        <div class='wrap'>
            <?php include_once(dirname(__FILE__) . '/stmsb-header.php') ?>
            <div id="sm-message-alert"></div>
            <h4>Session</h4>
            <p><?php esc_html_e('Add session for your product. Please select a name for you session of each individual product along with the duration that its is schedule for in minutes. For eg: if session is 1 hour 30 min long input 90 in session duration ',
                    'stmsb_session_booking') ?></p>
            <div class="sessionManagement">
                <div class='innerWrapper'>
                    <form method="post" class="createSessionList" id="updateSessionForm" >
                        <h4 class="marginBot"><?php esc_html_e('Add Session Type', 'stmsb_session_booking'); ?></h4>
                        <fieldset class="marginBot">
                            <label for="sessionTypeName"><Strong><?php esc_html_e('Session Type Name:',
                                        'stmsb_session_booking'); ?></Strong></label>
                            <input autocomplete="off"  class="large-text" type="text" name="sessionTypeName"
                                   id="sessionTypeName" value="<?php echo esc_html($getSessions->session_type); ?>" required >
                        </fieldset>
                        <fieldset class="marginBot">
                            <label for="sessionTypeDuration"><Strong><?php esc_html_e('Session Type Duration in Minutes',
                                        'stmsb_session_booking'); ?></Strong></label>
                            <input autocomplete="off" class="large-text" type="text"
                                   name="sessionTypeDuration" value="<?php echo esc_html($getSessions->session_duration); ?>"
                                   id="sessionTypeDuration" required>
                        </fieldset>
                        <fieldset class="marginBot">
                            <label for="sessionTypeProduct"><Strong><?php esc_html_e('Session Type Product:',
                                        'stmsb_session_booking'); ?></Strong></label>
                            <div class="selectwrap">
                                <select class="wide" name="sessionTypeProduct" id="sessionTypeProduct" required>
                                    <option value=""> ----- Please Select Product -----</option>
                                    <?php
                                    $products = new WP_Query (array(
                                        'post_type'      => 'product',
                                        'post_status'    => 'publish',
                                        'posts_per_page' => -1
                                    ));
                                    while ($products->have_posts()): $products->the_post(); ?>
                                        <option <?php if (esc_html($getSessions->session_type_product) == get_the_ID()): ?>selected<?php endif; ?>
                                        value="<?php echo esc_html(get_the_ID()); ?>"><?php the_title(); ?></option><?php
                                    endwhile;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="alignLeft marginTop ">
                                <button name="save" type="submit" class="button button-primary button-large"
                                        id="publish"
                                ><span class="glyphicon glyphicon-save"></span> Update
                                </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        <?php
    }

    function stmsb_validate_mgmt_input($sessionTypeName, $sessionTypeDuration, $sessionTypeProduct)
    {
        if (is_null($sessionTypeName) || $sessionTypeName == '') {
            stmsb_show_warning('Session Type Name cannot be blank! Please enter a valid session name to continue.');

            return false;
        }
        if (is_null($sessionTypeDuration) || $sessionTypeDuration == '') {
            stmsb_show_warning('Product cannot be Blank! Please select a product for the drop down menu to continue.');

            return false;
        }
        if ( ! is_numeric($sessionTypeProduct) || is_null($sessionTypeProduct) || $sessionTypeProduct == '') {
            stmsb_show_warning('Session Duration cannot be blank and must be a numeric! Please enter a valid number to continue.');

            return false;
        }

        return true;
    }

}