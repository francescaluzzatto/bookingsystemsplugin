<!DOCTYPE HTML>
<html>
<div id="popup">
    <p>Start Time</p><input type="time" id="startTime"> <br>
    <p>End Time</p> <input type="time" id="endTime"> <br>
    <p> Repeat on days: </p><br>
    <label><input type="checkbox" name="repeat_days" value="Monday">Monday</label>
    <label><input type="checkbox" name="repeat_days" value="Tuesday">Tuesday</label>
    <label><input type="checkbox" name="repeat_days" value="Wednesday">Wednesday</label>
    <label><input type="checkbox" name="repeat_days" value="Thursday">Thursday</label>
    <label><input type="checkbox" name="repeat_days" value="Friday">Friday</label>
    <label><input type="checkbox" name="repeat_days" value="Saturday">Saturday</label>
    <label><input type="checkbox" name="repeat_days" value="Sunday">Sunday</label><br>
    <p>Capacity:</p> <input type="number" id="capacity" name="capacity" min="1"><br>
    <p>Repeat Until</p> <input type="date" id= "repeat_until" name="repeat_until">
    <button id="saveTimeslot">Save</button>
</div>
</html>