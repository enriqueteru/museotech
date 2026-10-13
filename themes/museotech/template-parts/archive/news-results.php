<?php if (have_posts()) : ?>
    <div class="news-archive__grid">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content/news', 'card'); ?>
        <?php endwhile; ?>
    </div>
    <?php $previous_link = get_previous_posts_link(__('Artículos anteriores', 'museotech')); ?>
    <?php $next_link = get_next_posts_link(__('Ver más artículos', 'museotech')); ?>
    <?php $previous_link = $previous_link ? str_replace('<a ', '<a class="ui-button" ', $previous_link) : ''; ?>
    <?php $next_link = $next_link ? str_replace('<a ', '<a class="ui-button" ', $next_link) : ''; ?>
    <?php if ($previous_link || $next_link) : ?>
        <nav class="news-archive__pagination" aria-label="<?php esc_attr_e('Paginación de artículos', 'museotech'); ?>">
            <?php if ($previous_link) : ?><span class="news-archive__pagination-previous"><?php echo $previous_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php endif; ?>
            <?php if ($next_link) : ?><span class="news-archive__pagination-next"><?php echo $next_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php endif; ?>
        </nav>
    <?php endif; ?>
<?php else : ?>
    <p class="news-archive__empty"><?php esc_html_e('No hay artículos disponibles para este tema.', 'museotech'); ?></p>
<?php endif; ?>
