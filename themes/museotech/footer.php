<?php
$funding_logo = absint(museotech_option('footer_funding_logo'));
$unir_logo = absint(museotech_option('footer_unir_logo'));
$social_icons = [
    'YouTube' => '<path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4L15.8 12l-6.2 3.6Z"/>',
    'LinkedIn' => '<path d="M5.4 21H1V7.2h4.4V21ZM3.2 5.3A2.3 2.3 0 1 1 3.2.7a2.3 2.3 0 0 1 0 4.6ZM23 21h-4.4v-6.7c0-1.6 0-3.7-2.3-3.7s-2.6 1.8-2.6 3.6V21H9.3V7.2h4.2v1.9h.1a4.6 4.6 0 0 1 4.1-2.3c4.4 0 5.2 2.9 5.2 6.6V21Z"/>',
    'X' => '<path d="M18.9 2H22l-6.8 7.8L23.2 22H17l-4.9-6.4L6.5 22H3.3l7.3-8.4L3 2h6.3l4.4 5.8L18.9 2Zm-1.1 17.9h1.7L8.4 4H6.6l11.2 15.9Z"/>',
    'Instagram' => '<path d="M12 2.2c3.2 0 3.6 0 4.9.1 3.3.2 4.8 1.7 5 5 .1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.2 3.3-1.7 4.8-5 5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-3.3-.2-4.8-1.7-5-5C2 15.7 2 15.3 2 12.1s0-3.6.1-4.8c.2-3.3 1.7-4.8 5-5C8.4 2.2 8.8 2.2 12 2.2ZM12 0C8.7 0 8.3 0 7 .1 2.6.3.3 2.6.1 7 0 8.3 0 8.7 0 12s0 3.7.1 5c.2 4.4 2.5 6.7 6.9 6.9 1.3.1 1.7.1 5 .1s3.7 0 5-.1c4.4-.2 6.7-2.5 6.9-6.9.1-1.3.1-1.7.1-5s0-3.7-.1-5C23.7 2.6 21.4.3 17 .1 15.7 0 15.3 0 12 0Zm0 5.8A6.2 6.2 0 1 0 12 18a6.2 6.2 0 0 0 0-12.2ZM12 15.8a3.8 3.8 0 1 1 0-7.6 3.8 3.8 0 0 1 0 7.6Zm6.4-11.7A1.4 1.4 0 1 0 18.4 7a1.4 1.4 0 0 0 0-2.9Z"/>',
];
?>
<footer class="site-footer">
    <div class="site-footer__funding">
        <div class="site-footer__funding-inner">
            <p><?php echo esc_html(museotech_option('footer_credit')); ?></p>
            <?php if ($funding_logo) : ?>
                <div class="site-footer__funding-logo">
                    <?php echo wp_get_attachment_image($funding_logo, 'full', false, ['alt' => __('UNIR y Vicerrectorado de Transferencia', 'museotech')]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="site-footer__bottom-inner">
            <nav class="site-footer__social" aria-label="<?php esc_attr_e('Redes sociales', 'museotech'); ?>">
                <div class="site-footer__social-links">
                    <?php foreach (museotech_social_links() as $label => $url) : ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                            <svg class="site-footer__social-icon" viewBox="0 0 24 24" aria-hidden="true"><?php echo $social_icons[$label] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
                            <span class="site-footer__social-label"><?php echo esc_html($label); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="site-footer__credit">
                    <a href="https://solvenza.io" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Creado por Solvenza', 'museotech'); ?></a>
                </p>
            </nav>
            <nav class="site-footer__legal" aria-label="<?php esc_attr_e('Navegación legal', 'museotech'); ?>">
                <?php wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'fallback_cb' => false]); ?>
            </nav>
            <div class="site-footer__identity">
                <?php if ($unir_logo) : ?>
                    <a href="<?php echo esc_url(museotech_option('footer_unir_url')); ?>" rel="external noopener">
                        <?php echo wp_get_attachment_image($unir_logo, 'full', false, ['alt' => __('UNIR, La Universidad en Internet', 'museotech')]); ?>
                    </a>
                <?php endif; ?>
                <p><?php echo esc_html(wp_date('Y') . ' ' . museotech_option('footer_copyright')); ?></p>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
