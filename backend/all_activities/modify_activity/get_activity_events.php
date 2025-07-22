<?php 
//add_action('wp_ajax_get_activity_events', 'get_activity_events'); // Logged-in users
//add_action('wp_ajax_nopriv_get_activity_events', 'get_activity_events'); // Guests (if needed)
class GetActivityEvents {
    public function __construct(){
        add_action('wp_ajax_get_activity_events', [$this, 'get_activity_events']);
        add_action('wp_ajax_nopriv_get_activity_events', [$this, 'get_activity_events']);
    }

    public function get_activity_events() {
        ob_clean(); // Clears any accidental output
        header('Content-Type: application/json'); // Ensures JSON response
        error_log("get_activity_events function is being called!");
        //check if nonce is set and it is valid
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_events')) {
            wp_send_json_error("Invalid nonce");
        }
        //check if activity id is set and it is a digit
        if (!isset($_POST['activityId']) || empty($_POST['activityId'])) {
            wp_send_json_error("Invalid activity ID");
        }
        //get activity id 
        $activity_id = $_POST['activityId'];
        error_log("Received activityId: " . $activity_id);
        global $wpdb;
        error_log("Fetching events for Activity ID: " . $activity_id); // Debugging
        $table_name = 'wp_booking_seasons';
        //get row ONLY where id is activity_id
        $result = $wpdb->get_row($wpdb->prepare("SELECT timeslot_dates FROM $table_name WHERE id = %s", $activity_id));

        if (!$result || empty($result->timeslot_dates)) {
            wp_send_json_error("No time slots found");
        }
        //get unserialized timeslots=> dates
        $time_slots = unserialize($result->timeslot_dates); // Assuming it's serialized

        // initialize empty events array
        $events = [];
        // save time_slots as timeslot => dates (e.g 11:30-13:30 => [2025-03-12, 2025-03-15])
        foreach ($time_slots as $slot => $dates) {
            //get each date in the array of dates
            if(!isset($dates['dates'])){
                error_log("Missing 'dates' key for this iteration");
                continue; 
            }
                foreach ($dates['dates'] as $date) {
                    // add to events for each separate day: 
                    $events[] = [
                        //title= timeslot
                        'title' => sanitize_text_field($slot),
                        // start = date and first string in array $times, which will be the first timeslot on that specific day
                        'start' => sanitize_text_field($date . "T" . explode(' - ', $slot)[0]. ":00"), // Format: YYYY-MM-DDTHH:MM
                        // end = date and second string in array $times, which will be the second timeslot on that specific day
                        'end' => sanitize_text_field($date . "T" . explode(' - ', $slot)[1]. ":00"),
                        // Set allDay to false
                        'extendedProps' => ['capacity' => $dates['capacity']],
                        'allDay' => false
                    ];
                }
            
        }
        //var_dump($events);
        error_log("Events fetched successfully for Activity ID: " . $activity_id);
        error_log("Events: " . print_r($events, true));
        // send to js file
        echo json_encode($events);
        wp_die(); 
    }
}
new GetActivityEvents();
?>