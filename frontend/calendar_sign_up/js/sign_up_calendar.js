jQuery(document).ready(function() {
    //get activity id from wp_localize in modify_activity.php
    var activity_id = activity_data.activity_id;
    //activityId works 
    console.log(activity_id);
    console.log("jQuery is working");
    //get the specific calendar id of that specific activity
    var calendarEl = jQuery('#calendar-' + activity_id);
    console.log(calendarEl); 
    var startSlot = null; // Store first clicked time slot
    var endSlot = null;
    //create fullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl[0], {
    initialView: 'timeGridWeek', // Options: 'timeGridDay', 'timeGridWeek'
    slotDuration: '00:15:00',    // Customize time slot duration (e.g., 30 minutes)
    selectable: true,            // Allow selecting time slots
    allDaySlot: false,           // Hide "All Day" slot if not needed
    //events: function(fetchInfo, successCallback, failureCallback) { 
        //gets correct activityId
    //    console.log('Activity ID inside events:', activity_id); 
        //Gets correct AJAX URL
    //    console.log('AJAX request about to be sent');
    //    console.log("AJAX URL:", my_ajax_object.ajax_url);
    //    jQuery.ajax({
          //get ajax url  
    //        url:my_ajax_object.ajax_url,
    //        type: 'POST',
    //        data: {
            //POST action get_activity_events in get_activity_events.php 
    //            action: 'get_sign_up_events',
            //send activityId
    //            activityId: parseInt(activity_id, 10),
    //            nonce: my_ajax_object.get_events_nonce,
    //        },
    //        success: function(response) {
            //get raw response= array of events 
    //            console.log("Raw Response:", response);
                //check if response is an array
    //            if (Array.isArray(response)){
                    //log response 
    //                console.log('Events Data:', response);
    //                successCallback(response);
    //            } else {
                    //if not array, log error 
     //               console.error("Invalid response format. Expected array, got:", typeof response);
                    
     //           }
     //       },
     //       //if AJAX request fails, log error
     //       error: function(xhr, status, error) {
     //           console.error("AJAX Error:", error);
     //           console.error("XHR:", xhr);
     //           console.error("Status:", status);
     //           failureCallback(error);
     //       }
     //   });
    //},
    //show options for the calendar to show today, next, previous and day, week, month
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridDay,timeGridWeek,dayGridMonth'
    },
});
//render calendar
calendar.render();
});