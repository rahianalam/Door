<?php
/**
 * Author profile page
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();

$author_id    = get_query_var('author');
$author_name  = get_the_author_meta('display_name', $author_id);
$author_role  = dooroora_get_author_role($author_id);
$author_bio   = get_the_author_meta('description', $author_id);
$author_avatar = get_avatar_url($author_id, array('size' => 160));
$post_count   = count_user_posts($author_id);
?>

<div class="author-wrapper">
    <div class="container">

        <section class="author-hero">
            <div class="author-hero-inner">
                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar" loading="eager" />
                <h1 class="author-name"><?php echo esc_html($author_name); ?></h1>
                <?php if ($author_role) : ?>
                    <p class="author-role"><?php echo esc_html($author_role); ?></p>
                <?php endif; ?>
                <?php if ($author_bio) : ?>
                    <p class="author-bio"><?php echo esc_html($author_bio); ?></p>
                <?php endif; ?>
                <span class="author-stats"><?php echo esc_html(sprintf(_n('%d Guide Published', '%d Guides Published', $post_count, 'dooroora'), $post_count)); ?></span>
            </div>
        </section>

        <?php if (have_posts()) : ?>
            <section class="author-posts">
                <h2 class="section-title">
                    <?php printf(esc_html__('Articles by %s', 'dooroora'), $author_name); ?>
                </h2>
                <div class="author-posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/cards/blog-card'); ?>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            </section>
        <?php endif; ?>

    </div>
</div>

<?php
get_footer();
