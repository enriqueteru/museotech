<?php if (museotech_option('repository_visible')) : ?>
    <section class="home-feature home-feature--repository">
        <p><?php echo esc_html(museotech_option('repository_eyebrow')); ?></p>
        <h2><?php echo esc_html(museotech_option('repository_title')); ?></h2>
        <div><?php echo museotech_formatted_text(museotech_option('repository_description')); ?></div>
        <a href="<?php echo esc_url(museotech_option('repository_button_url')); ?>"><?php echo esc_html(museotech_option('repository_button_label')); ?></a>
    </section>
<?php endif; ?>