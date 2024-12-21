<?php
/**
 * Plugin Name:       Holiday Calendar
 * Description:      A WordPress plugin for managing holidays.
 * Version:           1.0.0
 * Author:            Tareq Monower
 * Author URI:        https://profiles.wordpress.org/tamimh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Set the timezone to UTC+06:00
date_default_timezone_set('Asia/Dhaka');

// Register the custom post type.
function hv_register_holiday_post_type() {
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
add_action('init', 'hv_register_holiday_post_type');

// Add a meta box for the holiday date.
function hv_add_holiday_meta_box() {
    add_meta_box('holiday_date', 'Holiday Date', 'hv_holiday_date_meta_box_callback', 'holiday', 'side');
}
add_action('add_meta_boxes', 'hv_add_holiday_meta_box');

function hv_holiday_date_meta_box_callback($post) {
    wp_nonce_field('hv_save_holiday_date', 'hv_holiday_date_nonce');
    $value = get_post_meta($post->ID, '_holiday_date', true);
    echo '<label for="holiday_date">Date:</label>';
    echo '<input type="date" id="holiday_date" name="holiday_date" value="' . esc_attr($value) . '" />';
}

// Save the holiday date meta data.
function hv_save_holiday_date($post_id) {
    if (!isset($_POST['hv_holiday_date_nonce']) || !wp_verify_nonce($_POST['hv_holiday_date_nonce'], 'hv_save_holiday_date')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['holiday_date'])) {
        $date = new DateTime($_POST['holiday_date'], new DateTimeZone('UTC'));
        update_post_meta($post_id, '_holiday_date', $date->format('Y-m-d'));
    }
}
add_action('save_post', 'hv_save_holiday_date');

// Include the shortcode view content from a separate file.
function hv_display_holiday_shortcode() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'assets/calender-view.php'; // Include the calendar view file
    return ob_get_clean();
}
add_shortcode('holiday_viewer', 'hv_display_holiday_shortcode');

// Enqueue styles for the plugin.
function hv_enqueue_styles() {
    wp_enqueue_style('hv-styles', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'hv_enqueue_styles');

// Display the shortcode on the admin edit page for the holiday post type.
function hv_display_shortcode_on_admin_edit() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'holiday') {
        $shortcode = '[holiday_viewer]';
        echo '<div class="notice notice-info is-dismissible" style="margin: 20px 0;">';
        echo '<p><strong>Shortcode:</strong> <code>' . esc_html($shortcode) . '</code></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'hv_display_shortcode_on_admin_edit');

// Add a meta box to display the shortcode in the post edit screen.
function hv_add_shortcode_meta_box() {
    add_meta_box('holiday_shortcode', 'Holiday Shortcode', 'hv_shortcode_meta_box_callback', 'holiday', 'normal', 'high');
}
add_action('add_meta_boxes', 'hv_add_shortcode_meta_box');

function hv_shortcode_meta_box_callback($post) {
    $shortcode = '[holiday_viewer]';
    echo '<p>Use the following shortcode to display this holiday on the front end:</p>';
    echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly style="width: 100%;"/>';
}

// Add the shortcode display to the wpbody-content alongside class .wp-heading-inline
function hv_add_shortcode_to_wpbody_content() {
    global $post;
    if ($post->post_type === 'holiday') {
 echo '<div class="wp-heading-inline" style="margin-top: 20px;">';
        echo '<strong>Short code:</strong> [holiday_viewer]';
        echo '</div>';
    }
}
add_action('edit_form_after_title', 'hv_add_shortcode_to_wpbody_content');

// Fetch today's date
$today = gmdate('Y-m-d H:i:s');
$today_date = gmdate('Y-m-d');

// Query for today's holiday
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