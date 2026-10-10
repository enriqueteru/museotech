<?php
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
<main id="main-content" class="repository-archive">
    <header class="repository-archive__intro section-banner">
        <?php get_template_part('template-parts/components/podcast-banner-model', null, [
            'model_path' => 'assets/models/repository-book.glb',
            'component_class' => 'repository-banner-model',
            'model_scale' => 1.05,
            'rotation_speed' => 0.16,
            'aria_label' => __('Libro 3D interactivo. Arrástralo o usa las flechas para girarlo; el giro automático respeta las preferencias de movimiento.', 'museotech'),
        ]); ?>
        <h1 class="section-banner__title"><?php esc_html_e('Repositorio', 'museotech'); ?></h1>
        <?php if ($archive_intro !== '') : ?>
            <p class="section-banner__description"><?php echo esc_html($archive_intro); ?></p>
        <?php endif; ?>
        <nav class="repository-archive__filters" aria-label="<?php esc_attr_e('Filtrar repositorios por formato', 'museotech'); ?>">
            <a class="ui-button ui-button--filter repository-archive__filter<?php echo $active_format === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url($repository_archive_url); ?>"<?php echo $active_format === '' ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Todos', 'museotech'); ?></a>
            <?php foreach ($repository_formats as $format) : ?>
                <a class="ui-button ui-button--filter repository-archive__filter<?php echo $active_format === $format->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('formato', $format->slug, $repository_archive_url)); ?>"<?php echo $active_format === $format->slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html($format->name); ?></a>
            <?php endforeach; ?>
        </nav>
    </header>

    <div class="repository-archive__layout">
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

        <section class="repository-archive__records" aria-label="<?php esc_attr_e('Recursos del repositorio', 'museotech'); ?>">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $format_terms = get_the_terms(get_the_ID(), 'formato');
                    if (is_wp_error($format_terms) || ! is_array($format_terms)) {
                        $format_terms = [];
                    }

                    $authors = museotech_field('autores');
                    $authors = is_scalar($authors) ? trim((string) $authors) : '';
                    $editorial = museotech_field('editorial');
                    $editorial = is_scalar($editorial) ? trim((string) $editorial) : '';
                    $identifier = museotech_field('isbn');
                    $identifier = is_scalar($identifier) ? trim((string) $identifier) : '';
                    $pages = museotech_field('paginas');
                    $pages = is_scalar($pages) ? trim((string) $pages) : '';
                    $repository_date = museotech_field('fecha');
                    $repository_date = is_scalar($repository_date) ? trim((string) $repository_date) : '';
                    $date_object = $repository_date !== '' ? DateTimeImmutable::createFromFormat('!Ymd', $repository_date, wp_timezone()) : false;
                    if ($date_object instanceof DateTimeImmutable && $date_object->format('Ymd') === $repository_date) {
                        $date_display = wp_date(get_option('date_format'), $date_object->getTimestamp());
                        $date_iso = $date_object->format('Y-m-d');
                    } else {
                        $date_display = get_the_date(get_option('date_format'));
                        $date_iso = get_the_date('Y-m-d');
                    }
                    $file_url = esc_url(museotech_file_url(museotech_field('archivo')));
                    $external_url = esc_url(museotech_file_url(museotech_field('recurso_externo')));
                    $uri_url = esc_url(museotech_file_url(museotech_field('uri')));
                    $description = get_the_excerpt();
                    if ($description === '') {
                        $description = wp_strip_all_tags(get_the_content());
                    }
                    $description = wp_trim_words($description, 48, '…');
                    ?>
                    <article <?php post_class('repository-record'); ?>>
                        <div class="repository-record__badges">
                            <span class="repository-record__badge repository-record__badge--date">
                                <time datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date_display); ?></time>
                            </span>
                            <?php foreach ($format_terms as $format) : ?>
                                <a class="repository-record__badge repository-record__badge--format" href="<?php echo esc_url(add_query_arg('formato', $format->slug, $repository_archive_url)); ?>">
                                    <?php echo esc_html($format->name); ?>
                                </a>
                            <?php endforeach; ?>
                            <?php if ($file_url || $external_url || $uri_url) : ?>
                                <span class="repository-record__badge repository-record__badge--available"><?php esc_html_e('Disponible', 'museotech'); ?></span>
                            <?php endif; ?>
                        </div>

                        <h2 class="repository-record__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                        <?php if ($authors !== '') : ?>
                            <p class="repository-record__authors"><span><?php esc_html_e('Autores', 'museotech'); ?>:</span> <?php echo esc_html($authors); ?></p>
                        <?php endif; ?>

                        <?php if ($description !== '') : ?>
                            <p class="repository-record__description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>

                        <div class="repository-record__metadata">
                            <?php if ($editorial !== '') : ?>
                                <p><span><?php esc_html_e('Editorial / revista', 'museotech'); ?>:</span> <?php echo esc_html($editorial); ?></p>
                            <?php endif; ?>
                            <?php if ($identifier !== '') : ?>
                                <p><span><?php esc_html_e('ISBN / ISSN', 'museotech'); ?>:</span> <?php echo esc_html($identifier); ?></p>
                            <?php endif; ?>
                            <?php if ($pages !== '') : ?>
                                <p><span><?php esc_html_e('Páginas', 'museotech'); ?>:</span> <?php echo esc_html($pages); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="repository-record__actions">
                            <a class="ui-button ui-button--compact repository-record__details" href="<?php the_permalink(); ?>"><?php esc_html_e('Ver ficha', 'museotech'); ?> <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <?php if ($file_url) : ?>
                                <a class="ui-button ui-button--compact" href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Abrir documento', 'museotech'); ?> <?php echo museotech_ui_icon('external-link'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <?php endif; ?>
                            <?php if ($external_url && $external_url !== $file_url) : ?>
                                <a class="ui-button ui-button--compact" href="<?php echo esc_url($external_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Recurso externo', 'museotech'); ?> <?php echo museotech_ui_icon('external-link'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <?php endif; ?>
                            <?php if ($uri_url && $uri_url !== $file_url && $uri_url !== $external_url) : ?>
                                <a class="ui-button ui-button--compact" href="<?php echo esc_url($uri_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Enlace relacionado', 'museotech'); ?> <?php echo museotech_ui_icon('external-link'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>

                <?php the_posts_pagination([
                    'mid_size' => 1,
                    'prev_text' => __('Anterior', 'museotech'),
                    'next_text' => __('Siguiente', 'museotech'),
                ]); ?>
            <?php else : ?>
                <p class="repository-archive__empty"><?php esc_html_e('No hay recursos disponibles con este formato.', 'museotech'); ?></p>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php get_footer(); ?>