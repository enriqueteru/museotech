<?php
$website = trim((string) museotech_field('web'));
if ($website && has_post_thumbnail()) :
    $collaborator_name = get_the_title();
    ?>
    <article <?php post_class('collaborator-card'); ?>>
        <a class="collaborator-card__link" href="<?php echo esc_url($website); ?>" rel="external" aria-label="<?php echo esc_attr(sprintf(__('Visitar la web de %s', 'museotech'), $collaborator_name)); ?>">
            <span class="collaborator-card__meta">
                <span><?php esc_html_e('Colaborador', 'museotech'); ?></span>
                <span class="collaborator-card__external" aria-hidden="true">↗</span>
            </span>
            <span class="collaborator-card__logo">
                <?php the_post_thumbnail('medium_large', ['class' => 'collaborator-card__image', 'alt' => '', 'loading' => 'lazy']); ?>
            </span>
            <span class="collaborator-card__footer">
                <strong class="collaborator-card__name"><?php echo esc_html($collaborator_name); ?></strong>
                <span class="collaborator-card__visit"><?php esc_html_e('Visitar sitio web', 'museotech'); ?></span>
            </span>
        </a>
    </article>
<?php endif; ?>