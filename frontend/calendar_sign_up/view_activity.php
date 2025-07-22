<?php 
class FrontendViewActivity{
    public function __construct(){
        add_shortcode('view_activity', [$this, 'frontend_view_activity']);
    }
    public function frontend_view_activity(){
        if(isset($_GET['activity_id'])){
            $activity_id = intval($_GET['activity_id']);
            global $wpdb;
            $table_name = 'wp_booking_seasons';
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $activity_id);
            $result = $wpdb->get_row($query);

            $output = '';
            if($result){
                // Display activity details
                $output .= '<h2>Activity Details</h2>';
                $output .= '<p>Activity Name: ' . esc_html(unserialize($result->activity_name)) . '</p>';
                $output .=   '<div id="calendar-'. $activity_id .'"></div>';
            } else {
                $output .= '<p>No activity found with this ID.</p>';
            }
        } else {
            $output .= '<p>No activity ID provided.</p>';
        }
        return $output;
    }
}
new FrontendViewActivity();

class LocalizeActivityId{
    function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'localize_activity_id']);
    }
    function localize_activity_id(){
        if(isset($_GET['activity_id'])){
            $activity_id = intval($_GET['activity_id']);
            wp_localize_script('calendar-sign-up', 'activity_data', [
                'activity_id' => $activity_id,
            ]);
        }
    }
}
new LocalizeActivityId();