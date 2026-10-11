<?php
$echoes_model_path = get_theme_file_path('/assets/models/echoes-of-marble.glb');
$echoes_model_url = get_theme_file_uri('/assets/models/echoes-of-marble.glb');

if (is_file($echoes_model_path)) {
    $echoes_model_url = add_query_arg('ver', (string) filemtime($echoes_model_path), $echoes_model_url);
}
?>
<?php if (is_file($echoes_model_path)) : ?>
    <section id="echoes-of-marble" class="home-echoes" aria-label="<?php esc_attr_e('Escultura Echoes of Marble', 'museotech'); ?>">
        <div class="home-echoes__content">
            <div class="home-echoes__model" data-home-echoes-model data-model-src="<?php echo esc_url($echoes_model_url); ?>" data-model-meshopt="true" role="group" tabindex="0" aria-label="<?php esc_attr_e('Modelo 3D interactivo. Arrástralo para girarlo o usa las flechas.', 'museotech'); ?>"></div>
        </div>
    </section>
<?php endif; ?>
