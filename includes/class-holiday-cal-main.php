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
        add_action('add_meta_boxes', array($this, 'hv_add_shortcode_meta_box'));
        add_action('edit_form_after_title', array($this, 'hv_add_shortcode_to_wpbody_content'));
    }

    // Register the custom post type.
    public function hv_register_holiday_post_type() {
        register_post_type('holiday', array(
            'labels' => array(
                'name' => __('Holidays'),
                'singular_name' => __('Holiday'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
        ));
    }

    // Add meta box for holiday details.
    public function hv_add_holiday_meta_box() {
        add_meta_box('holiday_details', 'Holiday Details', array($this, 'hv_holiday_details_meta_box_callback'), 'holiday', 'normal', 'high');
    }

    // Callback for holiday details meta box.
    public function hv_holiday_details_meta_box_callback($post) {
        wp_nonce_field('hv_save_holiday_data', 'hv_holiday_data_nonce');

        // Holiday Date
        $holiday_date = get_post_meta($post->ID, '_holiday_date', true);
        echo '<label for="holiday_date">Holiday Date:</label>';
        echo '<input type="date" id="holiday_date" name="holiday_date" value="' . esc_attr($holiday_date) . '" /><br><br>';

        // Page Link
        $page_link = get_post_meta($post->ID, '_holiday_page_link', true);
        echo '<label for="holiday_page_link">Page Link:</label>';
        echo '<input type="url" id="holiday_page_link" name="holiday_page_link" value="' . esc_attr($page_link) . '" /><br><br>';

        // Button Text
        $button_text = get_post_meta($post->ID, '_holiday_button_text', true);
        echo '<label for="holiday_button_text">Button Text:</label>';
        echo '<input type="text" id="holiday_button_text" name="holiday_button_text" value="' . esc_attr($button_text) . '" />';
    }

    // Save the holiday data.
    public function hv_save_holiday_data($post_id) {
        // Check nonce and user permissions.
        if (!isset($_POST['hv_holiday_data_nonce']) || !wp_verify_nonce($_POST['hv_holiday_data_nonce'], 'hv_save_holiday_data')) {
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
            $date = sanitize_text_field($_POST['holiday_date']);
            $dateTime = new DateTime($date, new DateTimeZone('UTC'));
            update_post_meta($post_id, '_holiday_date', $dateTime->format('Y-m-d'));
        }

        // Save the page link.
        if (isset($_POST['holiday_page_link'])) {
            $page_link = sanitize_text_field($_POST['holiday_page_link']);
            update_post_meta($post_id, '_holiday_page_link', $page_link);
        }

        // Save the button text.
        if (isset($_POST['holiday_button_text'])) {
            $button_text = sanitize_text_field($_POST['holiday_button_text']);
            update_post_meta($post_id, '_holiday_button_text', $button_text);
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
        wp_enqueue_style('hv-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    }

    // Display the shortcode on the admin edit page for the holiday post type.
    public function hv_display_shortcode_on_admin_edit() {
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'holiday') {
            $shortcode = '[holiday_viewer]';
            echo '<div class="notice notice-info is-dismissible" style="margin: 20px 0;">';
            echo '<p><strong>Shortcode:</strong> <code>' . esc_html($shortcode) . '</code></p>';
            echo '</div>';
        }
    }

    // Add a meta box to display the shortcode in the post edit screen.
    public function hv_add_shortcode_meta_box() {
        add_meta_box('holiday_shortcode', 'Holiday Shortcode', array($this, 'hv_shortcode_meta_box_callback'), 'holiday', 'normal', 'high');
    }

    // Callback for shortcode meta box.
    public function hv_shortcode_meta_box_callback($post) {
        $shortcode = '[holiday_viewer]';
        echo '<p>Use the following shortcode to display this holiday on the front end:</p>';
        echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly style="width: 100%;"/>';
    }

    // Add the shortcode display to the wpbody-content alongside class .wp-heading-inline
    public function hv_add_shortcode_to_wpbody_content() {
        global $post;
        if ($post->post_type === 'holiday') {
            echo '<div class="wp-heading-inline" style="margin-top: 20px;">';
            echo '<strong>Shortcode:</strong> [holiday_viewer]';
            echo '</div>';
        }
    }

    // Get today's date
    public function get_today_date() {
        return gmdate('Y-m-d');
    }

    // Query for today's holiday
    public function get_today_holiday() {
        $today_date = $this->get_today_date();
        $today_args = array(
            'post_type' => 'holiday',
            'meta_query' => array(
                array(
                    'key' => '_holiday_date',
                    'value' => $today_date,
                    'compare' => '=',
                    'type' => 'DATE'
                ),
            ),
        );
        return new WP_Query($today_args);
    }
}