<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e('Saltar al contenido', 'museotech'); ?></a>
<?php $is_contact_page = is_page_template('templates/page-contact.php'); ?>
<?php if ($is_contact_page) : ?>
    <div class="contact-page__announcement">
        <?php esc_html_e('Museotech conecta patrimonio, cultura y tecnología. Cuéntanos tu proyecto y exploremos nuevas posibilidades.', 'museotech'); ?>
    </div>
<?php endif; ?>
<header class="site-header<?php echo $is_contact_page ? ' site-header--contact' : ''; ?>">
    <?php if ($is_contact_page) : ?>
        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>">Museotech</a>
    <?php elseif (has_custom_logo()) : ?>
        <?php the_custom_logo(); ?>
    <?php else : ?>
        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <?php endif; ?>
    <?php if ($is_contact_page) : ?>
        <a class="contact-header__branding" href="<?php echo esc_url(home_url('/')); ?>">
            <span><?php esc_html_e('Circa', 'museotech'); ?></span>
            <svg width="92" height="50" viewBox="0 0 92 50" fill="none" aria-hidden="true">
                <ellipse cx="46" cy="25" rx="40" ry="19"></ellipse>
                <ellipse cx="46" cy="25" rx="14" ry="19"></ellipse>
                <ellipse cx="46" cy="25" rx="28" ry="19"></ellipse>
                <path d="M22 10.5C26.5 13.7 35.5 15.9 46 15.9S65.5 13.7 70 10.5M22 39.5C26.5 36.3 35.5 34.1 46 34.1S65.5 36.3 70 39.5M6 25h80M46 6v38"></path>
                <path class="contact-header__star" d="M48.3 22.7c1.2 1.2 2.8 2 5 2.4-2.2.4-3.8 1.2-5 2.4-1.2 1.2-2 2.8-2.4 5-.4-2.2-1.2-3.8-2.4-5-1.2-1.2-2.8-2-5-2.4 2.2-.4 3.8-1.2 5-2.4 1.2-1.2 2-2.8 2.4-5 .4 2.2 1.2 3.8 2.4 5Z"></path>
            </svg>
            <span><?php echo esc_html(wp_date('Y')); ?></span>
        </a>
    <?php else : ?>
        <a class="site-header__wordmark" href="<?php echo esc_url(home_url('/')); ?>">Museotech</a>
    <?php endif; ?>
    <button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="museotech-site-menu" aria-label="<?php esc_attr_e('Abrir navegación', 'museotech'); ?>">
        <?php if ($is_contact_page) : ?><span class="site-menu-toggle__label"><?php esc_html_e('Navegación', 'museotech'); ?></span><?php endif; ?>
        <span class="site-menu-toggle__lines" aria-hidden="true"></span>
    </button>
    <nav id="museotech-site-menu" class="site-menu" aria-label="<?php esc_attr_e('Navegación principal', 'museotech'); ?>" aria-hidden="true" inert hidden>
        <div class="site-menu__panels" aria-hidden="true">
            <?php for ($panel = 0; $panel < 8; $panel++) : ?><span class="site-menu__panel"></span><?php endfor; ?>
        </div>
        <div class="site-menu__content">
            <div class="site-menu__title"><span>[</span> <?php esc_html_e('navegación', 'museotech'); ?> <span>]</span></div>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'fallback_cb' => false,
                'items_wrap' => '<ul class="site-menu__list">%3$s</ul>',
                'walker' => new Museotech_Overlay_Menu_Walker(),
            ]);
            ?>
            <div class="site-menu__social">
                <?php foreach (museotech_social_links() as $label => $url) : ?>
                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
</header>