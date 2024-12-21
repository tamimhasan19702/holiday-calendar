<?php

$today = gmdate('Y-m-d H:i:s');
$today_date = gmdate('Y-m-d');

// Query for today's holiday
$today_args = array(
    'post_type' => 'holiday',
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '=',
            'type' => 'DATE'
        ),
    ),
);

// Execute today's holiday query
$today_query = new WP_Query($today_args);

// Prepare queries for past and upcoming holidays
$past_args = array(
    'post_type' => 'holiday',
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '<',
            'type' => 'DATE'
        ),
    ),
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'posts_per_page' => 1 // Limit to 1 past holiday
);

$upcoming_args = array(
    'post_type' => 'holiday',
    'meta_query' => array(
        array(
            'key' => '_holiday_date',
            'value' => $today_date,
            'compare' => '>',
            'type' => 'DATE'
        ),
    ),
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'posts_per_page' => 2 // Limit to 2 upcoming holidays
);

// Execute past and upcoming holiday queries
$past_query = new WP_Query($past_args);
$upcoming_query = new WP_Query($upcoming_args);
?>

<div class="holiday-viewer" style="display: flex; flex-direction: row;">

    <!-- Past Holiday Section -->
    <div class="holiday-section past-holiday" style="flex: 0 0 25%; padding: 10px;">
        <h3>Past Holiday</h3>
        <?php if ($past_query->have_posts()) : ?>
        <?php while ($past_query->have_posts()) : $past_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php the_title(); ?></h4>
            <p class="holiday-date">
                <?php echo date('j F Y', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true))); ?></p>

            <p class="holiday-description"><?php the_content(); ?></p>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p>No past holidays found.</p>
        <?php endif; ?>
    </div>

    <!-- Today's Holiday Section -->
    <div class="holiday-section today-holiday" style="flex: 0 0 50%; padding: 10px;">
        <h3>Today's Holiday</h3>
        <?php if ($today_query->have_posts()) : ?>
        <?php while ($today_query->have_posts()) : $today_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php the_title(); ?></h4>
            <p class="holiday-date">
                <?php echo date('j F Y', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true))); ?></p>
            <?php echo get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'holiday-image')); ?>
            <p class="holiday-description"><?php the_content(); ?></p>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p class="current-date"> <?php echo date('j F Y'); ?></p>
        <p>No holiday today.</p>
        <?php endif; ?>
    </div>

    <!-- Upcoming Holidays Section -->
    <div class="holiday-section upcoming-holidays" style="flex: 0 0 25%; padding: 10px;">
        <h3>Upcoming Holidays</h3>
        <?php if ($upcoming_query->have_posts()) : ?>
        <?php while ($upcoming_query->have_posts()) : $upcoming_query->the_post(); ?>
        <div class="holiday-card">
            <h4 class="holiday-title"><?php the_title(); ?></h4>
            <p class="holiday-date">
                <?php echo date('j F Y ', strtotime(get_post_meta(get_the_ID(), '_holiday_date', true))); ?></p>

            <p class="holiday-description"><?php the_content(); ?></p>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
        <p>No upcoming holidays found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Reset post data
wp_reset_postdata();
?>