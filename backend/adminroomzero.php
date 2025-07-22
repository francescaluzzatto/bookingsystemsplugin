
<?php //ADD SEASONAL BOOKING 
class RoomZeroAdmin {
    public function __construct(){
        add_action('admin_menu', [$this,'season_booking_plugin_menu']);
    }

    //add plug in menu 'Seasonal Booking'
public function season_booking_plugin_menu() {
    add_menu_page(
        'Season Booking',
        'Season Booking',
        'manage_options',
        'season-booking-plugin',
        [$this, 'season_booking_plugin_admin_page'],
        'dashicons-calendar-alt'
    );
    add_submenu_page(
        'season-booking-plugin',
        'All Activities',
        'All Activities',
        'manage_options',
        'all-activities',
        [$this,'all_activites_submenu_page']
    );
    add_menu_page(
        'Modify Activity', 
        'Modify Activity',
        'manage_options',
        'modify-activity',
        [$this, 'get_activity_details']
    );
}
    public function season_booking_plugin_admin_page(){
        $seasons_page= new ManageSeasons();
        $seasons_page->season_booking_plugin_admin_page();
        //season_booking_plugin_admin_page();
    }

    public function all_activites_submenu_page(){
        $all_activities= new AllActivities();
        $all_activities->all_activites_submenu_page();
        //all_activites_submenu_page();
    }

    public function get_activity_details(){
        $get_activity= new GetActivity();
        $get_activity->get_activity_details();
        //get_activity_details();
    }
}
new RoomZeroAdmin();

class AddScripts {
    public function __construct(){
        add_action('admin_enqueue_scripts', [$this, 'enqueue_fullcalendar_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'my_custom_form_styles']);
        add_action('admin_menu', [$this, 'hide_modify_activity_menu'], 999);
    }

    public function enqueue_fullcalendar_scripts($hook) {
        // Ensure it loads only on your specific admin page
        if ($hook !== 'toplevel_page_season-booking-plugin') {
            return;
        }
        wp_enqueue_script('jquery');
        $plugin_url = plugins_url('ROOMZERO');
        if (!wp_script_is('fullcalendar', 'enqueued')) {
            wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', array('jquery'), null, true);
        }//wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', array('jquery'), null, true);
        wp_enqueue_script('custom-calendar', $plugin_url.'/backend/calendar/js/calendar.js', array('jquery', 'fullcalendar'), null, true);
        //wp_enqueue_script('custom-calendar', $plugin_url.'/backend/all_activities/modify_activity/js/activity_calendar.js', array('jquery', 'fullcalendar'), null, true);
        wp_localize_script('custom-calendar', 'my_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'save_calendar_data_nonce' => wp_create_nonce('save_calendar_data')
        ]);
    }
    //add styles
    public function my_custom_form_styles() {
        wp_enqueue_style('custom-form-styles', plugin_dir_url(__FILE__) . '../css/styles.css');
    }

    public function hide_modify_activity_menu() {
        remove_menu_page('modify-activity');
    }
}
new AddScripts();
?>
<script>
    </script>
