<?php
$archive_title = post_type_archive_title('', false);
$archive_heading = museotech_option('archive_colaborador_heading');
$archive_heading = is_string($archive_heading) ? trim($archive_heading) : '';
$archive_title = $archive_title !== '' ? $archive_title : __('Colaboradores', 'museotech');
$archive_heading = $archive_heading !== $archive_title ? $archive_heading : '';
$contact_page = get_page_by_path('contacto');
$contact_url = $contact_page ? get_permalink($contact_page) : home_url('/contacto/');
?>
<?php get_header(); ?>
<main id="main-content" class="collaborators-page">
    <header class="collaborators-page__banner section-banner">
        <div class="collaborators-page__banner-inner">
            <div class="section-banner__copy">
                <h1 class="section-banner__title"><?php echo esc_html($archive_title); ?></h1>
                <?php if ($archive_heading !== '') : ?>
                    <p class="section-banner__description"><?php echo esc_html($archive_heading); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="collaborators-page__list" aria-label="<?php esc_attr_e('Listado de colaboradores', 'museotech'); ?>">
        <?php if (!have_posts()) : ?>
            <p class="collaborators-page__empty"><?php esc_html_e('No hay colaboradores disponibles.', 'museotech'); ?></p>
        <?php endif; ?>
        <div class="collaborators-page__grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content/card', 'colaborador'); ?>
            <?php endwhile; ?>
            <div class="collaborator-card collaborator-card--join">
                <a class="collaborator-card__link" href="<?php echo esc_url($contact_url); ?>" aria-label="<?php esc_attr_e('Ser colaborador: ir a contacto', 'museotech'); ?>">
                    <svg class="collaborator-card__join-icon" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
                        <path d="M24 10v28M10 24h28" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </a>
            </div>
        </div>
        <?php the_posts_pagination(); ?>
    </section>
</main>
<?php get_footer(); ?>
