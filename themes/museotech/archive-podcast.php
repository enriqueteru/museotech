<?php
if (isset($_GET['museotech_archive_fragment'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    nocache_headers();
    header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
    header('X-Museotech-Archive-Fragment: 1');
    get_template_part('template-parts/archive/podcast-results');
    return;
}
get_header();
?>
<main id="main-content" class="podcast-archive" data-archive-base="<?php echo esc_url(get_post_type_archive_link('podcast') ?: home_url('/podcast/')); ?>">
    <header class="podcast-archive__header section-banner">
        <div class="section-banner__copy">
            <h1 class="section-banner__title"><?php esc_html_e('Todos los podcasts', 'museotech'); ?></h1>
            <p class="section-banner__description"><?php esc_html_e('Conversaciones sobre tecnología, museos y sociedad para escuchar cuando quieras.', 'museotech'); ?></p>
        </div>
    </header>

    <div class="podcast-archive__results" data-archive-results>
        <?php get_template_part('template-parts/archive/podcast-results'); ?>
    </div>
    <p class="screen-reader-text podcast-archive__status" role="status" aria-live="polite"></p>
</main>
<?php get_footer(); ?>
