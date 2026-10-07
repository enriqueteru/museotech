<article <?php post_class(); ?>>
    <header><h1><?php the_title(); ?></h1></header>
    <?php if (has_post_thumbnail()) : ?><?php the_post_thumbnail('large'); ?><?php endif; ?>
    <?php get_template_part('template-parts/sections/section', 'equipo'); ?>
</article>