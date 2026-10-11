<?php global $wp_query; ?>
<?php get_header(); ?>
<main id="main-content" class="podcast-archive">
    <header class="podcast-archive__header section-banner">
        <div class="section-banner__copy">
            <h1 class="section-banner__title"><?php esc_html_e('Todos los podcasts', 'museotech'); ?></h1>
            <p class="section-banner__description"><?php esc_html_e('Conversaciones sobre tecnología, museos y sociedad para escuchar cuando quieras.', 'museotech'); ?></p>
        </div>
        <?php get_template_part('template-parts/components/podcast-banner-model'); ?>
    </header>

    <?php if (have_posts()) : ?>
        <section class="podcast-archive__episodes" aria-labelledby="podcast-archive-episodes-title">
            <header class="podcast-archive__episodes-header">
                <h2 id="podcast-archive-episodes-title"><?php esc_html_e('Episodios', 'museotech'); ?></h2>
                <p><?php echo esc_html(sprintf(_n('%d episodio', '%d episodios', (int) $wp_query->found_posts, 'museotech'), (int) $wp_query->found_posts)); ?></p>
            </header>
            <div class="podcast-archive__grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content/card', 'podcast'); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination([
                'mid_size' => 1,
                'prev_text' => __('Anteriores', 'museotech'),
                'next_text' => __('Siguientes', 'museotech'),
            ]); ?>
        </section>
    <?php else : ?>
        <p class="podcast-archive__empty"><?php esc_html_e('Todavía no hay podcasts disponibles.', 'museotech'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
