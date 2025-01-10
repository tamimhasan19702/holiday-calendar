<?php

$hcpt__today_date = gmdate('Y-m-d');
include_once __DIR__ . '/hcpt__query-data.php';

// Calculate days since the past holiday if it exists
if ($hcpt__holiday_info['recent_past_holiday']) {
    $hcpt__most_recent_past_date = strtotime($hcpt__holiday_info['recent_past_holiday']['date']);
    $hcpt__days_since = (strtotime($hcpt__today_date) - $hcpt__most_recent_past_date) / (60 * 60 * 24); // Convert seconds to days
} else {
    $hcpt__days_since = null; // Set to null if no past holiday
}

// Calculate days left until the upcoming holiday if it exists
if ($hcpt__holiday_info['recent_upcoming_holiday']) {
    $hcpt__most_recent_upcoming_date = strtotime($hcpt__holiday_info['recent_upcoming_holiday']['date']);
    $hcpt__days_left = ($hcpt__most_recent_upcoming_date - strtotime($hcpt__today_date)) / (60 * 60 * 24); // Convert seconds to days
} else {
    $hcpt__days_left = null; // Set to null if no upcoming holiday
}

?>

<div class="holiday-viewer">
    <!-- Past Holiday Section -->
    <div class="holiday-section past-holiday">
        <h3 class="holiday-heading"><?php _e('Past Holiday', 'holiday-calendar'); ?></h3>
        <?php if ($hcpt__holiday_info['recent_past_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($hcpt__holiday_info['recent_past_holiday']['title']); ?></h4>
                <p class="holiday-date">
                    <?php echo esc_html(gmdate('j F Y', strtotime($hcpt__holiday_info['recent_past_holiday']['date']))); ?>
                </p>
                <div class="days days-since">
                    <span class="highlight"><?php echo esc_html($hcpt__days_since); ?></span>
                    <span class="highlight-text"><?php _e('Days since', 'holiday-calendar'); ?></span>
                </div>
                <a href="<?php echo esc_url($hcpt__holiday_info['recent_past_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($hcpt__holiday_info['recent_past_holiday']['button_class']); ?>">
                    <?php echo esc_html($hcpt__holiday_info['recent_past_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="no-holidays"><?php _e('No past holidays found.', 'holiday-calendar'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Today's Holiday Section -->
    <div class="holiday-section today-holiday">
        <h3 class="holiday-heading"><?php _e('Today\'s Holiday', 'holiday-calendar'); ?></h3>
        <?php if ($hcpt__holiday_info['today_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($hcpt__holiday_info['today_holiday']['title']); ?></h4>
                <p class="holiday-date">
                    <?php echo esc_html(gmdate('j F Y')); ?>
                </p>
                <!-- Display the featured image -->
                <?php
                if (!empty($hcpt__holiday_info['today_holiday']['image'])):
                    $hcpt__image_url = esc_url($hcpt__holiday_info['today_holiday']['image']);
                    ?>
                    <img src="<?php echo esc_url($hcpt__image_url); ?>" class="holiday-image"
                        alt="<?php echo esc_attr($hcpt__holiday_info['today_holiday']['title']); ?>" />
                    <?php
                endif;
                ?>




                <div class="holiday-description">
                    <?php echo wp_kses_post($hcpt__holiday_info['today_holiday']['description']); ?>
                </div>
                <a href="<?php echo esc_url($hcpt__holiday_info['today_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($hcpt__holiday_info['today_holiday']['button_class']); ?>">
                    <?php echo esc_html($hcpt__holiday_info['today_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="current-date"><?php echo esc_html(gmdate('j F Y')); ?></p>
            <p class="current-no-holidays"><?php _e('No holiday today.', 'holiday-calendar'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Upcoming Holiday Section -->
    <div class="holiday-section upcoming-holidays">
        <h3 class="holiday-heading"><?php _e('Upcoming Holiday', 'holiday-calendar'); ?></h3>
        <?php if ($hcpt__holiday_info['recent_upcoming_holiday']): ?>
            <div class="holiday-card">
                <h4 class="holiday-title"><?php echo esc_html($hcpt__holiday_info['recent_upcoming_holiday']['title']); ?>
                </h4>
                <p class="holiday-date">
                    <?php
                    // Check if the date key exists before accessing it
                    if (isset($hcpt__holiday_info['recent_upcoming_holiday']['date'])) {
                        echo esc_html(gmdate('j F Y', strtotime($hcpt__holiday_info['recent_upcoming_holiday']['date'])));
                    } else {
                        echo esc_html(__('Date not available', 'holiday-calendar'));
                    }
                    ?>
                </p>
                <div class="days days-left">
                    <span class="highlight"><?php echo esc_html($hcpt__days_left); ?></span>
                    <span class="highlight-text"><?php _e('Days left', 'holiday-calendar'); ?></span>
                </div>
                <a href="<?php echo esc_url($hcpt__holiday_info['recent_upcoming_holiday']['page_link']); ?>"
                    class="holiday-button <?php echo esc_attr($hcpt__holiday_info['recent_upcoming_holiday']['button_class']); ?>">
                    <?php echo esc_html($hcpt__holiday_info['recent_upcoming_holiday']['button_text']); ?>
                </a>
            </div>
        <?php else: ?>
            <p class="no-holidays"><?php _e('No upcoming holidays found.', 'holiday-calendar'); ?></p>
        <?php endif; ?>
    </div>

</div>

<?php
// Reset post data
wp_reset_postdata();
?>