<?php
/**
 * Theme options panel via WordPress Settings API
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

add_action('admin_menu', 'dooroora_add_admin_menu');
function dooroora_add_admin_menu() {
    add_menu_page(
        __('DoorOora Settings', 'dooroora'),
        __('DoorOora', 'dooroora'),
        'manage_options',
        'dooroora-settings',
        'dooroora_settings_page',
        'dashicons-admin-home',
        30
    );
}

add_action('admin_init', 'dooroora_register_settings');
function dooroora_register_settings() {

    $fields = array(
        // General
        'dooroora_logo' => array(
            'label' => __('Logo URL (overrides Custom Logo)', 'dooroora'),
            'type'  => 'url',
            'tab'   => 'general',
        ),

        // Header
        'dooroora_top_bar_enabled' => array(
            'label' => __('Enable Top Bar', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'header',
        ),
        'dooroora_top_bar_text' => array(
            'label' => __('Top Bar Text', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'header',
        ),
        'dooroora_top_bar_url' => array(
            'label' => __('Top Bar URL', 'dooroora'),
            'type'  => 'url',
            'tab'   => 'header',
        ),
        'dooroora_cta_enabled' => array(
            'label' => __('Enable CTA Button', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'header',
        ),
        'dooroora_cta_text' => array(
            'label' => __('CTA Button Text', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'header',
        ),
        'dooroora_cta_url' => array(
            'label' => __('CTA Button URL', 'dooroora'),
            'type'  => 'url',
            'tab'   => 'header',
        ),
        // Hero
        'dooroora_hero_heading' => array(
            'label' => __('Hero Heading', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'hero',
        ),
        'dooroora_hero_description' => array(
            'label' => __('Hero Description', 'dooroora'),
            'type'  => 'textarea',
            'tab'   => 'hero',
        ),
        // Footer
        'dooroora_footer_description' => array(
            'label' => __('Footer Description', 'dooroora'),
            'type'  => 'textarea',
            'tab'   => 'footer',
        ),
        'dooroora_copyright_text' => array(
            'label' => __('Copyright Text (use {year} for dynamic year)', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'footer',
        ),
        // Social
        'dooroora_social_links' => array(
            'label' => __('Social Links (JSON)', 'dooroora'),
            'type'  => 'textarea',
            'tab'   => 'social',
        ),
        // Newsletter
        'dooroora_newsletter_text' => array(
            'label' => __('Newsletter Call-to-Action Text', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'newsletter',
        ),
        // SEO
        'dooroora_org_name' => array(
            'label' => __('Organization Name', 'dooroora'),
            'type'  => 'text',
            'tab'   => 'seo',
        ),
        'dooroora_org_logo' => array(
            'label' => __('Organization Logo URL', 'dooroora'),
            'type'  => 'url',
            'tab'   => 'seo',
        ),
        'dooroora_org_social_profiles' => array(
            'label' => __('Social Profile URLs (one per line)', 'dooroora'),
            'type'  => 'textarea',
            'tab'   => 'seo',
        ),
        'dooroora_sitemap_enabled' => array(
            'label' => __('Enable XML Sitemap', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'seo',
        ),
        'dooroora_homepage_schema' => array(
            'label' => __('Homepage Schema (JSON-LD)', 'dooroora'),
            'type'  => 'textarea',
            'tab'   => 'seo',
        ),
        'dooroora_robots_tag' => array(
            'label' => __('Noindex Tag Archives', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'seo',
        ),
        'dooroora_robots_author' => array(
            'label' => __('Noindex Author Archives', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'seo',
        ),
        'dooroora_robots_date' => array(
            'label' => __('Noindex Date Archives', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'seo',
        ),
        'dooroora_robots_search' => array(
            'label' => __('Noindex Search Results', 'dooroora'),
            'type'  => 'checkbox',
            'tab'   => 'seo',
        ),
    );

    $tabs = array(
        'general'    => __('General', 'dooroora'),
        'header'     => __('Header', 'dooroora'),
        'hero'       => __('Hero', 'dooroora'),
        'footer'     => __('Footer', 'dooroora'),
        'social'     => __('Social', 'dooroora'),
        'newsletter' => __('Newsletter', 'dooroora'),
        'seo'        => __('SEO', 'dooroora'),
    );

    // Register ALL option groups upfront so options.php accepts any tab's submission
    foreach ($tabs as $tab_key => $tab_label) {
        $option_group = 'dooroora_' . $tab_key;
        foreach ($fields as $id => $field) {
            if ($field['tab'] !== $tab_key) continue;
            register_setting($option_group, $id);
        }
    }

    // Only render sections/fields for the current tab
    $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
    if (!isset($tabs[$current_tab])) {
        $current_tab = 'general';
    }

    $page_id = 'dooroora_' . $current_tab;

    add_settings_section(
        $page_id . '_section',
        '',
        '__return_false',
        $page_id
    );

    foreach ($fields as $id => $field) {
        if ($field['tab'] !== $current_tab) continue;
        add_settings_field(
            $id,
            $field['label'],
            'dooroora_render_field',
            $page_id,
            $page_id . '_section',
            array('id' => $id, 'type' => $field['type'])
        );
    }
}

function dooroora_render_field($args) {
    $value = get_option($args['id'], '');
    $name  = esc_attr($args['id']);
    ?>
    <fieldset>
    <?php
    switch ($args['type']) {
        case 'checkbox':
            printf(
                '<input type="hidden" name="%s" value="0" /><input type="checkbox" name="%s" value="1" %s />',
                $name,
                $name,
                checked('1', $value, false)
            );
            break;
        case 'textarea':
            printf(
                '<textarea name="%s" class="large-text" rows="4">%s</textarea>',
                $name,
                esc_textarea($value)
            );
            break;
        case 'url':
            printf(
                '<input type="url" name="%s" value="%s" class="regular-text" />',
                $name,
                esc_url($value)
            );
            break;
        default:
            printf(
                '<input type="text" name="%s" value="%s" class="regular-text" />',
                $name,
                esc_attr($value)
            );
            break;
    }
    if ('dooroora_social_links' === $args['id']) {
        echo '<p class="description">' . esc_html__('JSON: {"facebook":"url","x":"url","linkedin":"url","pinterest":"url","youtube":"url"}', 'dooroora') . '</p>';
    }
    if ('dooroora_org_social_profiles' === $args['id']) {
        echo '<p class="description">' . esc_html__('One URL per line for Organization schema', 'dooroora') . '</p>';
    }
    if ('dooroora_sitemap_enabled' === $args['id']) {
        echo '<p class="description">' . esc_html__('Enable native WordPress XML sitemap at /wp-sitemap.xml', 'dooroora') . '</p>';
    }
    if ('dooroora_homepage_schema' === $args['id']) {
        echo '<p class="description">' . esc_html__('Paste valid JSON-LD schema for the homepage only.', 'dooroora') . '</p>';
    }
    ?>
    </fieldset>
    <?php
}

function dooroora_settings_page() {
    if (!current_user_can('manage_options')) return;

    $tabs = array(
        'general'    => __('General', 'dooroora'),
        'header'     => __('Header', 'dooroora'),
        'hero'       => __('Hero', 'dooroora'),
        'footer'     => __('Footer', 'dooroora'),
        'social'     => __('Social', 'dooroora'),
        'newsletter' => __('Newsletter', 'dooroora'),
        'seo'        => __('SEO', 'dooroora'),
    );

    $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
    if (!isset($tabs[$current_tab])) {
        $current_tab = 'general';
    }

    $page_id = 'dooroora_' . $current_tab;
    $tab_labels = array(
        'general'    => __('General Settings', 'dooroora'),
        'header'     => __('Header Settings', 'dooroora'),
        'hero'       => __('Hero Settings', 'dooroora'),
        'footer'     => __('Footer Settings', 'dooroora'),
        'social'     => __('Social Links', 'dooroora'),
        'newsletter' => __('Newsletter Settings', 'dooroora'),
        'seo'        => __('SEO / Schema Settings', 'dooroora'),
    );
    ?>
    <div class="wrap dooroora-settings-page">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $key => $label) : ?>
                <a href="?page=dooroora-settings&tab=<?php echo esc_attr($key); ?>" class="nav-tab <?php echo $current_tab === $key ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form action="options.php" method="post">
            <?php
            settings_fields('dooroora_' . $current_tab);
            printf('<input type="hidden" name="dooroora_current_tab" value="%s" />', esc_attr($current_tab));
            do_settings_sections($page_id);
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
