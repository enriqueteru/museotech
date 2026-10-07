<article <?php post_class('content-card content-card-colaborador'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
    <?php endif; ?>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php $website = museotech_field('web'); ?>
    <?php if ($website) : ?><p><a href="<?php echo esc_url($website); ?>" rel="external noopener"><?php esc_html_e('Sitio web', 'museotech'); ?></a></p><?php endif; ?>
</article>