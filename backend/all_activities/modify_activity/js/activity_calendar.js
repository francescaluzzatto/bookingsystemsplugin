jQuery(document).ready(function() {
    //get activity id from wp_localize in modify_activity.php
    var activityId = activityData.activityId;
    //activityId works 
    console.log(activityId);
    console.log("jQuery is working");
    //get the specific calendar id of that specific activity
var calendarEl = jQuery('#calendar-' + activityId);
console.log(calendarEl); 
var startSlot = null; // Store first clicked time slot
var endSlot = null;
//create fullCalendar
var calendar = new FullCalendar.Calendar(calendarEl[0], {
    initialView: 'timeGridWeek', // Options: 'timeGridDay', 'timeGridWeek'
    slotDuration: '00:15:00',    // Customize time slot duration (e.g., 30 minutes)
    selectable: true,            // Allow selecting time slots
    allDaySlot: false,           // Hide "All Day" slot if not needed
    events: function(fetchInfo, successCallback, failureCallback) { 
        //gets correct activityId
        console.log('Activity ID inside events:', activityId); 
        //Gets correct AJAX URL
        console.log('AJAX request about to be sent');
        console.log("AJAX URL:", my_ajax_object.ajax_url);
        jQuery.ajax({
          //get ajax url  
            url:my_ajax_object.ajax_url,
            type: 'POST',
            data: {
            //POST action get_activity_events in get_activity_events.php 
                action: 'get_activity_events',
            //send activityId
                activityId: activityId,
                nonce: my_ajax_object.get_events_nonce,
            },
            success: function(response) {
            //get raw response= array of events 
                console.log("Raw Response:", response);
                //check if response is an array
                if (Array.isArray(response)){
                    //log response 
                    console.log('Events Data:', response);
                    successCallback(response);
                } else {
                    //if not array, log error 
                    console.error("Invalid response format. Expected array, got:", typeof response);
                    
                }
            },
            //if AJAX request fails, log error
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                console.error("XHR:", xhr);
                console.error("Status:", status);
                failureCallback(error);
            }
        });
    },
    //show options for the calendar to show today, next, previous and day, week, month
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridDay,timeGridWeek,dayGridMonth'
    },
    eventClick: function(info){
        //show modify or delete popup
        jQuery("#modify_or_delete_popup").show();
        jQuery("#go_to_delete_event").on("click", function(){
            //show modify event popup
            jQuery("#delete_event_popup").show();
            //get event title and date
            jQuery('#eventTitle').text(info.event.title);
            jQuery('#eventDate').text(info.event.start.toISOString().split('T')[0]);
            console.log("timeslot:", info.event.title);
            console.log("date:", info.event.start.toISOString().split('T')[0]);
            console.log("event id:", activityId);
            //delete event
            jQuery("#delete_event").on("click", function(){
                jQuery("#sure_delete_single_event").show();
                jQuery('#sure_delete_single_event_button').on("click", function(){
                jQuery.ajax({
                    url: my_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_activity_event',
                        activityId: activityId,
                        //Send timeslot and date to delete
                        timeslot: info.event.title,
                        date: info.event.start.toISOString().split('T')[0],
                        nonce: my_ajax_object.delete_event_nonce,
                    },
                    success: function(response){
                        console.log("Event Deleted:", response);
                        calendar.refetchEvents();
                        jQuery("#modify_or_delete_popup").hide();
                        jQuery("#sure_delete_single_event").hide();
                        jQuery("#delete_event_popup").hide();
                        alert("Date deleted successfully");
                    },
                });
            });
            });
            jQuery("#delete_all_future_timeslots").on("click", function(){
                jQuery("#sure_delete_all_future_events").show();
                jQuery('#sure_delete_all_future_events_button').on("click", function(){
                jQuery.ajax({
                    url: my_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_all_future_timeslots',
                        activityId: activityId,
                        timeslot: info.event.title,
                        clickedDate: info.event.start.toISOString().split('T')[0],
                        nonce: my_ajax_object.delete_all_events_nonce,
                    },
                    success: function(response){
                        console.log("Events Deleted:", response);
                        calendar.refetchEvents();
                        jQuery("#modify_or_delete_popup").hide();
                        jQuery("#sure_delete_all_future_events").hide();
                        jQuery("#delete_event_popup").hide();
                        alert("Date deleted successfully");
                    },
                });
            });
            });
        });
        jQuery("#go_to_modify_event").on("click", function(){
            jQuery("#modify_event_popup").show();
            //show value of start time and end time in modify event popup
            jQuery("#modify_startTime").val(info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
            jQuery("#modify_endTime").val(info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
            jQuery("#modify_capacity").val(info.event.extendedProps.capacity);
            //when clicked to modify all future timeslots
            jQuery("#modify_all_future_timeslots").on("click", function(){
                jQuery("#sure_modify_all_future_events").show();
                jQuery('#sure_modify_all_future_events_button').on("click", function(){
                    //Get new value of start time 
                    var start_time = jQuery("#modify_startTime").val();
                    //get new value of end time
                    var end_time = jQuery("#modify_endTime").val();
                    //format the new timeslot
                    var formattedTimeSlot = start_time + " - " + end_time;
                    //get new capacity
                    var capacity = jQuery("#modify_capacity").val();
                    var repeat_until = new Date(jQuery("#modify_repeat_until").val());
                    var new_dates= [];
                    //get the current date of the timeslot
                    var tempDate = new Date(info.event.start);
                    //check if repeatUntil is filled in
                    if(repeat_until){
                        //if it is, repeat until we've reached the repeatUntil date 
                        while (tempDate <= repeat_until) {
                            new_dates.push(tempDate.toISOString().split("T")[0]); // Format as YYYY-MM-DD
                            // Move to the next week
                            tempDate.setDate(tempDate.getDate() + 7);
                        }
                    }
                    jQuery.ajax({
                        url: my_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'modify_all_future_timeslots',
                            activityId: activityId,
                            clickedDate: info.event.start.toISOString().split('T')[0],
                            timeslot: info.event.title,
                            new_timeslot: formattedTimeSlot,
                            new_dates: new_dates,
                            new_capacity: capacity,
                            nonce: my_ajax_object.modify_all_events_nonce,
                        },
                        success: function(response){
                            console.log("Event Modified:", response);
                            calendar.refetchEvents();
                            jQuery("#modify_event_popup").hide();
                            jQuery('#modify_or_delete_popup').hide();
                            jQuery('#sure_modify_all_future_events').hide();
                            alert("Date modified successfully");
                        },
                    });
                });
            });
            jQuery("#modify_saveTimeslot").on("click", function(){
                jQuery("#sure_modify_single_event").show();
                jQuery('#sure_modify_single_event_button').on("click", function(){
                    //Get new value of start time 
                    var start_time = jQuery("#modify_startTime").val();
                    //get new value of end time
                    var end_time = jQuery("#modify_endTime").val();
                    //format the new timeslot
                    var formattedTimeSlot = start_time + " - " + end_time;
                    //get new capacity
                    var capacity = jQuery("#modify_capacity").val();
                    jQuery.ajax({
                        url: my_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'modify_single_timeslot',
                            activityId: activityId,
                            timeslot: info.event.title,
                            new_timeslot: formattedTimeSlot,
                            clickedDate: info.event.start.toISOString().split('T')[0],
                            new_capacity: capacity,
                            nonce: my_ajax_object.modify_event_nonce,
                        },
                        success: function(response){
                            console.log("Event Modified:", response);
                            calendar.refetchEvents();
                            alert("Date modified successfully");
                            jQuery("#modify_event_popup").hide();
                            jQuery('#sure_modify_single_event').hide();
                            jQuery('#modify_or_delete_popup').hide();
                    
                        },
                        error: function(data) {
                            alert("Data not modified. Server error occurred.");
                        }
                    });
                });
            });
        });
    },
    select: function(info) {
        
        // check if first time slot is set. If it is not set, then sets it. 
        if (!startSlot) {
            //get startSlot
            startSlot = info.start; 
            //convert to string with 2 digits for hours and 2 digits for minutes. displays as --:--.
            var startTime = startSlot.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            //get value of input= startTime
            jQuery("#new_startTime").val(startTime);
            //the code runs again on second click. if second click has startSlot already set, then it goes to else, and sets the second time slot as endSlot
        } else {
            //get endSlot
            endSlot = info.end; // Store the second time slot clicked
            
            var endTime = endSlot.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // get value of input= endTime
            jQuery("#new_endTime").val(endTime);
            //GET THE NEW_EVENT_POPUP
            console.log("Popup exists:", jQuery("#new_popup").length > 0);
            //if it exists, show it
            jQuery("#new_popup").show();
            jQuery("#new_saveTimeslot").on("click", function(){
                        //get start time and end time of timeslot
                        var start_time = jQuery("#new_startTime").val();
                        var end_time = jQuery("#new_endTime").val();
                        // When the Save button is clicked in the popup
                        //initialize empty array 
                        var selectedDays = [];
                        // get the value of the checked boxed input, and push it to selectedDays
                        jQuery("input[name='new_repeat_days']:checked").each(function() {
                            selectedDays.push(jQuery(this).val()); 
                        });
                        //get repeat until value 
                        var repeatUntil = new Date(jQuery("#new_repeat_until").val()); 
                        //initialize empty object 
                        var occurrences = {};
                        //format time slots
                        var formattedTimeSlot = start_time + " - " + end_time;
                        //initialize empty array
                        var occurrenceDates = [];

                        // Get the current date of the timeslot 
                        var tempDate = new Date(startSlot);
                        //check if repeatUntil is filled in 
                        if(repeatUntil && selectedDays.length !== 0){
                        //if it is, repeat until we've reached the repeatUntil date
                            while (tempDate <= repeatUntil) {
                                //get the name of the day of the week of the specific date (determined by tempDate)
                                var dayName = tempDate.toLocaleString('en-us', { weekday: 'long' });

                                // check if the name of the day of the week of that specific date is included in the array of selected days for repitition of the day of the week 
                                if (selectedDays.includes(dayName)) {
                                    //push the date to occurenceDates
                                    occurrenceDates.push(tempDate.toISOString().split("T")[0]); // Format as YYYY-MM-DD
                                }

                                // Move to the next day
                                tempDate.setDate(tempDate.getDate() + 1);
                            }
                        }
                        else{
                        //just push the tempDate if repeatUntil is not filled in
                            occurrenceDates.push(tempDate.toISOString().split("T")[0]);
                        }

                        // Store the time slot and occurrences in an object
                        //occurrences[formattedTimeSlot]= occurrenceDates;
                        if (!occurrences[formattedTimeSlot]) {
                            occurrences[formattedTimeSlot] = {};
                        }
                        occurrences[formattedTimeSlot]['dates'] = occurrenceDates;
                        var capacity = jQuery("#new_capacity").val();
                        occurrences[formattedTimeSlot]['capacity'] = capacity;
                        //const repeatMonthly = jQuery("#repeat_monthly").is(":checked") ? 1 : 0;
                        // occurences[formattedTimeslot]['dates'] = occurenceDates;
                        //occurences[formattedTimeslot]['repeat_monthly'] = repeatMonthly;
                        console.log(occurrences);
                        console.log(selectedDays);
                        console.log(repeatUntil);
                        console.log(activityId);
                        // Send the data to WordPress via AJAX
                        jQuery.ajax({
                            url: my_ajax_object.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'save_new_timeslot_data',
                                activityId: activityId,
                                timeslot_data: occurrences,
                                week_days: selectedDays,
                                repeat_until: repeatUntil,
                                //repeat_monthly: repeatMonthly,
                                //activity_name: activity_name,
                            },
                            success: function(data) {
                                alert("Data saved correctly!");
                                // Refresh the calendar
                                calendar.refetchEvents();
                                
                            },
                            error: function(data) {
                                alert("Data not saved. Server error occurred.");
                            }
                        });

                        // Close the modal and reset the slots
                        jQuery("#new_popup").hide();
                        startSlot = null;
                        endSlot = null;
                    });
                    
                }
        }
});
calendar.render();
});