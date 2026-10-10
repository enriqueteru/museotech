<article <?php post_class(); ?>>
    <header class="collaborator-profile__header">
        <?php get_template_part('template-parts/components/podcast-banner-model', null, [
            'model_path' => 'assets/models/collaborator-hands.glb',
            'component_class' => 'collaborator-banner-model',
            'model_scale' => 1.05,
            'rotation_speed' => 0.16,
            'aria_label' => __('Modelo 3D interactivo de manos. Arrástralo o usa las flechas para girarlo; el giro automático respeta las preferencias de movimiento.', 'museotech'),
        ]); ?>
        <h1><?php the_title(); ?></h1>
    </header>
    <?php if (has_post_thumbnail()) : ?><?php the_post_thumbnail('large'); ?><?php endif; ?>
    <?php get_template_part('template-parts/sections/section', 'colaborador'); ?>
</article>