<section class="featured-posts-section">
    <div class="container">
        <h2 class="section-title">
            <?php esc_html_e('Latest from the Blog', 'dooroora'); ?>
        </h2>

        <?php
        $featured = new WP_Query(array(
            'posts_per_page' => 3,
            'post__not_in'   => get_option('sticky_posts'),
        ));

        if ($featured->have_posts()) : ?>
            <div class="post-grid featured-grid">
                <?php while ($featured->have_posts()) : $featured->the_post(); ?>
                    <?php get_template_part('template-parts/cards/post-card'); ?>
                <?php endwhile; ?>
            </div>
        <?php endif;
        wp_reset_postdata(); ?>
    </div>
</section>
