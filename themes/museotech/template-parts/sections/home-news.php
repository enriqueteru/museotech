<?php if (museotech_option('news_visible')) : ?>
    <section class="home-news">
        <header>
            <p><?php echo esc_html(museotech_option('news_eyebrow')); ?></p>
            <h2><?php echo esc_html(museotech_option('news_title')); ?></h2>
            <div><?php echo museotech_formatted_text(museotech_option('news_description')); ?></div>
        </header>
        <?php
        $news = new WP_Query([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => museotech_sanitize_count(museotech_option('news_count')),
            'ignore_sticky_posts' => true,
        ]);
        ?>
        <?php if ($news->have_posts()) : ?>
            <div class="content-list">
                <?php while ($news->have_posts()) : $news->the_post(); ?>
                    <?php get_template_part('template-parts/content/card'); ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
        <a href="<?php echo esc_url(museotech_option('news_button_url')); ?>"><?php echo esc_html(museotech_option('news_button_label')); ?></a>
    </section>
<?php endif; ?>