<?php
$description = museotech_field('descripcion_');
$audio_url = museotech_file_url(museotech_field('audio'));
?>
<dl class="podcast-details">
    <dt><?php esc_html_e('Institución / Programa', 'museotech'); ?></dt>
    <dd><?php echo esc_html((string) museotech_field('institucion')); ?></dd>
    <dt><?php esc_html_e('Entrevistado', 'museotech'); ?></dt>
    <dd><?php echo esc_html((string) museotech_field('entrevistado')); ?></dd>
    <dt><?php esc_html_e('Fecha de grabación', 'museotech'); ?></dt>
    <dd><?php echo esc_html((string) museotech_field('_fecha_de_grabacion')); ?></dd>
</dl>
<?php if ($description) : ?><div class="podcast-description"><?php echo wp_kses_post($description); ?></div><?php endif; ?>
<?php if ($audio_url) : ?><audio controls preload="metadata" src="<?php echo esc_url($audio_url); ?>"><?php esc_html_e('Tu navegador no admite audio HTML.', 'museotech'); ?></audio><?php endif; ?>