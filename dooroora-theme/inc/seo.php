<?php
/**
 * SEO: meta tags, Open Graph, dynamic titles
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'dooroora_seo_meta', 1);
function dooroora_seo_meta() {
    if (is_singular()) {
        dooroora_og_tags();
    }
}

function dooroora_og_tags() {
    $post_id = get_the_ID();
    $title   = single_post_title('', false);
    $desc    = function_exists('dooroora_get_fast_excerpt') ? dooroora_get_fast_excerpt($post_id, 25) : get_the_excerpt($post_id);
    $url     = get_permalink($post_id);
    $image   = get_the_post_thumbnail_url($post_id, 'full');
    $site_name = get_bloginfo('name');

    if (!$desc) {
        $desc = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 20);
    }
    $desc = wp_trim_words($desc, 25);

    // Check for per-post SEO overrides (from seo-manager) to avoid duplication
    $has_og_title = get_post_meta($post_id, 'dooroora_og_title', true);
    $has_og_desc  = get_post_meta($post_id, 'dooroora_og_desc', true);
    $has_og_image = get_post_meta($post_id, 'dooroora_og_image', true);

    if (empty($has_og_title)) {
        printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
        printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    }
    if (empty($has_og_desc)) {
        printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($desc));
        printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($desc));
    }
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url($url));
    printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr($site_name));
    printf('<meta property="og:type" content="article" />' . "\n");
    printf('<meta name="twitter:card" content="summary_large_image" />' . "\n");

    if ($image && empty($has_og_image)) {
        printf('<meta property="og:image" content="%s" />' . "\n", esc_url($image));
        printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($image));
    }

    printf('<meta property="article:published_time" content="%s" />' . "\n", esc_attr(get_the_time('c', $post_id)));
    printf('<meta property="article:modified_time" content="%s" />' . "\n", esc_attr(get_the_modified_time('c', $post_id)));

    $categories = get_the_category($post_id);
    foreach ($categories as $cat) {
        printf('<meta property="article:section" content="%s" />' . "\n", esc_attr($cat->name));
    }
}

add_filter('document_title_parts', 'dooroora_dynamic_title');
function dooroora_dynamic_title($title) {
    if (is_singular('post') && !empty($title['title'])) {
        $title['title'] = $title['title'] . ' | ' . get_bloginfo('name');
    }
    return $title;
}

add_filter('pre_get_document_title', 'dooroora_meta_title', 5);
function dooroora_meta_title($title) {
    // If seo-manager has a per-post override, let it handle the title
    if (is_singular()) {
        $seo_title = get_post_meta(get_the_ID(), 'dooroora_seo_title', true);
        if (!empty($seo_title)) {
            return $title;
        }
    }
    if (is_category()) {
        $cat_seo_title = get_term_meta(get_queried_object_id(), 'dooroora_cat_seo_title', true);
        if (!empty($cat_seo_title)) {
            return $title;
        }
    }

    if (is_singular('post')) {
        return single_post_title('', false) . ' | ' . get_bloginfo('name');
    }
    if (is_category()) {
        return single_cat_title('', false) . ' Guides | ' . get_bloginfo('name');
    }
    if (is_author()) {
        return get_the_author() . ' | ' . get_bloginfo('name');
    }
    return $title;
}

add_filter('document_title_separator', function () {
    return '|';
});

add_action('wp_head', 'dooroora_meta_description_tag', 2);
function dooroora_meta_description_tag() {
    // Skip if seo-manager already output a per-post/category meta description
    if (is_singular()) {
        $seo_desc = get_post_meta(get_the_ID(), 'dooroora_seo_desc', true);
        if (!empty($seo_desc)) return;
    }
    if (is_category()) {
        $term_seo_desc = get_term_meta(get_queried_object_id(), 'dooroora_cat_seo_desc', true);
        if (!empty($term_seo_desc)) return;
    }

    $desc = '';

    if (is_singular()) {
        $desc = function_exists('dooroora_get_fast_excerpt') ? dooroora_get_fast_excerpt(get_the_ID(), 25) : get_the_excerpt();
    } elseif (is_category()) {
        $cat = get_queried_object();
        $desc = category_description($cat->term_id);
        if (!$desc) {
            $desc = sprintf(__('Comprehensive %s guides — expert advice, troubleshooting, and buying resources.', 'dooroora'), single_cat_title('', false));
        }
    } elseif (is_home() && !is_front_page()) {
        $desc = get_bloginfo('description');
    }

    if ($desc) {
        $desc = wp_trim_words($desc, 25);
        printf('<meta name="description" content="%s" />' . "\n", esc_attr($desc));
    }
}

add_action('wp_head', 'dooroora_canonical_tag', 3);
function dooroora_canonical_tag() {
    if (is_singular()) {
        $seo_canonical = get_post_meta(get_the_ID(), 'dooroora_seo_canonical', true);
        if (!empty($seo_canonical)) return;
    }
    if (is_category()) {
        $cat_canonical = get_term_meta(get_queried_object_id(), 'dooroora_cat_canonical', true);
        if (!empty($cat_canonical)) return;
    }

    $url = '';

    if (is_singular()) {
        $url = get_permalink();
    } elseif (is_category()) {
        $url = get_category_link(get_queried_object_id());
    } elseif (is_home() && !is_front_page()) {
        $url = get_permalink(get_option('page_for_posts'));
    } elseif (is_front_page()) {
        $url = home_url('/');
    } elseif (is_author()) {
        $url = get_author_posts_url(get_queried_object_id());
    } elseif (is_search()) {
        $url = get_search_link();
    } elseif (is_tag()) {
        $url = get_tag_link(get_queried_object_id());
    } elseif (is_tax()) {
        $url = get_term_link(get_queried_object_id());
    }

    if (!empty($url)) {
        printf('<link rel="canonical" href="%s" />' . "\n", esc_url($url));
    }
}
