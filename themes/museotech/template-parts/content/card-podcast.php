<?php
$audio_url = museotech_file_url(museotech_field('audio'));
$guest = trim((string) museotech_field('entrevistado'));
$play_label = sprintf(__('Reproducir: %s', 'museotech'), get_the_title());
$pause_label = sprintf(__('Pausar: %s', 'museotech'), get_the_title());
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('podcast-archive-card'); ?>>
    <div class="podcast-archive-card__media">
        <a class="podcast-archive-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', ['loading' => 'lazy']); ?>
            <?php else : ?>
                <span class="podcast-archive-card__art" aria-hidden="true">
                    <?php for ($bar = 0; $bar < 14; $bar++) : ?><i></i><?php endfor; ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ($audio_url) : ?>
            <button class="podcast-archive-card__play" type="button" aria-label="<?php echo esc_attr($play_label); ?>" aria-pressed="false" data-play-label="<?php echo esc_attr($play_label); ?>" data-pause-label="<?php echo esc_attr($pause_label); ?>" data-podcast-play>
                <svg class="podcast-archive-card__play-icon podcast-archive-card__play-icon--start" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10-6.5L8 5.5Z"/></svg>
                <svg class="podcast-archive-card__play-icon podcast-archive-card__play-icon--pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h4v14H7V5Zm6 0h4v14h-4V5Z"/></svg>
            </button>
            <audio preload="none" src="<?php echo esc_url($audio_url); ?>" data-podcast-audio></audio>
        <?php else : ?>
            <a class="podcast-archive-card__play" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf(__('Abrir: %s', 'museotech'), get_the_title())); ?>">
                <svg class="podcast-archive-card__play-icon podcast-archive-card__play-icon--start" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10-6.5L8 5.5Z"/></svg>
            </a>
        <?php endif; ?>
    </div>

    <div class="podcast-archive-card__body">
        <div class="podcast-archive-card__meta">
            <span><?php esc_html_e('Podcast', 'museotech'); ?></span>
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d M y')); ?></time>
        </div>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php if ($guest !== '') : ?>
            <p><?php echo esc_html($guest); ?></p>
        <?php endif; ?>
    </div>
</article>