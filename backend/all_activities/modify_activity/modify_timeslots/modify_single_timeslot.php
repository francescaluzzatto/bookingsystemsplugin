<?php
//add_action('wp_ajax_modify_single_timeslot', 'modify_single_timeslot');
//add_action('wp_ajax_nopriv_modify_single_timeslot', 'modify_single_timeslot');

class ModifySingleTimeslot {
    public function __construct(){
        add_action('wp_ajax_modify_single_timeslot', [$this, 'modify_single_timeslot']);
        add_action('wp_ajax_nopriv_modify_single_timeslot', [$this, 'modify_single_timeslot']);
    }

    public function modify_single_timeslot(){
        error_log("modify_single_timeslot function is being called!");
        ob_clean(); // Clears any accidental output
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'modify_event')) {
            wp_send_json_error('Invalid nonce');
            wp_die();
        }
        global $wpdb;
        $table_name = 'wp_booking_seasons';
        // get data from activity_calendar.js
        $activity_id = sanitize_text_field($_POST['activityId']);
        $old_timeslot = sanitize_text_field($_POST['timeslot']);
        $new_timeslot= sanitize_text_field($_POST['new_timeslot']);
        $clickedDate = sanitize_text_field($_POST['clickedDate']);
        $new_capacity= sanitize_text_field($_POST['new_capacity']);
        //get the timeslot data from the database
        $result= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $activity_id));       
        //unserialize
        $db_timeslot_data = unserialize($result->timeslot_dates);
        error_log("Clicked Date: " . $clickedDate);
        error_log("db timeslot data: " . print_r($db_timeslot_data, true));
        error_log("Old Timeslot: " . $old_timeslot);
        error_log("old timeslot data". print_r($db_timeslot_data[$old_timeslot], true));
        error_log("Old Timeslot Dates: " . print_r($db_timeslot_data[$old_timeslot]['dates'], true));
        //check if the clicked date is in the old timeslot dates
        if(in_array($clickedDate, $db_timeslot_data[$old_timeslot]['dates'])){
            
            //initialize empty array for new timeslot
            if (!isset($db_timeslot_data[$new_timeslot])) {
                $db_timeslot_data[$new_timeslot] = ['dates'=>[]]; // Initialize as an array if not set
            }
            //if it is, add the clicked date to the new timeslot
            $db_timeslot_data[$new_timeslot]['dates'][]= $clickedDate;
            //find the index of the clicked date in the old timeslot dates and unset it
            $index= array_search($clickedDate, $db_timeslot_data[$old_timeslot]['dates']);
            error_log("Removed date from old timeslot: " . print_r($db_timeslot_data[$old_timeslot]['dates'][$index], true));
            unset($db_timeslot_data[$old_timeslot]['dates'][$index]);
            error_log("Index= " . $index);
            //add new capacity
            $db_timeslot_data[$new_timeslot]['capacity'] = $new_capacity;
            //if the old timeslot is empty, unset it
            if(empty($db_timeslot_data[$old_timeslot])){
                unset($db_timeslot_data[$old_timeslot]);
            }
        }
        else{
            //if it is not in the old timeslot, set new timeslot to clicked date
            if (!isset($db_timeslot_data[$new_timeslot])) {
                $db_timeslot_data[$new_timeslot] = ['dates'=>[]]; // Initialize as an array if not set
            }
            $db_timeslot_data[$new_timeslot]['dates'][]= [$clickedDate];
            //add new capacity
            $db_timeslot_data[$new_timeslot]['capacity'] = $new_capacity;
        }
        error_log("Updated timeslot data: " . print_r($db_timeslot_data, true));
        //serialize data
        $serialized_timeslots = serialize($db_timeslot_data);
        //update the timeslot_dates in the database
        $wpdb->update(
            $table_name, 
            array('timeslot_dates' => $serialized_timeslots),
            array('id' => $activity_id), // WHERE clause to specify the row to update
            array('%s'),
            array('%d')
        );
        wp_send_json_success("Event modified successfully");
        wp_die();
    }
}
new ModifySingleTimeslot();
?>