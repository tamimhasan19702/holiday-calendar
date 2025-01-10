<?php

function hcpt__get_holiday_info()
{
    $hcpt__today_date = gmdate('Y-m-d');

    // Query for all holidays
    $hcpt__all_holidays_query = new WP_Query(array(
        'post_type' => 'holiday',
        'posts_per_page' => -1, // Get all posts
        'meta_query' => array(
            array(
                'key' => '_holiday_dates',
                'compare' => 'EXISTS', // Ensure we only get posts with holiday dates
            ),
        ),
    ));

    // Initialize variables to hold holiday information
    $hcpt__recent_past_holiday = null;
    $hcpt__recent_upcoming_holiday = null;
    $hcpt__today_holiday = null;

    if ($hcpt__all_holidays_query->have_posts()) {
        while ($hcpt__all_holidays_query->have_posts()) {
            $hcpt__all_holidays_query->the_post();

            // Get the holiday dates
            $hcpt__holiday_dates = get_post_meta(get_the_ID(), '_holiday_dates', true);

            // Get all the meta values for the current post
            $hcpt__meta_values = get_post_meta(get_the_ID());

            // Prepare the data to return
            $hcpt__holiday_data = array(
                'title' => get_the_title(),
                'description' => get_the_content(),
                'page_link' => isset($hcpt__meta_values['_holiday_page_link'][0]) ? $hcpt__meta_values['_holiday_page_link'][0] : '',
                'button_text' => isset($hcpt__meta_values['_holiday_button_text'][0]) ? $hcpt__meta_values['_holiday_button_text'][0] : __(
                    'View More',
                    'holiday-calender'
                ),
                'button_class' => isset($hcpt__meta_values['_holiday_custom_class'][0]) ? $hcpt__meta_values['_holiday_custom_class'][0] : '',
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'medium'), // Get the thumbnail URL
            );

            // Check each holiday date
            foreach ($hcpt__holiday_dates as $hcpt__holiday_date) {
                // Ensure $hcpt__holiday_date is a string before using strtotime
                if (!is_string($hcpt__holiday_date)) {
                    continue; // Skip if it's not a string
                }

                // Check for today's holiday
                if (strtotime($hcpt__holiday_date) === strtotime($hcpt__today_date)) {
                    $hcpt__today_holiday = $hcpt__holiday_data; // Store today's holiday data
                }

                // Check for past holidays
                if (strtotime($hcpt__holiday_date) < strtotime($hcpt__today_date)) {
                    if (is_null($hcpt__recent_past_holiday) || (isset($hcpt__recent_past_holiday['date']) && strtotime($hcpt__holiday_date) > strtotime($hcpt__recent_past_holiday['date']))) {
                        $hcpt__recent_past_holiday = array_merge($hcpt__holiday_data, ['date' => $hcpt__holiday_date]); // Store recent past holiday data
                    }
                }

                // Check for upcoming holidays
                if (strtotime($hcpt__holiday_date) > strtotime($hcpt__today_date)) {
                    if (is_null($hcpt__recent_upcoming_holiday) || (isset($hcpt__recent_upcoming_holiday['date']) && strtotime($hcpt__holiday_date) < strtotime($hcpt__recent_upcoming_holiday['date']))) {
                        $hcpt__recent_upcoming_holiday = array_merge($hcpt__holiday_data, ['date' => $hcpt__holiday_date]); // Store recent upcoming holiday data
                    }
                }
            }
        }
    }

    // Reset post data
    wp_reset_postdata();

    // Return the results as an associative array
    return array(
        'recent_past_holiday' => $hcpt__recent_past_holiday,
        'recent_upcoming_holiday' => $hcpt__recent_upcoming_holiday,
        'today_holiday' => $hcpt__today_holiday,
    );
}

// Call the function and store the results
$hcpt__holiday_info = hcpt__get_holiday_info();

// Output the results
?>