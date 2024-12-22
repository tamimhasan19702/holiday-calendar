=== Holiday Calendar ===
Contributors: tamimh
Tags: holiday, calendar, wordpress-plugin
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple WordPress plugin that allows you to add holidays with name, description, image, and page link. The plugin will show holidays according to their dates and also show past and upcoming holidays.

== Description ==
Holiday Calendar is a WordPress plugin that allows you to add holidays with name, description, image, and page link. The plugin will show holidays according to their dates and also show past and upcoming holidays. It's a simple and efficient way to keep track of holidays and events.

== Installation ==

1. Upload `holiday-calendar.php` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings as needed.

== Configuration ==

To use the plugin, follow these steps:

1. Go to **Holiday Calendar > Add Holiday** in WordPress.
2. Enter the holiday name, description, image, and page link.
3. Set the start and end dates for the holiday.
4. Click the "Add Holiday" button to save the holiday.

> *Note:* Be careful when adding holidays, as this action is permanent and cannot be undone.

== Frequently Asked Questions ==

= What is the purpose of this plugin? =
This plugin helps you keep track of holidays and events by allowing you to add them with name, description, image, and page link. The plugin will show holidays according to their dates and also show past and upcoming holidays.

= How do I use the plugin? =
To use the plugin, go to **Holiday Calendar > Add Holiday** in WordPress, enter the holiday name, description, image, and page link, set the start and end dates for the holiday, and click the "Add Holiday" button to save the holiday.

== Screenshots ==

1. Screenshot of the add holiday form

== Changelog ==

= 1.0.0 =
* Initial release of the plugin
* Added functionality to add holidays with name, description, image, and page link
* Added functionality to show holidays according to their dates and also show past and upcoming holidays

== Upgrade Notice ==

= 1.0.0 =
Initial release of the plugin.

== Arbitrary section ==

This plugin uses the following hooks and filters to modify WordPress behavior:

* `wp_insert_post`
* `wp_delete_post`
* `wp_update_post`
* `wp_get_current_user`
* `get_posts`
* `get_post_meta`
* `get_post`
* `add_action`
* `add_filter`
* `wp_schedule_event`
* `wp_next_scheduled`
* `wp_unschedule_event`
* `wp_clear_scheduled_hook`
* `wp_get_schedules`
* `wp_get_schedule`
* `wp_reschedule_event`
* `wp_schedule_single_event`
* `wp_schedule_recurrence`
* `wp_get_ready_cron_jobs`
* `wp_cron`
* `wp_cron_schedules`
* `wp_cron_schedules_filter`
* `wp_cron_schedules_get_schedules`
* `wp_cron_schedules_get_schedules_for_event`
* `wp_cron_schedules_get_schedules_for_event_by_interval`
* `wp_cron_schedules_match_cron_pattern`
* `wp_cron_schedules_match_cron_pattern_by_interval`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval_by_interval`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval_by_interval_for_event`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval_by_interval_for_event_by_interval`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval_by_interval_for_event_by_interval_for_event`
* `wp_cron_schedules_match_cron_pattern_by_interval_for_event_by_interval_by_interval_for_event_by_interval_for_event_by_interval`
