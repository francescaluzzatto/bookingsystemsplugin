<?php
//add_action('wp_ajax_modify_all_future_timeslots', 'modify_all_future_timeslots');
//add_action('wp_ajax_nopriv_modify_all_future_timeslots', 'modify_all_future_timeslots');
class ModifyAllFutureTimeslots {
    public function __construct(){
        add_action('wp_ajax_modify_all_future_timeslots', [$this, 'modify_all_future_timeslots']);
        add_action('wp_ajax_nopriv_modify_all_future_timeslots', [$this, 'modify_all_future_timeslots']);
    }

    public function modify_all_future_timeslots(){
        error_log("modify_all_future_timeslots function is being called!");
        ob_clean(); // Clears any accidental output
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'modify_all_events')) {
            wp_send_json_error('Invalid nonce');
            wp_die();
        }
        global $wpdb;
        $table_name = 'wp_booking_seasons';
        // get data from activity_calendar.js
        $activity_id = $_POST['activityId'];
        $clicked_date = sanitize_text_field($_POST['clickedDate']);
        $old_timeslot = sanitize_text_field($_POST['timeslot']);
        $new_timeslot= sanitize_text_field($_POST['new_timeslot']);
        $new_dates= $_POST['new_dates'];
        $new_capacity= intval($_POST['new_capacity']);
        error_log("old timeslot". $old_timeslot);
        error_log("new timeslot". $new_timeslot);
        //get the timeslot data from the database
        $result= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $activity_id));
        //unserialize
        $db_timeslot_data = unserialize($result->timeslot_dates);
    
        if($new_timeslot !== $old_timeslot){
            //get the dates for the old timeslot
            if(empty($new_dates)){
                $dates= $db_timeslot_data[$old_timeslot]['dates'];
                // Ensure the new timeslot's dates array is initialized
            if (!isset($db_timeslot_data[$new_timeslot]['dates']) || !is_array($db_timeslot_data[$new_timeslot]['dates'])) {
                $db_timeslot_data[$new_timeslot]['dates'] = []; // Initialize as an empty array if it doesn't exist
            }
                // Always add the clicked_date to the new timeslot
        if (!in_array($clicked_date, $db_timeslot_data[$new_timeslot]['dates'])) {
            $db_timeslot_data[$new_timeslot]['dates'][] = $clicked_date;
            unset($db_timeslot_data[$old_timeslot]['dates'][array_search($clicked_date, $db_timeslot_data[$old_timeslot]['dates'])]);
        }
                //$db_timeslot_data[$new_timeslot]['dates']= $dates;
                foreach($dates as $date){
                    if($date > $clicked_date){
                        
                        if (!in_array($date, $db_timeslot_data[$new_timeslot]['dates'])) {
                            $db_timeslot_data[$new_timeslot]['dates'][] = $date;
                        }
                        $index= array_search($date, $db_timeslot_data[$old_timeslot]['dates']);
                        if($index !== false){
                            //remove the date from the old timeslot
                            unset($db_timeslot_data[$old_timeslot]['dates'][$index]);
                        }
                        
                    }
                } 
                if (empty($db_timeslot_data[$old_timeslot]['dates'])) {
                    unset($db_timeslot_data[$old_timeslot]);
                }
                    
            }
            else{
            //set the new timeslot to the dates gotten from the old timeslot
            //$db_timeslot_data[$new_timeslot]= $dates;
            $db_timeslot_data[$new_timeslot]['dates']= $new_dates;   
            //find the index of the new dates in the old timeslot dates and unset it
            foreach($new_dates as $new_date){
                $index= array_search($new_date, $db_timeslot_data[$old_timeslot]['dates']);
                $last_date= end($db_timeslot_data[$old_timeslot]['dates']);
                while($new_date <= $last_date){
                    if($index !== false){
                        unset($db_timeslot_data[$old_timeslot]['dates'][$index]);
                    }
                    $new_date = date('Y-m-d', strtotime($new_date . ' +7 days'));
                    $index = array_search($new_date, $db_timeslot_data[$old_timeslot]['dates']);
                    if(empty($db_timeslot_data[$old_timeslot])){
                        break;
                    }
                }
            }
        }
        if (empty($db_timeslot_data[$old_timeslot]['dates'])) {
            unset($db_timeslot_data[$old_timeslot]);
        }
            error_log("new timeslot dates:".print_r($db_timeslot_data[$new_timeslot]['dates'], true));
            //set new capacity
            $db_timeslot_data[$new_timeslot]['capacity'] = $new_capacity;
            //unset the old timeslot
            //unset($db_timeslot_data[$old_timeslot]);
        }else{
            $dates= $db_timeslot_data[$old_timeslot]['dates'];
            $db_timeslot_data[$old_timeslot]['dates']= $dates;
            error_log("old timeslot dates:".print_r($db_timeslot_data[$old_timeslot], true));
            //set new capacity
            $db_timeslot_data[$old_timeslot]['capacity'] = $new_capacity;
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
new ModifyAllFutureTimeslots();
?>