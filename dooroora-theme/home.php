<?php
/**
 * Blog archive template (/blog/)
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<div class="blog-archive-wrapper">
    <div class="container">

        <!-- Blog Hero -->
        <section class="blog-hero">
            <h1 class="blog-hero-title"><?php echo get_the_title(get_option('page_for_posts', true)); ?></h1>
            <p class="blog-hero-desc"><?php esc_html_e('Expert door guides, troubleshooting tutorials, security advice and installation resources.', 'dooroora'); ?></p>
            <div class="blog-hero-search">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <label for="blog-search-input" class="screen-reader-text"><?php esc_html_e('Search guides', 'dooroora'); ?></label>
                    <input type="search" id="blog-search-input" class="blog-search-input" placeholder="<?php esc_attr_e('Search guides...', 'dooroora'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="btn btn-primary blog-search-btn"><?php esc_html_e('Search', 'dooroora'); ?></button>
                </form>
            </div>
        </section>

        <!-- Filters -->
        <div class="blog-filters" id="blog-filters">
            <div class="blog-filters-row">
                <div class="blog-filter-categories">
                    <button class="blog-filter-tag active" data-filter="all"><?php esc_html_e('All', 'dooroora'); ?></button>
                    <?php
                    $categories = get_categories(array('hide_empty' => false));
                    $map = dooroora_get_category_mapping();
                    foreach ($categories as $cat) :
                        if (!isset($map[$cat->slug])) continue;
                    ?>
                        <button class="blog-filter-tag" data-filter="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="blog-filter-sort">
                    <select class="blog-sort-select" id="blog-sort-select">
                        <option value="latest"><?php esc_html_e('Latest', 'dooroora'); ?></option>
                        <option value="updated"><?php esc_html_e('Updated', 'dooroora'); ?></option>
                        <option value="popular"><?php esc_html_e('Popular', 'dooroora'); ?></option>
                        <option value="title"><?php esc_html_e('A-Z', 'dooroora'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid-container" id="blog-grid-container">
            <div class="blog-grid" id="blog-grid">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/cards/blog-card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p class="blog-no-results"><?php esc_html_e('No guides found.', 'dooroora'); ?></p>
                <?php endif; ?>
            </div>

            <?php the_posts_pagination(array('class' => 'blog-pagination')); ?>
        </div>

        <div class="blog-loading" id="blog-loading" style="display:none;">
            <i class="fa-solid fa-spinner fa-spin"></i> <?php esc_html_e('Loading guides...', 'dooroora'); ?>
        </div>

    </div>
</div>

<?php
get_footer();
