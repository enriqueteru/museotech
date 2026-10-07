<article <?php post_class(); ?>>
    <header><h1><?php the_title(); ?></h1></header>
    <?php if (get_post_type() === 'post') : ?>
        <p class="entry-meta">
            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
            <span><?php the_author_posts_link(); ?></span>
        </p>
        <?php the_category(' '); ?>
    <?php endif; ?>
    <?php if (has_post_thumbnail()) : ?><?php the_post_thumbnail('large'); ?><?php endif; ?>
    <div class="entry-content"><?php the_content(); ?></div>
</article>