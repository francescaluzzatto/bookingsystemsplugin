<?php 
//show all of the datbases' entries, for each entry 
function roomzero_display_timeslots() {
    global $wpdb;
    $table_name = 'wp_booking_seasons';

    $results = $wpdb->get_results("SELECT * FROM $table_name");
//iterate through each entry and show each of them 
    foreach ($results as $row) {
        echo " Excursion Name:".$row->excursion_name."<br>
        Start Date:". $row->start_date." <br>
                End Date:". $row->end_date ."<br>
                Available Time Slots:". $row->time_slots ."<br>
                Except for these dates:". $row->except_dates ."<br>";
    }


}
//Add shortcode to display entries 
add_shortcode('display_timeslots', 'roomzero_display_timeslots');

//show form which allows user to choose a timeslot and a date that they want to do the excursion on 
function roomzero_form_choose_timeslots(){
    ?>
    <h1>Choose the Time Slots in which you wish to go on this excursion:</h1>
    <form id="custom-form" method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        
        <label for="excursion">Choose which excursion you would like to participate in:</label>
        <?php
        global $wpdb;
        $table_name = 'wp_booking_seasons';
    
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        ?><select class='form-control' id='excursion' name='excursion'>
        <?php
        foreach ($results as $row) {
            echo "
                    <option value='". $row->excursion_name."'>".$row->excursion_name." <br>
                    ";
        }?>
        </select>
        <!-- preferably choose more than one excursion -->
        
        
        <button type="submit" name="choose_timeslots">Submit</button>
    </form>
    <?php

}
//add shortcode to show form 
add_shortcode('choose_timeslots', 'roomzero_form_choose_timeslots');