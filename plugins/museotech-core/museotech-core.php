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

/** Keep mail credentials in WordPress options, never in the public form. */
function museotech_core_contact_mailer(): string
{
    $saved = (string) get_option('museotech_core_contact_mailer', '');
    if (in_array($saved, ['wordpress', 'resend', 'smtp'], true)) {
        return $saved;
    }

    // Preserve the behavior of sites that configured Resend before SMTP existed.
    return get_option('museotech_core_resend_api_key') && get_option('museotech_core_resend_sender')
        ? 'resend'
        : 'wordpress';
}

function museotech_core_resend_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $sender = (string) get_option('museotech_core_resend_sender', '');
    $has_key = (string) get_option('museotech_core_resend_api_key', '') !== '';
    $mailer = museotech_core_contact_mailer();
    $smtp_host = (string) get_option('museotech_core_smtp_host', 'smtp.gmail.com');
    $smtp_port = (int) get_option('museotech_core_smtp_port', 587);
    $smtp_security = (string) get_option('museotech_core_smtp_security', 'tls');
    $smtp_user = (string) get_option('museotech_core_smtp_user', '');
    $smtp_sender = (string) get_option('museotech_core_smtp_sender', '');
    $has_smtp_password = (string) get_option('museotech_core_smtp_password', '') !== '';
    $status = isset($_GET['museotech_resend_status']) ? sanitize_key(wp_unslash($_GET['museotech_resend_status'])) : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Correo de Museotech', 'museotech-core'); ?></h1>
        <p><?php esc_html_e('Configura cómo se envía el formulario de contacto. Los mensajes llegan al correo de administración de WordPress.', 'museotech-core'); ?></p>
        <p><strong><?php echo esc_html(sprintf(__('Método seleccionado: %s', 'museotech-core'), strtoupper($mailer))); ?></strong></p>
        <?php if ($status === 'saved') : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Configuración guardada.', 'museotech-core'); ?></p></div>
        <?php elseif ($status === 'invalid') : ?>
            <div class="notice notice-error"><p><?php esc_html_e('Revisa los datos del método seleccionado. El remitente y las credenciales son obligatorios.', 'museotech-core'); ?></p></div>
        <?php endif; ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="museotech_core_resend_save">
            <?php wp_nonce_field('museotech_core_resend_save'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="museotech-contact-mailer"><?php esc_html_e('Método de envío', 'museotech-core'); ?></label></th>
                    <td>
                        <select id="museotech-contact-mailer" name="contact_mailer">
                            <option value="smtp" <?php selected($mailer, 'smtp'); ?>><?php esc_html_e('SMTP (Gmail u otra cuenta)', 'museotech-core'); ?></option>
                            <option value="resend" <?php selected($mailer, 'resend'); ?>>Resend</option>
                            <option value="wordpress" <?php selected($mailer, 'wordpress'); ?>><?php esc_html_e('Envío habitual de WordPress', 'museotech-core'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <h2><?php esc_html_e('SMTP', 'museotech-core'); ?></h2>
            <p><?php esc_html_e('Para Gmail usa smtp.gmail.com, puerto 587, TLS y una contraseña de aplicación. El remitente debe ser la cuenta autorizada para enviar.', 'museotech-core'); ?></p>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="museotech-smtp-host"><?php esc_html_e('Servidor SMTP', 'museotech-core'); ?></label></th>
                    <td><input id="museotech-smtp-host" name="smtp_host" type="text" class="regular-text" value="<?php echo esc_attr($smtp_host); ?>" autocomplete="off"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-smtp-port"><?php esc_html_e('Puerto', 'museotech-core'); ?></label></th>
                    <td><input id="museotech-smtp-port" name="smtp_port" type="number" min="1" max="65535" value="<?php echo esc_attr((string) $smtp_port); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-smtp-security"><?php esc_html_e('Seguridad', 'museotech-core'); ?></label></th>
                    <td><select id="museotech-smtp-security" name="smtp_security"><option value="tls" <?php selected($smtp_security, 'tls'); ?>>TLS / STARTTLS</option><option value="ssl" <?php selected($smtp_security, 'ssl'); ?>>SSL / SMTPS</option></select></td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-smtp-user"><?php esc_html_e('Usuario SMTP', 'museotech-core'); ?></label></th>
                    <td><input id="museotech-smtp-user" name="smtp_user" type="text" class="regular-text" value="<?php echo esc_attr($smtp_user); ?>" autocomplete="username"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-smtp-password"><?php esc_html_e('Contraseña de aplicación', 'museotech-core'); ?></label></th>
                    <td>
                        <input id="museotech-smtp-password" name="smtp_password" type="password" class="regular-text" value="" autocomplete="new-password" placeholder="<?php echo esc_attr($has_smtp_password ? __('Configurada; deja vacío para conservarla', 'museotech-core') : ''); ?>">
                        <?php if ($has_smtp_password) : ?><p><label><input name="clear_smtp_password" type="checkbox" value="1"> <?php esc_html_e('Eliminar la contraseña guardada', 'museotech-core'); ?></label></p><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-smtp-sender"><?php esc_html_e('Correo remitente SMTP', 'museotech-core'); ?></label></th>
                    <td><input id="museotech-smtp-sender" name="smtp_sender" type="email" class="regular-text" value="<?php echo esc_attr($smtp_sender); ?>" placeholder="tu-cuenta@gmail.com"></td>
                </tr>
            </table>
            <h2>Resend</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="museotech-resend-api-key"><?php esc_html_e('Clave API de Resend', 'museotech-core'); ?></label></th>
                    <td>
                        <input id="museotech-resend-api-key" name="resend_api_key" type="password" class="regular-text" value="" autocomplete="new-password" placeholder="<?php echo esc_attr($has_key ? __('Configurada; deja vacío para conservarla', 'museotech-core') : 're_...'); ?>">
                        <p class="description"><?php esc_html_e('La clave no se muestra después de guardarla.', 'museotech-core'); ?></p>
                        <?php if ($has_key) : ?>
                            <label><input name="clear_resend_api_key" type="checkbox" value="1"> <?php esc_html_e('Eliminar la clave guardada', 'museotech-core'); ?></label>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="museotech-resend-sender"><?php esc_html_e('Correo remitente', 'museotech-core'); ?></label></th>
                    <td>
                        <input id="museotech-resend-sender" name="resend_sender" type="email" class="regular-text" value="<?php echo esc_attr($sender); ?>" placeholder="contacto@tudominio.com">
                        <p class="description"><?php esc_html_e('Debe pertenecer a un dominio verificado para enviar en Resend.', 'museotech-core'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Guardar configuración', 'museotech-core')); ?>
        </form>
    </div>
    <?php
}

function museotech_core_register_resend_settings_page(): void
{
    add_options_page(
        __('Correo de Museotech', 'museotech-core'),
        __('Correo de Museotech', 'museotech-core'),
        'manage_options',
        'museotech-resend',
        'museotech_core_resend_settings_page'
    );
}
add_action('admin_menu', 'museotech_core_register_resend_settings_page');

function museotech_core_save_resend_settings(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('No tienes permisos para cambiar esta configuración.', 'museotech-core'));
    }

    check_admin_referer('museotech_core_resend_save');

    $sender = isset($_POST['resend_sender']) ? sanitize_email(wp_unslash($_POST['resend_sender'])) : '';
    $key = isset($_POST['resend_api_key']) ? trim((string) wp_unslash($_POST['resend_api_key'])) : '';
    $clear_key = isset($_POST['clear_resend_api_key']) && wp_unslash($_POST['clear_resend_api_key']) === '1';
    $mailer = isset($_POST['contact_mailer']) ? sanitize_key(wp_unslash($_POST['contact_mailer'])) : 'wordpress';
    $smtp_host = isset($_POST['smtp_host']) ? sanitize_text_field(wp_unslash($_POST['smtp_host'])) : '';
    $smtp_port = isset($_POST['smtp_port']) ? (int) wp_unslash($_POST['smtp_port']) : 0;
    $smtp_security = isset($_POST['smtp_security']) ? sanitize_key(wp_unslash($_POST['smtp_security'])) : '';
    $smtp_user = isset($_POST['smtp_user']) ? sanitize_text_field(wp_unslash($_POST['smtp_user'])) : '';
    $smtp_password = isset($_POST['smtp_password']) ? (string) wp_unslash($_POST['smtp_password']) : '';
    $smtp_sender = isset($_POST['smtp_sender']) ? sanitize_email(wp_unslash($_POST['smtp_sender'])) : '';
    $clear_smtp_password = isset($_POST['clear_smtp_password']) && wp_unslash($_POST['clear_smtp_password']) === '1';
    $saved_key = $clear_key ? '' : ($key !== '' ? $key : (string) get_option('museotech_core_resend_api_key', ''));
    $saved_smtp_password = $clear_smtp_password ? '' : ($smtp_password !== '' ? $smtp_password : (string) get_option('museotech_core_smtp_password', ''));
    $status = 'saved';

    if (
        ! in_array($mailer, ['wordpress', 'resend', 'smtp'], true)
        || ($sender !== '' && ! is_email($sender))
        || ($key !== '' && (strlen($key) > 256 || preg_match('/\s/', $key)))
        || ($smtp_sender !== '' && ! is_email($smtp_sender))
        || ($smtp_password !== '' && (strlen($smtp_password) > 512 || preg_match('/[\r\n]/', $smtp_password)))
        || ($mailer === 'resend' && ($saved_key === '' || ! is_email($sender)))
        || ($mailer === 'smtp' && (
            ! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.-]{0,252}$/D', $smtp_host)
            || $smtp_port < 1 || $smtp_port > 65535
            || ! in_array($smtp_security, ['tls', 'ssl'], true)
            || $smtp_user === '' || $saved_smtp_password === '' || ! is_email($smtp_sender)
        ))
    ) {
        $status = 'invalid';
    } else {
        update_option('museotech_core_contact_mailer', $mailer, false);
        update_option('museotech_core_resend_sender', $sender, false);
        if ($clear_key) {
            delete_option('museotech_core_resend_api_key');
        } elseif ($key !== '') {
            update_option('museotech_core_resend_api_key', $key, false);
        }
        update_option('museotech_core_smtp_host', $smtp_host, false);
        update_option('museotech_core_smtp_port', $smtp_port, false);
        update_option('museotech_core_smtp_security', $smtp_security, false);
        update_option('museotech_core_smtp_user', $smtp_user, false);
        update_option('museotech_core_smtp_sender', $smtp_sender, false);
        if ($clear_smtp_password) {
            delete_option('museotech_core_smtp_password');
        } elseif ($smtp_password !== '') {
            update_option('museotech_core_smtp_password', $smtp_password, false);
        }
    }

    wp_safe_redirect(add_query_arg('museotech_resend_status', $status, admin_url('options-general.php?page=museotech-resend')));
    exit;
}
add_action('admin_post_museotech_core_resend_save', 'museotech_core_save_resend_settings');

/** Send the contact message through Resend only when its configuration is complete. */
function museotech_core_send_contact_with_resend(string $recipient, string $subject, string $body, string $reply_to): bool
{
    $key = (string) get_option('museotech_core_resend_api_key', '');
    $sender = (string) get_option('museotech_core_resend_sender', '');
    if ($key === '' || ! is_email($sender)) {
        return false;
    }

    $response = wp_remote_post('https://api.resend.com/emails', [
        'timeout' => 15,
        'headers' => [
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode([
            'from' => sprintf('Museotech <%s>', $sender),
            'to' => [$recipient],
            'subject' => $subject,
            'text' => $body,
            'reply_to' => $reply_to,
        ]),
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $result = json_decode(wp_remote_retrieve_body($response), true);

    return $code >= 200 && $code < 300 && is_array($result) && ! empty($result['id']);
}

/** Configure WordPress' bundled PHPMailer for this contact email only. */
function museotech_core_configure_contact_smtp($phpmailer): void
{
    $sender = (string) get_option('museotech_core_smtp_sender', '');

    $phpmailer->isSMTP();
    $phpmailer->Host = (string) get_option('museotech_core_smtp_host', '');
    $phpmailer->Port = (int) get_option('museotech_core_smtp_port', 587);
    $phpmailer->SMTPSecure = (string) get_option('museotech_core_smtp_security', 'tls');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = (string) get_option('museotech_core_smtp_user', '');
    $phpmailer->Password = (string) get_option('museotech_core_smtp_password', '');
    $phpmailer->Timeout = 15;
    $phpmailer->setFrom($sender, 'Museotech', false);
    $phpmailer->Sender = $sender;
}

function museotech_core_send_contact_with_smtp(string $recipient, string $subject, string $body, array $headers): bool
{
    $headers[] = sprintf('From: Museotech <%s>', (string) get_option('museotech_core_smtp_sender', ''));
    add_action('phpmailer_init', 'museotech_core_configure_contact_smtp');

    try {
        return wp_mail($recipient, $subject, $body, $headers);
    } finally {
        remove_action('phpmailer_init', 'museotech_core_configure_contact_smtp');
    }
}

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

    $mailer = museotech_core_contact_mailer();
    if ($mailer === 'smtp') {
        $sent = museotech_core_send_contact_with_smtp($recipient, $subject, $body, $headers);
    } elseif ($mailer === 'resend') {
        $sent = museotech_core_send_contact_with_resend($recipient, $subject, $body, $email);
    } else {
        $sent = wp_mail($recipient, $subject, $body, $headers);
    }

    museotech_core_contact_redirect($sent ? 'sent' : 'error');
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
        ['in_footer' => true, 'strategy' => 'defer']
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
        '<script defer src="%1$s" data-config="%2$s" id="%3$s-js"></script>' . "\n",
        esc_url($src),
        esc_url(plugins_url('consent.json', __FILE__)),
        esc_attr($handle)
    );
}
add_filter('script_loader_tag', 'museotech_core_cookie_consent_script_tag', 10, 3);
