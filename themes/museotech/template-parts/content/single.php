<?php
$blog_page_id = (int) get_option('page_for_posts');
$blog_url = $blog_page_id ? get_permalink($blog_page_id) : home_url('/blog/');
$author_id = (int) get_the_author_meta('ID');
$categories = get_the_category();
$primary_category = $categories[0] ?? null;
$word_count = str_word_count(wp_strip_all_tags((string) get_post_field('post_content', get_the_ID())));
$reading_time = max(1, (int) ceil($word_count / 200));
$featured_image_id = get_post_thumbnail_id();
$featured_image_alt = $featured_image_id ? get_post_meta($featured_image_id, '_wp_attachment_image_alt', true) : '';
$featured_image_alt = $featured_image_alt !== '' ? $featured_image_alt : get_the_title();
$featured_image_caption = $featured_image_id ? wp_get_attachment_caption($featured_image_id) : '';
$article_content = get_the_content(null, false);

if ($featured_image_id) {
    $article_blocks = parse_blocks($article_content);
    $remove_featured_image = null;
    $remove_featured_image = static function (&$blocks) use (&$remove_featured_image, $featured_image_id): bool {
        foreach ($blocks as $index => &$block) {
            $block_image_id = absint($block['attrs']['id'] ?? 0);
            if (($block['blockName'] ?? '') === 'core/image' && $block_image_id === $featured_image_id) {
                unset($blocks[$index]);
                unset($block);

                return true;
            }

            if (! empty($block['innerBlocks']) && $remove_featured_image($block['innerBlocks'])) {
                unset($block);

                return true;
            }
        }
        unset($block);

        return false;
    };

    if ($remove_featured_image($article_blocks)) {
        $article_content = serialize_blocks($article_blocks);
    }
}

$contact_page = get_page_by_path('contacto');
$contact_url = $contact_page ? get_permalink($contact_page) : home_url('/contacto/');
$related_args = [
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post__not_in' => [get_the_ID()],
    'ignore_sticky_posts' => true,
];

if ($categories) {
    $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
}

$related_posts = new WP_Query($related_args);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
    <div class="single-article__shell">
        <a class="ui-button ui-button--compact single-article__back" href="<?php echo esc_url($blog_url); ?>">
            <?php esc_html_e('Volver a todos los artículos', 'museotech'); ?>
        </a>

        <div class="single-article__layout">
            <header class="single-article__lead">
              
                <div class="single-article__eyebrow">
                    <?php if ($primary_category) : ?>
                        <span><?php echo esc_html(ucfirst($primary_category->name)); ?></span>
                    <?php endif; ?>
                    <span><?php echo esc_html(sprintf(_n('%d minuto de lectura', '%d minutos de lectura', $reading_time, 'museotech'), $reading_time)); ?></span>
                </div>
                <h1><?php the_title(); ?></h1>
                <p class="single-article__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
            </header>

            <aside class="single-article__aside" aria-label="<?php esc_attr_e('Información del artículo', 'museotech'); ?>">
                <div class="single-article__aside-inner">
                    <section class="single-article__author">
                        <div>
                            <span><?php esc_html_e('Escrito por', 'museotech'); ?></span>
                            <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>"><?php the_author(); ?></a>

      
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(sprintf(__('Publicado el %s', 'museotech'), get_the_date())); ?></time>
                        <?php if (get_the_modified_time('U') !== get_the_time('U')) : ?>
                            <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(sprintf(__('Actualizado el %s', 'museotech'), get_the_modified_date())); ?></time>
                        <?php endif; ?>
                  
                        </div>
                    </section>
                </div>
            </aside>

            <?php if ($featured_image_id) : ?>
                <figure class="single-article__hero">
                    <?php echo wp_get_attachment_image($featured_image_id, 'full', false, [
                        'class' => 'single-article__hero-image',
                        'alt' => $featured_image_alt,
                        'loading' => 'eager',
                        'decoding' => 'async',
                    ]); ?>
                    <?php if ($featured_image_caption !== '') : ?>
                        <figcaption><?php echo wp_kses_post($featured_image_caption); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <div id="contenido" class="single-article__content entry-content">
                <?php echo apply_filters('the_content', $article_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <section class="single-article__cta" aria-labelledby="single-article-cta-title">
                <p class="single-article__cta-label"><?php esc_html_e('Participa en Museotech', 'museotech'); ?></p>
                <h2 id="single-article-cta-title"><?php esc_html_e('Participa en Museotech', 'museotech'); ?></h2>
                <p><?php esc_html_e('Comparte tus publicaciones y propuestas con el equipo de Museotech.', 'museotech'); ?></p>
                <a class="ui-button" href="<?php echo esc_url($contact_url); ?>">
                    <?php esc_html_e('Contacta con el equipo', 'museotech'); ?>
                    <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </section>
        </div>
    </div>

    <?php if ($related_posts->have_posts()) : ?>
        <section class="single-related" aria-labelledby="single-related-title">
            <div class="single-related__inner">
                <header class="single-related__header">
                    <h2 id="single-related-title"><?php esc_html_e('Sigue leyendo', 'museotech'); ?></h2>
                    <a class="ui-button" href="<?php echo esc_url($blog_url); ?>"><?php esc_html_e('Ver todos los artículos', 'museotech'); ?> <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                </header>
                <div class="single-related__grid">
                    <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                        <?php $related_categories = get_the_category(); ?>
                        <article class="single-related-card">
                            <a href="<?php the_permalink(); ?>">
                                <figure class="single-related-card__media">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('large', ['loading' => 'lazy']); ?>
                                    <?php else : ?>
                                        <span aria-hidden="true"></span>
                                    <?php endif; ?>
                                </figure>
                                <div class="single-related-card__body">
                                    <div class="single-related-card__meta">
                                        <span><?php echo esc_html($related_categories ? ucfirst($related_categories[0]->name) : __('Artículo', 'museotech')); ?></span>
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                                    </div>
                                    <h3><?php the_title(); ?></h3>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
</article>