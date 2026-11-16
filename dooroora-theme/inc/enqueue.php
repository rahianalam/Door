<?php
/**
 * Script and style enqueuing
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'dooroora_enqueue_scripts');
function dooroora_enqueue_scripts() {

    wp_enqueue_style(
        'dooroora-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'dooroora-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );

    wp_enqueue_style(
        'dooroora-style',
        DOOROORA_THEME_URI . '/style.css',
        array('dooroora-fontawesome'),
        DOOROORA_THEME_VERSION
    );

    wp_enqueue_style(
        'dooroora-main',
        DOOROORA_THEME_URI . '/assets/css/main.css',
        array('dooroora-style'),
        DOOROORA_THEME_VERSION
    );

    wp_enqueue_script(
        'dooroora-scripts',
        DOOROORA_THEME_URI . '/assets/js/theme.js',
        array(),
        DOOROORA_THEME_VERSION,
        true
    );

    wp_localize_script('dooroora-scripts', 'dooroora_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dooroora_nonce'),
    ));

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('admin_enqueue_scripts', 'dooroora_admin_enqueue');
function dooroora_admin_enqueue($hook) {
    if (strpos($hook, 'dooroora') === false && strpos($hook, 'post.php') === false && strpos($hook, 'post-new.php') === false) return;

    wp_enqueue_style(
        'dooroora-admin',
        DOOROORA_THEME_URI . '/assets/css/admin.css',
        array(),
        DOOROORA_THEME_VERSION
    );

    if (strpos($hook, 'post.php') !== false || strpos($hook, 'post-new.php') !== false) {
        wp_enqueue_script(
            'dooroora-admin-seo',
            DOOROORA_THEME_URI . '/assets/js/admin-seo.js',
            array(),
            DOOROORA_THEME_VERSION,
            true
        );
    }
}
