<?php if (museotech_option('objectives_visible')) : ?>
    <section id="objetivos" class="home-objectives">
        <header>
            <p><?php echo esc_html(museotech_option('objectives_title')); ?></p>
            <h2><?php echo esc_html(museotech_option('objectives_intro')); ?></h2>
        </header>
        <ul>
            <?php for ($index = 1; $index <= 6; $index++) : ?>
                <?php $objective = museotech_option('objective_' . $index); ?>
                <?php if ($objective) : ?>
                    <li data-icon="<?php echo esc_attr(museotech_option('objective_' . $index . '_icon')); ?>"><?php echo esc_html($objective); ?></li>
                <?php endif; ?>
            <?php endfor; ?>
        </ul>
    </section>
<?php endif; ?>