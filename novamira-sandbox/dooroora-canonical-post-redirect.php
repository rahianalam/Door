<?php
/**
 * Dooroora: 301-redirect posts reached through a non-canonical category path
 * (e.g. /hardware-knobs/post-slug) to their one permalink.
 * Prevents duplicate URLs in Google for posts that sit in several categories.
 */
defined('ABSPATH') || exit;

add_action('template_redirect', function () {
    if (!is_singular('post') || is_preview() || is_feed() || is_embed() || is_customize_preview()) {
        return;
    }
    if (get_query_var('page') || get_query_var('cpage') || get_query_var('amp')) {
        return;
    }
    $permalink = get_permalink(get_queried_object_id());
    if (!$permalink) {
        return;
    }
    $req_path   = untrailingslashit((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
    $canon_path = untrailingslashit((string) wp_parse_url($permalink, PHP_URL_PATH));
    if ($req_path === '' || strcasecmp(rawurldecode($req_path), rawurldecode($canon_path)) === 0) {
        return;
    }
    $query = wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    wp_safe_redirect($permalink . ($query ? '?' . $query : ''), 301, 'Dooroora canonical');
    exit;
}, 1);
