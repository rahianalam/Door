<?php
/**
 * Search results template
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<div class="search-wrapper">
    <div class="container">
        <header class="search-header">
            <h1 class="search-title">
                <?php
                printf(
                    esc_html__('Search Results for: %s', 'dooroora'),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            <div class="search-form-modern">
                <form role="search" method="get" class="search-form-modern-inner" action="<?php echo esc_url(home_url('/')); ?>">
                    <label for="search-field" class="screen-reader-text"><?php esc_html_e('Search', 'dooroora'); ?></label>
                    <input type="search" id="search-field" class="search-field-modern" placeholder="<?php esc_attr_e('Search guides, repairs, locks, garage doors...', 'dooroora'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="btn btn-primary search-btn-modern"><?php esc_html_e('Search', 'dooroora'); ?></button>
                </form>
            </div>
        </header>

        <?php if (have_posts()) : ?>
            <div class="search-results-count">
                <p><?php printf(esc_html__('Found %d result(s)', 'dooroora'), $wp_query->found_posts); ?></p>
            </div>
            <div class="search-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/cards/blog-card'); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <div class="search-no-results">
                <div class="search-no-results-icon"><i class="fa-regular fa-circle-question"></i></div>
                <p><?php esc_html_e('No results found for your search.', 'dooroora'); ?></p>
                <p><?php esc_html_e('Please try a different search term.', 'dooroora'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
