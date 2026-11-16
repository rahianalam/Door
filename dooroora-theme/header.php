<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e('Skip to content', 'dooroora'); ?>
    </a>

    <?php if (dooroora_is_top_bar_enabled()) : ?>
    <div class="top-bar" id="top-bar">
        <div class="container">
            <div class="top-bar-inner">
                <a href="<?php echo esc_url(get_option('dooroora_top_bar_url', '#')); ?>" class="top-bar-link">
                    <?php echo esc_html(get_option('dooroora_top_bar_text', __('New: Free Door Size Calculator', 'dooroora'))); ?> &rarr;
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <header id="masthead" class="site-header" itemscope itemtype="https://schema.org/WPHeader">
        <div class="header-inner">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <div class="site-logo"><?php the_custom_logo(); ?></div>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="site-title"><?php bloginfo('name'); ?></a>
                <?php endif; ?>
            </div>

            <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary Menu', 'dooroora'); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => 'dooroora_fallback_menu',
                    'walker'         => new DoorOora_Mega_Menu_Walker(),
                ));
                ?>
            </nav>

            <div class="header-actions">
                <?php if (dooroora_is_cta_enabled()) : ?>
                    <a href="<?php echo esc_url(get_option('dooroora_cta_url', '#')); ?>" class="btn btn-sm btn-primary header-cta"><?php echo esc_html(get_option('dooroora_cta_text', __('Get Started', 'dooroora'))); ?></a>
                <?php endif; ?>
                <button class="search-toggle" aria-label="<?php esc_attr_e('Toggle search', 'dooroora'); ?>">
                    <i class="fa-solid fa-search"></i>
                </button>
                <button class="menu-toggle mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'dooroora'); ?>" aria-controls="primary-menu" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mega menu dropdown (rendered by walker; fallback inline) -->
        <div class="mega-menu-overlay"></div>

        <!-- Off-canvas mobile menu -->
        <div class="off-canvas" id="off-canvas-menu">
            <div class="off-canvas-header">
                <span class="off-canvas-title"><?php esc_html_e('Menu', 'dooroora'); ?></span>
                <button class="off-canvas-close" aria-label="<?php esc_attr_e('Close menu', 'dooroora'); ?>">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="off-canvas-search">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="off-canvas-search-input" placeholder="<?php esc_attr_e('Search guides...', 'dooroora'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                </form>
            </div>
            <nav class="off-canvas-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'off-canvas-menu-list',
                    'container'      => false,
                    'fallback_cb'    => 'dooroora_fallback_menu',
                    'depth'          => 2,
                ));
                ?>
            </nav>
        </div>
        <div class="off-canvas-overlay"></div>

        <!-- Search overlay with live search -->
        <div class="search-overlay" id="search-overlay">
            <div class="search-overlay-inner">
                <button class="search-overlay-close" aria-label="<?php esc_attr_e('Close search', 'dooroora'); ?>">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <form role="search" method="get" class="search-overlay-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <label>
                        <span class="screen-reader-text"><?php esc_html_e('Search for:', 'dooroora'); ?></span>
                        <input type="search" class="search-overlay-input" id="live-search-input" placeholder="<?php esc_attr_e('Search guides, repairs, locks, garage doors...', 'dooroora'); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                    </label>
                    <button type="submit" class="btn btn-primary search-overlay-submit"><?php esc_html_e('Search', 'dooroora'); ?></button>
                </form>
                <div class="live-search-results" id="live-search-results">
                    <ul class="live-search-list"></ul>
                </div>
            </div>
        </div>
		
    <main id="primary" class="site-main">