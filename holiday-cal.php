<?php
/**
 * Plugin Name:       Holiday Calendar
 * Description:      A WordPress plugin to manage holidays with titles, descriptions, dates, and links, displaying past, present, and future holidays.
 * Version:           1.0.0
 * Author:            Tareq Monower
 * Author URI:        https://profiles.wordpress.org/tamimh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


if(!defined('WPINC')) {
    die;
}

define('HOLIDAY_CAL_VERSION', '1.0.0');



function holiday_cal_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-holiday-cal-activator.php';

    Holiday_Cal_Activator::activate();
}



function holiday_cal_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-holiday-cal-deactivator.php';

    Holiday_Cal_Deactivator::deactivate();
}


register_activation_hook(__FILE__, 'holiday_cal_activate');
register_deactivation_hook(__FILE__, 'holiday_cal_deactivate');


require_once plugin_dir_path(__FILE__) . 'includes/class-holiday-cal-main.php';



function hv_enqueue_styles() {
    wp_enqueue_style('hv-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css'));
}

add_action('wp_enqueue_scripts', 'hv_enqueue_styles');



function holiday_cal() {
    $plugin = new Holiday_Cal_Main();

    $plugin->run();
}

holiday_cal();