<?php get_header(); ?><main id="main-content">
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/single/equipo'); ?>
    <?php endwhile; ?>
</main><?php get_footer(); ?>