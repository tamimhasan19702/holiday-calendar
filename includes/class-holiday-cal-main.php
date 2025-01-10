<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Holiday_Cal_Main
{

    public function run()
    {
        add_action('init', array($this, 'hv_register_holiday_post_type'));
        add_action('add_meta_boxes', array($this, 'hv_add_holiday_meta_box'));
        add_action('save_post', array($this, 'hv_save_holiday_data'));
        add_shortcode('holiday_viewer', array($this, 'hv_display_holiday_shortcode'));
        add_action('admin_notices', array($this, 'hv_display_shortcode_on_admin_edit'));


    }

    // Register the custom post type with menu icon.
    public function hv_register_holiday_post_type()
    {
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
    public function hv_add_holiday_meta_box()
    {
        add_meta_box('holiday_details', 'Holiday Details', array($this, 'hv_holiday_details_meta_box_callback'), 'holiday', 'normal', 'high');
    }

    // Callback for holiday details meta box.
    public function hv_holiday_details_meta_box_callback($post)
    {
        wp_nonce_field('hv_save_holiday_data', 'hv_holiday_data_nonce');

        echo '<style>
    #holiday_details label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    #holiday_details input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    #holiday_details .date-field {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    #holiday_details .button {
        margin-top: 10px;
        padding: 10px 15px;
        background-color: #0073aa;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    #holiday_details .button.remove {
        background-color: #dc3232;
    }
    #holiday_details .button:hover {
        background-color: #005177;
    }
    #holiday_details .button.remove:hover {
        background-color: #a00;
    }
    #holiday_dates_container .date-field {
        margin-bottom: 20px;
    }
    #holiday_dates_container {
        margin-bottom: 20px;
    }
    .no-holidays {
        color: #dc3232;
        font-weight: bold;
    }
</style>';

        // Retrieve existing holiday dates
        $holiday_dates = get_post_meta($post->ID, '_holiday_dates', true);
        $holiday_dates = !empty($holiday_dates) ? (array) $holiday_dates : [];

        // Display existing holiday dates
        echo '<div id="holiday_dates_container">';
        foreach ($holiday_dates as $index => $date) {
            echo '<div class="date-field">';
            echo '<label for="holiday_date_' . esc_attr($index) . '">Holiday Date:</label>';
            echo '<input type="date" id="holiday_date_' . esc_attr($index) . '" name="holiday_dates[]" value="' . esc_attr($date) . '" />';
            echo '<button type="button" class="button remove" style="margin-left: 10px;" onclick="removeDateField(this)">Remove</button>';
            echo '</div>';
        }
        echo '</div>';

        // Add New Date Button
        echo '<div class="date-field" style="display: flex; align-items: center; margin-bottom: 20px;">';
        echo '<button type="button" class="button" id="add_date_button" onclick="addDateField()">Add New Date</button>';
        echo '</div>';

        // JavaScript for adding/removing date fields
        echo '<script>
            let dateFieldIndex = ' . count($holiday_dates) . ';
            
            function addDateField() {
                const container = document.getElementById("holiday_dates_container");
                const newField = document.createElement("div");
                newField.className = "date-field";
                newField.style.marginBottom = "20px";
                newField.innerHTML = \'<label for="holiday_date_\' + dateFieldIndex + \'">Holiday Date:</label>\' +
                    \'<input type="date" id="holiday_date_\' + dateFieldIndex + \'" name="holiday_dates[]" />\' +
                    \'<button type="button" class="button remove" style="margin-left: 10px;" onclick="removeDateField(this)">Remove</button>\';
                container.appendChild(newField);
                dateFieldIndex++;
            }
    
            function removeDateField(button) {
                const field = button.parentElement;
                field.parentElement.removeChild(field);
            }
        </script>';

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
    public function hv_save_holiday_data($post_id)
    {
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
    public function hv_display_holiday_shortcode()
    {
        $plugin_main_dir = dirname(dirname(__FILE__));
        ob_start();
        include_once $plugin_main_dir . '/assets/calender-view.php'; // Include the calendar view file
        return ob_get_clean();
    }

    // Enqueue styles for the plugin.


    // Display the shortcode on the admin edit page for the holiday post type.
    public function hv_display_shortcode_on_admin_edit()
    {
        // Check if the user is currently viewing the admin edit page for the holiday post type
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'holiday') {
            // Create a nonce for verification
            $nonce = wp_create_nonce('holiday_shortcode_nonce');

            // Prepare the shortcode
            $shortcode = '[holiday_viewer]';
            echo '<div class="notice notice-info is-dismissible" style="margin: 20px 0;">';
            echo '<p><strong>Use the following shortcode to view the holiday calendar:</strong> <code>' . esc_html($shortcode) . '</code></p>';
            echo '<p><button class="button holiday-shortcode-button" data-nonce="' . esc_attr($nonce) . '" onclick="copyToClipboard(\'' . esc_js($shortcode) . '\')">Copy Shortcode</button></p>';
            echo '</div>';
            echo '<script>
                function copyToClipboard(text) {
                    var tempInput = document.createElement("input");
                    tempInput.style.position = "absolute";
                    tempInput.style.left = "-9999px";
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand("copy");
                    document.body.removeChild(tempInput);
                    alert("Shortcode copied to clipboard");
                }
            </script>';
        }
    }


    // Add the shortcode display to the wpbody-content alongside class .wp-heading-inline

    // Get today's date
    public function get_today_date()
    {
        return gmdate('Y-m-d');
    }

    // Fetch today's holiday using caching for performance optimization
    public function get_today_holiday()
    {
        $today_date = $this->get_today_date();
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