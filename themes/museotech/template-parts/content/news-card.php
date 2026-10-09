<?php
$categories = get_the_category();
$category = $categories[0] ?? null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('news-archive-card'); ?>>
    <a class="news-archive-card__link" href="<?php the_permalink(); ?>">
        <figure class="news-archive-card__media">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', ['loading' => 'lazy']); ?>
            <?php else : ?>
                <span class="news-archive-card__placeholder" aria-hidden="true"></span>
            <?php endif; ?>
        </figure>
        <div class="news-archive-card__body">
            <div class="news-archive-card__meta">
                <span><?php echo esc_html($category ? ucfirst($category->name) : __('Artículo', 'museotech')); ?></span>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d M y')); ?></time>
            </div>
            <h2><?php the_title(); ?></h2>
        </div>
    </a>
</article>
