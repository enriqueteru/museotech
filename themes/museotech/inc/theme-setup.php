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

    if (is_page_template('templates/page-contact.php')) {
        $contact_style_path = get_theme_file_path('/assets/css/contact.css');
        wp_enqueue_style(
            'museotech-contact',
            get_theme_file_uri('/assets/css/contact.css'),
            ['museotech-navigation'],
            file_exists($contact_style_path) ? (string) filemtime($contact_style_path) : $version
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

    if (! is_front_page()) {
        return;
    }

    $home_style_path = get_theme_file_path('/assets/css/home-scroll.css');
    $home_script_path = get_theme_file_path('/assets/js/home-scroll.js');

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
}
add_action('wp_enqueue_scripts', 'museotech_enqueue_navigation_assets');

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