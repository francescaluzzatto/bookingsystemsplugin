<?php
/*
Plugin Name: RoomZero
Plugin URI: https://localhost/roomzero
Description: A plugin which helps users sign up to hikes, connected to a database and which handles the quantity so that excursions don't get overbooked. 
Version: 1.0
Author: Francesca Luzzatto
Author URI: https://www.francescaluzzatto.cloud
License: GPL2
*/

//LOAD THE ADMIN AND USER ROOMZERO.PHP PAGES
// Define plugin constants
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

class RoomZeroPlugin {
    public function __construct(){
        $this->define_constants();
        $this->load_dependencies();
    }

    //define plugin constants
    private function define_constants(){
        define('ROOMZERO_VERSION', '1.0');
    }

    //load plugin dependencies
    private function load_dependencies(){
        // Include Admin and User functionality
        if (is_admin()) {
            require_once MY_PLUGIN_PATH . 'backend/adminroomzero.php';
            require_once MY_PLUGIN_PATH . 'backend/header/manage_seasons.php';
            require_once MY_PLUGIN_PATH . 'backend/header/timeslot_form_handler.php';
            require_once MY_PLUGIN_PATH . 'backend/calendar/popup.php';
            require_once MY_PLUGIN_PATH .'backend/calendar/save_timeslot_data.php';
            require_once MY_PLUGIN_PATH .'backend/calendar/calendar.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/all_activities.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/modify_activity.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/get_activity_events.php';
            //require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/modify_event_popup.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/delete_event/delete_activity_event.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/delete_event/delete_all_future_timeslots.php';
            //require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/new_event/new_event_popup.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/new_event/add_new_event.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/modify_timeslots/modify_all_future_timeslots.php';
            require_once MY_PLUGIN_PATH .'backend/all_activities/modify_activity/modify_timeslots/modify_single_timeslot.php';
        } else {
            // For Frontend (Users)
            require_once MY_PLUGIN_PATH . 'frontend/usersroomzero.php';
            require_once MY_PLUGIN_PATH . 'frontend/display_timeslots.php';
            require_once MY_PLUGIN_PATH . 'frontend/show_calendar.php';
            require_once MY_PLUGIN_PATH . 'frontend/testajax/test_ajax.php';
            require_once MY_PLUGIN_PATH . 'frontend/calendar_sign_up/display_all_activities.php';
            require_once MY_PLUGIN_PATH . 'frontend/calendar_sign_up/create_activity_page.php';
            require_once MY_PLUGIN_PATH . 'frontend/calendar_sign_up/view_activity.php';
        }
        // For WordPress Admin area
    }
}

new RoomZeroPlugin();