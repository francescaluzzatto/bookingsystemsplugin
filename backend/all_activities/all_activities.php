<?php 

class AllActivities{

public function all_activites_submenu_page(){
        //Get table name
        global $wpdb;
        $table_name = 'wp_booking_seasons';
        $user_id= get_current_user_id();
        //get activity name from table
        $query = $wpdb->prepare("SELECT activity_name, id FROM $table_name WHERE user_id = %d", $user_id);
        $results = $wpdb->get_results($query);
        //get site url for dynamic page view        
        $url = get_site_url();
        $plugin_url = $url. '/wp-content/plugins/ROOMZERO';
    //iterate through each entry and show each of them 
        foreach ($results as $row) {
            // check if row is not null and not "", then:
            if($row->activity_name !== null && unserialize($row->activity_name) !== "" ){
            //show Activity with link to modify-activity submenu page and id= id of that specific activity 
            echo "Activity Name:".unserialize($row->activity_name)."<div id='activity_button'><a href='".esc_url(admin_url("admin.php?page=modify-activity&id=".$row->id))."'><button>Modify Activity</button></a></div> <br>";    
        }
        }
}
}


?>