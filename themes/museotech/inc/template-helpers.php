<?php

function museotech_field(string $name, ?int $post_id = null)
{
    $post_id = $post_id ?? get_the_ID();

    if (function_exists('get_field')) {
        return get_field($name, $post_id);
    }

    return get_post_meta($post_id, $name, true);
}

function museotech_option(string $name)
{
    $defaults = museotech_customizer_defaults();

    return get_theme_mod('museotech_' . $name, $defaults[$name] ?? '');
}

function museotech_formatted_text($text): string
{
    return wpautop(esc_html((string) $text));
}

function museotech_social_links(): array
{
    $links = [];
    foreach ([
        'YouTube' => 'youtube',
        'LinkedIn' => 'linkedin',
        'X' => 'x',
        'Instagram' => 'instagram',
    ] as $label => $name) {
        $url = museotech_option('footer_' . $name . '_url');
        if ($url) {
            $links[$label] = $url;
        }
    }

    return $links;
}

function museotech_file_url($value): string
{
    if (is_array($value)) {
        return (string) ($value['url'] ?? '');
    }

    if (is_numeric($value)) {
        return (string) wp_get_attachment_url((int) $value);
    }

    return is_string($value) ? $value : '';
}