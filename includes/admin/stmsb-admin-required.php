<?php
include_once(dirname(__FILE__) . '/../stmsb-function.php');
include_once(dirname(__FILE__) . '/stmsb-general-settings.php');
include_once(dirname(__FILE__) . '/stmsb-management.php');
include_once(dirname(__FILE__) . '/stmsb-session-slots.php');
include_once(dirname(__FILE__) . '/stmsb-view-appointments.php');
include_once(dirname(__FILE__) . '/stmsb-session-logs.php');
include_once(dirname(__FILE__).'/stmsb-admin-ajax-calls.php');
include_once(dirname(__FILE__) . '/../stmsb-pagination.php');

$stmsb_session_management = new STMSB_Session_Management();
$stmsb_session_slots      = new STMSB_Session_Slots();
$stmsb_view_appointments = new STMSB_View_Appointments();
$stmsb_session_log = new STMSB_Session_Log();
$stmsb_general_settings = new STMSB_General_Settings();
