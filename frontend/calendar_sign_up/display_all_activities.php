<?php 
class FrontendDisplayAllActivities{
    public function __construct(){
        add_shortcode('display_all_activities', [$this, 'frontend_display_all_activities']);
    }
    public function frontend_display_all_activities($atts){
        //create activity details page
        $create_activity= new CreateActivityPage();
        $activity_details_page_id= $create_activity->create_activity_details_page();
        // Check if the page was created successfully
        if (is_wp_error($activity_details_page_id)) {
            return 'Error creating activity details page.';
        }
        $activity_details_page_url = get_permalink($activity_details_page_id);
        // Extract the user_id from the shortcode attributes
        $atts = shortcode_atts([
            'user_id' => 0, // Default to 0 if no user_id is provided
        ], $atts);

        $user_id = intval($atts['user_id']); // Sanitize the user_id

        global $wpdb;
        $table_name = 'wp_booking_seasons';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id);
        $results = $wpdb->get_results($query);

        $output = '';
        if ($results) {
            echo '<div class="activity-list">';
            foreach ($results as $row) {
                if($row->activity_name !== null && unserialize($row->activity_name) !== "" ){
                $activity_name = unserialize($row->activity_name);

                // Generate a link for each activity
                $activity_url = add_query_arg([
                    'activity_id' => $row->id, // Pass the activity ID as a query parameter
                ], $activity_details_page_url); // Use the current page's permalink

                $output .= '<div class="activity-item">';
                $output .= '<h3>Activity Name: ' . esc_html($activity_name) . '</h3>';
                $output .= '<a href="' . esc_url($activity_url) . '">View Activity</a>';
                $output .= '</div>';
            }
        }
            $output .= '</div>';
        } else {
            $output .= '<p>No activities found for this user.</p>';
        }
        return $output;
    }
    
}
new FrontendDisplayAllActivities();

