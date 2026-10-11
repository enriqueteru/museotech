<?php if (museotech_option('objectives_visible')) : ?>
    <section id="objetivos" class="home-objectives" data-scroll-trigger-start="top 78%">
        <header class="home-objectives__header" data-reveal-group>
            <h2 data-scroll-title><?php echo esc_html(museotech_option('objectives_title')); ?></h2>
        </header>
        <ol class="home-objectives__grid">
            <?php for ($index = 1; $index <= 6; $index++) : ?>
                <?php $objective = museotech_option('objective_' . $index); ?>
                <?php if ($objective) : ?>
                    <li class="home-objectives__item" data-icon="<?php echo esc_attr(museotech_option('objective_' . $index . '_icon')); ?>" data-reveal-group>
                        <h4 aria-hidden="true" data-scroll-fade><span data-count data-count-to="<?php echo esc_attr((string) $index); ?>"><?php echo esc_html(sprintf('%02d', $index)); ?></span></h4>
                        <p class="home-objectives__title" data-scroll-fade><?php echo esc_html($objective); ?></p>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>
        </ol>
    </section>
<?php endif; ?>