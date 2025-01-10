<?php
/**
 * Plugin Name:       Holiday Calendar
 * Description: Holiday Calendar is a WordPress plugin that simplifies holiday management by allowing you to add titles, descriptions, multiple dates, and links. It displays holidays in three cards for past, present, and future events, ensuring easy tracking and engagement.
 * Version:           1.0.2
 * Author:            Tareq Monower
 * Author URI:        https://profiles.wordpress.org/tamimh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain:       holiday-calendar
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!defined('WPINC')) {
    die;
}

define('HCPT__VERSION', '1.0.0');

function hcpt__activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__activator.php';
    HCPT__Activator::activate();
}

function hcpt__deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__deactivator.php';
    HCPT__Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'hcpt__activate');
register_deactivation_hook(__FILE__, 'hcpt__deactivate');

require_once plugin_dir_path(__FILE__) . 'includes/class-hcpt__main.php';

function hcpt__enqueue_styles()
{
    wp_enqueue_style('hcpt__styles', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css'));
}

add_action('wp_enqueue_scripts', 'hcpt__enqueue_styles');

function hcpt__enqueue_admin_assets()
{
    wp_enqueue_style('hcpt__admin_styles', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin-style.css'));
    wp_enqueue_script('hcpt__admin_scripts', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/admin-script.js'), true);
}

add_action('admin_enqueue_scripts', 'hcpt__enqueue_admin_assets');

function hcpt__init()
{
    $plugin = new HCPT__Main();
    $plugin->run();
}

hcpt__init();