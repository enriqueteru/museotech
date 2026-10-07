<footer class="site-footer">
    <p><?php echo esc_html(museotech_option('footer_credit')); ?></p>
    <?php $funding_logo = absint(museotech_option('footer_funding_logo')); ?>
    <?php if ($funding_logo) : ?><?php echo wp_get_attachment_image($funding_logo, 'medium'); ?><?php endif; ?>
    <div class="site-footer__social">
        <?php foreach (museotech_social_links() as $label => $url) : ?>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?></a>
        <?php endforeach; ?>
    </div>
    <nav aria-label="<?php esc_attr_e('Navegación legal', 'museotech'); ?>">
        <?php wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'fallback_cb' => false]); ?>
    </nav>
    <?php $unir_logo = absint(museotech_option('footer_unir_logo')); ?>
    <?php if ($unir_logo) : ?>
        <a href="<?php echo esc_url(museotech_option('footer_unir_url')); ?>" rel="external noopener">
            <?php echo wp_get_attachment_image($unir_logo, 'medium'); ?>
        </a>
    <?php endif; ?>
    <p><?php echo esc_html(museotech_option('footer_copyright')); ?></p>
</footer>
<?php wp_footer(); ?>
</body>
</html>