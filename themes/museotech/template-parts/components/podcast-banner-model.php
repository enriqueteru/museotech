<?php
$component_args = isset($args) && is_array($args) ? $args : [];
$model_path = isset($component_args['model_path']) && is_string($component_args['model_path'])
    ? '/' . ltrim($component_args['model_path'], '/')
    : '/assets/models/podcast-banner.glb';
$model_file_path = get_theme_file_path($model_path);
$model_url = isset($component_args['model_url']) && is_string($component_args['model_url'])
    ? $component_args['model_url']
    : get_theme_file_uri($model_path);
if (is_file($model_file_path)) {
    $model_url = add_query_arg('ver', (string) filemtime($model_file_path), $model_url);
}
$rotation_speed = isset($component_args['rotation_speed']) ? (float) $component_args['rotation_speed'] : 0.2;
$model_scale = isset($component_args['model_scale']) ? (float) $component_args['model_scale'] : 1.05;
$model_rotation_x = isset($component_args['model_rotation_x']) ? (float) $component_args['model_rotation_x'] : 0;
$model_rotation_y = isset($component_args['model_rotation_y']) ? (float) $component_args['model_rotation_y'] : 0;
$model_rotation_z = isset($component_args['model_rotation_z']) ? (float) $component_args['model_rotation_z'] : 0;
$model_position_x = isset($component_args['model_position_x']) ? (float) $component_args['model_position_x'] : 0;
$model_position_y = isset($component_args['model_position_y']) ? (float) $component_args['model_position_y'] : 0;
$model_position_z = isset($component_args['model_position_z']) ? (float) $component_args['model_position_z'] : 0;
$model_offset_x = isset($component_args['model_offset_x']) ? (float) $component_args['model_offset_x'] : 0;
$model_offset_y = isset($component_args['model_offset_y']) ? (float) $component_args['model_offset_y'] : 0;
$meshopt_compressed = ! empty($component_args['meshopt_compressed']);
$rotation_speed = max(-1.5, min(1.5, $rotation_speed));
$model_scale = max(0.4, min(2.5, $model_scale));
$model_rotation_x = max(-360, min(360, $model_rotation_x));
$model_rotation_y = max(-360, min(360, $model_rotation_y));
$model_rotation_z = max(-360, min(360, $model_rotation_z));
$model_position_x = max(-5, min(5, $model_position_x));
$model_position_y = max(-5, min(5, $model_position_y));
$model_position_z = max(-5, min(5, $model_position_z));
$model_offset_x = max(-1000, min(1000, $model_offset_x));
$model_offset_y = max(-1000, min(1000, $model_offset_y));
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
        data-model-meshopt="<?php echo $meshopt_compressed ? 'true' : 'false'; ?>"
        data-model-rotation-speed="<?php echo esc_attr((string) $rotation_speed); ?>"
        data-model-scale="<?php echo esc_attr((string) $model_scale); ?>"
        data-model-rotation-x="<?php echo esc_attr((string) $model_rotation_x); ?>"
        data-model-rotation-y="<?php echo esc_attr((string) $model_rotation_y); ?>"
        data-model-rotation-z="<?php echo esc_attr((string) $model_rotation_z); ?>"
        data-model-position-x="<?php echo esc_attr((string) $model_position_x); ?>"
        data-model-position-y="<?php echo esc_attr((string) $model_position_y); ?>"
        data-model-position-z="<?php echo esc_attr((string) $model_position_z); ?>"
        style="--model-offset-x: <?php echo esc_attr((string) $model_offset_x); ?>px; --model-offset-y: <?php echo esc_attr((string) $model_offset_y); ?>px;"
        role="group"
        tabindex="0"
        aria-label="<?php echo esc_attr($aria_label); ?>"
    ></div>
<?php endif; ?>
