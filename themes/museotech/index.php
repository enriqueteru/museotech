<?php get_header(); ?>
<main id="main-content">
    <?php if (is_singular()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content/single'); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <?php get_template_part('template-parts/archive/archive', 'posts'); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>