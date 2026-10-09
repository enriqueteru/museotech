<?php
/*
Template Name: Museotech: Contacto
Template Post Type: page
*/

get_header();
?>
<main id="main-content" class="contact-page">
    <?php while (have_posts()) : the_post(); ?>
        <section class="contact-page__masthead" aria-label="<?php esc_attr_e('Contacto', 'museotech'); ?>">
            <div class="contact-page__marquee" aria-hidden="true">
                <div class="contact-page__marquee-track">
                    <?php for ($copy = 0; $copy < 2; $copy++) : ?>
                        <span><?php esc_html_e('Contacto / Contacto / Contacto / Contacto / ', 'museotech'); ?></span>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
        <section class="contact-page__layout">
            <div class="contact-page__details">
                <h1 class="contact-page__intro"><?php esc_html_e('Abramos juntos nuevas posibilidades. Escríbenos.', 'museotech'); ?></h1>
                <div class="contact-page__item contact-page__social">
                    <h2><?php esc_html_e('Redes sociales', 'museotech'); ?></h2>
                    <p>
                        <?php foreach (museotech_social_links() as $label => $url) : ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?></a>
                        <?php endforeach; ?>
                    </p>
                </div>
                <div class="contact-page__contact-info">
                    <div class="contact-page__item">
                        <h2><?php esc_html_e('Contacto', 'museotech'); ?></h2>
                        <a href="mailto:info@museotech.org">info@museotech.org</a>
                    </div>
                </div>
            </div>
            <div class="contact-page__form-column">
                <?php echo do_shortcode('[museotech_contact_form]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </section>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>