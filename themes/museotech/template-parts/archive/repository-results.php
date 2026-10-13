<?php $repository_archive_url = get_post_type_archive_link('repositorios') ?: home_url('/repositorios/'); ?>
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
