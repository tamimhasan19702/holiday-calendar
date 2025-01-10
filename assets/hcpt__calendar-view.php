<?php



if (!defined('ABSPATH'))
    exit;

$today_date = gmdate('Y-m-d');
include_once __DIR__ . '/hcpt__query-data.php';

var_dump($hcpt__holiday_info);



// Calculate days since the past holiday if it exists
if ($hcpt__holiday_info['recent_past_holiday']) {
    $most_recent_past_date = strtotime($hcpt__holiday_info['recent_past_holiday']['date']);
    $days_since = (strtotime($today_date) - $most_recent_past_date) / (60 * 60 * 24); // Convert seconds to days
} else {
    $days_since = null; // Set to null if no past holiday
}

// Calculate days left until the upcoming holiday if it exists
if ($hcpt__holiday_info['recent_upcoming_holiday']) {
    $most_recent_upcoming_date = strtotime($hcpt__holiday_info['recent_upcoming_holiday']['date']);
    $days_left = ($most_recent_upcoming_date - strtotime($today_date)) / (60 * 60 * 24); // Convert seconds to days
} else {
    $days_left = null; // Set to null if no upcoming holiday
}

?>

<div class="hcpt__holiday-viewer">

    <!-- Past Holiday Section -->
    <div class="hcpt__holiday-section hcpt__past-holiday">
        <h3 class="hcpt__holiday-heading">Past Holiday</h3>
        <?php if ($hcpt__holiday_info['recent_past_holiday']): ?>
            <div class="hcpt__holiday-card">
                <?php if (!empty($hcpt__holiday_info['recent_past_holiday']['title'])): ?>
                    <h4 class="hcpt__holiday-title"><?php echo esc_html($hcpt__holiday_info['recent_past_holiday']['title']); ?>
                    </h4>
                <?php endif; ?>
                <?php if (!empty($hcpt__holiday_info['recent_past_holiday']['date'])): ?>
                    <p class="hcpt__holiday-date">
                        <?php echo esc_html(gmdate('j F Y', strtotime($hcpt__holiday_info['recent_past_holiday']['date']))); ?>
                    </p>
                <?php endif; ?>
                <div class="hcpt__days hcpt__days-since">
                    <span class="hcpt__highlight"><?php echo esc_html($days_since); ?></span>
                    <span class="hcpt__highlight-text">Days since</span>
                </div>
                <?php if (!empty($hcpt__holiday_info['recent_past_holiday']['button_text'])): ?>
                    <a href="<?php echo esc_url($hcpt__holiday_info['recent_past_holiday']['page_link']); ?>"
                        class="hcpt__holiday-button <?php echo esc_attr($hcpt__holiday_info['recent_past_holiday']['button_class']); ?>">
                        <?php echo esc_html($hcpt__holiday_info['recent_past_holiday']['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="hcpt__no-holidays">No past holidays found.</p>
        <?php endif; ?>
    </div>

    <!-- Today's Holiday Section -->
    <div class="hcpt__holiday-section hcpt__today-holiday">
        <h3 class="hcpt__holiday-heading">Today's Holiday</h3>
        <?php if ($hcpt__holiday_info['today_holiday']): ?>
            <div class="hcpt__holiday-card">

                <?php if (!empty($hcpt__holiday_info['today_holiday']['title'])): ?>
                    <h4 class="hcpt__holiday-title"><?php echo esc_html($hcpt__holiday_info['today_holiday']['title']); ?></h4>
                <?php endif; ?>

                <?php if (!empty($hcpt__holiday_info['today_holiday']['date'])): ?>
                    <p class="hcpt__holiday-date">
                        <?php echo esc_html(gmdate('j F Y')); ?>
                    </p>
                <?php endif; ?>

                <!-- Display the featured image using URL only for today's holiday -->
                <?php if (!empty($hcpt__holiday_info['today_holiday']['image'])): ?>
                    <img src="<?php echo esc_url($hcpt__holiday_info['today_holiday']['image']); ?>" class="hcpt__holiday-image"
                        alt="<?php echo esc_attr($hcpt__holiday_info['today_holiday']['title']); ?>" />
                <?php endif; ?>

                <?php if (!empty($hcpt__holiday_info['today_holiday']['description'])): ?>
                    <div class="hcpt__holiday-description">
                        <?php echo wp_kses_post($hcpt__holiday_info['today_holiday']['description']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($hcpt__holiday_info['today_holiday']['button_text'])): ?>
                    <a href="<?php echo esc_url($hcpt__holiday_info['today_holiday']['page_link']); ?>"
                        class="hcpt__holiday-button <?php echo esc_attr($hcpt__holiday_info['today_holiday']['button_class']); ?>">
                        <?php echo esc_html($hcpt__holiday_info['today_holiday']['button_text']); ?>
                    </a>
                <?php endif; ?>

            </div>
        <?php else: ?>
            <p class="hcpt__current-date"><?php echo esc_html(gmdate('j F Y')); ?></p>
            <p class="hcpt__current-no-holidays">No holiday today.</p>
        <?php endif; ?>
    </div>



    <!-- Upcoming Holiday Section -->
    <div class="hcpt__holiday-section hcpt__upcoming-holidays">
        <h3 class="hcpt__holiday-heading">Upcoming Holiday</h3>
        <?php if ($hcpt__holiday_info['recent_upcoming_holiday']): ?>
            <div class="hcpt__holiday-card">

                <?php if (!empty($hcpt__holiday_info['recent_upcoming_holiday']['title'])): ?>
                    <h4 class="hcpt__holiday-title">
                        <?php echo esc_html($hcpt__holiday_info['recent_upcoming_holiday']['title']); ?>
                    </h4>
                <?php endif; ?>

                <?php if (!empty($hcpt__holiday_info['recent_upcoming_holiday']['date'])): ?>
                    <p class="hcpt__holiday-date">
                        <?php
                        // Check if the date key exists before accessing it
                        if (isset($hcpt__holiday_info['recent_upcoming_holiday']['date'])) {
                            echo esc_html(gmdate('j F Y', strtotime($hcpt__holiday_info['recent_upcoming_holiday']['date'])));
                        } else {
                            echo esc_html('Date not available');
                        }
                        ?>
                    </p>
                <?php endif; ?>

                <div class="hcpt__days hcpt__days-left">
                    <span class="hcpt__highlight"><?php echo esc_html($days_left); ?></span>
                    <span class="hcpt__highlight-text">Days left</span>
                </div>

                <?php if (!empty($hcpt__holiday_info['recent_upcoming_holiday']['button_text'])): ?>
                    <a href="<?php echo esc_url($hcpt__holiday_info['recent_upcoming_holiday']['page_link']); ?>"
                        class="hcpt__holiday-button <?php echo esc_attr($hcpt__holiday_info['recent_upcoming_holiday']['button_class']); ?>">
                        <?php echo esc_html($hcpt__holiday_info['recent_upcoming_holiday']['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="hcpt__no-holidays">No upcoming holidays found.</p>
        <?php endif; ?>
    </div>

</div>

<?php
// Reset post data
wp_reset_postdata();
?>