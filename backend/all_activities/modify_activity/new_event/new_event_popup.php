<?php
ob_start(); // Start output buffering
?>
<!DOCTYPE html>
<html lang="en">
<div id="new_popup">
    <p>Start Time</p><input type="time" id="new_startTime"> <br>
    <p>End Time</p> <input type="time" id="new_endTime"> <br>
    <p> Repeat on days: </p><br>
    <label><input type="checkbox" name="new_repeat_days" value="Monday">Monday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Tuesday">Tuesday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Wednesday">Wednesday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Thursday">Thursday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Friday">Friday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Saturday">Saturday</label>
    <label><input type="checkbox" name="new_repeat_days" value="Sunday">Sunday</label><br>
    <p>Repeat Until</p> <input type="date" id= "new_repeat_until" name="new_repeat_until"><br>
    <p>Capacity:</p> <input type="number" id="new_capacity" name="new_capacity" min="1"><br>
    <button id="new_saveTimeslot">Save</button>
    <button onclick="jQuery('#new_popup').hide();">Cancel</button>
</div>
</html>
<?php 
$html= ob_get_clean(); // Get the buffered content
return $html; // Output the HTML content

