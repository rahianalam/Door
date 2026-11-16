<article id="post-<?php the_ID(); ?>" <?php post_class('guide-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a class="guide-card-thumb" href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('blog-grid', array('loading' => 'lazy')); ?>
        </a>
    <?php endif; ?>
    <div class="guide-card-body">
        <?php if (has_category()) : ?>
            <div class="guide-card-category"><?php the_category(', '); ?></div>
        <?php endif; ?>
        <h3 class="guide-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="guide-card-meta">
            <span class="guide-card-author"><?php echo esc_html(get_the_author()); ?></span>
            <span class="guide-card-date"><?php echo esc_html(get_the_date()); ?></span>
            <span class="guide-card-readtime"><?php echo esc_html(dooroora_reading_time()); ?></span>
        </div>
        <div class="guide-card-excerpt"><?php echo esc_html(dooroora_excerpt(30)); ?></div>
        <a class="guide-card-cta" href="<?php the_permalink(); ?>"><?php esc_html_e('Read Guide', 'dooroora'); ?> &rarr;</a>
    </div>
</article>
