<div class="content-list">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content/card', get_post_type()); ?>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No hay contenido disponible.', 'museotech'); ?></p>
    <?php endif; ?>
</div>