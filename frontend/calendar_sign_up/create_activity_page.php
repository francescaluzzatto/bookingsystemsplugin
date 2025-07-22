<?php 
class CreateActivityPage{
    function create_activity_details_page(){
        $page= get_page_by_path('activity-details');
        if($page){
            // If the page already exists, do nothing
            return $page->ID;
        } else {
            // Create a new page
            $new_page = array(
                'post_title' => 'Activity Details',
                'post_content' => '[view_activity]',
                'post_status' => 'publish',
                'post_type' => 'page',
            );
            // Insert the post into the database
            $page_id= wp_insert_post($new_page);
            return $page_id;
        }
    }
}
new CreateActivityPage();