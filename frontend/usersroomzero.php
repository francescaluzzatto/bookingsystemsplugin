<?php 

class FrontendAddScripts{
    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'frontend_roomzero_enqueue_fullcalendar']);
        add_action('wp_enqueue_scripts', [$this, 'my_custom_form_styles']);
    }
//add styles 
public function my_custom_form_styles() {
    wp_enqueue_style('custom-form-styles', plugin_dir_url(__FILE__) . '../css/styles.css');
}
//add_action('wp_enqueue_scripts', 'my_custom_form_styles');

//add fullCalendar scripts 
public function frontend_roomzero_enqueue_fullcalendar() {

        // Enqueue FullCalendar JS from unpkg
        //wp_enqueue_script('rrule', 'https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js', array(), null, true);
        wp_enqueue_script('jquery');
        $plugin_url = plugins_url('ROOMZERO');
        if (!wp_script_is('fullcalendar', 'enqueued')) {
            wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', array('jquery'), null, true);
        }
        wp_enqueue_script('calendar-sign-up', $plugin_url.'/frontend/calendar_sign_up/js/sign_up_calendar.js', array('jquery', 'fullcalendar'), null, true);
        wp_localize_script('calendar-sign-up', 'my_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'calendar_sign_up_nonce' => wp_create_nonce('calendar_sign_up')
        ]);

        //wp_enqueue_script('fullcalendar-script', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', array(), null, true);
        
        //wp_enqueue_script('fullcalendar-rrule', 'https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.15/index.global.min.js', array(), null, true);
}
//add_action('wp_enqueue_scripts', 'frontend_roomzero_enqueue_fullcalendar');

}
new FrontendAddScripts();



