<?php
//add_action('wp_ajax_delete_activity_event', 'delete_activity_event');
//add_action('wp_ajax_nopriv_delete_activity_event', 'delete_activity_event');
class DeleteActivityEvent {
    public function __construct(){
        add_action('wp_ajax_delete_activity_event', [$this, 'delete_activity_event']);
        add_action('wp_ajax_nopriv_delete_activity_event', [$this, 'delete_activity_event']);
    }

    public function delete_activity_event(){
        header('Content-Type: application/json'); // Ensures JSON response
        ob_clean(); // Clears any accidental output
        // Check if the nonce is set and valid
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'delete_event')) {
        error_log("Error: Invalid nonce - " . print_r($_POST, true));
            echo json_encode(array('error' => 'Invalid nonce'));
            wp_die();
        }
        error_log("get_activity_events function is being called!");
        if (!isset($_POST['activityId']) || empty($_POST['activityId']) ||
        !isset($_POST['timeslot']) || !isset($_POST['date']) ) {
            error_log("Error: Invalid request data - " . print_r($_POST, true));
            echo json_encode(array('error' => 'Invalid request'));
            wp_die();
        }

        $activity_id = $_POST['activityId'];
        $timeslot = sanitize_text_field($_POST['timeslot']);
        $date = sanitize_text_field($_POST['date']);
        error_log("Received delete request - Activity ID: $activity_id, Timeslot: $timeslot, Date: $date");
        
        global $wpdb;
        $table_name = 'wp_booking_seasons';

        $result= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $activity_id));
        if (!$result || empty($result->timeslot_dates)) {
            echo json_encode("No time slots found");
        }
        error_log("Retrieved timeslot dates: " . print_r($result->timeslot_dates, true));
        // Unserialize the timeslot_dates
        $time_slots = unserialize($result->timeslot_dates);
        // Remove the specific date from the timeslot
        //return the array of dates for that specific timeslot-> eg 11:30-13:30 => [2025-03-12, 2025-03-15] returns [2025-03-12, 2025-03-15]
        //sets the time slot to the timeslot got with $_POST['timeslot']
        if (isset($time_slots[$timeslot]['dates'])) {
            //gets the date that matches the date and the timeslot gotten from $_POST['date'] and $_POST['timeslot']
            //returns the INDEX of the date 
            $index = array_search($date, $time_slots[$timeslot]['dates']);
            //if the index is not false, unset the date from the timeslot
            if ($index !== false) {
                //remove ONLY that date from the timeslot
                //gets specific date at specific index time_slots[$timeslot] gets the date, [$index] gets the date at that index
                unset($time_slots[$timeslot]['dates'][$index]);
                //if the timeslot is empty, unset the timeslot
                if (empty($time_slots[$timeslot]['dates'])) {
                    unset($time_slots[$timeslot]);
                }
            }
        }
        //serialize the timeslot_dates
        $serialized_time_slots = serialize($time_slots);
        error_log("updated timeslot_dates: " . $serialized_time_slots);
        //update the timeslot_dates in the database

        $wpdb->update(
            $table_name, 
            array('timeslot_dates' => $serialized_time_slots),
            array('id' => $activity_id), // WHERE clause to specify the row to update
            array('%s'),
            array('%d')
        );
        //echo json_encode("Event deleted successfully");
        wp_send_json_success("Event deleted successfully");
        wp_die();
    }
}
new DeleteActivityEvent();