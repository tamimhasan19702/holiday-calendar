<?php

$today = gmdate('Y-m-d H:i:s');
$today_date = gmdate('Y-m-d');

// Query for today's holiday
$today_query = new WP_Query(array(
    'post_type' => 'holiday',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '=',
            'type' => 'DATE',
        ),
    ),
));

// Prepare queries for past and upcoming holidays
$past_query = new WP_Query(array(
    'post_type' => 'holiday',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '<',
            'type' => 'DATE',
        ),
    ),
    'orderby' => 'meta_value', // Ensure this is set to 'meta_value'
    'order' => 'DESC',
));

$upcoming_query = new WP_Query(array(
    'post_type' => 'holiday',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '>',
            'type' => 'DATE',
        ),
    ),
    'orderby' => 'meta_value', // Ensure this is set to 'meta_value'
    'order' => 'ASC',
));
?>

<div class="holiday-viewer" style="display: flex; flex-direction: row;">

    <!-- Past Holiday Section -->
    <div class="holiday-section past-holiday" style="flex: 0 0 25%; padding: 10px;">
        <h3 class="holiday-heading">Past Holiday</h3>

        <?php if ($past_query->have_posts()) : ?>
        <?php while ($past_query->have_posts()) : $past_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php the_title(); ?></h4>
            <p class="holiday-date">
                <?php echo esc_html(gmdate('j F Y', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true)))); ?>
            </p>
            <?php
                    // Get button text, page link, and custom class
                    $button_text = get_post_meta(get_the_ID(), '_holiday_button_text', true);
                    $page_link = get_post_meta(get_the_ID(), '_holiday_page_link', true);
                    $custom_class = get_post_meta(get_the_ID(), '_holiday_custom_class', true); // Retrieve custom class
                    if ($button_text && $page_link) {
                        echo '<a href="' . esc_url($page_link) . '" class="holiday-button ' . esc_attr($custom_class) . '">' . esc_html($button_text) . '</a>';
                    }
                    ?>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p class="no-holidays">No past holidays found.</p>
        <?php endif; ?>
    </div>

    <!-- Today's Holiday Section -->
    <div class="holiday-section today-holiday" style="flex: 0 0 50%; padding: 10px;">
        <h3 class="holiday-heading">Today's Holiday</h3>
        <?php if ($today_query->have_posts()) : ?>
        <?php while ($today_query->have_posts()) : $today_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php esc_html(the_title()); ?></h4>
            <p class="holiday-date">
                <?php echo esc_html(gmdate('j F Y', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true)))); ?>
            </p>
            <?php echo wp_kses_post(get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'holiday-image'))); ?>
            <div class="holiday-description">
                <?php echo wp_kses_post(wpautop(get_the_content())); ?>
            </div>
            <?php
                    // Get button text, page link, and custom class
                    $button_text = get_post_meta(get_the_ID(), '_holiday_button_text', true);
                    $page_link = get_post_meta(get_the_ID(), '_holiday_page_link', true);
                    $custom_class = get_post_meta(get_the_ID(), '_holiday_custom_class', true); // Retrieve custom class
                    if ($button_text && $page_link) {
                        echo '<a href="' . esc_url($page_link) . '" class="holiday-button-today ' . esc_attr($custom_class) . '">' . esc_html($button_text) . '</a>';
                    }
                    ?>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p class="current-date"><?php echo esc_html(gmdate('j F Y')); ?></p>
        <p class="current-no-holidays">No holiday today.</p>
        <?php endif; ?>
    </div>


    <!-- Upcoming Holidays Section -->
    <div class="holiday-section upcoming-holidays" style="flex: 0 0 25%; padding: 10px;">
        <h3 class="holiday-heading">Upcoming Holiday</h3>
        <?php if ($upcoming_query->have_posts()) : ?>
        <?php while ($upcoming_query->have_posts()) : $upcoming_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php the_title(); ?></h4>
            <p class="holiday-date">
                <?php echo esc_html(gmdate('j F Y', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true)))); ?>
            </p>
            <?php
                    // Get button text, page link, and custom class
                    $button_text = get_post_meta(get_the_ID(), '_holiday_button_text', true);
                    $page_link = get_post_meta(get_the_ID(), '_holiday_page_link', true);
                    $custom_class = get_post_meta(get_the_ID(), '_holiday_custom_class', true); // Retrieve custom class
                    if ($button_text && $page_link) {
                        echo '<a href="' . esc_url($page_link) . '" class="holiday-button ' . esc_attr($custom_class) . '">' . esc_html($button_text) . '</a>';
                    }
                    ?>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p class="no-holidays">No upcoming holidays found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Reset post data
wp_reset_postdata();
?>