<?php
/*
Template Name: Museotech: Legal
Template Post Type: page
*/

get_header();
?>
<main id="main-content" class="legal-page">
    <?php while (have_posts()) : the_post(); ?>
        <div class="legal-page__shell">
            <article <?php post_class('legal-page__document'); ?>>
                <p class="legal-page__eyebrow"><?php esc_html_e('Información institucional', 'museotech'); ?></p>
                <header class="legal-page__header">
                    <h1><?php the_title(); ?></h1>
                    <span class="legal-page__rule" aria-hidden="true"></span>
                </header>
                <div class="legal-page__content entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
