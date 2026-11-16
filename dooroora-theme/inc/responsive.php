<?php
/**
 * Mobile Optimization & Responsiveness Module
 * Ensures every blog post is 100% mobile-friendly and responsive.
 *
 * @package DoorOora
 * @since 1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Filter the_content to make all tables, figures, and embeds fully mobile responsive
 */
add_filter('the_content', 'dooroora_mobile_responsive_content', 20);
function dooroora_mobile_responsive_content($content) {
    if (!is_singular('post')) {
        return $content;
    }

    // 1. Wrap all tables in a mobile-responsive scroll container with touch hint
    $content = preg_replace_callback(
        '/<table([^>]*)>([\s\S]*?)<\/table>/i',
        function ($matches) {
            $attrs = $matches[1];
            $body  = $matches[2];

            // Strip fixed inline width styles that break mobile layout
            $clean_attrs = preg_replace('/width\s*:\s*[^;"]+;?/i', '', $attrs);

            // Return wrapped table
            return '<div class="dooroora-table-wrapper">' .
                   '<div class="dooroora-table-scroll">' .
                   '<table' . $clean_attrs . '>' . $body . '</table>' .
                   '</div>' .
                   '<div class="dooroora-table-hint"><i class="fa-solid fa-arrows-left-right"></i> Scroll horizontally to view full table &rarr;</div>' .
                   '</div>';
        },
        $content
    );

    // 2. Wrap iframes / videos in responsive 16:9 containers
    $content = preg_replace_callback(
        '/<iframe([^>]*)>([\s\S]*?)<\/iframe>/i',
        function ($matches) {
            return '<div class="dooroora-responsive-embed"><iframe' . $matches[1] . '>' . $matches[2] . '</iframe></div>';
        },
        $content
    );

    // 3. Ensure images have responsive attributes
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function ($matches) {
            $img_tag = $matches[0];
            if (strpos($img_tag, 'loading=') === false) {
                $img_tag = str_replace('<img ', '<img loading="lazy" ', $img_tag);
            }
            if (strpos($img_tag, 'decoding=') === false) {
                $img_tag = str_replace('<img ', '<img decoding="async" ', $img_tag);
            }
            return $img_tag;
        },
        $content
    );

    return $content;
}

/**
 * Add responsive body class
 */
add_filter('body_class', 'dooroora_responsive_body_class');
function dooroora_responsive_body_class($classes) {
    if (is_singular('post')) {
        $classes[] = 'dooroora-mobile-ready';
    }
    return $classes;
}
