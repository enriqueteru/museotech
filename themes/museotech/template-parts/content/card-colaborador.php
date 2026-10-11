<?php
$website = trim((string) museotech_field('web'));
if ($website && has_post_thumbnail()) :
    $collaborator_name = get_the_title();
    ?>
    <article <?php post_class('collaborator-card'); ?>>
        <a class="collaborator-card__link" href="<?php echo esc_url($website); ?>" rel="external" aria-label="<?php echo esc_attr(sprintf(__('Visitar la web de %s', 'museotech'), $collaborator_name)); ?>">
            <?php the_post_thumbnail('medium_large', ['class' => 'collaborator-card__image', 'alt' => '', 'loading' => 'lazy']); ?>
        </a>
    </article>
<?php endif; ?>
