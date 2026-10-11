<?php if (museotech_option('podcast_visible')) : ?>
    <?php
    $podcast_model_path = get_theme_file_path('/assets/models/podcast.glb');
    $podcast_model_url = get_theme_file_uri('/assets/models/podcast.glb');
    if (is_file($podcast_model_path)) {
        $podcast_model_url = add_query_arg('ver', (string) filemtime($podcast_model_path), $podcast_model_url);
    }
    ?>
    <section class="home-podcast home-feature-stack__card home-feature-stack__card--podcast" aria-labelledby="home-podcast-title">
        <div class="home-podcast__content">
            <div class="home-podcast__copy" data-reveal-group>
                <p class="home-podcast__eyebrow"><?php echo esc_html(museotech_option('podcast_eyebrow')); ?></p>
                <h2 class="home-podcast__headline" id="home-podcast-title" data-scroll-title><?php echo esc_html(museotech_option('podcast_title')); ?></h2>
                <div class="home-podcast__description" data-scroll-fade><?php echo museotech_formatted_text(museotech_option('podcast_description')); ?></div>
                <a class="ui-button home-podcast__action" data-scroll-fade data-magnetic href="<?php echo esc_url(museotech_option('podcast_button_url')); ?>">
                    <?php echo esc_html(museotech_option('podcast_button_label')); ?>
                    <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </div>
            <?php if (is_file($podcast_model_path)) : ?>
                <div class="home-podcast__visual">
                    <div class="home-podcast__model" data-home-podcast-model data-model-src="<?php echo esc_url($podcast_model_url); ?>" data-model-meshopt="true" role="group" tabindex="0" aria-label="<?php esc_attr_e('Modelo 3D interactivo. Arrástralo para girarlo o usa las flechas.', 'museotech'); ?>"></div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
