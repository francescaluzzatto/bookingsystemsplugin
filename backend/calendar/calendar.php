<?php 
class CalendarManageSeasons{
    
public function show_calendar(){
    ?>
    <div id="calendar"></div>
    <?php
}
}
new CalendarManageSeasons();

//function enqueue_calendar_js_scripts(){
//    $url = get_site_url();
//    $plugin_url = $url. '/wp-content/plugins/ROOMZERO';
//    wp_enqueue_script(
//        'roomzero_calendar_script',
//        $plugin_url .'/backend/calendar/js/calendar.js',
//        array('jquery'),
//        null,
//        true
//    );    

//}


?>