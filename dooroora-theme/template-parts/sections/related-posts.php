<section class="related-posts-section">
    <h2 class="section-title"><?php esc_html_e('Continue Reading', 'dooroora'); ?></h2>

    <?php
    $categories = wp_get_post_categories(get_the_ID(), array('fields' => 'ids'));
    if (!empty($categories)) :
        $related = new WP_Query(array(
            'category__in'        => $categories,
            'posts_per_page'      => 3,
            'post__not_in'        => array(get_the_ID()),
            'ignore_sticky_posts' => true,
        ));
        if ($related->have_posts()) :
    ?>
        <div class="related-posts-subsection">
            <h3 class="related-posts-subtitle"><?php esc_html_e('Related Guides in Same Category', 'dooroora'); ?></h3>
            <div class="related-posts-grid">
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <article class="related-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a class="related-post-card-thumb" href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('related-post', array('loading' => 'lazy')); ?>
                            </a>
                        <?php endif; ?>
                        <h4 class="related-post-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; endif; ?>

    <?php
    $related_terms = wp_get_post_terms(get_the_ID(), 'category');
    $related_slugs = array();
    foreach ($related_terms as $term) {
        $related_slugs[] = $term->slug;
    }
    $map = dooroora_get_category_mapping();
    $related_cats = array();
    foreach ($map as $slug => $data) {
        if (!in_array($slug, $related_slugs)) {
            $related_cats[$slug] = $data;
        }
    }
    if (!empty($related_cats)) :
        $cat_cross = new WP_Query(array(
            'category_name'    => implode(',', array_keys(array_slice($related_cats, 0, 2))),
            'posts_per_page'   => 3,
            'post__not_in'     => array(get_the_ID()),
            'ignore_sticky_posts' => true,
        ));
        if ($cat_cross->have_posts()) :
    ?>
        <div class="related-posts-subsection">
            <h3 class="related-posts-subtitle"><?php esc_html_e('Related Categories', 'dooroora'); ?></h3>
            <div class="related-posts-grid">
                <?php while ($cat_cross->have_posts()) : $cat_cross->the_post(); ?>
                    <article class="related-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a class="related-post-card-thumb" href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('related-post', array('loading' => 'lazy')); ?>
                            </a>
                        <?php endif; ?>
                        <h4 class="related-post-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; endif; ?>
</section>
