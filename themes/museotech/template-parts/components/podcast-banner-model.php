<?php
$component_args = isset($args) && is_array($args) ? $args : [];
$model_path = isset($component_args['model_path']) && is_string($component_args['model_path'])
    ? '/' . ltrim($component_args['model_path'], '/')
    : '/assets/models/podcast-banner-meshy.glb';
$model_file_path = get_theme_file_path($model_path);
$model_url = isset($component_args['model_url']) && is_string($component_args['model_url'])
    ? $component_args['model_url']
    : get_theme_file_uri($model_path);
if (is_file($model_file_path)) {
    $model_url = add_query_arg('ver', (string) filemtime($model_file_path), $model_url);
}
$rotation_speed = isset($component_args['rotation_speed']) ? (float) $component_args['rotation_speed'] : 0.2;
$model_scale = isset($component_args['model_scale']) ? (float) $component_args['model_scale'] : 1.05;
$rotation_speed = max(-1.5, min(1.5, $rotation_speed));
$model_scale = max(0.4, min(2.5, $model_scale));
$aria_label = isset($component_args['aria_label']) && is_string($component_args['aria_label'])
    ? $component_args['aria_label']
    : __('Modelo 3D interactivo. Arrástralo o usa las flechas para girarlo; el giro automático respeta las preferencias de movimiento.', 'museotech');
$component_class = isset($component_args['component_class']) && is_string($component_args['component_class'])
    ? sanitize_html_class($component_args['component_class'])
    : 'podcast-banner-model';
?>
<?php if (is_file($model_file_path)) : ?>
    <div
        class="<?php echo esc_attr($component_class); ?>"
        data-podcast-banner-model
        data-model-src="<?php echo esc_url($model_url); ?>"
        data-model-rotation-speed="<?php echo esc_attr((string) $rotation_speed); ?>"
        data-model-scale="<?php echo esc_attr((string) $model_scale); ?>"
        role="group"
        tabindex="0"
        aria-label="<?php echo esc_attr($aria_label); ?>"
    ></div>
<?php endif; ?>
