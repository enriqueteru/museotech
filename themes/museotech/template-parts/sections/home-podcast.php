<?php if (museotech_option('podcast_visible')) : ?>
    <section class="home-feature home-feature--podcast">
        <p><?php echo esc_html(museotech_option('podcast_eyebrow')); ?></p>
        <h2><?php echo esc_html(museotech_option('podcast_title')); ?></h2>
        <div><?php echo museotech_formatted_text(museotech_option('podcast_description')); ?></div>
        <a href="<?php echo esc_url(museotech_option('podcast_button_url')); ?>"><?php echo esc_html(museotech_option('podcast_button_label')); ?></a>
    </section>
<?php endif; ?>