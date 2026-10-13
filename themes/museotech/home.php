<?php
if (isset($_GET['museotech_archive_fragment'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    nocache_headers();
    header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
    header('X-Museotech-Archive-Fragment: 1');
    get_template_part('template-parts/archive/news-results');
    return;
}

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
<main id="main-content" class="news-archive" data-archive-base="<?php echo esc_url($blog_url); ?>">
    <header class="news-archive__header section-banner">
        <div class="section-banner__copy">
            <h1 class="section-banner__title"><?php esc_html_e('Todos los artículos', 'museotech'); ?></h1>
            <?php if ($archive_description !== '') : ?>
                <p class="section-banner__description"><?php echo esc_html($archive_description); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <nav class="news-archive__filters" aria-label="<?php esc_attr_e('Filtrar artículos por tema', 'museotech'); ?>">
        <a class="ui-button ui-button--filter news-archive__filter<?php echo $active_topic === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url($blog_url); ?>"<?php echo $active_topic === '' ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Todos', 'museotech'); ?></a>
        <?php foreach ($filter_categories as $category) : ?>
            <a class="ui-button ui-button--filter news-archive__filter<?php echo $active_topic === $category->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tema', $category->slug, $blog_url)); ?>"<?php echo $active_topic === $category->slug ? ' aria-current="page"' : ''; ?>>
                <?php echo esc_html(ucfirst($category->name)); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="news-archive__results" data-archive-results>
        <?php get_template_part('template-parts/archive/news-results'); ?>
    </div>
    <p class="news-archive__status" role="status" aria-live="polite"></p>
</main>
<?php get_footer(); ?>
