<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Holiday_Cal_Main {

    public function run() {
        add_action('init', array($this, 'hv_register_holiday_post_type'));
        add_action('add_meta_boxes', array($this, 'hv_add_holiday_meta_box'));
        add_action('save_post', array($this, 'hv_save_holiday_data'));
        add_shortcode('holiday_viewer', array($this, 'hv_display_holiday_shortcode'));
       
        add_action('admin_notices', array($this, 'hv_display_shortcode_on_admin_edit'));

       
    }

    // Register the custom post type with menu icon.
    public function hv_register_holiday_post_type() {
        register_post_type('holiday', array(
            'labels' => array(
                'name' => __('Holidays', 'holiday-cal'),
                'singular_name' => __('Holiday', 'holiday-cal'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-calendar', // Add calendar icon in the menu
        ));
    }


    // Add meta box for holiday details.
    public function hv_add_holiday_meta_box() {
        add_meta_box('holiday_details', 'Holiday Details', array($this, 'hv_holiday_details_meta_box_callback'), 'holiday', 'normal', 'high');
    }

    // Callback for holiday details meta box.
    public function hv_holiday_details_meta_box_callback($post) {
        wp_nonce_field('hv_save_holiday_data', 'hv_holiday_data_nonce');

        // Style the meta box.
        echo '<style>
            #holiday_details label {
                display: block;
                margin-bottom: 10px;
            }
            #holiday_details input {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
            }
            #holiday_details br {
                clear: both;
            }
        </style>';

        // Holiday Date
        $holiday_date = get_post_meta($post->ID, '_holiday_date', true);
        echo '<label for="holiday_date">Holiday Date:</label>';
        echo '<input type="date" id="holiday_date" name="holiday_date" value="' . esc_attr($holiday_date) . '" />';

        // Page Link
        $page_link = get_post_meta($post->ID, '_holiday_page_link', true);
        echo '<label for="holiday_page_link">Button Link:</label>';
        echo '<input type="url" id="holiday_page_link" name="holiday_page_link" value="' . esc_attr($page_link) . '" />';

        // Button Text
        $button_text = get_post_meta($post->ID, '_holiday_button_text', true);
        echo '<label for="holiday_button_text">Button Text:</label>';
        echo '<input type="text" id="holiday_button_text" name="holiday_button_text" value="' . esc_attr($button_text) . '" />';

        // Custom Class
        $custom_class = get_post_meta($post->ID, '_holiday_custom_class', true);
        echo '<label for="holiday_custom_class">Custom Button Class:</label>';
        echo '<input type="text" id="holiday_custom_class" name="holiday_custom_class" value="' . esc_attr($custom_class) . '" />';
    }

    // Save the holiday data.
    public function hv_save_holiday_data($post_id) {
        // Check nonce and user permissions.
        if (!isset($_POST['hv_holiday_data_nonce']) || !wp_verify_nonce(wp_unslash(sanitize_key($_POST['hv_holiday_data_nonce'])), 'hv_save_holiday_data')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        if (get_post_type($post_id) !== 'holiday' || !current_user_can('edit_post', $post_id)) {
            return;
        }
    
        // Sanitize and save the holiday date.
        if (isset($_POST['holiday_date'])) {
            $date = sanitize_text_field(wp_unslash($_POST['holiday_date']));
            $dateTime = new DateTime($date, new DateTimeZone('UTC'));
            update_post_meta($post_id, '_holiday_date', $dateTime->format('Y-m-d'));
        }
    
        // Save the page link.
        if (isset($_POST['holiday_page_link'])) {
            $page_link = esc_url_raw(wp_unslash($_POST['holiday_page_link']));
            update_post_meta($post_id, '_holiday_page_link', $page_link);
        }
    
        // Save the button text.
        if (isset($_POST['holiday_button_text'])) {
            $button_text = sanitize_text_field(wp_unslash($_POST['holiday_button_text']));
            update_post_meta($post_id, '_holiday_button_text', $button_text);
        }
    
        // Save the custom class.
        if (isset($_POST['holiday_custom_class'])) {
            $custom_class = sanitize_text_field(wp_unslash($_POST['holiday_custom_class']));
            update_post_meta($post_id, '_holiday_custom_class', $custom_class);
        }
    }

    // Include the shortcode view content from a separate file.
    public function hv_display_holiday_shortcode() {
        $plugin_main_dir = dirname(dirname(__FILE__));
        ob_start();
        include_once $plugin_main_dir . '/assets/calender-view.php'; // Include the calendar view file
        return ob_get_clean();
    }

    // Enqueue styles for the plugin.
    public function hv_enqueue_styles() {
        wp_enqueue_style('hv-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), HOLIDAY_CAL_VERSION);
    }

    // Display the shortcode on the admin edit page for the holiday post type.
    public function hv_display_shortcode_on_admin_edit() {
        // Check if the required parameters are set
        if (isset($_REQUEST['post_type'], $_REQUEST['_wpnonce'])) {
            // Unsplash the nonce and post type
            $nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
            $post_type = isset($_REQUEST['post_type']) ? sanitize_text_field(wp_unslash($_REQUEST['post_type'])) : '';
    
            // Verify the nonce
            if (!empty($nonce) && wp_verify_nonce($nonce, 'edit-post')) {
                // Sanitize the post type after verifying the nonce
                $post_type = sanitize_text_field($post_type);
    
                // Check if the post type is 'holiday'
                if ($post_type === 'holiday') {
                    $shortcode = '[holiday_viewer]';
                    echo '<div class="notice notice-info is-dismissible" style="margin: 20px 0;">';
                    echo '<p><strong>Use the following shortcode to view the holiday calendar:</strong> <code>' . esc_html($shortcode) . '</code></p>';
                    echo '</div>';
                }
            }
        }
    }
    

    // Add the shortcode display to the wpbody-content alongside class .wp-heading-inline

    // Get today's date
    public function get_today_date() {
        return gmdate('Y-m-d');
    }

    // Fetch today's holiday using caching for performance optimization
    public function get_today_holiday() {
        $today_date = $this->get_today_date();
        $cache_key = 'today_holiday_' . $today_date;
        $holidays = wp_cache_get($cache_key, 'holidays');

        if ($holidays === false) {
            $query = new WP_Query(array(
                'post_type' => 'holiday',
                'meta_query' => array(
                    array(
                        'key' => '_holiday_date',
                        'value' => $today_date,
                        'compare' => '=',
                        'type' => 'DATE',
                    ),
                ),
            ));
            $holidays = $query->posts;
            wp_cache_set($cache_key, $holidays, 'holidays', HOUR_IN_SECONDS);
        }

        return $holidays;
    }

  


}