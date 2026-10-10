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
<header class="site-header">
    <?php if (has_custom_logo()) : ?>
        <?php the_custom_logo(); ?>
    <?php else : ?>
        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <?php endif; ?>
    <span class="site-header__partner">
        <img src="<?php echo esc_url(home_url('/wp-content/uploads/2026/10/unir-logo.png')); ?>" alt="UNIR · Universidad Internacional de La Rioja" width="558" height="133">
    </span>
    <button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="museotech-site-menu" aria-label="<?php esc_attr_e('Abrir navegación', 'museotech'); ?>">
        <span class="site-menu-toggle__lines" aria-hidden="true"></span>
    </button>
    <nav id="museotech-site-menu" class="site-menu" aria-label="<?php esc_attr_e('Navegación principal', 'museotech'); ?>" aria-hidden="true" inert hidden>
        <div class="site-menu__panels" aria-hidden="true">
            <?php for ($panel = 0; $panel < 8; $panel++) : ?><span class="site-menu__panel"></span><?php endfor; ?>
        </div>
        <div class="site-menu__content">
            <div class="site-menu__brand">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a class="site-menu__brand-link" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                <?php endif; ?>
            </div>
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