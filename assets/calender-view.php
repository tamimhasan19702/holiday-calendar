<?php

$today_date = gmdate('Y-m-d');
include_once __DIR__ . '/query-data.php';

// Calculate days since the past holiday if it exists
if ($holiday_info['recent_past_holiday']) {
    $most_recent_past_date = strtotime($holiday_info['recent_past_holiday']['date']);
    $days_since = (strtotime($today_date) - $most_recent_past_date) / (60 * 60 * 24); // Convert seconds to days
} else {
    $days_since = null; // Set to null if no past holiday
}

// Calculate days left until the upcoming holiday if it exists
if ($holiday_info['recent_upcoming_holiday']) {
    $most_recent_upcoming_date = strtotime($holiday_info['recent_upcoming_holiday']['date']);
    $days_left = ($most_recent_upcoming_date - strtotime($today_date)) / (60 * 60 * 24); // Convert seconds to days
} else {
    $days_left = null; // Set to null if no upcoming holiday
}

?>

<div class="holiday-viewer">

    <!-- Past Holiday Section -->
    <div class="holiday-section past-holiday">
        <h3 class="holiday-heading">Past Holiday</h3>
        <?php if ($holiday_info['recent_past_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($holiday_info['recent_past_holiday']['title']); ?></h4>
                <p class="holiday-date">
                    <?php echo esc_html(gmdate('j F Y', strtotime($holiday_info['recent_past_holiday']['date']))); ?>
                </p>
                <div class="days days-since">
                    <span class="highlight"><?php echo esc_html($days_since); ?></span>
                    <span class="highlight-text">Days since</span>
                </div>
                <a href="<?php echo esc_url($holiday_info['recent_past_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($holiday_info['recent_past_holiday']['button_class']); ?>">
                    <?php echo esc_html($holiday_info['recent_past_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="no-holidays">No past holidays found.</p>
        <?php endif; ?>
    </div>

    <!-- Today's Holiday Section -->
    <div class="holiday-section today-holiday">
        <h3 class="holiday-heading">Today's Holiday</h3>
        <?php if ($holiday_info['today_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($holiday_info['today_holiday']['title']); ?></h4>
                <p class="holiday-date">
                    <?php echo esc_html(gmdate('j F Y')); ?>
                </p>
                <!-- Display the featured image -->
                <?php if (!empty($holiday_info['today_holiday']['image'])): ?>
                    <img src="<?php echo esc_url($holiday_info['today_holiday']['image']); ?>"
                        alt="<?php echo esc_attr($holiday_info['today_holiday']['title']); ?>" class="holiday-image" />
                <?php endif; ?>
                <div class="holiday-description"><?php echo wp_kses_post($holiday_info['today_holiday']['description']); ?>
                </div>
                <a href="<?php echo esc_url($holiday_info['today_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($holiday_info['today_holiday']['button_class']); ?>">
                    <?php echo esc_html($holiday_info['today_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="current-date"><?php echo esc_html(gmdate('j F Y')); ?></p>
            <p class="current-no-holidays">No holiday today.</p>
        <?php endif; ?>
    </div>

    <!-- Upcoming Holiday Section -->
    <div class="holiday-section upcoming-holidays">
        <h3 class="holiday-heading">Upcoming Holiday</h3>
        <?php if ($holiday_info['recent_upcoming_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($holiday_info['recent_upcoming_holiday']['title']); ?></h4>
                <p class="holiday-date">
                    <?php
                    // Check if the date key exists before accessing it
                    if (isset($holiday_info['recent_upcoming_holiday']['date'])) {
                        echo esc_html(gmdate('j F Y', strtotime($holiday_info['recent_upcoming_holiday']['date'])));
                    } else {
                        echo esc_html('Date not available');
                    }
                    ?>
                </p>
                <div class="days days-left">
                    <span class="highlight"><?php echo esc_html($days_left); ?></span>
                    <span class="highlight-text">Days left</span>
                </div>
                <a href="<?php echo esc_url($holiday_info['recent_upcoming_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($holiday_info['recent_upcoming_holiday']['button_class']); ?>">
                    <?php echo esc_html($holiday_info['recent_upcoming_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="no-holidays">No upcoming holidays found.</p>
        <?php endif; ?>
    </div>

</div>

<?php
// Reset post data
wp_reset_postdata();
?>