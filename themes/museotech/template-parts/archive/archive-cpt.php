<?php $post_type = get_query_var('post_type'); ?>
<?php $post_type = is_array($post_type) ? reset($post_type) : $post_type; ?>
<header class="archive-header">
	<h1><?php post_type_archive_title(); ?></h1>
	<?php if ($post_type) : ?>
		<p><?php echo esc_html(museotech_option('archive_' . $post_type . '_heading')); ?></p>
	<?php endif; ?>
</header>
<?php get_template_part('template-parts/archive/archive', 'posts'); ?>