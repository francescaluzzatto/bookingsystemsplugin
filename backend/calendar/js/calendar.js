   jQuery(document).ready(function() {
        console.log("jQuery is working");
    var calendarEl = jQuery('#calendar');
    var startSlot = null; // Store first clicked time slot
    var endSlot = null;
    var calendar = new FullCalendar.Calendar(calendarEl[0], {
        initialView: 'timeGridWeek', // Options: 'timeGridDay', 'timeGridWeek'
        slotDuration: '00:15:00',    // Customize time slot duration (e.g., 30 minutes)
        selectable: true,            // Allow selecting time slots
        allDaySlot: false,           // Hide "All Day" slot if not needed
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth'
        },
        select: function(info) {
            // check if first time slot is set. If it is not set, then sets it. 
            if (!startSlot) {
                //get startSlot
                startSlot = info.start; 
                //convert to string with 2 digits for hours and 2 digits for minutes. displays as --:--.
                var startTime = startSlot.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                //get value of input= startTime
                jQuery("#startTime").val(startTime);
                //the code runs again on second click. if second click has startSlot already set, then it goes to else, and sets the second time slot as endSlot
            } else {
                //get endSlot
                endSlot = info.end; // Store the second time slot clicked
                
                var endTime = endSlot.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
                // get value of input= endTime
                
                jQuery("#endTime").val(endTime);
                //show the popup
                jQuery("#popup").show();
                
                jQuery("#saveTimeslot").on("click", function(){
                    
                            //get activity name
                            var activity_name = jQuery('#activity_name').val();
                            //get start time and end time of timeslot
                            var start_time = jQuery("#startTime").val();
                            var end_time = jQuery("#endTime").val();
                            // When the Save button is clicked in the popup
                            //initialize empty array 
                            var selectedDays = [];
                            // get the value of the checked boxed input, and push it to selectedDays
                            jQuery("input[name='repeat_days']:checked").each(function() {
                                selectedDays.push(jQuery(this).val()); 
                            });
                            //get repeat until value 
                            var repeatUntil = new Date(jQuery("#repeat_until").val()); 
                            //initialize empty object 
                            var occurrences = {};
                            var formattedTimeSlot = start_time + " - " + end_time;
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
                            if(!occurrences[formattedTimeSlot]){
                                occurrences[formattedTimeSlot] = {};
                            }
                            // Store the time slot and occurrences in an object
                            occurrences[formattedTimeSlot]['dates'] = occurrenceDates;
                            var capacity = jQuery("#capacity").val();
                            occurrences[formattedTimeSlot]['capacity'] = capacity;
    
                            // Send the data to WordPress via AJAX
                            jQuery.ajax({
                                url: my_ajax_object.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'save_timeslot_data',
                                    timeslot_data: occurrences,
                                    week_days: selectedDays,
                                    repeat_until: repeatUntil,
                                    activity_name: activity_name,
                                    nonce: my_ajax_object.save_calendar_data_nonce,
                                },
                                success: function(data) {
                                    alert("Data saved correctly!");
                                    setTimeout(function() {
                                        calendar.refetchEvents();
                                    }, 500);
                                },
                                error: function(data) {
                                    alert("Data not saved. Server error occurred.");
                                }
                            });
    
                            // Close the modal and reset the slots
                            jQuery("#popup").hide();
                            startSlot = null;
                            endSlot = null;
                        });
                        
                    }
            }
        });
        calendar.render();
    });
