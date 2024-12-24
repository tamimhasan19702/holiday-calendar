<?php
/**
 * Plugin Name:      Custom Location Banner
 * Description:       A simple WordPress plugin that displays a custom location banner and file links on the post page according to the user's location.
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

define('custom_location_banner', '1.0.0');

function custom_location_banner_activate() {
    
    require_once plugin_dir_path(__FILE__) . 'includes/class-custom-location-banner-activator.php';

    Custom_Location_Banner_Activator::activate();
}

function custom_location_banner_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-custom-location-banner-deactivator.php';

    Custom_Location_Banner_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'custom_location_banner_activate');
register_deactivation_hook(__FILE__, 'custom_location_banner_deactivate');

require_once plugin_dir_path(__FILE__) . 'includes/class-custom-location-banner-main.php';


function custom_location_banner() {
    $plugin = new Custom_Location_Banner_Main();

    $plugin->run();
}

custom_location_banner();