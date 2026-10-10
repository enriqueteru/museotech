<?php
get_header();

$blog_page_id = (int) get_option('page_for_posts');
$blog_url = $blog_page_id ? get_permalink($blog_page_id) : home_url('/blog/');
$archive_description = trim((string) museotech_option('news_description'));
$active_topic = isset($_GET['tema']) ? sanitize_key(wp_unslash($_GET['tema'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$filter_categories = [];

foreach (['museos', 'ia', 'arte', 'tecnologia', 'patrimonio'] as $category_slug) {
    $category = get_category_by_slug($category_slug);
    if ($category) {
        $filter_categories[] = $category;
    }
}
?>
<main id="main-content" class="news-archive">
    <header class="news-archive__header section-banner">
        <?php get_template_part('template-parts/components/podcast-banner-model', null, [
            'model_path' => 'assets/models/blog-pluma.glb',
            'component_class' => 'blog-banner-model',
            'model_scale' => 1.05,
            'rotation_speed' => 0.2,
            'aria_label' => __('Pluma 3D interactiva. Arrástrala o usa las flechas para girarla; el giro automático respeta las preferencias de movimiento.', 'museotech'),
        ]); ?>
        <p class="section-banner__eyebrow"><?php esc_html_e('Blog Museotech', 'museotech'); ?></p>
        <h1 class="section-banner__title"><?php esc_html_e('Todos los artículos', 'museotech'); ?></h1>
        <?php if ($archive_description !== '') : ?>
            <p class="section-banner__description"><?php echo esc_html($archive_description); ?></p>
        <?php endif; ?>
        <nav class="news-archive__filters" aria-label="<?php esc_attr_e('Filtrar artículos por tema', 'museotech'); ?>">
            <a class="ui-button ui-button--filter news-archive__filter<?php echo $active_topic === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url($blog_url); ?>"<?php echo $active_topic === '' ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Todos', 'museotech'); ?></a>
            <?php foreach ($filter_categories as $category) : ?>
                <a class="ui-button ui-button--filter news-archive__filter<?php echo $active_topic === $category->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tema', $category->slug, $blog_url)); ?>"<?php echo $active_topic === $category->slug ? ' aria-current="page"' : ''; ?>>
                <?php echo esc_html(ucfirst($category->name)); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>

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
        <p class="news-archive__status" role="status" aria-live="polite"></p>
    <?php else : ?>
        <p class="news-archive__empty"><?php esc_html_e('No hay artículos disponibles para este tema.', 'museotech'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>