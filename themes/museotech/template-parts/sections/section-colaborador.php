<?php $website = museotech_field('web'); ?>
<?php if ($website) : ?>
    <p><a href="<?php echo esc_url($website); ?>" rel="external noopener"><?php echo esc_html($website); ?></a></p>
<?php endif; ?>