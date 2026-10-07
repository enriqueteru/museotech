<?php get_header(); ?>
<main id="main-content">
    <?php get_template_part('template-parts/sections/home', 'hero'); ?>
    <?php get_template_part('template-parts/sections/home', 'objectives'); ?>
    <?php get_template_part('template-parts/sections/home', 'transition', ['index' => 1]); ?>
    <?php get_template_part('template-parts/sections/home', 'podcast'); ?>
    <?php get_template_part('template-parts/sections/home', 'repository'); ?>
    <?php get_template_part('template-parts/sections/home', 'transition', ['index' => 2]); ?>
    <?php get_template_part('template-parts/sections/home', 'news'); ?>
</main>
<?php get_footer(); ?>