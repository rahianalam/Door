<?php
/**
 * DoorOora Theme Functions
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

define('DOOROORA_THEME_VERSION', '1.0.0');
define('DOOROORA_THEME_DIR', get_template_directory());
define('DOOROORA_THEME_URI', get_template_directory_uri());

foreach (array(
    'inc/theme-setup.php',
    'inc/enqueue.php',
    'inc/custom-posts.php',
    'inc/theme-options.php',
    'inc/theme-options-extended.php',
    'inc/newsletter-admin.php',
    'inc/helpers.php',
    'inc/ajax.php',
    'inc/schema.php',
    'inc/breadcrumbs.php',
    'inc/seo.php',
    'inc/seo-manager.php',
    'inc/sidebar.php',
    'inc/blocks.php',
    'inc/content-scoring.php',
    'inc/dashboard-widgets.php',
    'inc/performance.php',
    'inc/responsive.php',
) as $file) {
    require_once DOOROORA_THEME_DIR . '/' . $file;
}
