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

/**
 * Render one of the locally selected Lucide 1.53.0 icons.
 *
 * @see https://lucide.dev License notices: assets/icons/LUCIDE-LICENSE.txt.
 */
function museotech_ui_icon(string $name): string
{
    $paths = [
        'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'external-link' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
    ];

    if (! isset($paths[$name])) {
        return '';
    }

    return sprintf(
        '<svg class="ui-icon ui-icon--%1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
        esc_attr($name),
        $paths[$name]
    );
}