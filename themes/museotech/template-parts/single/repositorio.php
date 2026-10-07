<article <?php post_class(); ?>>
    <header><h1><?php the_title(); ?></h1></header>
    <div class="entry-content"><?php the_content(); ?></div>
    <?php get_template_part('template-parts/sections/section', 'repositorio'); ?>
</article>