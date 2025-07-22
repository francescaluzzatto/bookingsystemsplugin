<?php 
//add_action('wp_ajax_save_new_timeslot_data', 'save_new_timeslot_data');
//add_action('wp_ajax_nopriv_save_new_timeslot_data', 'save_new_timeslot_data');
class SaveNewTimeslotData {
    public function __construct(){
        add_action('wp_ajax_save_new_timeslot_data', [$this, 'save_new_timeslot_data']);
        add_action('wp_ajax_nopriv_save_new_timeslot_data', [$this, 'save_new_timeslot_data']);
    }

    public function save_new_timeslot_data() {
        error_log("save_new_timeslot_data function is being called!");
        ob_clean(); // Clears any accidental output
        global $wpdb;
        $table_name = 'wp_booking_seasons';

        // get data
        $activity_id = $_POST['activityId'];
        $post_timeslot_data = $_POST['timeslot_data'];
        $post_week_days= $_POST['week_days'];
        $post_repeat_until= $_POST['repeat_until'];
        error_log("Received timeslot data: " . print_r($post_timeslot_data, true));
        error_log("Received week days: " . print_r($post_week_days, true));
        error_log("Received repeat until: " . print_r($post_repeat_until, true));

        // serialize the array data before saving it
        //$serialized_timeslots = serialize($timeslot_data);
        //$serialized_week_days = serialize($week_days);
        //$serialized_repeat_until= serialize($repeat_until);

        $result= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $activity_id));
        $db_timeslot_data = unserialize($result->timeslot_dates);
        $db_week_days = unserialize($result->week_days);
        $db_repeat_until = unserialize($result->repeat_until);
        error_log("DB Timeslot Data: " . print_r($db_timeslot_data, true));
        error_log("Post Timeslot Data: " . print_r($post_timeslot_data, true));
        // merge the new timeslot data with the existing data
        //recursive so that if they have the same key, the values will be merged into an array
        $merged_timeslot_data = array_merge_recursive((array)$db_timeslot_data, (array)$post_timeslot_data);
        $merged_week_days = array_merge_recursive((array)$db_week_days, (array)$post_week_days);
        $merged_repeat_until = array_merge_recursive((array)$db_repeat_until, (array)$post_repeat_until);
        error_log("Merged timeslot data: " . print_r($merged_timeslot_data, true));
        error_log("Merged week days: " . print_r($merged_week_days, true));
        error_log("Merged repeat until: " . print_r($merged_repeat_until, true));
        //Serialize merged data
        $serialized_merged_timeslots = serialize($merged_timeslot_data);
        $serialized_merged_week_days = serialize($merged_week_days);
        $serialized_merged_repeat_until = serialize($merged_repeat_until);

        $wpdb->update(
            $table_name, 
            array('timeslot_dates' => $serialized_merged_timeslots, 
            'week_days' => $serialized_merged_week_days,
            'repeat_until' => $serialized_merged_repeat_until),
            array('id' => $activity_id), // WHERE clause to specify the row to update
            array('%s'),
            array('%d')
        );
        wp_send_json_success("Event added successfully");
        wp_die();

    }
}
new SaveNewTimeslotData();
?>