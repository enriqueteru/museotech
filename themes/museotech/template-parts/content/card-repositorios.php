<article <?php post_class('content-card content-card-repositorio'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
    <?php endif; ?>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php the_excerpt(); ?>
    <p><?php echo esc_html((string) museotech_field('autores')); ?></p>
    <a class="ui-button ui-button--compact" href="<?php the_permalink(); ?>"><?php esc_html_e('Ver recurso', 'museotech'); ?></a>
</article>