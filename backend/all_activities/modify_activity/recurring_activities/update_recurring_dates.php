<?php 
function update_recurring_dates(){
    global $wpdb;
    $table_name = 'wp_booking_seasons';
    $results= $wpdb->get_results("SELECT id, timeslot_dates FROM $table_name");
    foreach($results as $result){
        $timeslot_data = unserialize($result->timeslot_dates);
        foreach($timeslot_data as $timeslot => $data){
            if(isset($data['repeat_monthly']) && $data['repeat_monthly']==1 ){
            $new_dates = [];
            foreach($data['dates'] as $date){
                $new_date = date('Y-m-d', strtotime($date . " +1 month"));
                $new_dates[] = $new_date;
            }
            $timeslot_data[$timeslot]['dates'] = $new_dates;
            }
        }
        $serialized_timeslots = serialize($timeslot_data);
        $wpdb->update(
            $table_name, 
            array('timeslot_dates' => $serialized_timeslots),
            array('id' => $result->id), // WHERE clause to specify the row to update
            array('%s'),
            array('%d')
        );
    }
}
?>