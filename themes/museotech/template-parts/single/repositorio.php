<?php
$repository_id = get_the_ID();
$repository_title = get_the_title($repository_id);
$repository_permalink = get_permalink($repository_id);
$repository_archive_url = get_post_type_archive_link('repositorios') ?: home_url('/repositorios/');

$repository_authors_value = museotech_field('autores', $repository_id);
$repository_authors = is_scalar($repository_authors_value) ? trim((string) $repository_authors_value) : '';
$repository_editorial_value = museotech_field('editorial', $repository_id);
$repository_editorial = is_scalar($repository_editorial_value) ? trim((string) $repository_editorial_value) : '';
$repository_identifier_value = museotech_field('isbn', $repository_id);
$repository_identifier = is_scalar($repository_identifier_value) ? trim((string) $repository_identifier_value) : '';
$repository_pages_value = museotech_field('paginas', $repository_id);
$repository_pages = is_scalar($repository_pages_value) ? trim((string) $repository_pages_value) : '';

$repository_date_value = museotech_field('fecha', $repository_id);
$repository_date_raw = is_scalar($repository_date_value) ? trim((string) $repository_date_value) : '';
$repository_date = null;
foreach (['d/m/Y', 'Ymd', 'Y-m-d'] as $repository_date_format) {
    $candidate_date = $repository_date_raw !== ''
        ? DateTimeImmutable::createFromFormat('!' . $repository_date_format, $repository_date_raw, wp_timezone())
        : false;

    if ($candidate_date instanceof DateTimeImmutable && $candidate_date->format($repository_date_format) === $repository_date_raw) {
        $repository_date = $candidate_date;
        break;
    }
}

if ($repository_date instanceof DateTimeImmutable) {
    $repository_date_display = wp_date(get_option('date_format'), $repository_date->getTimestamp());
    $repository_date_iso = $repository_date->format('Y-m-d');
    $repository_year = $repository_date->format('Y');
} else {
    $repository_date_display = get_the_date(get_option('date_format'), $repository_id);
    $repository_date_iso = get_the_date('Y-m-d', $repository_id);
    $repository_year = get_the_date('Y', $repository_id);
}

$repository_format_terms = get_the_terms($repository_id, 'formato');
if (is_wp_error($repository_format_terms) || ! is_array($repository_format_terms)) {
    $repository_format_terms = [];
}
$repository_format_names = [];
foreach ($repository_format_terms as $repository_format_term) {
    if (isset($repository_format_term->name)) {
        $repository_format_names[] = (string) $repository_format_term->name;
    }
}

$repository_file_value = museotech_field('archivo', $repository_id);
$repository_file_url = trim((string) museotech_file_url($repository_file_value));
$repository_file_url = $repository_file_url !== '' ? esc_url_raw($repository_file_url) : '';
$repository_file_name = '';
$repository_file_size = '';
$repository_file_extension = '';
if ($repository_file_url !== '') {
    $repository_file_path = wp_parse_url($repository_file_url, PHP_URL_PATH);
    $repository_file_name = is_string($repository_file_path) ? rawurldecode(wp_basename($repository_file_path)) : '';
    $repository_file_name = $repository_file_name !== '' ? $repository_file_name : __('Archivo adjunto', 'museotech');
    $repository_file_extension = strtoupper((string) pathinfo($repository_file_name, PATHINFO_EXTENSION));

    $repository_attachment_id = attachment_url_to_postid($repository_file_url);
    if ($repository_attachment_id) {
        $repository_attachment_path = get_attached_file($repository_attachment_id);
        if ($repository_attachment_path && is_file($repository_attachment_path)) {
            $repository_file_bytes = filesize($repository_attachment_path);
            if ($repository_file_bytes !== false) {
                $repository_file_size = size_format($repository_file_bytes, 1);
            }
        }
    }
}

$repository_resource_specs = [
    'recurso_externo' => __('Recurso externo', 'museotech'),
    'uri' => __('Página del editor', 'museotech'),
];
$repository_resources = [];
$repository_seen_urls = [];
foreach ($repository_resource_specs as $repository_resource_field => $repository_resource_label) {
    $repository_resource_value = museotech_field($repository_resource_field, $repository_id);
    $repository_resource_url = trim((string) museotech_file_url($repository_resource_value));
    $repository_resource_url = $repository_resource_url !== '' ? esc_url_raw($repository_resource_url) : '';

    if ($repository_resource_url === '' || $repository_resource_url === $repository_file_url || isset($repository_seen_urls[$repository_resource_url])) {
        continue;
    }

    $repository_resource_host = wp_parse_url($repository_resource_url, PHP_URL_HOST);
    $repository_resources[] = [
        'label' => $repository_resource_label,
        'url' => $repository_resource_url,
        'host' => is_string($repository_resource_host) ? $repository_resource_host : '',
    ];
    $repository_seen_urls[$repository_resource_url] = true;
}

$repository_details = [];
if ($repository_format_names) {
    $repository_details[__('Tipo de recurso', 'museotech')] = implode(', ', $repository_format_names);
}
if ($repository_editorial !== '') {
    $repository_details[__('Editorial / revista', 'museotech')] = $repository_editorial;
}
if ($repository_identifier !== '') {
    $repository_details[__('ISBN / ISSN', 'museotech')] = $repository_identifier;
}
if ($repository_pages !== '') {
    $repository_details[__('Páginas', 'museotech')] = $repository_pages;
}

$repository_citation_lead = $repository_authors;
if ($repository_year !== '') {
    $repository_citation_lead .= ($repository_citation_lead !== '' ? ' ' : '') . '(' . $repository_year . ')';
}
$repository_citation = ($repository_citation_lead !== '' ? $repository_citation_lead . '. ' : '') . $repository_title;
if ($repository_editorial !== '') {
    $repository_citation .= '. ' . $repository_editorial;
}
if ($repository_pages !== '') {
    $repository_citation .= ', pp. ' . $repository_pages;
}
if ($repository_identifier !== '') {
    $repository_citation .= '. ISBN/ISSN: ' . $repository_identifier;
}
if (! empty($repository_resources)) {
    $repository_citation .= '. ' . $repository_resources[0]['url'];
}
if (substr($repository_citation, -1) !== '.') {
    $repository_citation .= '.';
}

$repository_export_data = array_filter([
    'title' => $repository_title,
    'url' => $repository_permalink,
    'authors' => $repository_authors !== '' ? $repository_authors : null,
    'publication_date' => $repository_date_iso !== '' ? $repository_date_iso : null,
    'publisher' => $repository_editorial !== '' ? $repository_editorial : null,
    'isbn_issn' => $repository_identifier !== '' ? $repository_identifier : null,
    'pages' => $repository_pages !== '' ? $repository_pages : null,
    'format' => $repository_format_names ?: null,
    'file' => $repository_file_url !== '' ? $repository_file_url : null,
    'external_resources' => $repository_resources ?: null,
], static fn ($value) => $value !== null && $value !== '' && $value !== []);

$repository_created = [
    'label' => __('Creado', 'museotech'),
    'display' => get_the_date(get_option('date_format'), $repository_id),
    'datetime' => get_the_date('c', $repository_id),
];
$repository_modified = [
    'label' => __('Actualizado', 'museotech'),
    'display' => get_the_modified_date(get_option('date_format'), $repository_id),
    'datetime' => get_the_modified_date('c', $repository_id),
];
$repository_export_filename = sanitize_file_name('museotech-' . get_post_field('post_name', $repository_id) . '.json');
$repository_content = get_the_content(null, false, $repository_id);
$repository_has_content = trim(wp_strip_all_tags($repository_content)) !== '' || (function_exists('has_blocks') && has_blocks($repository_content));
?>
<div class="repository-single__layout">
    <article <?php post_class('repository-single__main'); ?>>
        <header class="repository-single__header">
            <a class="ui-button ui-button--compact repository-single__back" href="<?php echo esc_url($repository_archive_url); ?>">
                <?php esc_html_e('Volver a listado', 'museotech'); ?>
            </a>
            <div class="repository-single__header-meta">
                <p class="repository-single__published">
                    <span><?php esc_html_e('Publicado', 'museotech'); ?></span>
                    <time datetime="<?php echo esc_attr($repository_date_iso); ?>"><?php echo esc_html($repository_date_display); ?></time>
                </p>
                <div class="repository-single__badges" aria-label="<?php esc_attr_e('Formato y disponibilidad', 'museotech'); ?>">
                    <?php foreach ($repository_format_names as $repository_format_name) : ?>
                        <span class="repository-single__badge"><?php echo esc_html($repository_format_name); ?></span>
                    <?php endforeach; ?>
                    <?php if ($repository_file_url !== '' || $repository_resources) : ?>
                        <span class="repository-single__badge repository-single__badge--available"><?php esc_html_e('Disponible', 'museotech'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <h1 class="repository-single__title"><?php echo esc_html($repository_title); ?></h1>
            <?php if ($repository_authors !== '') : ?>
                <p class="repository-single__authors">
                    <span><?php esc_html_e('Autores', 'museotech'); ?>:</span>
                    <?php echo esc_html($repository_authors); ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if ($repository_has_content) : ?>
            <section class="repository-single__section repository-single__description" aria-labelledby="repository-description-title">
                <h2 id="repository-description-title"><?php esc_html_e('Descripción', 'museotech'); ?></h2>
                <div class="repository-single__content entry-content"><?php the_content(); ?></div>
            </section>
        <?php endif; ?>

        <?php if ($repository_file_url !== '') : ?>
            <section class="repository-single__section repository-single__files" aria-labelledby="repository-files-title">
                <div class="repository-single__section-heading">
                    <h2 id="repository-files-title"><?php esc_html_e('Archivos', 'museotech'); ?></h2>
                    <p><?php echo esc_html($repository_file_size !== '' ? sprintf(__('1 archivo · %s', 'museotech'), $repository_file_size) : __('Archivo adjunto', 'museotech')); ?></p>
                </div>
                <div class="repository-single__files-table-wrap">
                    <table class="repository-single__files-table">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('Nombre', 'museotech'); ?></th>
                                <th scope="col"><?php esc_html_e('Tamaño', 'museotech'); ?></th>
                                <th scope="col"><span class="screen-reader-text"><?php esc_html_e('Acción', 'museotech'); ?></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="repository-single__file-name">
                                    <a href="<?php echo esc_url($repository_file_url); ?>" download="<?php echo esc_attr($repository_file_name); ?>">
                                        <?php echo esc_html($repository_file_name); ?>
                                    </a>
                                    <?php if ($repository_file_extension !== '') : ?>
                                        <span><?php echo esc_html($repository_file_extension); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($repository_file_size !== '' ? $repository_file_size : __('No disponible', 'museotech')); ?></td>
                                <td>
                                    <a class="ui-button ui-button--compact repository-single__file-download" href="<?php echo esc_url($repository_file_url); ?>" download="<?php echo esc_attr($repository_file_name); ?>">
                                        <?php esc_html_e('Descargar', 'museotech'); ?>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </article>

    <?php
    get_template_part(
        'template-parts/sections/section',
        'repositorio',
        [
            'details' => $repository_details,
            'resources' => $repository_resources,
            'citation' => $repository_citation,
            'export_data' => $repository_export_data,
            'export_filename' => $repository_export_filename,
            'created' => $repository_created,
            'modified' => $repository_modified,
        ]
    );
    ?>
</div>