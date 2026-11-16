<?php
/**
 * Breadcrumb system
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

function dooroora_get_breadcrumbs_data() {
    $crumbs = array();

    $crumbs[] = array(
        'label' => __('Home', 'dooroora'),
        'url'   => home_url('/'),
    );

    if (is_home() && !is_front_page()) {
        $crumbs[] = array(
            'label' => get_the_title(get_option('page_for_posts')),
            'url'   => get_permalink(get_option('page_for_posts')),
        );
    } elseif (is_singular('post')) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $cat = $categories[0];
            $chain = array();
            while ($cat->parent) {
                $chain[] = $cat;
                $cat = get_category($cat->parent);
            }
            $chain[] = $cat;
            $chain = array_reverse($chain);
            foreach ($chain as $c) {
                $crumbs[] = array(
                    'label' => $c->name,
                    'url'   => get_category_link($c->term_id),
                );
            }
        }
        $crumbs[] = array(
            'label' => get_the_title(),
            'url'   => get_permalink(),
        );
    } elseif (is_category()) {
        $cat = get_queried_object();
        if ($cat->parent) {
            $parent = get_category($cat->parent);
            $crumbs[] = array(
                'label' => $parent->name,
                'url'   => get_category_link($parent->term_id),
            );
        }
        $crumbs[] = array(
            'label' => single_cat_title('', false),
            'url'   => get_category_link($cat->term_id),
        );
    } elseif (is_author()) {
        $crumbs[] = array(
            'label' => get_the_author(),
            'url'   => get_author_posts_url(get_query_var('author')),
        );
    } elseif (is_search()) {
        $crumbs[] = array(
            'label' => sprintf(__('Search: %s', 'dooroora'), get_search_query()),
            'url'   => '',
        );
    } elseif (is_page()) {
        $crumbs[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );
    } elseif (is_404()) {
        $crumbs[] = array(
            'label' => __('404 Not Found', 'dooroora'),
            'url'   => '',
        );
    }

    return $crumbs;
}

function dooroora_breadcrumbs() {
    $crumbs = dooroora_get_breadcrumbs_data();
    if (empty($crumbs)) {
        return;
    }
    ?>
    <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'dooroora'); ?>">
        <ol class="breadcrumbs-list">
            <?php
            $count = count($crumbs);
            foreach ($crumbs as $i => $crumb) :
                $is_last = ($i === $count - 1);
                ?>
                <li class="breadcrumbs-item <?php echo $is_last ? 'breadcrumbs-item--current' : ''; ?>">
                    <?php if (!empty($crumb['url']) && !$is_last) : ?>
                        <a href="<?php echo esc_url($crumb['url']); ?>"><?php echo esc_html($crumb['label']); ?></a>
                    <?php else : ?>
                        <span><?php echo esc_html($crumb['label']); ?></span>
                    <?php endif; ?>
                </li>
                <?php if (!$is_last) : ?>
                    <li class="breadcrumbs-separator" aria-hidden="true">
                        <i class="fa-solid fa-chevron-right"></i>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}
