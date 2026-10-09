<?php

class Museotech_Overlay_Menu_Walker extends Walker_Nav_Menu
{
    private int $item_index = 0;

    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0): void
    {
        $item = $data_object;
        $this->item_index++;
        $index = str_pad((string) $this->item_index, 2, '0', STR_PAD_LEFT);
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'site-menu__item';
        $class_names = implode(' ', array_map('sanitize_html_class', array_filter($classes)));
        $attributes = '';

        if (! empty($item->url)) {
            $attributes .= ' href="' . esc_url($item->url) . '"';
        }
        if (! empty($item->target)) {
            $attributes .= ' target="' . esc_attr($item->target) . '"';
        }
        if (! empty($item->xfn)) {
            $attributes .= ' rel="' . esc_attr($item->xfn) . '"';
        }
        if (in_array('current-menu-item', $classes, true) || in_array('current_page_item', $classes, true)) {
            $attributes .= ' aria-current="page"';
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $item_output = sprintf(
            '<li class="%1$s" style="--menu-index:%2$d"><span class="site-menu__index" aria-hidden="true">%3$s</span><a class="site-menu__link"%4$s><span class="site-menu__name">%5$s</span></a>',
            esc_attr($class_names),
            $this->item_index,
            esc_html($index),
            $attributes,
            esc_html($title)
        );

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null): void
    {
        $output .= "</li>\n";
    }
}