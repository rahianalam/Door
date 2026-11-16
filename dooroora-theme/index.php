<?php
/**
 * Fallback template
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<div class="content-area">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-summary"><?php the_excerpt(); ?></div>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No content found.', 'dooroora'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
