TEST AJAX
<?php
function custom_ajax_display_shortcode() {
    ob_start();
    //Create container for data:
    ?>
    
    <div id="data-container">
        <!--button to fetch data-->
        <button id="fetch-data-btn">Fetch Data</button>
        <!-- data list to display data-->
        <ul id="data-list"></ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_ajax_display', 'custom_ajax_display_shortcode');
// Register the AJAX action
function custom_fetch_data() {
    // Validate the nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'my_nonce_action')) {
        wp_send_json_error('Invalid nonce', 400); 
    }
    //add debugging 
    define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
echo "this is a test function";
    global $wpdb;
    $table_name = 'wp_booking_seasons';
    var_dump("Table name:", $table_name);
    //select all rows from the database 

    $results = $wpdb->get_results("SELECT * FROM $table_name");
    var_dump($results);
    ob_start(); // Start output buffering

    // Prepare data for JSON response
    $data = [];
    foreach ($results as $row) {
        var_dump("row data:", $row);
        $data[] = [
            'excursion_name' => $row->excursion_name,        
            'start_date' => $row->start_date,
            'end_date' => $row->end_date
        ];
    }
    var_dump("prepared data:",$data);
    // Send JSON response
    echo json_encode($data);
    wp_send_json($data);
    die(); 
}
add_action('wp_ajax_custom_fetch_data', 'custom_fetch_data');
add_action('wp_ajax_nopriv_custom_fetch_data', 'custom_fetch_data');

function enqueue_frontend_js_scripts(){
    $url = get_site_url();
    $plugin_url = $url. '/wp-content/plugins/ROOMZERO';
    wp_enqueue_script(
        'roomzero_user_script',
        $plugin_url . '/frontend/testajax/roomzerouser.js',
        array('jquery'),
        null,
        true
    );
    wp_add_inline_script('roomzero-user-script', 'const ajaxData = {"ajaxurl": "' . admin_url('admin-ajax.php') . '", "nonce":"'. wp_create_nonce('my_nonce_action').'"};');
}

add_action('wp_enqueue_scripts', 'enqueue_frontend_js_scripts');