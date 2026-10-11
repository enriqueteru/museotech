<?php if (museotech_option('hero_visible')) : ?>
    <?php
    $description_parts = preg_split('/\R\s*\R/', trim((string) museotech_option('hero_description'))) ?: [];
    $hero_description = $description_parts[0] ?? '';
    $hero_model_path = get_theme_file_path('/assets/models/face2.glb');
    $hero_model_url = get_theme_file_uri('/assets/models/face2.glb');
    if (file_exists($hero_model_path)) {
        $hero_model_url = add_query_arg('ver', (string) filemtime($hero_model_path), $hero_model_url);
    }
    ?>
    <section class="home-hero" data-hero>
        <div class="home-hero__content">
            <div class="home-hero__model" data-home-hero-model data-model-src="<?php echo esc_url($hero_model_url); ?>" data-model-meshopt="true" role="group" tabindex="0" aria-label="<?php esc_attr_e('Modelo 3D interactivo. Arrástralo para girarlo o usa las flechas.', 'museotech'); ?>"></div>
            <div class="home-hero__copy">
                <h1 class="home-hero__headline" data-hero-title><?php esc_html_e('La cultura, conectada al futuro.', 'museotech'); ?></h1>
                <?php if ($hero_description) : ?>
                    <p class="home-hero__description" data-hero-fade><?php echo esc_html($hero_description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
