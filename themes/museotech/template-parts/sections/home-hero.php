<?php if (museotech_option('hero_visible')) : ?>
    <section class="home-hero">
        <div class="home-hero__content">
            <h1><?php echo esc_html(museotech_option('hero_title')); ?></h1>
            <h2><?php echo esc_html(museotech_option('hero_tagline')); ?></h2>
            <div><?php echo museotech_formatted_text(museotech_option('hero_description')); ?></div>
            <a href="<?php echo esc_url(museotech_option('hero_button_url')); ?>"><?php echo esc_html(museotech_option('hero_button_label')); ?></a>
        </div>
        <?php $image_id = absint(museotech_option('hero_image')); ?>
        <?php if ($image_id) : ?><div class="home-hero__media"><?php echo wp_get_attachment_image($image_id, 'full'); ?></div><?php endif; ?>
    </section>
<?php endif; ?>