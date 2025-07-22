<?php 
ob_start(); // Start output buffering
?>
<!DOCTYPE HTML>
<html>

<div id= "modify_or_delete_popup">
    <h2>Do you want to modify this event?</h2>
    <button id="go_to_modify_event">Modify Event</button><br>
    <h2>Do you want to delete this event?</h2>
    <button id="go_to_delete_event">Delete Event</button><br><br>
    <button onclick="jQuery('#modify_or_delete_popup').hide();">Cancel</button>
</div>
<div id="modify_event_popup">
    <h2>Modify Event </h2>
    <p>Start Time</p><input type="time" id="modify_startTime"> <br>
    <p>End Time</p> <input type="time" id="modify_endTime"> <br>
    
    <p>Capacity:</p> <input type="number" id="modify_capacity" name="modify_capacity" min="1"><br>
    <button id="modify_saveTimeslot">Save for this timeslot only</button>
    <button id="modify_all_future_timeslots">Modify for all future timeslots with this timeslot</button>
    <br>
    <button onclick="jQuery('#modify_event_popup').hide();">Cancel</button>
</div>
<div id="delete_event_popup">
    <h2>Delete Event </h2>
    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
    <p><strong>Date:</strong> <span id="eventDate"></span></p>
    <button id="delete_event">Delete this event only</button>
    <button id="delete_all_future_timeslots">Delete all future timeslots with this timeslot</button><br>
    <button onclick="jQuery('#delete_event_popup').hide();">Cancel</button>
</div>
<div id="sure_modify_single_event" class="sure_popup">
    <h2>Are you sure you want to modify this event?</h2>
    <button id="sure_modify_single_event_button">Yes</button>
    <button onclick="jQuery('#sure_modify_single_event').hide();">No</button>
</div>
<div id="sure_modify_all_future_events" class="sure_popup">
<p> Modify Repeat Until (Fill this in if you want your timeslot dates to end on a different date than previously specified): </p> <input type="date" id= "modify_repeat_until" name="modify_repeat_until">
    <h2>Are you sure you want to modify all future events with this timeslot?</h2>
    <button id="sure_modify_all_future_events_button">Yes</button>
    <button onclick="jQuery('#sure_modify_all_future_events').hide();">No</button>
</div>
<div id="sure_delete_single_event" class="sure_popup">
    <h2>Are you sure you want to delete this event?</h2>
    <button id="sure_delete_single_event_button">Yes</button>
    <button onclick="jQuery('#sure_delete_single_event').hide();">No</button>
</div>
<div id="sure_delete_all_future_events" class="sure_popup">
    <h2>Are you sure you want to delete all future events with this timeslot?</h2>
    <button id="sure_delete_all_future_events_button">Yes</button>
    <button onclick="jQuery('#sure_delete_all_future_events').hide();">No</button>
</div>
</html>
<?php
$modify_event_html= ob_get_clean(); // Get the buffered content
return $modify_event_html; // Output the HTML content