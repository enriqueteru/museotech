<?php

if (! defined('WP_CLI') || ! WP_CLI) {
    exit;
}

function museotech_migration_render_widget(array $node): string
{
    $settings = $node['settings'] ?? [];

    switch ($node['widgetType'] ?? '') {
        case 'heading':
            $title = isset($settings['title']) ? wp_kses_post($settings['title']) : '';
            $tag = sanitize_key($settings['header_size'] ?? 'h2');
            if (! in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                $tag = 'h2';
            }

            return $title !== '' ? sprintf('<%1$s>%2$s</%1$s>', $tag, $title) : '';

        case 'text-editor':
            return is_string($settings['editor'] ?? null) ? wp_kses_post($settings['editor']) : '';

        case 'button':
            $label = isset($settings['text']) ? wp_kses_post($settings['text']) : '';
            $url = $settings['link']['url'] ?? '';
            if ($label === '' || ! is_string($url) || $url === '') {
                return '';
            }

            return sprintf('<p><a href="%1$s">%2$s</a></p>', esc_url($url), $label);

        case 'image':
            $image = $settings['image'] ?? [];
            $attachment_id = absint($image['id'] ?? 0);
            if ($attachment_id) {
                return wp_get_attachment_image($attachment_id, 'large') ?: '';
            }

            $url = is_array($image) ? ($image['url'] ?? '') : '';
            if (! is_string($url) || $url === '') {
                return '';
            }

            return sprintf('<img src="%1$s" alt="%2$s">', esc_url($url), esc_attr($image['alt'] ?? ''));

        case 'form':
            return '[museotech_contact_form]';

        case 'social-icons':
            $links = [];
            foreach (($settings['social_icon_list'] ?? []) as $social) {
                $url = $social['link']['url'] ?? '';
                $icon = $social['social_icon']['value'] ?? '';
                if (! is_string($url) || $url === '') {
                    continue;
                }

                $label = is_string($icon) ? ucwords(str_replace(['fab fa-', 'fa-brands fa-', '-'], ['', '', ' '], $icon)) : __('Red social', 'museotech-core');
                $links[] = sprintf('<li><a href="%1$s" rel="external noopener">%2$s</a></li>', esc_url($url), esc_html($label));
            }

            return $links ? '<ul>' . implode('', $links) . '</ul>' : '';

        case 'icon':
        case 'posts':
        case 'loop-grid':
            return '';

        default:
            return '';
    }
}

function museotech_migration_render_elementor_nodes(array $nodes): string
{
    $output = '';

    foreach ($nodes as $node) {
        if (! is_array($node)) {
            continue;
        }

        if (($node['elType'] ?? '') === 'widget') {
            $output .= museotech_migration_render_widget($node);
            continue;
        }

        $children = museotech_migration_render_elementor_nodes($node['elements'] ?? []);
        if ($children === '') {
            continue;
        }

        if (($node['elType'] ?? '') === 'section') {
            $output .= '<section>' . $children . '</section>';
        } else {
            $output .= '<div>' . $children . '</div>';
        }
    }

    return $output;
}

function museotech_migration_elementor_content(string $json): string
{
    $elements = json_decode($json, true);
    if (! is_array($elements)) {
        return '';
    }

    return museotech_migration_render_elementor_nodes($elements);
}

function museotech_migration_disable_elementor_mode(int $post_id): void
{
    $edit_mode = get_post_meta($post_id, '_elementor_edit_mode', true);
    if (! is_string($edit_mode) || $edit_mode === '') {
        return;
    }

    if (! metadata_exists('post', $post_id, '_museotech_pre_migration_elementor_edit_mode')) {
        add_post_meta($post_id, '_museotech_pre_migration_elementor_edit_mode', $edit_mode, true);
    }

    delete_post_meta($post_id, '_elementor_edit_mode');
}

$dry_run = getenv('MUSEOTECH_MIGRATION_DRY_RUN') === '1';
$documents = get_posts([
    'post_type' => ['page', 'post'],
    'post_status' => ['publish', 'private', 'draft', 'future'],
    'posts_per_page' => -1,
    'meta_key' => '_elementor_data',
    'orderby' => 'ID',
    'order' => 'ASC',
]);
$changed = 0;
$skipped = 0;

foreach ($documents as $document) {
    $json = (string) get_post_meta($document->ID, '_elementor_data', true);
    if ($json === '') {
        $skipped++;
        WP_CLI::line("SKIP {$document->ID} no Elementor data");
        continue;
    }

    $hash = hash('sha256', $json);
    if (get_post_meta($document->ID, '_museotech_elementor_migration_hash', true) === $hash) {
        if (! $dry_run) {
            museotech_migration_disable_elementor_mode((int) $document->ID);
        }
        $skipped++;
        WP_CLI::line("SKIP {$document->ID} already migrated");
        continue;
    }

    $current_content = (string) $document->post_content;
    $new_content = $current_content;
    $reason = '';

    if ($document->post_type === 'post' && trim($current_content) !== '') {
        $reason = 'native article body preserved';
    } elseif ($document->post_name === 'home' && trim($current_content) !== '') {
        $reason = 'homepage now uses Museotech Customizer sections';
    } elseif ($document->post_name === 'contacto') {
        if (! has_shortcode($current_content, 'museotech_contact_form')) {
            $new_content = rtrim($current_content) . "\n\n[museotech_contact_form]\n";
        }
        $reason = 'native contact content plus Museotech form';
    } elseif ($document->post_type === 'page' && trim($current_content) !== '') {
        $reason = 'existing native page content preserved';
    } else {
        $acf_content = $document->post_type === 'page' && function_exists('get_field')
            ? (string) get_field('contenido', $document->ID)
            : '';

        if (trim($acf_content) !== '') {
            $new_content = wp_kses_post($acf_content);
            if (! str_contains($new_content, '<p') && ! str_contains($new_content, '<h')) {
                $new_content = wpautop($new_content);
            }
            $reason = 'ACF contenido moved to native post_content';
        } else {
            $new_content = museotech_migration_elementor_content($json);
            $reason = 'Elementor widgets converted to native HTML';
        }
    }

    WP_CLI::line(sprintf(
        '%1$s %2$d %3$s bytes=%4$d reason=%5$s',
        $dry_run ? 'DRY-RUN' : 'MIGRATE',
        $document->ID,
        $document->post_title,
        strlen($new_content),
        $reason . (get_post_meta($document->ID, '_elementor_edit_mode', true) ? '; builder mode will be disabled' : '')
    ));

    if (! $dry_run && $new_content !== $current_content) {
        if (! metadata_exists('post', $document->ID, '_museotech_pre_migration_content')) {
            add_post_meta($document->ID, '_museotech_pre_migration_content', $current_content, true);
        }

        $result = wp_update_post([
            'ID' => $document->ID,
            'post_content' => $new_content,
        ], true);
        if (is_wp_error($result)) {
            WP_CLI::warning("Could not update {$document->ID}: {$result->get_error_message()}");
            $skipped++;
            continue;
        }
    }

    if (! $dry_run) {
        update_post_meta($document->ID, '_museotech_elementor_migration_hash', $hash);
        museotech_migration_disable_elementor_mode((int) $document->ID);
    }
    $changed++;
}

WP_CLI::success(sprintf(
    '%1$s: %2$d documents processed, %3$d skipped. Elementor source metadata was preserved.',
    $dry_run ? 'Dry run complete' : 'Migration complete',
    $changed,
    $skipped
));