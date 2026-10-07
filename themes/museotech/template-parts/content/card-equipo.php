<article <?php post_class('content-card content-card-equipo'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
    <?php endif; ?>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
</article>