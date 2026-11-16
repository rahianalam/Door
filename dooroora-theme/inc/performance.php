<?php
/**
 * Performance optimizations: caching, asset optimization, loopback protection, resource hints
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// 1. Prevent loopback oEmbed requests to dooroora.com (prevents 15-second cURL timeouts)
add_filter('pre_oembed_result', function($result, $url) {
    if (strpos($url, home_url()) !== false || strpos($url, 'dooroora.com') !== false) {
        return '';
    }
    return $result;
}, 10, 2);

// 2. Disable self-pingbacks
add_action('pre_ping', function(&$links) {
    $home = home_url();
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
});

// 3. Resource Hints for External Assets (Google Fonts)
add_filter('wp_resource_hints', function($hints, $relation_type) {
    if ('preconnect' === $relation_type) {
        $hints[] = [
            'href' => 'https://fonts.googleapis.com',
        ];
        $hints[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    } elseif ('dns-prefetch' === $relation_type) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://cdnjs.cloudflare.com';
    }
    return $hints;
}, 10, 2);

// 4. Defer JavaScript execution for non-critical theme scripts
add_filter('script_loader_tag', function($tag, $handle, $src) {
    if (is_admin()) return $tag;
    if ($handle === 'dooroora-scripts') {
        if (false === strpos($tag, 'defer')) {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }
    }
    return $tag;
}, 10, 3);

// 5. Remove bloat: WP Emoji, RSD, WLW Manifest, Shortlink, WP Generator
add_action('init', function() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'wp_oembed_add_host_js');
});

// 6. Automatically populate post_excerpt on save if empty
add_action('save_post', function($post_id, $post, $update) {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;
    if ($post->post_type !== 'post') return;
    if (empty($post->post_excerpt) && !empty($post->post_content)) {
        $clean = preg_replace('/<!--(.|\s)*?-->/', '', $post->post_content);
        $clean = strip_shortcodes($clean);
        $excerpt = wp_trim_words(wp_strip_all_tags($clean), 30, '...');
        if (!empty($excerpt)) {
            remove_action('save_post', __FUNCTION__);
            wp_update_post([
                'ID' => $post_id,
                'post_excerpt' => $excerpt
            ]);
        }
    }
}, 20, 3);
