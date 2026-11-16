<?php
/**
 * Archive template
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<div class="archive-wrapper">
    <div class="container">
        <header class="archive-header">
            <?php
            the_archive_title('<h1 class="archive-title">', '</h1>');
            the_archive_description('<div class="archive-desc">', '</div>');
            ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="archive-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/cards/blog-card'); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No posts found.', 'dooroora'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
