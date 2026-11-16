<div class="author-box" itemscope itemtype="https://schema.org/Person">
    <div class="author-box-avatar">
        <?php echo get_avatar(get_the_author_meta('ID'), 96, '', get_the_author(), array('loading' => 'lazy')); ?>
    </div>
    <div class="author-box-info">
        <h4 class="author-box-name" itemprop="name"><?php the_author_posts_link(); ?></h4>
        <span class="author-box-role" itemprop="jobTitle"><?php echo esc_html(dooroora_get_author_role()); ?></span>
        <p class="author-box-bio" itemprop="description"><?php echo esc_html(get_the_author_meta('description')); ?></p>
        <a class="author-box-link" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
            <?php esc_html_e('View All Guides by', 'dooroora'); ?> <?php the_author(); ?> &rarr;
        </a>
    </div>
</div>
