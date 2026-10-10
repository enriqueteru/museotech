<?php
/**
 * Plugin Name: Museotech Core
 * Description: Tipos de contenido y formulario de contacto de Museotech.
 * Version: 0.1.0
 * Text Domain: museotech-core
 */

if (! defined('ABSPATH')) {
    exit;
}

function museotech_core_register_content_types(): void
{
    $post_types = [
        'podcast' => [
            'name' => __('Podcasts', 'museotech-core'),
            'singular' => __('Podcast', 'museotech-core'),
            'archive' => 'podcasts',
            'rewrite' => 'podcast',
            'supports' => ['title', 'thumbnail', 'custom-fields'],
        ],
        'repositorios' => [
            'name' => __('Repositorios', 'museotech-core'),
            'singular' => __('Repositorio', 'museotech-core'),
            'archive' => 'repositorios',
            'rewrite' => 'repositorios',
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields', 'autosave'],
        ],
        'equipo' => [
            'name' => __('Equipo', 'museotech-core'),
            'singular' => __('Miembro del equipo', 'museotech-core'),
            'archive' => 'equipo',
            'rewrite' => 'equipo',
            'supports' => ['title', 'thumbnail', 'custom-fields'],
        ],
        'colaborador' => [
            'name' => __('Colaboradores', 'museotech-core'),
            'singular' => __('Colaborador', 'museotech-core'),
            'archive' => 'colaboradores',
            'rewrite' => 'colaborador',
            'supports' => ['title', 'thumbnail', 'custom-fields'],
        ],
    ];

    foreach ($post_types as $slug => $config) {
        if (post_type_exists($slug)) {
            continue;
        }

        register_post_type($slug, [
            'labels' => [
                'name' => $config['name'],
                'singular_name' => $config['singular'],
            ],
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => $config['archive'],
            'rewrite' => ['slug' => $config['rewrite'], 'with_front' => false],
            'supports' => $config['supports'],
        ]);
    }

    foreach ([
        'formato' => [__('Formatos', 'museotech-core'), 'repositorios'],
        'rol' => [__('Roles', 'museotech-core'), 'equipo'],
    ] as $slug => [$label, $post_type]) {
        if (taxonomy_exists($slug)) {
            continue;
        }

        register_taxonomy($slug, [$post_type], [
            'labels' => ['name' => $label, 'singular_name' => $label],
            'public' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => $slug, 'with_front' => false],
        ]);
    }
}
add_action('init', 'museotech_core_register_content_types', 20);

function museotech_core_schedule_rewrite_flush(): void
{
    add_option('museotech_core_flush_rewrites', '1', '', false);
}
register_activation_hook(__FILE__, 'museotech_core_schedule_rewrite_flush');

function museotech_core_flush_rewrites(): void
{
    if (get_option('museotech_core_flush_rewrites')) {
        flush_rewrite_rules();
        delete_option('museotech_core_flush_rewrites');
    }
}
add_action('init', 'museotech_core_flush_rewrites', 99);

function museotech_core_contact_form(): string
{
    $status = isset($_GET['museotech_contact'])
        ? sanitize_key(wp_unslash($_GET['museotech_contact']))
        : '';
    $messages = [
        'sent' => __('Gracias. Tu mensaje se ha enviado.', 'museotech-core'),
        'invalid' => __('Revisa los campos obligatorios e inténtalo de nuevo.', 'museotech-core'),
        'error' => __('No se pudo enviar el mensaje. Inténtalo más tarde.', 'museotech-core'),
    ];

    ob_start();
    ?>
    <?php if (isset($messages[$status])) : ?>
        <p class="museotech-contact-form__status" role="status"><?php echo esc_html($messages[$status]); ?></p>
    <?php endif; ?>
    <form class="museotech-contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="museotech_core_contact">
        <?php wp_nonce_field('museotech_core_contact', 'museotech_core_contact_nonce'); ?>
        <div class="museotech-contact-form__honeypot" aria-hidden="true">
            <label for="museotech-website-check"><?php esc_html_e('Deja este campo vacío', 'museotech-core'); ?></label>
            <input id="museotech-website-check" type="text" name="website_check" value="" tabindex="-1" autocomplete="off">
        </div>
        <div class="museotech-contact-form__fields">
            <p class="museotech-contact-form__field">
                <label class="screen-reader-text" for="museotech-contact-name"><?php esc_html_e('Nombre', 'museotech-core'); ?></label>
                <input id="museotech-contact-name" name="name" type="text" placeholder="<?php esc_attr_e('Nombre', 'museotech-core'); ?>" autocomplete="name" required>
            </p>
            <p class="museotech-contact-form__field">
                <label class="screen-reader-text" for="museotech-contact-company"><?php esc_html_e('Empresa', 'museotech-core'); ?></label>
                <input id="museotech-contact-company" name="company" type="text" placeholder="<?php esc_attr_e('Institución', 'museotech-core'); ?>" autocomplete="organization">
            </p>
            <p class="museotech-contact-form__field">
                <label class="screen-reader-text" for="museotech-contact-email"><?php esc_html_e('Email', 'museotech-core'); ?></label>
                <input id="museotech-contact-email" name="email" type="email" placeholder="<?php esc_attr_e('Email', 'museotech-core'); ?>" autocomplete="email" required>
            </p>
            <p class="museotech-contact-form__field">
                <label class="screen-reader-text" for="museotech-contact-budget"><?php esc_html_e('Asunto', 'museotech-core'); ?></label>
                <input id="museotech-contact-budget" name="budget" type="text" placeholder="<?php esc_attr_e('Asunto', 'museotech-core'); ?>" inputmode="text">
            </p>
            <p class="museotech-contact-form__field museotech-contact-form__field--message">
                <label class="screen-reader-text" for="museotech-contact-message"><?php esc_html_e('Mensaje', 'museotech-core'); ?></label>
                <textarea id="museotech-contact-message" name="message" rows="4" placeholder="<?php esc_attr_e('Mensaje', 'museotech-core'); ?>" required></textarea>
            </p>
        </div>
        <p class="museotech-contact-form__consent">
            <label for="museotech-contact-consent">
                <input id="museotech-contact-consent" name="consent" type="checkbox" value="1" required>
                <span class="museotech-contact-form__checkmark" aria-hidden="true"></span>
                <?php esc_html_e('Doy mi consentimiento para el tratamiento de mis datos de acuerdo con la', 'museotech-core'); ?>
                <a href="<?php echo esc_url(home_url('/politicas-de-privacidad/')); ?>"><?php esc_html_e('Política de privacidad', 'museotech-core'); ?></a>.
            </label>
        </p>
        <div class="museotech-contact-form__submit-row">
            <button class="ui-button" type="submit"><?php esc_html_e('Enviar', 'museotech-core'); ?></button>
        </div>
    </form>
    <?php

    return (string) ob_get_clean();
}
add_shortcode('museotech_contact_form', 'museotech_core_contact_form');

function museotech_core_contact_redirect(string $status): void
{
    $referer = wp_get_referer() ?: home_url('/contacto/');
    $redirect = wp_validate_redirect($referer, home_url('/contacto/'));

    wp_safe_redirect(add_query_arg('museotech_contact', $status, $redirect));
    exit;
}

function museotech_core_handle_contact(): void
{
    check_admin_referer('museotech_core_contact', 'museotech_core_contact_nonce');

    $website_check = isset($_POST['website_check']) ? sanitize_text_field(wp_unslash($_POST['website_check'])) : '';
    if ($website_check !== '') {
        museotech_core_contact_redirect('sent');
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $budget = isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $consent = isset($_POST['consent']) && wp_unslash($_POST['consent']) === '1';

    if ($name === '' || ! is_email($email) || $message === '' || ! $consent) {
        museotech_core_contact_redirect('invalid');
    }

    $recipient = sanitize_email((string) get_option('admin_email'));
    if (! is_email($recipient)) {
        museotech_core_contact_redirect('error');
    }

    $subject = __('Nuevo mensaje de "Museo tech"', 'museotech-core');
    $body = sprintf(
        "Nombre: %1$s\nEmpresa: %2$s\nEmail: %3$s\nAsunto: %4$s\n\nMensaje:\n%5$s",
        $name,
        $company !== '' ? $company : __('No indicado', 'museotech-core'),
        $email,
        $budget !== '' ? $budget : __('No indicado', 'museotech-core'),
        $message
    );
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('Reply-To: %s <%s>', $name, $email),
    ];

    museotech_core_contact_redirect(wp_mail($recipient, $subject, $body, $headers) ? 'sent' : 'error');
}
add_action('admin_post_nopriv_museotech_core_contact', 'museotech_core_handle_contact');
add_action('admin_post_museotech_core_contact', 'museotech_core_handle_contact');

function museotech_core_enqueue_cookie_consent(): void
{
    $sdk_path = __DIR__ . '/assets/cookies-compliance/consent.min.js';
    if (! file_exists($sdk_path)) {
        return;
    }

    $version = '1.5.3-' . (string) filemtime($sdk_path);
    wp_enqueue_script(
        'museotech-core-cookie-consent',
        plugins_url('assets/cookies-compliance/consent.min.js', __FILE__),
        [],
        $version,
        false
    );

    $style_path = __DIR__ . '/assets/cookies-compliance/consent-overrides.css';
    wp_enqueue_style(
        'museotech-core-cookie-consent',
        plugins_url('assets/cookies-compliance/consent-overrides.css', __FILE__),
        [],
        file_exists($style_path) ? (string) filemtime($style_path) : $version
    );
}
add_action('wp_enqueue_scripts', 'museotech_core_enqueue_cookie_consent', 1);

function museotech_core_cookie_consent_script_tag(string $tag, string $handle, string $src): string
{
    if ($handle !== 'museotech-core-cookie-consent') {
        return $tag;
    }

    return sprintf(
        '<script src="%1$s" data-config="%2$s" id="%3$s-js"></script>' . "\n",
        esc_url($src),
        esc_url(plugins_url('consent.json', __FILE__)),
        esc_attr($handle)
    );
}
add_filter('script_loader_tag', 'museotech_core_cookie_consent_script_tag', 10, 3);