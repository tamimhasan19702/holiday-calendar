<?php
namespace HCPT;

/**
 * Plugin Name:       Holiday Calendar
 * Description: Holiday Calendar is a WordPress plugin that simplifies holiday management by allowing you to add titles, descriptions, multiple dates, and links. It displays holidays in three cards for past, present, and future events, ensuring easy tracking and engagement.
 * Version:           1.0.3
 * Author:            Tareq Monower
 * Author URI:        https://profiles.wordpress.org/tamimh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       holiday-calendar
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!defined('WPINC')) {
    die;
}

define('HCPT__HOLIDAY_CAL_VERSION', '1.0.0');

function hcpt__holiday_cal_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__holiday-cal-activator.php';

    \HCPT\HCPT__Holiday_Cal_Activator::activate();
}

function hcpt__holiday_cal_deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__holiday-cal-deactivator.php';

    \HCPT\HCPT__Holiday_Cal_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'HCPT\hcpt__holiday_cal_activate');
register_deactivation_hook(__FILE__, 'HCPT\hcpt__holiday_cal_deactivate');

require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__holiday-cal-main.php';

function hcpt__enqueue_styles()
{
    wp_enqueue_style('hcpt__styles', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css'));
    wp_enqueue_script('hcpt__scripts', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/script.js'), true);
}

add_action('wp_enqueue_scripts', 'HCPT\hcpt__enqueue_styles');



function hcpt__enqueue_admin_assets()
{
    wp_enqueue_style('hcpt__admin-styles', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin-style.css'));
    wp_enqueue_script('hcpt__admin-scripts', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/admin-script.js'), true);
}
add_action('admin_enqueue_scripts', 'HCPT\hcpt__enqueue_admin_assets');


function hcpt__holiday_cal()
{
    $plugin = new \HCPT\HCPT__Holiday_Cal_Main();

    $plugin->run();
}

hcpt__holiday_cal();