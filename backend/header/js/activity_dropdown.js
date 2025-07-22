jQuery(document).ready(function() {
    jQuery.ajax({
        //call admin ajax
        url: my_ajax_object.ajax_url,
        // GET the data
        type: 'GET',
        data: {
            //Call the action get_existing_activities
            action: 'get_existing_activities'
        },
        success: function (response){
            if (response.success){
                //get dropdown menu
                let dropdown = jQuery('#activity_dropdown');
                console.log(data);
                //iterate through each activity 
                response.data.forEach(function(activity) {
                    //add the option for each activity
                    dropdown.append('<option value="' + activity + '">' + activity + '</option>');
                });
            } else {
                console.error("Error fetching activities");
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });


});