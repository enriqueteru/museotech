<?php

function museotech_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Navegación principal', 'museotech'),
        'footer' => __('Navegación legal', 'museotech'),
    ]);
}
add_action('after_setup_theme', 'museotech_setup');

function museotech_add_performance_hints(array $urls, string $relation_type): array
{
    if ($relation_type === 'preconnect') {
        $urls[] = [
            'href' => 'https://cdnjs.cloudflare.com',
            'crossorigin' => '',
        ];
    }

    return $urls;
}
add_filter('wp_resource_hints', 'museotech_add_performance_hints', 10, 2);

function museotech_preload_primary_fonts(): void
{
    $fonts = [
        '/assets/fonts/america/GTAmerica-ExpandedLight.woff2',
        '/assets/fonts/satoshi/Satoshi-Regular.woff2',
    ];

    foreach ($fonts as $font) {
        $font_path = get_theme_file_path($font);
        if (! is_file($font_path)) {
            continue;
        }

        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin fetchpriority="high">' . "\n",
            esc_url(get_theme_file_uri($font))
        );
    }
}
add_action('wp_head', 'museotech_preload_primary_fonts', 1);

function museotech_defer_noncritical_styles(string $html, string $handle, string $href, string $media): string
{
    $deferred_handles = [
        'museotech-core-cookie-consent',
        'museotech-home-repository',
        'museotech-home-scroll',
    ];
    $front_page_handles = ['museotech-news-archive'];

    if (! in_array($handle, $deferred_handles, true)
        && (! is_front_page() || ! in_array($handle, $front_page_handles, true))) {
        return $html;
    }

    $style_id = esc_attr($handle . '-css');
    $style_url = esc_url($href);

    return sprintf(
        '<link rel="preload" id="%1$s" href="%2$s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
        . '<noscript><link rel="stylesheet" href="%2$s"></noscript>' . "\n",
        $style_id,
        $style_url
    );
}
add_filter('style_loader_tag', 'museotech_defer_noncritical_styles', 10, 4);

function museotech_enqueue_navigation_assets(): void
{
    $style_path = get_theme_file_path('/assets/css/navigation.css');
    $script_path = get_theme_file_path('/assets/js/navigation.js');
    $version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'museotech-navigation',
        get_theme_file_uri('/assets/css/navigation.css'),
        [],
        file_exists($style_path) ? (string) filemtime($style_path) : $version
    );

    $typography_style_path = get_theme_file_path('/assets/css/typography.css');
    wp_enqueue_style(
        'museotech-typography',
        get_theme_file_uri('/assets/css/typography.css'),
        ['museotech-navigation'],
        file_exists($typography_style_path) ? (string) filemtime($typography_style_path) : $version
    );

    $ui_components_style_path = get_theme_file_path('/assets/css/ui-components.css');
    wp_enqueue_style(
        'museotech-ui-components',
        get_theme_file_uri('/assets/css/ui-components.css'),
        ['museotech-typography'],
        file_exists($ui_components_style_path) ? (string) filemtime($ui_components_style_path) : $version
    );

    $preloader_style_path = get_theme_file_path('/assets/css/site-preloader.css');
    wp_enqueue_style(
        'museotech-site-preloader',
        get_theme_file_uri('/assets/css/site-preloader.css'),
        ['museotech-ui-components'],
        file_exists($preloader_style_path) ? (string) filemtime($preloader_style_path) : $version
    );

    if (is_page_template('templates/page-contact.php')) {
        $contact_style_path = get_theme_file_path('/assets/css/contact.css');
        wp_enqueue_style(
            'museotech-contact',
            get_theme_file_uri('/assets/css/contact.css'),
            ['museotech-navigation'],
            file_exists($contact_style_path) ? (string) filemtime($contact_style_path) : $version
        );
    }

    if (is_page_template('templates/page-legal.php')) {
        $legal_style_path = get_theme_file_path('/assets/css/legal-pages.css');
        wp_enqueue_style(
            'museotech-legal-pages',
            get_theme_file_uri('/assets/css/legal-pages.css'),
            ['museotech-navigation'],
            file_exists($legal_style_path) ? (string) filemtime($legal_style_path) : $version
        );
    }

    wp_enqueue_script(
        'museotech-gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js',
        [],
        '3.12.4',
        ['in_footer' => true]
    );

    wp_enqueue_script(
        'museotech-navigation',
        get_theme_file_uri('/assets/js/navigation.js'),
        [],
        file_exists($script_path) ? (string) filemtime($script_path) : $version,
        ['in_footer' => true]
    );

    $preloader_script_path = get_theme_file_path('/assets/js/site-preloader.js');
    wp_enqueue_script(
        'museotech-site-preloader',
        get_theme_file_uri('/assets/js/site-preloader.js'),
        [],
        file_exists($preloader_script_path) ? (string) filemtime($preloader_script_path) : $version,
        ['in_footer' => true]
    );

    if (is_post_type_archive('equipo')) {
        $team_style_path = get_theme_file_path('/assets/css/team.css');
        $team_script_path = get_theme_file_path('/assets/js/team.js');

        wp_enqueue_style(
            'museotech-team',
            get_theme_file_uri('/assets/css/team.css'),
            ['museotech-navigation'],
            file_exists($team_style_path) ? (string) filemtime($team_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-team',
            get_theme_file_uri('/assets/js/team.js'),
            ['museotech-gsap'],
            file_exists($team_script_path) ? (string) filemtime($team_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_home()) {
        $news_archive_style_path = get_theme_file_path('/assets/css/news-archive.css');
        $news_archive_script_path = get_theme_file_path('/assets/js/news-archive.js');
        wp_enqueue_style(
            'museotech-news-archive',
            get_theme_file_uri('/assets/css/news-archive.css'),
            ['museotech-navigation'],
            file_exists($news_archive_style_path) ? (string) filemtime($news_archive_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-news-archive',
            get_theme_file_uri('/assets/js/news-archive.js'),
            [],
            file_exists($news_archive_script_path) ? (string) filemtime($news_archive_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_home()) {
        $blog_banner_style_path = get_theme_file_path('/assets/css/blog-banner-model.css');
        wp_enqueue_style(
            'museotech-blog-banner-model',
            get_theme_file_uri('/assets/css/blog-banner-model.css'),
            ['museotech-navigation'],
            file_exists($blog_banner_style_path) ? (string) filemtime($blog_banner_style_path) : $version
        );

        $blog_banner_script_path = get_theme_file_path('/assets/js/home-hero-model.js');
        wp_enqueue_script(
            'museotech-home-hero-model',
            get_theme_file_uri('/assets/js/home-hero-model.js'),
            is_front_page() ? ['museotech-scroll-trigger'] : [],
            file_exists($blog_banner_script_path) ? (string) filemtime($blog_banner_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_post_type_archive('repositorios')) {
        $repository_archive_style_path = get_theme_file_path('/assets/css/repository-archive.css');

        wp_enqueue_style(
            'museotech-repository-archive',
            get_theme_file_uri('/assets/css/repository-archive.css'),
            ['museotech-navigation'],
            file_exists($repository_archive_style_path) ? (string) filemtime($repository_archive_style_path) : $version
        );
    }

    if (is_singular('repositorios')) {
        $repository_single_style_path = get_theme_file_path('/assets/css/repository-single.css');
        $repository_single_script_path = get_theme_file_path('/assets/js/repository-single.js');

        wp_enqueue_style(
            'museotech-repository-single',
            get_theme_file_uri('/assets/css/repository-single.css'),
            ['museotech-navigation'],
            file_exists($repository_single_style_path) ? (string) filemtime($repository_single_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-repository-single',
            get_theme_file_uri('/assets/js/repository-single.js'),
            [],
            file_exists($repository_single_script_path) ? (string) filemtime($repository_single_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_post_type_archive('repositorios')) {
        $repository_banner_style_path = get_theme_file_path('/assets/css/repository-banner-model.css');
        wp_enqueue_style(
            'museotech-repository-banner-model',
            get_theme_file_uri('/assets/css/repository-banner-model.css'),
            ['museotech-navigation'],
            file_exists($repository_banner_style_path) ? (string) filemtime($repository_banner_style_path) : $version
        );

        $repository_banner_script_path = get_theme_file_path('/assets/js/home-hero-model.js');
        wp_enqueue_script(
            'museotech-home-hero-model',
            get_theme_file_uri('/assets/js/home-hero-model.js'),
            [],
            file_exists($repository_banner_script_path) ? (string) filemtime($repository_banner_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_post_type_archive('colaborador')) {
        $collaborator_archive_style_path = get_theme_file_path('/assets/css/collaborator-archive.css');

        wp_enqueue_style(
            'museotech-collaborator-archive',
            get_theme_file_uri('/assets/css/collaborator-archive.css'),
            ['museotech-navigation'],
            file_exists($collaborator_archive_style_path) ? (string) filemtime($collaborator_archive_style_path) : $version
        );
    }

    if (is_post_type_archive('colaborador')) {
        $collaborator_banner_style_path = get_theme_file_path('/assets/css/collaborator-banner-model.css');
        wp_enqueue_style(
            'museotech-collaborator-banner-model',
            get_theme_file_uri('/assets/css/collaborator-banner-model.css'),
            ['museotech-navigation'],
            file_exists($collaborator_banner_style_path) ? (string) filemtime($collaborator_banner_style_path) : $version
        );

        $collaborator_banner_script_path = get_theme_file_path('/assets/js/home-hero-model.js');
        wp_enqueue_script(
            'museotech-home-hero-model',
            get_theme_file_uri('/assets/js/home-hero-model.js'),
            [],
            file_exists($collaborator_banner_script_path) ? (string) filemtime($collaborator_banner_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_singular('post')) {
        $single_post_style_path = get_theme_file_path('/assets/css/single-post.css');
        $single_post_script_path = get_theme_file_path('/assets/js/single-post.js');

        wp_enqueue_style(
            'museotech-single-post',
            get_theme_file_uri('/assets/css/single-post.css'),
            ['museotech-navigation'],
            file_exists($single_post_style_path) ? (string) filemtime($single_post_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-single-post',
            get_theme_file_uri('/assets/js/single-post.js'),
            [],
            file_exists($single_post_script_path) ? (string) filemtime($single_post_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_post_type_archive('podcast')) {
        $podcast_archive_style_path = get_theme_file_path('/assets/css/podcast-archive.css');
        $podcast_archive_script_path = get_theme_file_path('/assets/js/podcast-archive.js');

        wp_enqueue_style(
            'museotech-podcast-archive',
            get_theme_file_uri('/assets/css/podcast-archive.css'),
            ['museotech-navigation'],
            file_exists($podcast_archive_style_path) ? (string) filemtime($podcast_archive_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-podcast-archive',
            get_theme_file_uri('/assets/js/podcast-archive.js'),
            [],
            file_exists($podcast_archive_script_path) ? (string) filemtime($podcast_archive_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_singular('podcast')) {
        $podcast_single_style_path = get_theme_file_path('/assets/css/podcast-single.css');
        $podcast_single_script_path = get_theme_file_path('/assets/js/podcast-single.js');

        wp_enqueue_style(
            'museotech-podcast-single',
            get_theme_file_uri('/assets/css/podcast-single.css'),
            ['museotech-navigation'],
            file_exists($podcast_single_style_path) ? (string) filemtime($podcast_single_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-podcast-single',
            get_theme_file_uri('/assets/js/podcast-single.js'),
            [],
            file_exists($podcast_single_script_path) ? (string) filemtime($podcast_single_script_path) : $version,
            ['in_footer' => true]
        );
    }

    if (is_post_type_archive('podcast')) {
        $podcast_banner_style_path = get_theme_file_path('/assets/css/podcast-banner-model.css');
        $podcast_banner_script_path = get_theme_file_path('/assets/js/home-hero-model.js');

        wp_enqueue_style(
            'museotech-podcast-banner-model',
            get_theme_file_uri('/assets/css/podcast-banner-model.css'),
            ['museotech-navigation'],
            file_exists($podcast_banner_style_path) ? (string) filemtime($podcast_banner_style_path) : $version
        );

        wp_enqueue_script(
            'museotech-home-hero-model',
            get_theme_file_uri('/assets/js/home-hero-model.js'),
            [],
            file_exists($podcast_banner_script_path) ? (string) filemtime($podcast_banner_script_path) : $version,
            ['in_footer' => true]
        );
    }


    if (is_home() || is_post_type_archive('repositorios') || is_post_type_archive('colaborador') || is_post_type_archive('podcast')) {
        $section_banner_style_path = get_theme_file_path('/assets/css/section-banners.css');
        wp_enqueue_style(
            'museotech-section-banners',
            get_theme_file_uri('/assets/css/section-banners.css'),
            ['museotech-typography'],
            file_exists($section_banner_style_path) ? (string) filemtime($section_banner_style_path) : $version
        );
    }

    if (! is_front_page()) {
        if (is_home() || is_post_type_archive('podcast') || is_post_type_archive('repositorios') || is_post_type_archive('colaborador')) {
            $section_banner_animation_path = get_theme_file_path('/assets/js/home-text-animation.js');
            wp_enqueue_script(
                'museotech-scroll-trigger',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js',
                ['museotech-gsap'],
                '3.12.4',
                ['in_footer' => true]
            );

            wp_enqueue_script(
                'museotech-section-banner-text-animation',
                get_theme_file_uri('/assets/js/home-text-animation.js'),
                ['museotech-scroll-trigger'],
                file_exists($section_banner_animation_path) ? (string) filemtime($section_banner_animation_path) : $version,
                ['in_footer' => true]
            );
        }

        return;
    }

    $home_page_style_path = get_theme_file_path('/assets/css/home.css');
    $home_style_path = get_theme_file_path('/assets/css/home-scroll.css');
    $home_script_path = get_theme_file_path('/assets/js/home-scroll.js');
    $home_marquee_script_path = get_theme_file_path('/assets/js/home-marquee.js');
    $home_text_animation_script_path = get_theme_file_path('/assets/js/home-text-animation.js');
    $home_hero_model_script_path = get_theme_file_path('/assets/js/home-hero-model.js');

    wp_enqueue_style(
        'museotech-home',
        get_theme_file_uri('/assets/css/home.css'),
        ['museotech-navigation'],
        file_exists($home_page_style_path) ? (string) filemtime($home_page_style_path) : $version
    );

    $home_repository_style_path = get_theme_file_path('/assets/css/home-repository.css');
    wp_enqueue_style(
        'museotech-home-repository',
        get_theme_file_uri('/assets/css/home-repository.css'),
        ['museotech-home'],
        file_exists($home_repository_style_path) ? (string) filemtime($home_repository_style_path) : $version
    );

    if (! is_home()) {
        $news_archive_style_path = get_theme_file_path('/assets/css/news-archive.css');
        wp_enqueue_style(
            'museotech-news-archive',
            get_theme_file_uri('/assets/css/news-archive.css'),
            ['museotech-home'],
            file_exists($news_archive_style_path) ? (string) filemtime($news_archive_style_path) : $version
        );
    }

    wp_enqueue_style(
        'museotech-home-scroll',
        get_theme_file_uri('/assets/css/home-scroll.css'),
        ['museotech-navigation'],
        file_exists($home_style_path) ? (string) filemtime($home_style_path) : $version
    );

    wp_enqueue_script(
        'museotech-scroll-trigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js',
        ['museotech-gsap'],
        '3.12.4',
        ['in_footer' => true]
    );

    wp_enqueue_script(
        'museotech-home-scroll',
        get_theme_file_uri('/assets/js/home-scroll.js'),
        ['museotech-scroll-trigger'],
        file_exists($home_script_path) ? (string) filemtime($home_script_path) : $version,
        ['in_footer' => true]
    );

    wp_enqueue_script(
        'museotech-home-marquee',
        get_theme_file_uri('/assets/js/home-marquee.js'),
        ['museotech-gsap'],
        file_exists($home_marquee_script_path) ? (string) filemtime($home_marquee_script_path) : $version,
        ['in_footer' => true]
    );

    wp_enqueue_script(
        'museotech-home-text-animation',
        get_theme_file_uri('/assets/js/home-text-animation.js'),
        ['museotech-scroll-trigger'],
        file_exists($home_text_animation_script_path) ? (string) filemtime($home_text_animation_script_path) : $version,
        ['in_footer' => true]
    );

    wp_enqueue_script(
        'museotech-home-hero-model',
        get_theme_file_uri('/assets/js/home-hero-model.js'),
        ['museotech-scroll-trigger'],
        file_exists($home_hero_model_script_path) ? (string) filemtime($home_hero_model_script_path) : $version,
        ['in_footer' => true]
    );

}
add_action('wp_enqueue_scripts', 'museotech_enqueue_navigation_assets');

function museotech_home_hero_model_import_map(): void
{
    if (! is_front_page() && ! is_home() && ! is_post_type_archive('podcast') && ! is_post_type_archive('colaborador') && ! is_post_type_archive('repositorios')) {
        return;
    }

    $import_map = [
        'imports' => [
            'three' => get_theme_file_uri('/node_modules/three/build/three.module.js'),
            'three/addons/' => get_theme_file_uri('/node_modules/three/examples/jsm/'),
        ],
    ];

    echo '<script type="importmap">' . wp_json_encode($import_map) . '</script>' . "\n";
}
add_action('wp_head', 'museotech_home_hero_model_import_map', 0);

function museotech_home_hero_model_module_tag(string $tag, string $handle): string
{
    if ($handle !== 'museotech-home-hero-model') {
        return $tag;
    }

    return str_replace('<script ', '<script type="module" ', $tag);
}
add_filter('script_loader_tag', 'museotech_home_hero_model_module_tag', 10, 2);

function museotech_filter_blog_archive(WP_Query $query): void
{
    if (is_admin() || ! $query->is_main_query() || ! $query->is_home()) {
        return;
    }

    $query->set('posts_per_page', 9);

    if (! isset($_GET['tema'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return;
    }

    $topic = sanitize_key(wp_unslash($_GET['tema'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ($topic && get_category_by_slug($topic)) {
        $query->set('category_name', $topic);
    }
}
add_action('pre_get_posts', 'museotech_filter_blog_archive');

function museotech_activate(): void
{
    $locations = (array) get_theme_mod('nav_menu_locations', []);

    foreach (['primary' => 'Principal', 'footer' => 'Legal'] as $location => $menu_name) {
        $menu = wp_get_nav_menu_object($menu_name);
        if ($menu) {
            $locations[$location] = (int) $menu->term_id;
        }
    }

    set_theme_mod('nav_menu_locations', $locations);
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'museotech_activate');
