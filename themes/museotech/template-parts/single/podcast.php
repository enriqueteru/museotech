<?php
$archive_url = get_post_type_archive_link('podcast');
$description = museotech_field('descripcion_');
$audio_url = museotech_file_url(museotech_field('audio'));
$institution = trim((string) museotech_field('institucion'));
$guest = trim((string) museotech_field('entrevistado'));
$recording_date = trim((string) museotech_field('_fecha_de_grabacion'));
$author_id = (int) get_the_author_meta('ID');
$description_text = wp_strip_all_tags((string) $description);
$excerpt = wp_trim_words($description_text !== '' ? $description_text : get_the_excerpt(), 28);
$play_label = sprintf(__('Escuchar: %s', 'museotech'), get_the_title());
$pause_label = sprintf(__('Pausar: %s', 'museotech'), get_the_title());
$related_podcasts = new WP_Query([
    'post_type' => 'podcast',
    'posts_per_page' => 3,
    'post__not_in' => [get_the_ID()],
    'ignore_sticky_posts' => true,
]);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('podcast-single'); ?>>
    <section class="podcast-single__hero">
        <div class="podcast-single__inner">
            <a class="ui-button ui-button--compact podcast-single__back" href="<?php echo esc_url($archive_url ?: home_url('/podcasts/')); ?>">
                <?php esc_html_e('Volver a todos los podcasts', 'museotech'); ?>
            </a>

            <div class="podcast-single__layout">
                <main class="podcast-single__main">
                    <header class="podcast-single__header">
                        <p class="podcast-single__eyebrow">
                            <span><?php esc_html_e('Podcast', 'museotech'); ?></span>
                            <?php if ($recording_date !== '') : ?><span><?php echo esc_html($recording_date); ?></span><?php endif; ?>
                            <?php if ($institution !== '') : ?><span><?php echo esc_html($institution); ?></span><?php endif; ?>
                        </p>
                        <h1><?php the_title(); ?></h1>
                    </header>

                    <?php if ($audio_url) : ?>
                        <section class="podcast-single__player-panel" aria-labelledby="podcast-episode-title">
                            <div class="podcast-single__player" data-podcast-single-player>
                                <button class="podcast-single__play-control" type="button" aria-label="<?php echo esc_attr($play_label); ?>" aria-pressed="false" data-play-label="<?php echo esc_attr($play_label); ?>" data-pause-label="<?php echo esc_attr($pause_label); ?>" data-play-text="<?php esc_attr_e('Escuchar', 'museotech'); ?>" data-pause-text="<?php esc_attr_e('Pausar', 'museotech'); ?>" data-podcast-single-play>
                                    <span class="podcast-single__play-icon" aria-hidden="true">
                                        <svg class="podcast-single__icon-start" viewBox="0 0 24 24"><path d="M8 5.5v13l10-6.5L8 5.5Z"/></svg>
                                        <svg class="podcast-single__icon-pause" viewBox="0 0 24 24"><path d="M7 5h4v14H7V5Zm6 0h4v14h-4V5Z"/></svg>
                                    </span>
                                    <span data-podcast-play-text><?php esc_html_e('Escuchar', 'museotech'); ?></span>
                                </button>
                                <div class="podcast-single__player-visual">
                                    <span><?php esc_html_e('Episodio', 'museotech'); ?></span>
                                    <div class="podcast-single__waveform" aria-hidden="true">
                                        <?php for ($bar = 0; $bar < 42; $bar++) : ?><i></i><?php endfor; ?>
                                    </div>
                                    <label class="screen-reader-text" for="podcast-progress-<?php the_ID(); ?>"><?php esc_html_e('Posición del audio', 'museotech'); ?></label>
                                    <input id="podcast-progress-<?php the_ID(); ?>" type="range" min="0" max="100" value="0" step="0.1" data-podcast-single-progress>
                                </div>
                                <div class="podcast-single__player-time" aria-live="off">
                                    <span data-podcast-current-time>0:00</span>
                                    <span data-podcast-duration>--:--</span>
                                </div>
                                <audio preload="metadata" src="<?php echo esc_url($audio_url); ?>" data-podcast-single-audio></audio>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div id="contenido-podcast" class="podcast-single__content entry-content">
                        <?php if ($description) : ?>
                            <?php echo wp_kses_post($description); ?>
                        <?php else : ?>
                            <?php the_content(); ?>
                        <?php endif; ?>
                    </div>
                </main>

                <aside class="podcast-single__aside" aria-label="<?php esc_attr_e('Información del podcast', 'museotech'); ?>">
                    <section class="podcast-single__author">
                        <div>
                            <span><?php esc_html_e('Publicado por', 'museotech'); ?></span>
                            <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>"><?php the_author(); ?></a>
                            <p class="podcast-single__dates">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(sprintf(__('Publicado el %s', 'museotech'), get_the_date())); ?></time>
                                <?php if (get_the_modified_time('U') !== get_the_time('U')) : ?>
                                    <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(sprintf(__('Actualizado el %s', 'museotech'), get_the_modified_date())); ?></time>
                                <?php endif; ?>
                            </p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </section>

    <?php if ($related_podcasts->have_posts()) : ?>
        <section class="podcast-single__related" aria-labelledby="podcast-related-title">
            <div class="podcast-single__related-inner">
                <header class="podcast-single__related-header">
                    <h2 id="podcast-related-title"><?php esc_html_e('Sigue escuchando', 'museotech'); ?></h2>
                    <a class="ui-button" href="<?php echo esc_url($archive_url ?: home_url('/podcasts/')); ?>"><?php esc_html_e('Ver todos los podcasts', 'museotech'); ?> <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                </header>
                <div class="podcast-single__related-grid">
                    <?php while ($related_podcasts->have_posts()) : $related_podcasts->the_post(); ?>
                        <article class="podcast-single__related-card">
                            <a href="<?php the_permalink(); ?>">
                                <figure><?php if (has_post_thumbnail()) : ?><?php the_post_thumbnail('large', ['loading' => 'lazy']); ?><?php else : ?><span aria-hidden="true"></span><?php endif; ?></figure>
                                <div class="podcast-single__related-card-body">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                                    <h3><?php the_title(); ?></h3>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
</article>
