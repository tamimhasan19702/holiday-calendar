<?php
/*
Plugin Name: Holiday Viewer
Description: Display holidays based on today's date using a custom post type.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register the custom post type.
function hv_register_holiday_post_type()
{
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
function hv_add_holiday_meta_box()
{
    add_meta_box('holiday_date', 'Holiday Date', 'hv_holiday_date_meta_box_callback', 'holiday', 'side');
}
add_action('add_meta_boxes', 'hv_add_holiday_meta_box');

function hv_holiday_date_meta_box_callback($post)
{
    wp_nonce_field('hv_save_holiday_date', 'hv_holiday_date_nonce');
    $value = get_post_meta($post->ID, '_holiday_date', true);
    echo '<label for="holiday_date">Date:</label>';
    echo '<input type="datetime-local" id="holiday_date" name="holiday_date" value="' . esc_attr($value) . '" />';
}

// Save the holiday date meta data.
function hv_save_holiday_date($post_id)
{
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
        // Convert to UTC before saving
        $date = new DateTime($_POST['holiday_date'], new DateTimeZone('UTC'));
        update_post_meta($post_id, '_holiday_date', $date->format('Y-m-d H:i:s'));
    }
}
add_action('save_post', 'hv_save_holiday_date');

// Create the shortcode to display holidays.
function hv_display_holiday_shortcode()
{
    $today = gmdate('Y-m-d H:i:s'); // Get current UTC date and time
    $today_date = gmdate('Y-m-d'); // Get current UTC date (without time)

    // Query for today's holiday
    $today_args = array(
        'post_type' => 'holiday',
        'meta_query' => array(
            array(
                'key' => '_holiday_date',
                'value' => $today_date,
                'compare' => 'LIKE',
                'type' => 'DATE'
            ),
        ),
    );

    // Query for previous holidays
    $previous_args = array(
        'post_type' => 'holiday',
        'meta_query' => array(
            array(
                'key' => '_holiday_date',
                'value' => $today_date,
                'compare' => '<',
                'type' => 'DATE'
            ),
        ),
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'posts_per_page' => 3 // Limit to 3 previous holidays
    );

    // Query for upcoming holidays
    $upcoming_args = array(
        'post_type' => 'holiday',
        'meta_query' => array(
            array(
                'key' => '_holiday_date',
                'value' => $today_date,
                'compare' => '>',
                'type' => 'DATE'
            ),
        ),
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'posts_per_page' => 3 // Limit to 3 upcoming holidays
    );

    // Execute queries
    $today_query = new WP_Query($today_args);
    $previous_query = new WP_Query($previous_args);
    $upcoming_query = new WP_Query($upcoming_args);

    $output = '<div class="holiday-viewer">';

    // Today's Holiday
    if ($today_query->have_posts()) {
        $output .= '<div class="holiday-today">';
        while ($today_query->have_posts()) {
            $today_query->the_post();
            $date = get_post_meta(get_the_ID(), '_holiday_date', true);
            $image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'holiday-image'));
            $description = get_the_content();

            $output .= '<div class="holiday-card">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<p>Today – ' . date('j F Y', strtotime($date)) . ' – is ' . get_the_title() . '.</p>';
            $output .= $image;
            $output .= '<p>' . $description . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';
    } else {
        $output .= '<p>Today – ' . date('j F Y', strtotime($today)) . ' – is not a holiday.</p>';
    }

    // Previous Holidays
    if ($previous_query->have_posts()) {
        $output .= '<div class="holiday-previous">';
        $output .= '<h3>Previous Holidays</h3>';
        while ($previous_query->have_posts()) {
            $previous_query->the_post();
            $date = get_post_meta(get_the_ID(), '_holiday_date', true);

            $output .= '<div class="holiday-card">';
            $output .= '<h4>' . get_the_title() . '</h4>';
            $output .= '<p>' . date('j F Y', strtotime($date)) . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';
    }

    // Upcoming Holidays
    if ($upcoming_query->have_posts()) {
        $output .= '<div class="holiday-upcoming">';
        $output .= '<h3>Upcoming Holidays</h3>';
        while ($upcoming_query->have_posts()) {
            $upcoming_query->the_post();
            $date = get_post_meta(get_the_ID(), '_holiday_date', true);

            $output .= '<div class="holiday-card">';
            $output .= '<h4>' . get_the_title() . '</h4>';
            $output .= '<p>' . date('j F Y', strtotime($date)) . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';
    }

    $output .= '</div>'; // Close holiday-viewer div

    wp_reset_postdata();
    return $output;
}

// Add a shortcode for displaying holidays.
add_shortcode('holiday_viewer', 'hv_display_holiday_shortcode');

// Enqueue styles for the plugin.
function hv_enqueue_styles()
{
    wp_enqueue_style('hv-styles', plugin_dir_url(__FILE__) . 'styles.css');
}
add_action('wp_enqueue_scripts', 'hv_enqueue_styles');

// Add a meta box to display the shortcode in the post edit screen.
function hv_add_shortcode_meta_box()
{
    add_meta_box('holiday_shortcode', 'Holiday Shortcode', 'hv_shortcode_meta_box_callback', 'holiday', 'normal', 'high');
}
add_action('add_meta_boxes', 'hv_add_shortcode_meta_box');

function hv_shortcode_meta_box_callback($post)
{
    $shortcode = '[holiday_viewer]';
    echo '<p>Use the following shortcode to display this holiday on the front end:</p>';
    echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly style="width: 100%;"/>';
}

// Add the shortcode display to the wpbody-content alongside class .wp-heading-inline
function hv_add_shortcode_to_wpbody_content()
{
    global $post;
    if ($post->post_type === 'holiday') {
        echo '<div class="wp-heading-inline" style="margin-top: 20px;">';
        echo '<strong>Shortcode:</strong> [holiday_viewer]';
        echo '</div>';
    }
}
add_action('edit_form_after_title', 'hv_add_shortcode_to_wpbody_content');