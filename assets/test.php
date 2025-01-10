<div class="holiday-viewer" style="display: flex; flex-direction: row;">


    <div class="holiday-section past-holiday">
        <h3 class="holiday-heading">Past Holiday</h3>

        <?php if ($past_query->have_posts()): ?>
            <?php while ($past_query->have_posts()):
                $past_query->the_post(); ?>
                <div class="holiday-card">
                    <h4 class="holiday-title"><?php the_title(); ?></h4>
                    <?php
                    $holiday_dates = get_post_meta(get_the_ID(), '_holiday_dates', true);
                    $most_recent_past_date = null;

                    // Find the most recent past date
                    foreach ($holiday_dates as $holiday_date) {
                        if (strtotime($holiday_date) < strtotime($today_date)) {
                            if (is_null($most_recent_past_date) || strtotime($holiday_date) > strtotime($most_recent_past_date)) {
                                $most_recent_past_date = $holiday_date;
                            }
                        }
                    }

                    // Display the most recent past holiday date
                    if ($most_recent_past_date) {
                        echo '<p class="holiday-date">' . esc_html(gmdate('j F Y', strtotime($most_recent_past_date))) . '</p>';
                        // Calculate days since the past holiday
                        $days_since = (strtotime($today_date) - strtotime($most_recent_past_date)) / (60 * 60 * 24); // Convert seconds to days
                        echo '<div class="days days-since"><span class="highlight">' . esc_html($days_since) . '</span> <span class="highlight-text">Days since</span></div>';
                    }
                    ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-holidays">No past holidays found.</p>
        <?php endif; ?>
    </div>


    <div class="holiday-section today-holiday">
        <h3 class="holiday-heading">Today's Holiday</h3>
        <?php if ($today_query->have_posts()): ?>
            <?php while ($today_query->have_posts()):
                $today_query->the_post(); ?>
                <div class="holiday-card">
                    <h4 class="holiday-title"><?php esc_html(the_title()); ?></h4>
                    <?php
                    $holiday_dates = get_post_meta(get_the_ID(), '_holiday_dates', true);
                    foreach ($holiday_dates as $holiday_date) {
                        if (strtotime($holiday_date) === strtotime($today_date)) {
                            echo '<p class="holiday-date">' . esc_html(gmdate('j F Y', strtotime($holiday_date))) . '</p>';
                            echo wp_kses_post(get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'holiday-image')));
                            echo '<div class="holiday-description">' . wp_kses_post(wpautop(get_the_content())) . '</div>';
                        }
                    }
                    // Get button text, page link, and custom class
                    $button_text = get_post_meta(get_the_ID(), '_holiday_button_text', true);
                    $page_link = get_post_meta(get_the_ID(), '_holiday_page_link', true);
                    $custom_class = get_post_meta(get_the_ID(), '_holiday_custom_class', true);
                    if ($button_text && $page_link) {
                        echo '<a href="' . esc_url($page_link) . '" class="holiday-button-today ' . esc_attr($custom_class) . '">' . esc_html($button_text) . '</a>';
                    }
                    ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="current-date"><?php echo esc_html(gmdate('j F Y')); ?></p>
            <p class="current-no-holidays">No holiday today.</p>
        <?php endif; ?>
    </div>


    <div class="holiday-section upcoming-holidays">
        <h3 class="holiday-heading">Upcoming Holiday</h3>
        <?php if ($upcoming_query->have_posts()): ?>
            <?php while ($upcoming_query->have_posts()):
                $upcoming_query->the_post(); ?>
                <div class="holiday-card">
                    <h4 class="holiday-title"><?php esc_html(the_title()); ?></h4>
                    <?php
                    $holiday_dates = get_post_meta(get_the_ID(), '_holiday_dates', true);
                    foreach ($holiday_dates as $holiday_date) {
                        if (strtotime($holiday_date) > strtotime($today_date)) {
                            echo '<p class="holiday-date">' . esc_html(gmdate('j F Y', strtotime($holiday_date))) . '</p>';
                            // Calculate days left until the upcoming holiday
                            $days_left = (strtotime($holiday_date) - strtotime($today_date)) / (60 * 60 * 24); // Convert seconds to days
                            echo '<div class="days days-left"><span class="highlight">' . esc_html($days_left) . '</span> <span class="highlight-text">Days left</span></div>';
                        }
                    }
                    // Get button text, page link, and custom class
                    $button_text = get_post_meta(get_the_ID(), '_holiday_button_text', true);
                    $page_link = get_post_meta(get_the_ID(), '_holiday_page_link', true);
                    $custom_class = get_post_meta(get_the_ID(), '_holiday_custom_class', true);
                    if ($button_text && $page_link) {
                        echo '<a href="' . esc_url($page_link) . '" class="holiday-button ' . esc_attr($custom_class) . '">' . esc_html($button_text) . '</a>';
                    }
                    ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-holidays">No upcoming holidays found.</p>
        <?php endif; ?>
    </div>
</div>