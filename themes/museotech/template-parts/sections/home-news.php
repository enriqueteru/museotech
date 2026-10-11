<?php if (museotech_option('news_visible')) : ?>
    <?php
    $news = new WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => museotech_sanitize_count(museotech_option('news_count')),
        'ignore_sticky_posts' => true,
    ]);
    ?>
    <section class="home-news" aria-labelledby="home-news-title">
        <header class="home-news__header" data-reveal-group>
            <div class="home-news__intro">
                <?php if (museotech_option('news_eyebrow')) : ?>
                    <p>Blog</p>
                <?php endif; ?>
                <h2 id="home-news-title" data-scroll-title><?php echo esc_html(museotech_option('news_eyebrow')); ?></h2>
                <?php if (museotech_option('news_description')) : ?>
                    <div class="home-news__description" data-scroll-fade><?php echo museotech_formatted_text(museotech_option('news_description')); ?></div>
                <?php endif; ?>
            </div>
            <?php if (museotech_option('news_button_label') && museotech_option('news_button_url')) : ?>
                <a class="ui-button" data-scroll-fade data-magnetic href="<?php echo esc_url(museotech_option('news_button_url')); ?>"><?php echo esc_html(museotech_option('news_button_label')); ?></a>
            <?php endif; ?>
        </header>
        <?php if ($news->have_posts()) : ?>
            <div class="news-archive__grid">
                <?php while ($news->have_posts()) : $news->the_post(); ?>
                    <?php get_template_part('template-parts/content/news', 'card'); ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </section>
<?php endif; ?>