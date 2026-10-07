<?php
$fields = [
    'autores' => __('Autores', 'museotech'),
    'editorial' => __('Editorial / Revista', 'museotech'),
    'fecha' => __('Fecha', 'museotech'),
    'isbn' => __('ISBN / ISSN', 'museotech'),
    'paginas' => __('Páginas', 'museotech'),
];
?>
<dl class="repository-details">
    <?php foreach ($fields as $name => $label) : ?>
        <?php $value = museotech_field($name); ?>
        <?php if ($value !== '' && $value !== null) : ?>
            <dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html((string) $value); ?></dd>
        <?php endif; ?>
    <?php endforeach; ?>
</dl>
<?php foreach (['archivo' => __('Abrir archivo', 'museotech'), 'recurso_externo' => __('Recurso externo', 'museotech'), 'uri' => __('URI', 'museotech')] as $name => $label) : ?>
    <?php $url = museotech_file_url(museotech_field($name)); ?>
    <?php if ($url) : ?><p><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a></p><?php endif; ?>
<?php endforeach; ?>