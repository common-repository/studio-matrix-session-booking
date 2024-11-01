<?php
/**
 * @link              http://www.studiomatrix.com.np/
 * @since             1.0.0
 * @package           stmsb-session-booking
 *
 * @wordpress-plugin
 * Plugin Name:       Studio Matrix Session Booking
 * Plugin URI:        https://studiomatrix.com.np/
 * Description:       Having problems with managing your appointment for a woocommerce platform. This plugin help you to manage the session and view your appointment.
 * Version:           1.0.0
 * Author:            Studio Matrix Pvt. Ltd.
 * Author URI:        https://profiles.wordpress.org/studiomatrix
 * Text Domain:       stmsb_session_booking
 * Domain Path:       /languages
 */

#Exit if accessed Directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('STMSB_SessionBooking')) :

    class STMSB_SessionBooking
    {
        /**
         * Plugin version.
         * @var string
         */
        const VERSION = '1.0';

        /**
         * Instance of this class.
         * @var object
         */
        protected static $instance = null;

        /**
         * STMSB_SessionBooking constructor.
         *
         */
        private function __construct()
        {
            if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.3', '>=')) {

                // define constants
                define('STMSB_PLUGIN_NAME', 'Studio Matrix Session Booking');
                define('STMSB_SESSION_BOOKING_SITE_URL', get_option('siteurl'));
                define('STMSB_PLUGIN_BASENAME', plugin_basename(__FILE__));
                define('STMSB_SESSION_BOOKING_FOLDER', dirname(plugin_basename(__FILE__)));
                define('STMSB_SESSION_BOOKING_URL', plugin_dir_url( __FILE__ ));
                define('STMSB_SESSION_BOOKING_FILE_PATH', dirname(__FILE__));
                define('STMSB_SESSION_BOOKING_DIR_NAME', basename(dirname(__FILE__)));
                define('STMSB_STUDIO_MATRIX_LOGO', plugins_url( 'css/images/logo.png', __FILE__ ));
                global $wpdb;
                define('STMSB_SESSION_SESSION_INFO_TABLE', $wpdb->prefix . 'stmsb_session_info');
                define('STMSB_SESSION_PRODUCT_TABLE', $wpdb->prefix . 'stmsb_product_info');
                define('STMSB_SESSION_DATE_TABLE', $wpdb->prefix . 'stmsb_date_info');
                define('STMSB_SESSION_POST', $wpdb->prefix . 'posts');
                define('STMSB_STORE_DATE_TIME_FORMAT', 'Y-m-d H:i:s');
                define('STMSB_DISPLAY_DATE_FORMAT', get_option('stmsb_display_date_format') ?: 'd F Y');
                define('STMSB_DISPLAY_TIME_FORMAT', get_option('stmsb_display_time_format') ?: 'g:i A');
                define('STMSB_DISPLAY_ROW', get_option('stmsb_display_rows') ?: 10);
                define('STMSB_USER_TIMEZONE', get_option('stmsb_booking_timezone') ?: 'Asia/Kathmandu');

                $this->stmsb_includes();
                // Hooks.
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(
                    $this,
                    'stmsb_plugin_action_links'
                ));
                add_filter('plugin_row_meta', array($this, 'stmsb_plugin_row_meta'), 10, 2);


            } else {
                add_action('admin_notices', array($this, 'stmsb_woocommerce_missing_alert'));
            }
        }

        /**
         * Return an instance of this class.
         * @return object A single instance of this class.
         */
        public static function stmsb_get_instance()
        {
            // If the single instance hasn't been set, set it now.
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Echoes the woocommerce missing alert
         *
         */
        public function stmsb_woocommerce_missing_alert()
        {
            echo '<div class="error notice is-dismissible"><p>' . sprintf(__('Studio Matrix Session Booking depends on the last version of %s or later to work!',
                    'stmsb_session_booking'),
                    '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __('WooCommerce 2.3',
                        'stmsb_session_booking') . '</a>') . '</p></div>';

            return;
        }

        /**
         * Includes.
         */
        private function stmsb_includes()
        {
            include_once(dirname(__FILE__) . '/includes/stmsb-session-booking-scripts.php');
            include_once(dirname(__FILE__) . '/includes/admin/stmsb-admin-required.php');
            include_once(dirname(__FILE__) . '/includes/public/stmsb-public-required.php');
        }


        public function stmsb_plugin_action_links($actions)
        {
            $new_actions = array(
                'settings' => '<a href="' . admin_url('admin.php?page=general_settings') . '" title="' . esc_attr(__('View settings',
                        'stmsb_session_booking')) . '">' . __('Settings', 'stmsb_session_booking') . '</a>',
            );

            return array_merge($new_actions, $actions);
        }

        public static function stmsb_plugin_row_meta($links, $file)
        {
            if (strpos($file, STMSB_PLUGIN_BASENAME) !== false) {
                $row_meta = array(
                    'docs' => '<a href="' . esc_url(apply_filters('sm_session_booking_docs_url', 'https://studiomatrix.com.np')) . '" aria-label="' . esc_attr__('View Studio Matrix Session Booking documentation', 'stmsb_session_booking') . '">' . esc_html__('Documentation', 'stmsb_session_booking') . '</a>'
                );
                return array_merge($links, $row_meta);
            }

            return (array)$links;
        }
    }

    /**
     * Runs while installing the plugin
     *
     */
    function stmsb_install_plugin()
    {
        global $wpdb;
        global $stmsb_db_version;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $stmsb_db_version = '1.0';
        $charset_collate = $wpdb->get_charset_collate();
        $tableProductSessions = "CREATE TABLE {$wpdb->prefix}stmsb_product_info ( `session_id` INT(4) NOT NULL AUTO_INCREMENT , `session_date_id` INT(11) NOT NULL ,`session_start` DATETIME NOT NULL ,`session_end` DATETIME NOT NULL ,`order_id` INT(11) NOT NULL ,`customer_name` VARCHAR(50) NOT NULL ,`customer_email` VARCHAR(100) NOT NULL ,`session_key` VARCHAR(70) NOT NULL ,`admin_booking` INT(1) NOT NULL ,`admin_booking_email` VARCHAR(100) NOT NULL , PRIMARY KEY (`session_id`))$charset_collate;";
        $tableSessionInfo = "CREATE TABLE {$wpdb->prefix}stmsb_session_info(`session_type_id` bigint(20) NOT NULL AUTO_INCREMENT,`session_type` varchar(255) NOT NULL,`session_duration` int(5) NOT NULL,`session_type_product` varchar(255) NOT NULL,PRIMARY KEY (`session_type_id`))  $charset_collate;";
        $tableTypeDate = "CREATE TABLE `{$wpdb->prefix}stmsb_date_info` (`session_date_id` int(11) NOT NULL AUTO_INCREMENT,`session_type_id` int(20) NOT NULL,`session_date_start` DATETIME NOT NULL,`session_date_end` DATETIME NOT NULL,PRIMARY KEY (`session_date_id`))  $charset_collate;";
        $sqlToCreate = " INSERT INTO {$wpdb->prefix}stmsb_session_info (session_type_id, session_type, session_duration, session_type_product)
VALUES (1 , '',0, 0)";
        dbDelta($tableProductSessions);
        dbDelta($tableSessionInfo);
        dbDelta($tableTypeDate);
        dbDelta($sqlToCreate);
        add_option('stmsb_db_version', $stmsb_db_version);
    }

    /**
     * Runs while uninstalling the plugin
     */
    function stmsb_uninstall_plugin()
    {
        global $wpdb;
        $stmsb_product_table = $wpdb->prefix . 'stmsb_product_info';
        $stmsb_session_table = $wpdb->prefix . 'stmsb_session_info';
        $stmsb_date_table = $wpdb->prefix . 'stmsb_date_info';
        $wpdb->query("DROP TABLE IF EXISTS $stmsb_product_table");
        $wpdb->query("DROP TABLE IF EXISTS $stmsb_session_table");
        $wpdb->query("DROP TABLE IF EXISTS $stmsb_date_table");
        delete_option("stmsb_db_version");
        delete_option("stmsb_booking_timezone");
        delete_option("stmsb_display_date_Format");
        delete_option("stmsb_display_time_Format");
        delete_option("stmsb_display_rows");
        delete_option("stmsb_custom_css");
    }

    add_action('plugins_loaded', array('STMSB_SessionBooking', 'stmsb_get_instance'));

    //register
    register_activation_hook(__FILE__, 'stmsb_install_plugin');

    //runs while uninstalling the plugin
    register_uninstall_hook(__FILE__, 'stmsb_uninstall_plugin');
endif;