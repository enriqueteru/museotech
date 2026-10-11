<?php if (museotech_option('repository_visible')) : ?>
    <?php
    $repository_model_path = get_theme_file_path('/assets/models/thinker.glb');
    $repository_model_url = get_theme_file_uri('/assets/models/thinker.glb');
    if (is_file($repository_model_path)) {
        $repository_model_url = add_query_arg('ver', (string) filemtime($repository_model_path), $repository_model_url);
    }
    ?>
    <section class="home-repository home-feature-stack__card home-feature-stack__card--repository" aria-labelledby="home-repository-title">
        <div class="home-repository__content">
            <div class="home-repository__copy" data-reveal-group>
                <p class="home-repository__eyebrow"><?php echo esc_html(museotech_option('repository_eyebrow')); ?></p>
                <h2 class="home-repository__headline" id="home-repository-title" data-scroll-title><?php echo esc_html(museotech_option('repository_title')); ?></h2>
                <div class="home-repository__description" data-scroll-fade><?php echo museotech_formatted_text(museotech_option('repository_description')); ?></div>
                <a class="ui-button home-repository__action" data-scroll-fade data-magnetic href="<?php echo esc_url(museotech_option('repository_button_url')); ?>">
                    <?php echo esc_html(museotech_option('repository_button_label')); ?>
                    <?php echo museotech_ui_icon('arrow-right'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </div>
            <?php if (is_file($repository_model_path)) : ?>
                <div class="home-repository__visual">
                    <div class="home-repository__model" data-home-repository-model data-model-src="<?php echo esc_url($repository_model_url); ?>" data-model-meshopt="true" role="group" tabindex="0" aria-label="<?php esc_attr_e('Modelo 3D interactivo. Arrástralo para girarlo o usa las flechas.', 'museotech'); ?>"></div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
