<?php get_header(); ?>
<main id="main-content">
    <header class="archive-header"><h1><?php the_archive_title(); ?></h1></header>
    <?php get_template_part('template-parts/archive/archive', 'posts'); ?>
</main>
<?php get_footer(); ?>