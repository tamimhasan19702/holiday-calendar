<?php

class HCPT__Main
{
    public function run()
    {
        add_action('init', array($this, 'hcpt__register_holiday_post_type'));
        add_action('add_meta_boxes', array($this, 'hcpt__add_holiday_meta_box'));
        add_action('save_post', array($this, 'hcpt__save_holiday_data'));
        add_shortcode('holiday_viewer', array($this, 'hcpt__display_holiday_shortcode'));
        add_action('admin_notices', array($this, 'hcpt__display_shortcode_on_admin_edit'));
    }

    // Register the custom post type with menu icon.
    public function hcpt__register_holiday_post_type()
    {
        register_post_type('holiday', array(
            'labels' => array(
                'name' => __('Holidays', 'holiday-calendar'),
                'singular_name' => __('Holiday', 'holiday-calendar'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-calendar', // Add calendar icon in the menu
        ));
    }

    // Add meta box for holiday details.
    public function hcpt__add_holiday_meta_box()
    {
        add_meta_box('holiday_details', 'Holiday Details', array($this, 'hcpt__holiday_details_meta_box_callback'), 'holiday', 'normal', 'high');
    }

    // Callback for holiday details meta box.
    public function hcpt__holiday_details_meta_box_callback($post)
    {
        wp_nonce_field('hcpt__save_holiday_data', 'hcpt__holiday_data_nonce');

        // Retrieve existing holiday dates
        $holiday_dates = get_post_meta($post->ID, '_holiday_dates', true);
        $holiday_dates = !empty($holiday_dates) ? (array) $holiday_dates : [];

        // Start output buffering
        ob_start();
        ?>
        <div id="holiday_dates_container">
            <?php foreach ($holiday_dates as $index => $date): ?>
                <div class="date-field">
                    <label for="holiday_date_<?php echo esc_attr($index); ?>">Holiday Date:</label>
                    <input type="date" id="holiday_date_<?php echo esc_attr($index); ?>" name="holiday_dates[]"
                        value="<?php echo esc_attr($date); ?>" />
                    <button type="button" class="button remove" style="margin-left: 10px;"
                        onclick="removeDateField(this)">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="date-field" style="display: flex; align-items: center; margin-bottom: 20px;">
            <button type="button" class="button" id="add_date_button" onclick="addDateField()">Add New Date</button>
        </div>
        <label for="holiday_page_link">Button Link:</label>
        <input type="url" id="holiday_page_link" name="holiday_page_link"
            value="<?php echo esc_attr(get_post_meta($post->ID, '_holiday_page_link', true)); ?>" />
        <label for="holiday_button_text">Button Text:</label>
        <input type="text" id="holiday_button_text" name="holiday_button_text"
            value="<?php echo esc_attr(get_post_meta($post->ID, '_holiday_button_text', true)); ?>" />
        <label for="holiday_custom_class">Custom Button Class:</label>
        <input type="text" id="holiday_custom_class" name="holiday_custom_class"
            value="<?php echo esc_attr(get_post_meta($post->ID, '_holiday_custom_class', true)); ?>" />
        <?php
        // End output buffering and output the content
        echo ob_get_clean();
    }

    // Save the holiday data.
    public function hcpt__save_holiday_data($post_id)
    {
        // Check nonce and user permissions.
        if (!isset($_POST['hcpt__holiday_data_nonce']) || !wp_verify_nonce(wp_unslash(sanitize_key($_POST['hcpt__holiday_data_nonce'])), 'hcpt__save_holiday_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'holiday' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize and save the holiday dates.
        if (isset($_POST['holiday_dates']) && is_array($_POST['holiday_dates'])) {
            $holiday_dates = array_map('sanitize_text_field', wp_unslash($_POST['holiday_dates']));
            // Remove empty values
            $holiday_dates = array_filter($holiday_dates);
            update_post_meta($post_id, '_holiday_dates', $holiday_dates);
        } else {
            // If no dates are provided, delete the meta
            delete_post_meta($post_id, '_holiday_dates');
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
    public function hcpt__display_holiday_shortcode()
    {
        $plugin_main_dir = dirname(dirname(__FILE__));
        ob_start();
        include_once $plugin_main_dir . '/assets/hcpt__calender-view.php'; // Include the calendar view file
        return ob_get_clean();
    }

    // Display the shortcode on the admin edit page for the holiday post type.
    public function hcpt__display_shortcode_on_admin_edit()
    {
        // Check if the user is currently viewing the admin edit page for the holiday post type
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'holiday') {
            // Create a nonce for verification
            $nonce = wp_create_nonce('holiday_shortcode_nonce');

            // Prepare the shortcode
            $shortcode = '[holiday_viewer]';
            ?>
            <div class="notice notice-info is-dismissible" style="margin: 20px 0;">
                <p><strong>Use the following shortcode to view the holiday calendar:</strong>
                    <code><?php echo esc_html($shortcode); ?></code>
                </p>
                <p><button class="button holiday-shortcode-button" data-nonce="<?php echo esc_attr($nonce); ?>"
                        onclick="copyToClipboard('<?php echo esc_js($shortcode); ?>')">Copy Shortcode</button></p>
            </div>
            <?php
        }
    }

    // Get today's date
    public function hcpt__get_today_date()
    {
        return gmdate('Y-m-d');
    }

    // Fetch today's holiday using caching for performance optimization
    public function hcpt__get_today_holiday()
    {
        $today_date = $this->hcpt__get_today_date();
        $cache_key = 'today_holiday_' . $today_date;
        $holidays = wp_cache_get($cache_key, 'holidays');

        if ($holidays === false) {
            $query = new WP_Query(array(
                'post_type' => 'holiday',
                'meta_query' => array(
                    array(
                        'key' => '_holiday_dates', // Change to the key for the repeater
                        'value' => $today_date,
                        'compare' => 'LIKE',
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