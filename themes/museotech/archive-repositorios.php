<?php
if (isset($_GET['museotech_archive_fragment'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    nocache_headers();
    header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
    header('X-Museotech-Archive-Fragment: 1');
    get_template_part('template-parts/archive/repository-results');
    return;
}

get_header();

$repository_archive_url = get_post_type_archive_link('repositorios') ?: home_url('/repositorios/');
$repository_formats = get_terms([
    'taxonomy' => 'formato',
    'hide_empty' => true,
]);
$active_format = isset($_GET['formato']) ? sanitize_title(wp_unslash($_GET['formato'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if (is_wp_error($repository_formats)) {
    $repository_formats = [];
}

$available_format_slugs = wp_list_pluck($repository_formats, 'slug');
if (! in_array($active_format, $available_format_slugs, true)) {
    $active_format = '';
}

$archive_intro = trim((string) museotech_option('archive_repositorios_heading'));
$contact_page = get_page_by_path('contacto');
$contact_url = $contact_page ? get_permalink($contact_page) : home_url('/contacto/');
?>
<main id="main-content" class="repository-archive" data-archive-base="<?php echo esc_url($repository_archive_url); ?>">
    <header class="repository-archive__intro section-banner">
        <div class="section-banner__copy">
            <h1 class="section-banner__title"><?php esc_html_e('Repositorio', 'museotech'); ?></h1>
            <?php if ($archive_intro !== '') : ?>
                <p class="section-banner__description"><?php echo esc_html($archive_intro); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <div class="repository-archive__layout">
        <nav class="repository-archive__filters" aria-label="<?php esc_attr_e('Filtrar repositorios por formato', 'museotech'); ?>">
            <a class="ui-button ui-button--filter repository-archive__filter<?php echo $active_format === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url($repository_archive_url); ?>"<?php echo $active_format === '' ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Todos', 'museotech'); ?></a>
            <?php foreach ($repository_formats as $format) : ?>
                <a class="ui-button ui-button--filter repository-archive__filter<?php echo $active_format === $format->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('formato', $format->slug, $repository_archive_url)); ?>"<?php echo $active_format === $format->slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html($format->name); ?></a>
            <?php endforeach; ?>
        </nav>

        <section class="repository-archive__records" data-archive-results aria-label="<?php esc_attr_e('Recursos del repositorio', 'museotech'); ?>">
            <?php get_template_part('template-parts/archive/repository-results'); ?>
        </section>

        <aside class="repository-archive__sidebar" aria-labelledby="repository-contribute-title">
            <div class="repository-archive__contribute">
                <p class="repository-archive__contribute-label"><?php esc_html_e('Participa', 'museotech'); ?></p>
                <h2 id="repository-contribute-title"><?php esc_html_e('Participa en Museotech', 'museotech'); ?></h2>
                <p><?php esc_html_e('Comparte tus publicaciones y propuestas con el equipo de Museotech.', 'museotech'); ?></p>
                <a class="ui-button repository-archive__contribute-link" href="<?php echo esc_url($contact_url); ?>">
                    <?php esc_html_e('Contacta con el equipo', 'museotech'); ?>
                    <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </div>
        </aside>

        <p class="screen-reader-text repository-archive__status" role="status" aria-live="polite"></p>
    </div>
</main>
<?php get_footer(); ?>
