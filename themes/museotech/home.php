<?php get_header(); ?>
<main id="main-content">
    <header class="archive-header">
        <h1><?php echo esc_html(get_the_title((int) get_option('page_for_posts')) ?: __('Blog', 'museotech')); ?></h1>
    </header>
    <?php get_template_part('template-parts/archive/archive', 'posts'); ?>
</main>
<?php get_footer(); ?>