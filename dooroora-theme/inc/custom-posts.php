<?php
/**
 * Custom post types and taxonomies
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('init', 'dooroora_register_tool_cpt');
function dooroora_register_tool_cpt() {
    $labels = array(
        'name'                  => __('Tools', 'dooroora'),
        'singular_name'         => __('Tool', 'dooroora'),
        'add_new'               => __('Add New', 'dooroora'),
        'add_new_item'          => __('Add New Tool', 'dooroora'),
        'edit_item'             => __('Edit Tool', 'dooroora'),
        'new_item'              => __('New Tool', 'dooroora'),
        'view_item'             => __('View Tool', 'dooroora'),
        'search_items'          => __('Search Tools', 'dooroora'),
        'not_found'             => __('No tools found', 'dooroora'),
        'not_found_in_trash'    => __('No tools found in Trash', 'dooroora'),
        'all_items'             => __('All Tools', 'dooroora'),
        'menu_name'             => __('Tools', 'dooroora'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'menu_icon'           => 'dashicons-admin-tools',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'             => array('slug' => 'tool'),
    );

    register_post_type('tool', $args);
}
