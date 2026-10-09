<?php
$archive_title = post_type_archive_title('', false);
$archive_heading = museotech_option('archive_colaborador_heading');
$archive_heading = is_string($archive_heading) ? trim($archive_heading) : '';
$archive_title = $archive_title !== '' ? $archive_title : __('Colaboradores', 'museotech');
$archive_heading = $archive_heading !== '' ? $archive_heading : $archive_title;
?>
<?php get_header(); ?>
<main id="main-content" class="collaborators-page">
    <header class="collaborators-page__banner section-banner">
        <div class="collaborators-page__banner-inner">
            <h1 class="section-banner__title"><?php echo esc_html($archive_title); ?></h1>
            <p class="section-banner__description"><?php echo esc_html($archive_heading); ?></p>
        </div>
    </header>

    <section class="collaborators-page__list" aria-label="<?php esc_attr_e('Listado de colaboradores', 'museotech'); ?>">
        <?php if (have_posts()) : ?>
            <div class="collaborators-page__grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content/card', 'colaborador'); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p class="collaborators-page__empty"><?php esc_html_e('No hay colaboradores disponibles.', 'museotech'); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>