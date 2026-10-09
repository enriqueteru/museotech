<?php
$website = trim((string) museotech_field('web'));
if ($website && has_post_thumbnail()) :
    ?>
    <article <?php post_class('collaborator-card'); ?>>
        <a class="collaborator-card__link" href="<?php echo esc_url($website); ?>" rel="external">
            <?php the_post_thumbnail('medium_large', ['class' => 'collaborator-card__image', 'alt' => get_the_title(), 'loading' => 'lazy']); ?>
        </a>
    </article>
<?php endif; ?>