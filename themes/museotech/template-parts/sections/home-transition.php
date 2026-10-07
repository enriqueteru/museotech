<?php
$transition_index = absint($args['index'] ?? 0);
$image_id = absint(museotech_option('transition_' . $transition_index . '_image'));
if (! $image_id) {
    return;
}
?>
<section class="home-scroll-transition" data-home-scroll-transition aria-hidden="true">
    <div class="home-scroll-transition__visual">
        <?php echo wp_get_attachment_image($image_id, 'full', false, ['class' => 'home-scroll-transition__image', 'alt' => '', 'loading' => 'eager', 'decoding' => 'async']); ?>
        <?php foreach (['top', 'bottom'] as $edge) : ?>
            <div class="home-scroll-transition__rows home-scroll-transition__rows--<?php echo esc_attr($edge); ?>">
                <?php for ($row = 0; $row < 4; $row++) : ?>
                    <div class="home-scroll-transition__row"></div>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>