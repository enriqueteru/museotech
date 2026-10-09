<?php $marquee_words = [__('Novedades', 'museotech'), __('Reflexiones', 'museotech'), __('Actualidad', 'museotech')]; ?>
<section class="home-marquee" data-home-marquee aria-label="<?php esc_attr_e('Novedades, reflexiones y actualidad', 'museotech'); ?>">
    <div class="home-marquee__inner" data-home-marquee-track aria-hidden="true">
        <?php for ($group = 0; $group < 2; $group++) : ?>
            <div class="home-marquee__group">
                <?php for ($item = 0; $item < 6; $item++) : ?>
                    <div class="home-marquee__part" data-home-marquee-part>
                        <span><?php echo esc_html($marquee_words[$item % count($marquee_words)]); ?></span>
                        <svg class="home-marquee__arrow" data-home-marquee-arrow viewBox="0 0 72 100" focusable="false" aria-hidden="true">
                            <path fill="currentColor" d="M70.4,58.9L70.1,57l-0.2-0.9-0.2-0.9c-18.7,3.4-27.6,13.4-31.9,22.7V3H34.1v75.3C29.9,68.6,20.9,58.1,2.2,55.1L1.6,58.9c28.7,4.5,32.2,27.6,32.5,35.3-0.1,1.7,0,2.7,0,2.8l3.8,0.1v-4.1C38.4,84.4,42.5,63.9,70.4,58.9z" />
                        </svg>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>
</section>
