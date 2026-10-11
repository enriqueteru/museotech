<?php get_header(); ?>
<main id="main-content">
    <?php get_template_part('template-parts/sections/home', 'hero'); ?>
    <?php get_template_part('template-parts/sections/home', 'objectives'); ?>
    <?php if (museotech_option('podcast_visible') || museotech_option('repository_visible')) : ?>
        <div class="home-feature-stack" data-home-feature-stack>
            <div class="home-feature-stack__stage">
                <?php get_template_part('template-parts/sections/home', 'podcast'); ?>
                <?php get_template_part('template-parts/sections/home', 'repository'); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php get_template_part('template-parts/sections/home', 'marquee'); ?>
    <?php get_template_part('template-parts/sections/home', 'news'); ?>
</main>
<?php get_footer(); ?>
