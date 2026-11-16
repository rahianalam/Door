<?php
/**
 * Sidebar registration and widget areas
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('widgets_init', 'dooroora_register_sidebars');
function dooroora_register_sidebars() {
    register_sidebar(array(
        'name'          => __('Single Post Sidebar', 'dooroora'),
        'id'            => 'sidebar-single',
        'description'   => __('Sidebar displayed on single post pages.', 'dooroora'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Blog Archive Sidebar', 'dooroora'),
        'id'            => 'sidebar-blog',
        'description'   => __('Sidebar displayed on blog archive pages.', 'dooroora'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'dooroora'),
        'id'            => 'footer-1',
        'description'   => __('Footer widget column.', 'dooroora'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
