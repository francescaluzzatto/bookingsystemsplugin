<?php 
class GetActivity{
// function to get activity details 
    public function get_activity_details(){
        echo "Modify Activity Page";
        // check if get['id'] is set, 
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            //set activity id to ID 
            $activity_id = $_GET['id'];
            //get wpdb
            global $wpdb;
            // get table name
            $table_name= 'wp_booking_seasons';
            //select ONLY those activities that correspond to that activity ID given in all_activities
            $results= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $activity_id));
            if($results){
                //show activity name 
                echo "<h1>". unserialize($results-> activity_name)."</h1>";
            }
            else{
                //no activity found
                die("No activity found with ID " . $activity_id);
            }
            
            ?>
                <!-- add the calendar, set to calendar id  -->
            <div id="calendar-<?php echo $activity_id; ?>"></div>

            <?php
            $html= include MY_PLUGIN_PATH . 'backend/all_activities/modify_activity/new_event/new_event_popup.php';
            echo $html; // Output the HTML content

            $modify_event_html= include MY_PLUGIN_PATH . 'backend/all_activities/modify_activity/modify_event_popup.php';
            echo $modify_event_html; // Output the HTML content

        }
    }
}
new GetActivity();

class CallModifyActivityJs{
    public function __construct(){
        add_action('admin_enqueue_scripts', [$this, 'enqueue_modify_activity_calendar_js_scripts']);
    }

    public function enqueue_modify_activity_calendar_js_scripts($hook){
        //check that you're on the page modify-activity
        if ($hook !== 'toplevel_page_modify-activity') {
            return;
        }
        //if (isset($_GET['page']) && $_GET['page'] === 'modify-activity') {
            //get activity_id again for this function
        $activity_id = $_GET['id'];
        //get site url and plugin url 
        $url = get_site_url();
        $plugin_url = $url. '/wp-content/plugins/ROOMZERO'; 
        //enqueue jquery 
        wp_enqueue_script('jquery');
        //$plugin_url = plugins_url('ROOMZERO');
        //enqueue fullcalendar
        if (!wp_script_is('fullcalendar', 'enqueued')) {
            wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', array('jquery'), null, true);
        }
        //wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', array('jquery'), null, true);
        //enqueue activity_calendar.js with fullcalendar
        wp_enqueue_script(
            'roomzero_modify_calendar_script', 
            $plugin_url.'/backend/all_activities/modify_activity/js/activity_calendar.js', 
            array('jquery', 'fullcalendar'), 
            null, 
            true
        );
        //get admin ajax and nonce
        wp_localize_script('roomzero_modify_calendar_script', 'my_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_ajax_nonce'),
            'get_events_nonce' => wp_create_nonce('get_events'),
            'delete_event_nonce' => wp_create_nonce('delete_event'),
            'delete_all_events_nonce' => wp_create_nonce('delete_all_events'),
            'modify_all_events_nonce' => wp_create_nonce('modify_all_events'),
            'modify_event_nonce' => wp_create_nonce('modify_event'),
        ]);
        // Pass acitivity id so i can use it in js page 
        wp_localize_script('roomzero_modify_calendar_script', 'activityData', array(
            'activityId' => $activity_id
        ));
    } 
}
new CallModifyActivityJs();
//}
//add_action('admin_enqueue_scripts', 'enqueue_modify_activity_calendar_js_scripts');

?>

