<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a class="post-card-thumbnail" href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('blog-grid'); ?>
        </a>
    <?php endif; ?>

    <div class="post-card-body">
        <?php if (has_category()) : ?>
            <div class="post-card-categories">
                <?php the_category(', '); ?>
            </div>
        <?php endif; ?>

        <h3 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="post-card-excerpt">
            <?php the_excerpt(); ?>
        </div>

        <div class="post-card-meta">
            <span class="post-card-date"><?php echo esc_html(get_the_date()); ?></span>
        </div>
    </div>
</article>
