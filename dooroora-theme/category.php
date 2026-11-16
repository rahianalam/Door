<?php
/**
 * Category template with hero, popular, related cats
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();

$category      = get_queried_object();
$category_slug = $category->slug;
$cat_icon      = dooroora_get_category_icon($category_slug);
$cat_color     = dooroora_get_category_color($category_slug);
$cat_desc      = dooroora_get_category_description($category_slug);
$cat_count     = $category->count;

$editor_picks = new WP_Query(array(
    'category_name'    => $category_slug,
    'posts_per_page'   => 3,
    'meta_key'         => 'dooroora_editor_pick',
    'meta_value'       => '1',
    'ignore_sticky_posts' => true,
));

$popular = new WP_Query(array(
    'category_name'    => $category_slug,
    'posts_per_page'   => 4,
    'ignore_sticky_posts' => true,
    'orderby'          => 'comment_count',
    'order'            => 'DESC',
));

$map = dooroora_get_category_mapping();
$related_categories = array();
foreach ($map as $slug => $data) {
    if ($slug !== $category_slug) {
        $related_categories[$slug] = $data;
    }
}
?>

<div class="category-wrapper">
    <div class="container">

        <?php dooroora_breadcrumbs(); ?>

        <!-- Category Hero -->
        <section class="category-hero" style="--cat-color: <?php echo esc_attr($cat_color); ?>">
            <div class="category-hero-icon"><i class="<?php echo esc_attr($cat_icon); ?>"></i></div>
            <h1 class="category-hero-title"><?php single_cat_title(); ?></h1>
            <p class="category-hero-desc"><?php echo $cat_desc ? esc_html($cat_desc) : category_description(); ?></p>
            <div class="category-hero-stats">
                <span class="cat-stat"><strong><?php echo esc_html($cat_count); ?></strong> <?php echo _n('Guide Published', 'Guides Published', $cat_count, 'dooroora'); ?></span>
            </div>
        </section>

        <!-- Editor's Picks -->
        <?php if ($editor_picks->have_posts()) : ?>
            <section class="category-featured">
                <h2 class="section-title"><?php esc_html_e("Editor's Picks", 'dooroora'); ?></h2>
                <div class="featured-grid">
                    <?php while ($editor_picks->have_posts()) : $editor_picks->the_post(); ?>
                        <?php get_template_part('template-parts/cards/blog-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Latest Articles Grid -->
        <section class="category-posts">
            <h2 class="section-title"><?php esc_html_e('Latest Articles', 'dooroora'); ?></h2>
            <?php if (have_posts()) : ?>
                <div class="category-posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/cards/blog-card'); ?>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <p><?php esc_html_e('No posts in this category.', 'dooroora'); ?></p>
            <?php endif; ?>
        </section>

        <!-- Popular Articles -->
        <?php if ($popular->have_posts()) : ?>
            <section class="category-popular">
                <h2 class="section-title"><?php esc_html_e('Popular Articles', 'dooroora'); ?></h2>
                <div class="popular-grid">
                    <?php while ($popular->have_posts()) : $popular->the_post(); ?>
                        <?php get_template_part('template-parts/cards/blog-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Related Categories -->
        <section class="category-related">
            <h2 class="section-title"><?php esc_html_e('Related Categories', 'dooroora'); ?></h2>
            <div class="related-cats-grid">
                <?php foreach (array_slice($related_categories, 0, 3) as $slug => $data) :
                    $cat_obj = get_category_by_slug($slug);
                    if (!$cat_obj) continue;
                ?>
                    <a href="<?php echo esc_url(get_category_link($cat_obj->term_id)); ?>" class="related-cat-card" style="--cat-color: <?php echo esc_attr($data['color']); ?>">
                        <span class="related-cat-icon"><i class="<?php echo esc_attr($data['icon']); ?>"></i></span>
                        <span class="related-cat-name"><?php echo esc_html($data['label']); ?> &rarr;</span>
                        <span class="related-cat-desc"><?php echo esc_html($data['description']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</div>

<?php
get_footer();
