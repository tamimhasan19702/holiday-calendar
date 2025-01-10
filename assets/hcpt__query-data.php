<?php


function hcpt__get_holiday_info()
{
    $today_date = gmdate('Y-m-d');

    // Query for all holidays
    $all_holidays_query = new WP_Query(array(
        'post_type' => 'hcpt__holiday',
        'posts_per_page' => -1, // Get all posts
        'meta_query' => array(
            array(
                'key' => 'hcpt__holiday_dates',
                'compare' => 'EXISTS', // Ensure we only get posts with holiday dates
            ),
        ),
    ));

    // Initialize variables to hold holiday information
    $recent_past_holiday = null;
    $recent_upcoming_holiday = null;
    $today_holiday = null;

    if ($all_holidays_query->have_posts()) {
        while ($all_holidays_query->have_posts()) {
            $all_holidays_query->the_post();

            // Get the holiday dates
            $holiday_dates = get_post_meta(get_the_ID(), 'hcpt__holiday_dates', true);

            // Get all the meta values for the current post
            $meta_values = get_post_meta(get_the_ID());

            // Prepare the data to return
            $holiday_data = array(
                'title' => get_the_title(),
                'description' => get_the_content(),
                'page_link' => isset($meta_values['hcpt__holiday_page_link'][0]) ? $meta_values['hcpt__holiday_page_link'][0] : '',
                'button_text' => isset($meta_values['hcpt__holiday_button_text'][0]) ? $meta_values['hcpt__holiday_button_text'][0] : '',
                'button_class' => isset($meta_values['hcpt__holiday_custom_class'][0]) ? $meta_values['hcpt__holiday_custom_class'][0] : '',
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'medium'), // Get the thumbnail URL
            );

            // Check each holiday date
            foreach ($holiday_dates as $holiday_date) {
                // Ensure $holiday_date is a string before using strtotime
                if (!is_string($holiday_date)) {
                    continue; // Skip if it's not a string
                }

                // Check for today's holiday
                if (strtotime($holiday_date) === strtotime($today_date)) {
                    $today_holiday = $holiday_data; // Store today's holiday data
                }

                // Check for past holidays
                if (strtotime($holiday_date) < strtotime($today_date)) {
                    if (is_null($recent_past_holiday) || (isset($recent_past_holiday['date']) && strtotime($holiday_date) > strtotime($recent_past_holiday['date']))) {
                        $recent_past_holiday = array_merge($holiday_data, ['date' => $holiday_date]); // Store recent past holiday data
                    }
                }

                // Check for upcoming holidays
                if (strtotime($holiday_date) > strtotime($today_date)) {
                    if (is_null($recent_upcoming_holiday) || (isset($recent_upcoming_holiday['date']) && strtotime($holiday_date) < strtotime($recent_upcoming_holiday['date']))) {
                        $recent_upcoming_holiday = array_merge($holiday_data, ['date' => $holiday_date]); // Store recent upcoming holiday data
                    }
                }
            }
        }
    }

    // Reset post data
    wp_reset_postdata();

    // Return the results as an associative array
    return array(
        'recent_past_holiday' => $recent_past_holiday,
        'recent_upcoming_holiday' => $recent_upcoming_holiday,
        'today_holiday' => $today_holiday,
    );
}

// Call the function and store the results
$hcpt__holiday_info = hcpt__get_holiday_info();

// Output the results