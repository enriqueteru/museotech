<?php

function museotech_sanitize_count($value): int
{
    return max(1, min(48, absint($value)));
}

function museotech_objective_icon_choices(): array
{
    return [
        'university' => __('Universidad', 'museotech'),
        'terminal' => __('Tecnología', 'museotech'),
        'book-open' => __('Libro', 'museotech'),
        'search' => __('Investigación', 'museotech'),
        'globe' => __('Proyección internacional', 'museotech'),
        'robot' => __('Inteligencia artificial', 'museotech'),
    ];
}

function museotech_sanitize_objective_icon($value): string
{
    $choices = museotech_objective_icon_choices();

    return isset($choices[$value]) ? $value : 'university';
}

function museotech_customizer_register(WP_Customize_Manager $customize): void
{
    $defaults = museotech_customizer_defaults();
    $sections = [
        'hero' => [
            'title' => __(' presentación', 'museotech'),
            'fields' => [
                'hero_visible' => [__('Mostrar sección', 'museotech'), 'checkbox'],
                'hero_title' => [__('Nombre', 'museotech'), 'text'],
                'hero_tagline' => [__('Titular', 'museotech'), 'text'],
                'hero_description' => [__('Descripción', 'museotech'), 'textarea'],
                'hero_image' => [__('Imagen', 'museotech'), 'image'],
                'hero_button_label' => [__('Texto del enlace', 'museotech'), 'text'],
                'hero_button_url' => [__('Destino del enlace', 'museotech'), 'url'],
            ],
        ],
        'objectives' => [
            'title' => __(' objetivos', 'museotech'),
            'fields' => [
                'objectives_visible' => [__('Mostrar sección', 'museotech'), 'checkbox'],
                'objectives_title' => [__('Título', 'museotech'), 'text'],
                'objectives_intro' => [__('Introducción', 'museotech'), 'text'],
                'objective_1' => [__('Objetivo 1', 'museotech'), 'text'],
                'objective_1_icon' => [__('Icono del objetivo 1', 'museotech'), 'select'],
                'objective_2' => [__('Objetivo 2', 'museotech'), 'text'],
                'objective_2_icon' => [__('Icono del objetivo 2', 'museotech'), 'select'],
                'objective_3' => [__('Objetivo 3', 'museotech'), 'text'],
                'objective_3_icon' => [__('Icono del objetivo 3', 'museotech'), 'select'],
                'objective_4' => [__('Objetivo 4', 'museotech'), 'text'],
                'objective_4_icon' => [__('Icono del objetivo 4', 'museotech'), 'select'],
                'objective_5' => [__('Objetivo 5', 'museotech'), 'text'],
                'objective_5_icon' => [__('Icono del objetivo 5', 'museotech'), 'select'],
                'objective_6' => [__('Objetivo 6', 'museotech'), 'text'],
                'objective_6_icon' => [__('Icono del objetivo 6', 'museotech'), 'select'],
            ],
        ],
        'transitions' => [
            'title' => __('Home: transiciones entre secciones', 'museotech'),
            'fields' => [
                'transition_1_image' => [__('Imagen: objetivos a podcasts', 'museotech'), 'image'],
                'transition_2_image' => [__('Imagen: repositorio a noticias', 'museotech'), 'image'],
            ],
        ],
        'podcast' => [
            'title' => __(' podcasts', 'museotech'),
            'fields' => [
                'podcast_visible' => [__('Mostrar sección', 'museotech'), 'checkbox'],
                'podcast_eyebrow' => [__('Etiqueta', 'museotech'), 'text'],
                'podcast_title' => [__('Titular', 'museotech'), 'text'],
                'podcast_description' => [__('Descripción', 'museotech'), 'textarea'],
                'podcast_button_label' => [__('Texto del enlace', 'museotech'), 'text'],
                'podcast_button_url' => [__('Destino del enlace', 'museotech'), 'url'],
            ],
        ],
        'repository' => [
            'title' => __(' repositorio', 'museotech'),
            'fields' => [
                'repository_visible' => [__('Mostrar sección', 'museotech'), 'checkbox'],
                'repository_eyebrow' => [__('Etiqueta', 'museotech'), 'text'],
                'repository_title' => [__('Titular', 'museotech'), 'text'],
                'repository_description' => [__('Descripción', 'museotech'), 'textarea'],
                'repository_button_label' => [__('Texto del enlace', 'museotech'), 'text'],
                'repository_button_url' => [__('Destino del enlace', 'museotech'), 'url'],
            ],
        ],
        'news' => [
            'title' => __(' noticias', 'museotech'),
            'fields' => [
                'news_visible' => [__('Mostrar sección', 'museotech'), 'checkbox'],
                'news_eyebrow' => [__('Etiqueta', 'museotech'), 'text'],
                'news_title' => [__('Titular', 'museotech'), 'text'],
                'news_description' => [__('Descripción', 'museotech'), 'textarea'],
                'news_count' => [__('Entradas visibles', 'museotech'), 'number'],
                'news_button_label' => [__('Texto del enlace', 'museotech'), 'text'],
                'news_button_url' => [__('Destino del enlace', 'museotech'), 'url'],
            ],
        ],
        'archives' => [
            'title' => __('Secciones: listados', 'museotech'),
            'fields' => [
                'archive_podcast_heading' => [__('Introducción de Podcasts', 'museotech'), 'text'],
                'archive_podcast_count' => [__('Podcasts por página', 'museotech'), 'number'],
                'archive_repositorios_heading' => [__('Introducción de Repositorio', 'museotech'), 'text'],
                'archive_repositorios_count' => [__('Recursos por página', 'museotech'), 'number'],
                'archive_equipo_heading' => [__('Introducción de Equipo', 'museotech'), 'text'],
                'archive_equipo_count' => [__('Miembros por página', 'museotech'), 'number'],
                'archive_colaborador_heading' => [__('Introducción de Colaboradores', 'museotech'), 'text'],
                'archive_colaborador_count' => [__('Colaboradores por página', 'museotech'), 'number'],
            ],
        ],
        'footer' => [
            'title' => __('Pie de página', 'museotech'),
            'fields' => [
                'footer_credit' => [__('Crédito del proyecto', 'museotech'), 'text'],
                'footer_funding_logo' => [__('Logotipo de financiación', 'museotech'), 'image'],
                'footer_unir_logo' => [__('Logotipo UNIR', 'museotech'), 'image'],
                'footer_unir_url' => [__('Enlace UNIR', 'museotech'), 'url'],
                'footer_youtube_url' => [__('YouTube', 'museotech'), 'url'],
                'footer_linkedin_url' => [__('LinkedIn', 'museotech'), 'url'],
                'footer_x_url' => [__('X', 'museotech'), 'url'],
                'footer_instagram_url' => [__('Instagram', 'museotech'), 'url'],
                'footer_copyright' => [__('Texto legal', 'museotech'), 'text'],
            ],
        ],
    ];

    $customize->add_panel('museotech_content', [
        'title' => __('Museotech', 'museotech'),
        'priority' => 30,
    ]);

    $sanitizers = [
        'checkbox' => 'rest_sanitize_boolean',
        'image' => 'absint',
        'number' => 'museotech_sanitize_count',
        'select' => 'museotech_sanitize_objective_icon',
        'text' => 'sanitize_text_field',
        'textarea' => 'sanitize_textarea_field',
        'url' => 'esc_url_raw',
    ];

    foreach ($sections as $section_id => $section) {
        $customize->add_section('museotech_' . $section_id, [
            'title' => $section['title'],
            'panel' => 'museotech_content',
        ]);

        foreach ($section['fields'] as $name => [$label, $type]) {
            $setting_id = 'museotech_' . $name;
            $customize->add_setting($setting_id, [
                'default' => $defaults[$name],
                'sanitize_callback' => $sanitizers[$type],
                'transport' => 'refresh',
            ]);

            if ($type === 'image') {
                $customize->add_control(new WP_Customize_Media_Control($customize, $setting_id, [
                    'label' => $label,
                    'section' => 'museotech_' . $section_id,
                    'mime_type' => 'image',
                ]));
                continue;
            }

            $control = [
                'label' => $label,
                'section' => 'museotech_' . $section_id,
                'type' => $type,
            ];

            if ($type === 'number') {
                $control['input_attrs'] = ['min' => 1, 'max' => 48, 'step' => 1];
            }

            if ($type === 'select') {
                $control['choices'] = museotech_objective_icon_choices();
            }

            $customize->add_control($setting_id, $control);
        }
    }
}
add_action('customize_register', 'museotech_customizer_register');

function museotech_archive_page_size(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive()) {
        return;
    }

    $post_type = $query->get('post_type');
    if (!is_string($post_type) || !in_array($post_type, ['podcast', 'repositorios', 'equipo', 'colaborador'], true)) {
        return;
    }

    $count = museotech_option('archive_' . $post_type . '_count');
    if ($count) {
        $query->set('posts_per_page', museotech_sanitize_count($count));
    }
}
add_action('pre_get_posts', 'museotech_archive_page_size');