<?php
$args = isset($args) && is_array($args) ? $args : [];
$details = isset($args['details']) && is_array($args['details']) ? $args['details'] : [];
$resources = isset($args['resources']) && is_array($args['resources']) ? $args['resources'] : [];
$citation = isset($args['citation']) && is_string($args['citation']) ? $args['citation'] : '';
$export_data = isset($args['export_data']) && is_array($args['export_data']) ? $args['export_data'] : [];
$export_filename = isset($args['export_filename']) && is_string($args['export_filename']) ? $args['export_filename'] : 'museotech-repositorio.json';
$created = isset($args['created']) && is_array($args['created']) ? $args['created'] : [];
$modified = isset($args['modified']) && is_array($args['modified']) ? $args['modified'] : [];
?>
<aside class="repository-single__sidebar" aria-label="<?php esc_attr_e('Información del repositorio', 'museotech'); ?>">
    <?php if ($details) : ?>
        <section class="repository-single__panel repository-single__details" aria-labelledby="repository-details-title">
            <h2 id="repository-details-title"><?php esc_html_e('Detalles', 'museotech'); ?></h2>
            <dl>
                <?php foreach ($details as $label => $value) : ?>
                    <div>
                        <dt><?php echo esc_html((string) $label); ?></dt>
                        <dd><?php echo esc_html((string) $value); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </section>
    <?php endif; ?>

    <?php if ($resources) : ?>
        <section class="repository-single__panel repository-single__external" aria-labelledby="repository-external-title">
            <h2 id="repository-external-title"><?php esc_html_e('Recursos externos', 'museotech'); ?></h2>
            <ul>
                <?php foreach ($resources as $resource) : ?>
                    <?php if (! is_array($resource) || empty($resource['url'])) { continue; } ?>
                    <li>
                        <a class="ui-button ui-button--resource" href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <span><?php echo esc_html(isset($resource['label']) ? (string) $resource['label'] : __('Abrir recurso', 'museotech')); ?></span>
                            <?php if (! empty($resource['host'])) : ?>
                                <small><?php echo esc_html((string) $resource['host']); ?></small>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($citation !== '') : ?>
        <section class="repository-single__panel repository-single__citation" aria-labelledby="repository-citation-title">
            <h2 id="repository-citation-title"><?php esc_html_e('Cita sugerida', 'museotech'); ?></h2>
            <p id="repository-single-citation"><?php echo esc_html($citation); ?></p>
            <button class="ui-button ui-button--compact repository-single__action" type="button" data-repository-copy data-copy-target="repository-single-citation" aria-describedby="repository-single-copy-status">
                <?php esc_html_e('Copiar cita', 'museotech'); ?>
            </button>
            <span class="repository-single__status" id="repository-single-copy-status" role="status" aria-live="polite"></span>
        </section>
    <?php endif; ?>

    <?php if ($export_data) : ?>
        <section class="repository-single__panel repository-single__export" aria-labelledby="repository-export-title">
            <h2 id="repository-export-title"><?php esc_html_e('Exportar', 'museotech'); ?></h2>
            <p><?php esc_html_e('Descarga los metadatos disponibles en formato JSON.', 'museotech'); ?></p>
            <button class="ui-button ui-button--compact repository-single__action" type="button" data-repository-export data-export-target="repository-single-export-data" data-export-filename="<?php echo esc_attr($export_filename); ?>" aria-describedby="repository-single-export-status">
                <?php esc_html_e('Exportar JSON', 'museotech'); ?>
            </button>
            <span class="repository-single__status" id="repository-single-export-status" role="status" aria-live="polite"></span>
            <script type="application/json" id="repository-single-export-data"><?php echo wp_json_encode($export_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
        </section>
    <?php endif; ?>

    <?php if ($created || $modified) : ?>
        <section class="repository-single__panel repository-single__technical" aria-labelledby="repository-technical-title">
            <h2 id="repository-technical-title"><?php esc_html_e('Metadatos técnicos', 'museotech'); ?></h2>
            <dl>
                <?php foreach ([$created, $modified] as $technical_date) : ?>
                    <?php if (empty($technical_date['display'])) { continue; } ?>
                    <div>
                        <dt><?php echo esc_html(isset($technical_date['label']) ? (string) $technical_date['label'] : ''); ?></dt>
                        <dd><time datetime="<?php echo esc_attr(isset($technical_date['datetime']) ? (string) $technical_date['datetime'] : ''); ?>"><?php echo esc_html((string) $technical_date['display']); ?></time></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </section>
    <?php endif; ?>
</aside>