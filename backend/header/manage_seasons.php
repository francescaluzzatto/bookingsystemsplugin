<?php 
if (!function_exists('show_calendar')) {
require_once plugin_dir_path(__FILE__) . '../calendar/calendar.php';
}
class ManageSeasons {
//add form to allow admins to manage seasonal bookings
public function season_booking_plugin_admin_page() {
    global $success_message; 
     // Display success message if it exists
     if (!empty($success_message)) {
        echo '<div class="success-message" style="color: black; margin-bottom: 20px;">' . esc_html($success_message) . '</div>';
    }
    //call function to show seasons header
    $this->manage_seasons_header();
    //call function to show calendar
    $calendar_manage_seasons= new CalendarManageSeasons();
    $calendar_manage_seasons->show_calendar();
}

public function manage_seasons_header(){
    ?>
    <div class="wrap">
    <h1>Manage Seasons</h1>
    <p> Your User Id is: <?php echo get_current_user_id(); ?></p>
    <p> When you Add Your Shortcode, please add user_id="<?php echo get_current_user_id(); ?>" to the shortcode. </p>
    <p> This will allow users to see the calendar and sign up for your activities. </p>
    <?php
    //get_existing_activities();
    $this->get_new_activity();
    $this->get_seasonal_header();
    ?>


<?php
}



public function get_new_activity(){
    ?>
    <div id="activity_container">
    <input type="text" id="activity_name" placeholder="Enter activity name" />
</div>
<?php
}

public function get_seasonal_header(){
    ?>
    <div id="season-calendar">
        <!-- form to allow admins to input seasonal data -->
        <form method="POST" action="">
            <div id= "inputContainer">
            <label for="excursion_name">Excursion Name:</label>
            <input type="text" name="excursion_name"><br>
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date"><br>
            <label for="end_date"> End Date </label>
            <input type="date" name= "end_date"><br>
            <label for="timeslot" > Time Slot </label>
            <!-- add an array to allow multiple time slots when button is pressed -->
            <input type="time" name="timeslot[]" id="timeSlot"> - <input type="time" name="timeslot[]" id="timeSlot"><br>
            </div>
            <button type="button" onclick="addnewTimeSlot()">Add New Time Slot </button><br>
            <div id="inputContainer2">
                <!-- add array to allow multiple except dates, and also to allow them to repeat daily, weekly, or monthly or not at all -->
            <label for="except_date">Except: (add all of the dates you DO NOT want to be accessible to users)</label>
            <input type="date" name="except_date[]" id="exceptDate">
            <label for="frequency">Repeat:</label>
            <select id="frequency" name="frequency[]">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value= "none" >None </option>
            </select><br>
            </div>
            <button type="button" onclick="addnewExceptDate()"> Add New Except Date </button><br>
            <button type="submit" name="submit_timeslot">Submit</button>
            
        </form>
    </div>
</div>
<?php
}
}
new ManageSeasons();

add_action('wp_ajax_get_existing_activities', 'get_existing_activities');
add_action('wp_ajax_nopriv_get_existing_activities', 'get_existing_activities');

function get_existing_activities(){
    ?>
    <select id="activity_dropdown">
        <option value="">Select an activity</option>
    </select>
    <?php
    //Get table name
    global $wpdb;
    $table_name = 'wp_booking_seasons';
    //get activity name from table
    $results = $wpdb->get_results("SELECT activity_name FROM $table_name");

//iterate through each entry and show each of them 
    foreach ($results as $row) {
        if($row->activity_name !== null && unserialize($row->activity_name) !== "" ){
        echo "Activity Name:".unserialize($row->activity_name)."<br>";
        }
    }
}

class CallHeaderJs{
public function __construct(){
    add_action('admin_enqueue_scripts', [$this, 'enqueue_js_scripts']);
}
public function enqueue_js_scripts(){
    $url = get_site_url();
    $plugin_url = $url. '/wp-content/plugins/ROOMZERO';
    wp_enqueue_script(
        'roomzero_header_script',
        $plugin_url . '/backend/header/js/header.js',
        array('jquery'),
        null,
        true
    );
}
}
new CallHeaderJs();
//add_action('admin_enqueue_scripts', 'enqueue_js_scripts');