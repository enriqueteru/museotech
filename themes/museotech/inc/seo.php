<?php

/**
 * Check whether an SEO plugin already owns document metadata.
 */
function museotech_seo_plugin_is_active(): bool
{
    static $is_active = null;

    if ($is_active !== null) {
        return $is_active;
    }

    $plugin_markers = [
        'wordpress-seo/',
        'seo-by-rank-math/',
        'all-in-one-seo-pack/',
        'wp-seopress/',
        'autodescription/',
    ];
    $active_plugins = (array) get_option('active_plugins', []);
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, array_keys((array) get_site_option('active_sitewide_plugins', [])));
    }

    foreach ($active_plugins as $plugin) {
        foreach ($plugin_markers as $marker) {
            if (str_contains((string) $plugin, $marker)) {
                $is_active = true;
                return $is_active;
            }
        }
    }

    $is_active = defined('WPSEO_VERSION')
        || defined('RANK_MATH_VERSION')
        || defined('SEOPRESS_VERSION')
        || defined('AIOSEO_VERSION')
        || class_exists('The_SEO_Framework\\Load');

    return $is_active;
}

/**
 * Return a short, plain-text description for the current query.
 */
function museotech_seo_description(): string
{
    $description = '';

    if (is_front_page()) {
        $description = (string) museotech_option('hero_description');
    } elseif (is_home()) {
        $description = (string) museotech_option('news_description');
    } elseif (is_post_type_archive('podcast')) {
        $description = (string) museotech_option('podcast_description');
    } elseif (is_post_type_archive('repositorios')) {
        $description = (string) museotech_option('archive_repositorios_heading');
    } elseif (is_post_type_archive('colaborador')) {
        $description = (string) museotech_option('archive_colaborador_heading');
    } elseif (is_post_type_archive('equipo')) {
        $description = __('Conoce al equipo de investigación de Museotech y a las personas que trabajan en tecnología, museos y cultura.', 'museotech');
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $description = $term instanceof WP_Term ? term_description($term) : '';
    } elseif (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            if ($post->post_type === 'podcast') {
                $description = (string) museotech_field('descripcion_', $post->ID);
            } elseif (in_array($post->post_type, ['equipo', 'colaborador'], true)) {
                $description = (string) museotech_field('bio', $post->ID);
            }

            if (trim($description) === '') {
                $description = has_excerpt($post) ? get_the_excerpt($post) : $post->post_content;
            }
        }
    } elseif (is_search()) {
        $description = sprintf(__('Resultados de búsqueda en %s.', 'museotech'), get_bloginfo('name'));
    }

    if (trim($description) === '') {
        $description = (string) get_bloginfo('description');
    }

    $description = html_entity_decode(wp_strip_all_tags(strip_shortcodes($description)), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    $description = trim((string) preg_replace('/\s+/u', ' ', $description));
    if (function_exists('mb_substr')) {
        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157);
            $last_space = mb_strrpos($description, ' ');
            if ($last_space !== false) {
                $description = mb_substr($description, 0, $last_space);
            }
            $description .= '…';
        }
    } else {
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157);
            $last_space = strrpos($description, ' ');
            if ($last_space !== false) {
                $description = substr($description, 0, $last_space);
            }
            $description .= '…';
        }
    }

    return trim($description);
}

/**
 * Return the canonical URL for non-singular views. WordPress core supplies it for singular posts.
 */
function museotech_archive_canonical_url(): string
{
    if (is_front_page()) {
        return home_url('/');
    }

    if (is_search() || is_404() || is_singular()) {
        return '';
    }

    $page_number = max(1, (int) get_query_var('paged'));
    $url = get_pagenum_link($page_number, false);

    if (is_post_type_archive('repositorios')) {
        $url = get_post_type_archive_link('repositorios') ?: $url;
    }

    return (string) $url;
}

/**
 * Print descriptions, social metadata, archive canonicals and basic structured data.
 */
function museotech_print_seo_metadata(): void
{
    if (museotech_seo_plugin_is_active() || is_404()) {
        return;
    }

    $description = museotech_seo_description();
    $title = html_entity_decode(trim(wp_get_document_title()), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    $canonical_url = is_singular() ? (string) wp_get_canonical_url() : museotech_archive_canonical_url();
    $metadata_url = $canonical_url;
    if ($metadata_url === '') {
        $metadata_url = is_search()
            ? add_query_arg('s', get_search_query(), home_url('/'))
            : home_url('/');
    }
    $image = '';
    $image_alt = '';

    if (is_singular() && has_post_thumbnail()) {
        $image_id = (int) get_post_thumbnail_id();
        $image_source = wp_get_attachment_image_src($image_id, 'full');
        if ($image_source) {
            $image = (string) $image_source[0];
            $image_alt = (string) get_post_meta($image_id, '_wp_attachment_image_alt', true);
        }
    }

    if ($image === '') {
        $logo_id = (int) get_theme_mod('custom_logo');
        if ($logo_id) {
            $image = (string) wp_get_attachment_image_url($logo_id, 'full');
            $image_alt = (string) get_bloginfo('name');
        }
    }

    $language = str_replace('_', '-', (string) get_bloginfo('language'));
    $open_graph_locale = str_replace('-', '_', (string) get_locale());
    $social_links = function_exists('museotech_social_links') ? array_values(museotech_social_links()) : [];
    $organization = [
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
    ];
    if ($social_links) {
        $organization['sameAs'] = $social_links;
    }

    $website = [
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'inLanguage' => $language,
        'publisher' => ['@id' => home_url('/#organization')],
    ];
    $page_type = is_post_type_archive() || is_home() || is_archive() ? 'CollectionPage' : 'WebPage';
    $page_schema = [
        '@type' => $page_type,
        '@id' => $metadata_url . '#webpage',
        'url' => $metadata_url,
        'name' => $title,
        'description' => $description,
        'isPartOf' => ['@id' => home_url('/#website')],
        'inLanguage' => $language,
    ];

    $schema = [$organization, $website, $page_schema];
    if (is_singular('post')) {
        $article = [
            '@type' => 'BlogPosting',
            '@id' => $metadata_url . '#article',
            'mainEntityOfPage' => ['@id' => $metadata_url . '#webpage'],
            'headline' => get_the_title(),
            'description' => $description,
            'datePublished' => get_the_date(DATE_W3C),
            'dateModified' => get_the_modified_date(DATE_W3C),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author(),
            ],
            'publisher' => ['@id' => home_url('/#organization')],
            'inLanguage' => $language,
        ];
        if ($image !== '') {
            $article['image'] = [$image];
        }
        $schema[] = $article;
    }

    $schema_data = [
        '@context' => 'https://schema.org',
        '@graph' => $schema,
    ];
    ?>
    <?php if ($description !== '') : ?><meta name="description" content="<?php echo esc_attr($description); ?>"><?php endif; ?>
    <?php if ($canonical_url !== '' && ! is_singular() && ! is_search()) : ?><link rel="canonical" href="<?php echo esc_url($canonical_url); ?>"><?php endif; ?>
    <meta property="og:locale" content="<?php echo esc_attr($open_graph_locale); ?>">
    <meta property="og:type" content="<?php echo is_singular('post') ? 'article' : 'website'; ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <?php if ($description !== '') : ?><meta property="og:description" content="<?php echo esc_attr($description); ?>"><?php endif; ?>
    <meta property="og:url" content="<?php echo esc_url($metadata_url); ?>">
    <?php if ($image !== '') : ?>
        <meta property="og:image" content="<?php echo esc_url($image); ?>">
        <?php if ($image_alt !== '') : ?><meta property="og:image:alt" content="<?php echo esc_attr($image_alt); ?>"><?php endif; ?>
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php else : ?>
        <meta name="twitter:card" content="summary">
    <?php endif; ?>
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <?php if ($description !== '') : ?><meta name="twitter:description" content="<?php echo esc_attr($description); ?>"><?php endif; ?>
    <script type="application/ld+json"><?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    <?php
}
add_action('wp_head', 'museotech_print_seo_metadata', 2);

/**
 * Avoid indexing parameterized copies of the repository archive.
 */
function museotech_seo_robots(array $robots): array
{
    if (museotech_seo_plugin_is_active()) {
        return $robots;
    }

    if (is_404() || is_search() || (is_post_type_archive('repositorios') && isset($_GET['formato']))) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }

    return $robots;
}
add_filter('wp_robots', 'museotech_seo_robots');
