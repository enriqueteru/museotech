<?php if (museotech_option('objectives_visible')) : ?>
    <section id="objetivos" class="home-objectives">
        <header class="home-objectives__header">
            <h2><?php echo esc_html(museotech_option('objectives_title')); ?></h2>
        </header>
        <ol class="home-objectives__grid">
            <?php for ($index = 1; $index <= 6; $index++) : ?>
                <?php $objective = museotech_option('objective_' . $index); ?>
                <?php if ($objective) : ?>
                    <li class="home-objectives__item" data-icon="<?php echo esc_attr(museotech_option('objective_' . $index . '_icon')); ?>">
                        <h6 aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index)); ?></h6>
                        <p class="home-objectives__title"><?php echo esc_html($objective); ?></p>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>
        </ol>
    </section>
<?php endif; ?>