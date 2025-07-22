
// show fullcalendar
//document.addEventListener('DOMContentLoaded', function() {
//    var calendarEl = document.getElementById('calendar');
//    var calendar = new FullCalendar.Calendar(calendarEl, {
//        initialView: 'dayGridMonth',  // Set initial view (Month view)
//        events: [ 
            
//            {
//            title: 'Meeting',
//            start: '2024-12-20',            
//            end: '2024-12-22',              
//          }
//        ]  
//    });
//    calendar.render();
//});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('fetch-data-btn').addEventListener('click', function() {
        console.log('Ajax URL:', ajaxData.ajaxurl);
        // Perform AJAX request
        jQuery.ajax({
            url: ajaxData.ajaxurl, 
            type: 'GET',
            data: {
                //get data by action 
                action: 'custom_fetch_data',
                //Set nonce security 
                //security: ajaxData.nonce,
            },
            success: function(response) {
                console.log('AJAX Success', response);
                //create a list within data-list to display all the rows in the data 
                const dataList = document.getElementById('data-list');
                dataList.innerHTML = ''; 
                const data = JSON.parse(response);
                data.forEach(item => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${item.excursion_name}: ${item.start_date} - ${item.end_date}`;
                    dataList.appendChild(listItem);
                });
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});