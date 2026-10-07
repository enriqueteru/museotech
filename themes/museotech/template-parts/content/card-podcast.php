<article <?php post_class('content-card content-card-podcast'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
    <?php endif; ?>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p><?php echo esc_html((string) museotech_field('entrevistado')); ?></p>
    <?php $audio_url = museotech_file_url(museotech_field('audio')); ?>
    <?php if ($audio_url) : ?><audio controls preload="none" src="<?php echo esc_url($audio_url); ?>"><?php esc_html_e('Tu navegador no admite audio HTML.', 'museotech'); ?></audio><?php endif; ?>
</article>