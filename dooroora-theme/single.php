<?php
/**
 * Single post template — authority content page
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();

$author_id    = get_post_field('post_author', get_the_ID());
$author_name  = get_the_author_meta('display_name', $author_id);
$author_role  = dooroora_get_author_role($author_id);
$author_avatar = get_avatar_url($author_id, array('size' => 80));
$updated_date = dooroora_get_post_updated_date();
$reading_time = dooroora_reading_time();
?>

<div class="single-post-wrapper">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>

                <!-- Breadcrumbs -->
                <?php dooroora_breadcrumbs(); ?>

                <!-- Hero Image (single featured image) -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="single-hero-image">
                        <?php the_post_thumbnail('hero-large', array('loading' => 'eager', 'class' => 'single-hero-img')); ?>
                    </div>
                <?php endif; ?>

                <div class="single-content-layout">
                    <div class="single-content-main">

                        <!-- Article Header -->
                        <header class="single-header">
                            <?php if (has_category()) : ?>
                                <div class="single-category"><?php the_category(', '); ?></div>
                            <?php endif; ?>

                            <h1 class="single-title"><?php the_title(); ?></h1>

                            <div class="single-author-info">
                                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="single-author-avatar" loading="lazy" />
                                <div class="single-author-details">
                                    <span class="single-author-name"><?php echo esc_html($author_name); ?></span>
                                    <span class="single-author-role"><?php echo esc_html($author_role); ?></span>
                                </div>
                            </div>

                            <div class="single-dates">
                                <span class="single-date-published"><i class="fa-regular fa-calendar"></i> <?php esc_html_e('Published:', 'dooroora'); ?> <?php echo esc_html(get_the_date()); ?></span>
                                <?php if ($updated_date) : ?>
                                    <span class="single-date-updated"><i class="fa-regular fa-pen-to-square"></i> <?php esc_html_e('Last Updated:', 'dooroora'); ?> <?php echo esc_html($updated_date); ?></span>
                                <?php endif; ?>
                                <span class="single-reading-time"><i class="fa-regular fa-clock"></i> <?php echo esc_html($reading_time); ?></span>
                            </div>
                        </header>

                        <!-- Trust Block -->
                        <div class="single-trust-block">
                            <span class="trust-block-item"><i class="fa-solid fa-check-circle"></i> <?php esc_html_e('Reviewed by DoorOora Editorial Team', 'dooroora'); ?></span>
                            <span class="trust-block-item"><i class="fa-solid fa-check-circle"></i> <?php esc_html_e('Fact Checked', 'dooroora'); ?></span>
                            <span class="trust-block-item"><i class="fa-solid fa-check-circle"></i> <?php esc_html_e('Updated for Accuracy', 'dooroora'); ?></span>
                        </div>

                        <!-- Social Sharing -->
                        <?php get_template_part('template-parts/sections/social-sharing'); ?>

                        <!-- Table of Contents (H2 only, collapsed default) -->
                        <div class="single-toc collapsed" id="single-toc">
                            <button class="toc-toggle" aria-expanded="false">
                                <span class="toc-title"><?php esc_html_e('In This Article', 'dooroora'); ?></span>
                                <i class="fa-solid fa-chevron-down toc-arrow"></i>
                            </button>
                            <div class="toc-content">
                                <nav aria-label="<?php esc_attr_e('Table of Contents', 'dooroora'); ?>">
                                    <ul class="toc-list" id="toc-list"></ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="single-content entry-content" id="single-content">
                            <?php the_content(); ?>
                        </div>

                        <!-- FAQ Section -->
                        <?php
                        $faq_blocks = dooroora_extract_faq_blocks();
                        if (!empty($faq_blocks)) :
                        ?>
                            <div class="single-faqs" itemscope itemtype="https://schema.org/FAQPage">
                                <h2 class="faq-section-title"><?php esc_html_e('Frequently Asked Questions', 'dooroora'); ?></h2>
                                <?php foreach ($faq_blocks as $faq) : ?>
                                    <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <button class="faq-question" itemprop="name" aria-expanded="false">
                                            <?php echo esc_html($faq['question']); ?>
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <div itemprop="text"><?php echo wp_kses_post($faq['answer']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <?php if (has_tag()) : ?>
                            <div class="single-tags">
                                <span class="tags-label"><?php esc_html_e('Tags:', 'dooroora'); ?></span>
                                <?php the_tags('', ' '); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Author Box -->
                        <?php get_template_part('template-parts/sections/author-box'); ?>

                        <!-- Related Posts -->
                        <?php get_template_part('template-parts/sections/related-posts'); ?>

                        <!-- Comments -->
                        <?php
                        if (comments_open() || get_comments_number()) {
                            comments_template();
                        }
                        ?>

                    </div><!-- .single-content-main -->

                    <!-- Sidebar (Search + Latest Posts + Categories with icons) -->
                    <aside class="single-sidebar" id="single-sidebar">
                        <?php if (is_active_sidebar('sidebar-single')) : ?>
                            <?php dynamic_sidebar('sidebar-single'); ?>
                        <?php else : ?>
                            <div class="sidebar-widget sidebar-widget--search">
                                <h3 class="sidebar-widget-title"><?php esc_html_e('Search', 'dooroora'); ?></h3>
                                <?php get_search_form(); ?>
                            </div>
                            <div class="sidebar-widget sidebar-widget--latest">
                                <h3 class="sidebar-widget-title"><?php esc_html_e('Latest Posts', 'dooroora'); ?></h3>
                                <ul class="sidebar-latest-list">
                                    <?php
                                    $latest = new WP_Query(array('posts_per_page' => 5, 'ignore_sticky_posts' => true));
                                    while ($latest->have_posts()) : $latest->the_post();
                                    ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <span class="sidebar-latest-thumb"><?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?></span>
                                                <?php endif; ?>
                                                <span class="sidebar-latest-info">
                                                    <span class="sidebar-latest-title"><?php the_title(); ?></span>
                                                    <span class="sidebar-latest-date"><?php echo esc_html(get_the_date()); ?></span>
                                                </span>
                                            </a>
                                        </li>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </ul>
                            </div>
                            <div class="sidebar-widget sidebar-widget--categories">
                                <h3 class="sidebar-widget-title"><?php esc_html_e('Categories', 'dooroora'); ?></h3>
                                <ul class="sidebar-category-list">
                                    <?php
                                    $cats = get_categories(array('hide_empty' => false));
                                    $map = dooroora_get_category_mapping();
                                    foreach ($cats as $cat) :
                                        $icon = isset($map[$cat->slug]['icon']) ? $map[$cat->slug]['icon'] : 'fa-solid fa-tag';
                                    ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                                <i class="<?php echo esc_attr($icon); ?>"></i>
                                                <span><?php echo esc_html($cat->name); ?></span>
                                                <span class="sidebar-cat-count"><?php echo esc_html($cat->count); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </aside>
                </div><!-- .single-content-layout -->

            </article>

        <?php endwhile; ?>
    </div>
</div>

<?php
get_footer();
