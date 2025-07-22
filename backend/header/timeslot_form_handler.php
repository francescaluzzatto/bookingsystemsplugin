<?php 

//function to handle data and insert it into MySQL database
function timeslot_form_handler() {
    global $wpdb; // Declare $wpdb as global
    //show success message 
    global $success_message;
    //check to see if submit_timeslot is selected (submit button)
    submit_timeslot();

}
add_action('init', 'timeslot_form_handler');

function submit_timeslot(){
    if (isset($_POST['submit_timeslot'])) {
        //check that the data inserted is not empty 
        if (!empty($_POST["excursion_name"]) && !empty($_POST['timeslot']) && is_array($_POST['timeslot']) && !empty($_POST['except_date']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        //determine table name
        $table_name ='wp_booking_seasons';

        // Sanitize and validate inputs
        $excursion_name= sanitize_text_field($_POST['excursion_name']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $timeslots = array_map('sanitize_text_field', $_POST['timeslot']);
        $except_dates= array_map('sanitize_text_field', $_POST['except_date']);
        $except_dates = array_map(function($date) {
            return date('Y-m-d', strtotime($date));
        }, $except_dates);
        $frequencies= array_map('sanitize_text_field', $_POST['frequency']);
        // Group time slots into pairs and format them with a hyphen
        $formatted_timeslots = [];
        for ($i = 0; $i < count($timeslots); $i += 2) {
            if (isset($timeslots[$i + 1])) { // Ensure pairs exist
                $formatted_timeslots[] = $timeslots[$i] . ' - ' . $timeslots[$i + 1];
            }
        }
        //json encode time slots so they can be inserted in an array in the database 
        $timeslots_json = json_encode($formatted_timeslots);

        //initialize empty except dates array 
            $except_dates_array= [];
            // loop for each date chosen in the except dates array and choose to map the array (indice) to the date in except dates 
            foreach($except_dates as $index => $date){
                //set current except date as date 
                $current_except_date = $date;
                //find the frequency corresponding to that date by finding the indice of that date 
                $frequency= $frequencies[$index];
                if ($frequency === 'none') {
                    // If frequency is 'none', just add the date
                    $except_dates_array[] = $current_except_date;
                }else{
                    //loop over every date in the array until it reaches the end date of the season
            while (strtotime($current_except_date) <= strtotime($end_date)){
                //add the current date to the except dates array
                $except_dates_array[]= $current_except_date;
                switch ($frequency){
                    case 'daily':
                        //if frequency is daily, then add every day to except dates array 
                        $current_except_date = date('Y-m-d', strtotime($current_except_date . ' + 1 day'));
                        break;
                    case 'weekly':
                        //if frequency is weekly, then add the current day plus the same day every week to except dates array 
                        $current_except_date= date('Y-m-d', strtotime($current_except_date . ' + 1 week'));
                        break;
                    case 'monthly':
                        //if frequency is monthly, then add the current day plus the same day every month to except dates array 
                        $current_except_date= date('Y-m-d', strtotime($current_except_date . ' + 1 month'));

               }
            }
        }
    } //create a unique array of all the except dates, and encode them json so they can be added to the database
            $all_except_dates= array_unique($except_dates_array);
            //Extract just values from array 
            

            $all_except_dates_json = json_encode($all_except_dates); 
            $frequencies_json = json_encode($frequencies);
        $wpdb->insert(
            $table_name,
            [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'time_slots' => $timeslots_json,
                'except_dates' => $all_except_dates_json,
                'excursion_name' =>$excursion_name,
                'frequency' => $frequencies_json,
                //'all_dates' => $all_dates,
            ],
            ['%s', '%s', '%s', '%s', '%s'] // Data types: string, string, string
        );
        // Set success message
        $success_message = "Thank you! Your submission was received.";
        
    }
}

}

?>