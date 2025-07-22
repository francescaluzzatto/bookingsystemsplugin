<?php
//function to create a div to display calendar 
function roomzero_fullcalendar_shortcode_no_jquery($atts) {
    global $wpdb;
    $table_name = 'wp_booking_seasons';
    //select all rows from the database 

    $results = $wpdb->get_results("SELECT * FROM $table_name");
    ob_start(); // Start output buffering
    show_form();
    show_calendar();
    ?>
    
    
    <?php

    return ob_get_clean(); // Return the buffered content
}

add_shortcode('roomzero_fullcalendar_no_jquery', 'roomzero_fullcalendar_shortcode_no_jquery');

function show_form(){
    ?>
    <div id="calendar"></div>
    <div id="date-selection-form" style="display:none;">
    <h3>Select Date Information</h3>
    <form id="date-form">
        <label for="event-title">Event Title:</label>
        <input type="text" id="event-title" name="event-title" readonly style="width:400px"><br><br>
        <!input type="text" id="event-title" name="event-title" required><br><br>

        <input type="hidden" id="selected-date" name="selected-date">
        <div id="time-slots">
            <!-- Time slots will be dynamically inserted here -->
        </div><br>
        <input type="submit" value="Submit">
    </form>
    <form id="bookingForm" style="display:none">
    <label for="date">Select Date:</label><br>
        <div id="datediv">
        </div>
    <input type="date" id="date" name="date" required><br>

    <label for="excursion">Select Excursion:</label><br>
    <select id="excursion" name="excursion">
        <?php foreach($results as $row): ?>
            <option value="<?php echo $row->excursion_name; ?>" > <?php echo $row->excursion_name; ?> </option>
            <?php endforeach; ?>
    </select><br>
            <label for="time">Select Time Slot: </label>
            <div id="timediv"></div>
    <button type="submit">Book Now</button>
    <p id="errorMessage" style="color: red; display: none;">No more spots available!</p>
</form>
</div>
<?php 
}

function show_calendar(){
    ?>
    <script>
    // show fullcalendar
document.addEventListener('DOMContentLoaded', function() {
// show calendar within the calendar div 
var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
    //Set month view of the calendar 
    initialView: 'dayGridMonth', 
    //start the events array
    events: [
    <?php 
    //initialize an empty events array
    $events= [];
    //iterate through each row of events 
    foreach ($results as $row){ 
        //INSERT SEASONAL EVENTS 
        //insert into events array the json_encoded (string version) of the title, startdate and end date. These correspond to the season in which the excursions are available for booking 
        $events[]= json_encode([
            //fetch each row value from the database 
            'title'=> $row->excursion_name,
            'rrule' => [
                'freq'=> 'daily',
                'interval' => 1,
                'dtstart' => $row->start_date,
                'until' => $row->end_date,

            ],
            
            'timeSlots' => esc_js($row->time_slots),
            //add the array of values of the except_dates so that they are EXCLUDED FROM THE CALENDAR. 
            'exdate' => array_values(json_decode($row->except_dates, true)),
        ]);          
        //INSERT EXCEPT DATES INTO THE CALENDAR
                }
                //join together all of the events with , into the events array
                echo implode(",\n", $events); ?>
    ],
    dateClick: function(info) {
        //Get the window to scroll down to form 
        var dateForm = document.getElementById('date-selection-form');
        var bookingForm= document.getElementById('bookingForm');
        bookingForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.getElementById('bookingForm').style.display = 'block';
        dateForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.getElementById('selected-date').value = info.dateStr;
        document.getElementById('date-selection-form').style.display = 'block';

        //var eventTitleInput = document.getElementById('event-title');
        //eventTitleInput.value= info.title;
        var clickedDate = info.dateStr;
    
    // Filter events to find those that match the clicked date
    var eventsOnDate = [];
    
    // Loop through events and check if the date is in the event's date range or is an exception
    <?php foreach ($results as $row): ?>
        var eventStartDate = '<?php echo $row->start_date; ?>';
        var eventEndDate = '<?php echo $row->end_date; ?>';
        var eventTitle = '<?php echo $row->excursion_name; ?>';
        var eventTimeSlots = JSON.parse('<?php echo $row->time_slots; ?>');
        var exceptDates = <?php echo json_encode(array_values(json_decode($row->except_dates, true))); ?>;
        
        // Check if the clicked date is within the event date range and not an exception
        if (clickedDate >= eventStartDate && clickedDate <= eventEndDate){ //&& !exceptDates.includes(clickedDate)) {
            eventsOnDate.push({ title: eventTitle, timeSlots: eventTimeSlots });
        }
    <?php endforeach; ?>

        

        // Get all events on the calendar
        var allEvents = calendar.getEvents();

        // Get the "Event Title" input field
        var eventTitleInput = document.getElementById('event-title');
        var timeSlotsDisplay = document.getElementById('time-slots');
        // Display the event titles in the input field
        if (eventsOnDate.length > 0) {
        var eventTitles = [];
            var timeSlots = [];

            eventsOnDate.forEach(function(event) {
                eventTitles.push(event.title);
                console.log(event.timeSlots);
                // Extract time slots if available
                
                event.timeSlots.forEach(slot =>{
                    timeSlots.push(slot);
                });
            });

            // Populate the event title and time slots
            eventTitleInput.value = eventTitles.join(", ");
            console.log(timeSlots);
            timeSlotsDisplay.innerHTML = "<strong>Available Time Slots:</strong><ul><li>" + timeSlots.join("</li><li>") + "</li></ul>";
        } else {
            // Clear fields if no events
            eventTitleInput.value = "";
            timeSlotsDisplay.innerHTML = "<strong>No time slots available for the selected date.</strong>";
        }
        var eventDate = document.getElementById("date");
        date.value= clickedDate;
        //get timeslots div
        var timeslotdiv = document.getElementById("timediv");
        //create a select for the timeslot options
        let select = document.createElement("select");
        timeslotdiv.appendChild(select);
        //loop through all the time slots
        timeSlots.forEach(timeslot=>{
            console.log(timeSlots);
            //create options for each timeslot
            var input = document.createElement("option");
            
            input.className = "inputTime"; 
            select.appendChild(input); 
            input.value= timeslot;
            input.innerHTML= timeslot;
        } );

        //alert("Date clicked: " + info.dateStr); // Date string from FullCalendar
    }
})
calendar.refetchEvents();
calendar.render();
});
    </script>
    <?php
}