<?php 
//add_action('wp_ajax_delete_all_future_timeslots', 'delete_all_future_timeslots');
//add_action('wp_ajax_nopriv_delete_all_future_timeslots', 'delete_all_future_timeslots');
class DeleteAllFutureTimeslots {
    public function __construct(){
        add_action('wp_ajax_delete_all_future_timeslots', [$this, 'delete_all_future_timeslots']);
        add_action('wp_ajax_nopriv_delete_all_future_timeslots', [$this, 'delete_all_future_timeslots']);
    }

    public function delete_all_future_timeslots(){
        ob_clean(); // Clears any accidental output
        // Check that the function is being called
        error_log("delete_all_future_timeslots is being called!");
        //check if the nonce is set and valid
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'delete_all_events')) {
            error_log("Error: Invalid nonce - " . print_r($_POST, true));
            echo json_encode(array('error' => 'Invalid nonce'));
            wp_die();
        }
        $activity_id = $_POST['activityId'];
        $timeslot = sanitize_text_field($_POST['timeslot']);
        $clicked_date = sanitize_text_field($_POST['clickedDate']);
        global $wpdb;
        $table_name = 'wp_booking_seasons';
        $results= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $activity_id));
        if (!$results || empty($results->timeslot_dates)) {
            echo json_encode("No time slots found");
        }
        $time_slots = unserialize($results->timeslot_dates);
        $dates= $time_slots[$timeslot]['dates'];
        unset($time_slots[$timeslot]['dates'][array_search($clicked_date, $time_slots[$timeslot]['dates'])]);
        foreach($dates as $date){
            if ($date > $clicked_date){
                error_log("Deleting future date: $date from timeslot: $timeslot");
                // Remove the date from the timeslot
                unset($time_slots[$timeslot]['dates'][array_search($date, $time_slots[$timeslot]['dates'])]);
            } else {
                error_log("Keeping past date: $date in timeslot: $timeslot");
            }
        }
        error_log("Time slots after deletion: " . print_r($time_slots, true));
        $serialized_time_slots = serialize($time_slots);
        $wpdb->update(
            $table_name, 
            array('timeslot_dates' => $serialized_time_slots),
            array('id' => $activity_id), 
            array('%s'),
            array('%d')
        );
    }
    
}
new DeleteAllFutureTimeslots();