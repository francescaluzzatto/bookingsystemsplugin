document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('season-calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        editable: false,
        events: ajaxData.ajaxurl + '?action=get_seasons', // Fetch existing seasons

        select: function (info) {
            var timeSlots = prompt("Enter time slots for the selected range (comma-separated, e.g., 9:00 AM, 10:00 AM):");
            if (timeSlots) {
                jQuery.post(ajaxData.ajaxurl, {
                    action: 'add_season',
                    start_date: info.startStr,
                    end_date: info.endStr,
                    time_slots: timeSlots,
                }, function (response) {
                    if (response.success) {
                        alert('Season added successfully!');
                        calendar.refetchEvents();
                    } else {
                        alert('Failed to add season.');
                    }
                });
            }
        },

        eventClick: function (info) {
            if (confirm("Do you want to delete this season?")) {
                jQuery.post(ajaxData.ajaxurl, {
                    action: 'delete_season',
                    id: info.event.id,
                }, function (response) {
                    if (response.success) {
                        alert('Season deleted successfully!');
                        calendar.refetchEvents();
                    } else {
                        alert('Failed to delete season.');
                    }
                });
            }
        },
    });

    calendar.render();
});
