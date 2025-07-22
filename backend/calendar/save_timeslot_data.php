<?php 

class SaveTimeslotData{

    public function __construct(){
        add_action('wp_ajax_save_timeslot_data', [$this, 'save_timeslot_data']);
        add_action('wp_ajax_nopriv_save_timeslot_data', [$this, 'save_timeslot_data']);
    }
public function generate_secure_activity_id($length = 16) {
    return bin2hex(random_bytes($length / 2)); // Generate a secure random ID
}

//add_action('wp_ajax_save_timeslot_data', 'save_timeslot_data');
//add_action('wp_ajax_nopriv_save_timeslot_data', 'save_timeslot_data');

public function save_timeslot_data() {
    if (!wp_verify_nonce($_POST['nonce'], 'save_calendar_data')) {
        wp_send_json_error('Invalid nonce');
        wp_die();
    }
    global $wpdb;
    $table_name = 'wp_booking_seasons';

    // get data
    $timeslot_data = $_POST['timeslot_data'];
    $week_days= sanitize_text_field($_POST['week_days']);
    $repeat_until= sanitize_text_field($_POST['repeat_until']);
    $activity_name= sanitize_text_field($_POST['activity_name']);
    $new_id= $this -> generate_secure_activity_id(16);

    // serialize the array data before saving it
    $serialized_timeslots = serialize($timeslot_data);
    $serialized_week_days = serialize($week_days);
    $serialized_repeat_until= serialize($repeat_until);
    $serialized_activity_name= serialize($activity_name);
    $user_id= get_current_user_id();
    

    // insert the serialized data into the database
    $wpdb->insert($table_name, [
        'timeslot_dates' => $serialized_timeslots,
        'week_days' => $serialized_week_days,
        'repeat_until' => $serialized_repeat_until,
        'activity_name' => $serialized_activity_name,
        'user_id' => $user_id,
        'id' => $new_id,
    ]);

}
}
new SaveTimeslotData();
?>