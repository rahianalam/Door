<?php
/**
 * Theme setup: supports, menus, image sizes, category mapping
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'dooroora_theme_setup');
function dooroora_theme_setup() {
    load_theme_textdomain('dooroora', DOOROORA_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 280,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');

    add_image_size('hero-large', 1920, 800, true);
    add_image_size('category-card', 600, 400, true);
    add_image_size('blog-grid', 400, 300, true);
    add_image_size('related-post', 350, 220, true);
}

add_action('init', 'dooroora_register_menus');
function dooroora_register_menus() {
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'dooroora'),
        'footer'  => esc_html__('Footer Menu', 'dooroora'),
    ));
}

function dooroora_get_category_mapping() {
    return array(
        'locks-security'          => array(
            'label'       => __('Locks & Security', 'dooroora'),
            'icon'        => 'fa-solid fa-lock',
            'description' => __('Secure your property with top-rated locks and security systems.', 'dooroora'),
            'color'       => '#1a3a5c',
        ),
        'garage-doors'            => array(
            'label'       => __('Garage Doors', 'dooroora'),
            'icon'        => 'fa-solid fa-warehouse',
            'description' => __('Durable and stylish garage doors for every home.', 'dooroora'),
            'color'       => '#2d6a4f',
        ),
        'door-types-materials'    => array(
            'label'       => __('Door Types & Materials', 'dooroora'),
            'icon'        => 'fa-solid fa-door-open',
            'description' => __('Explore wood, steel, fiberglass, and more door options.', 'dooroora'),
            'color'       => '#7b4f2c',
        ),
        'hardware-knobs'          => array(
            'label'       => __('Hardware & Knobs', 'dooroora'),
            'icon'        => 'fa-solid fa-wrench',
            'description' => __('Handles, hinges, knobs, and all door hardware.', 'dooroora'),
            'color'       => '#b8860b',
        ),
        'installation-framing'    => array(
            'label'       => __('Installation & Framing', 'dooroora'),
            'icon'        => 'fa-solid fa-tape',
            'description' => __('Professional guides for door installation and framing.', 'dooroora'),
            'color'       => '#8b4513',
        ),
        'barn-sliding-doors'      => array(
            'label'       => __('Barn & Sliding Doors', 'dooroora'),
            'icon'        => 'fa-solid fa-arrows-left-right',
            'description' => __('Rustic barn doors and modern sliding door solutions.', 'dooroora'),
            'color'       => '#556b2f',
        ),
        'smart-doorbells-sensors' => array(
            'label'       => __('Smart Doorbells & Sensors', 'dooroora'),
            'icon'        => 'fa-solid fa-video',
            'description' => __('Smart doorbells, motion sensors, and video security.', 'dooroora'),
            'color'       => '#4a90d9',
        ),
        'sealing-weatherproofing' => array(
            'label'       => __('Sealing & Weatherproofing', 'dooroora'),
            'icon'        => 'fa-solid fa-shield-halved',
            'description' => __('Weather strips, seals, and insulation for doors.', 'dooroora'),
            'color'       => '#5c6b7a',
        ),
    );
}
