<?php
/**
 * Theme helper functions
 *
 * @package DoorOora
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

if (!function_exists('dooroora_get_social_links')) {
    function dooroora_get_social_links() {
        $raw = get_option('dooroora_social_links', '');
        if (empty($raw)) {
            return array();
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : array();
    }
}

if (!function_exists('dooroora_get_category_icon')) {
    function dooroora_get_category_icon($category_slug) {
        $map = dooroora_get_category_mapping();
        return isset($map[$category_slug]['icon']) ? $map[$category_slug]['icon'] : 'fa-solid fa-tag';
    }
}

if (!function_exists('dooroora_get_category_color')) {
    function dooroora_get_category_color($category_slug) {
        $map = dooroora_get_category_mapping();
        return isset($map[$category_slug]['color']) ? $map[$category_slug]['color'] : '#333333';
    }
}

if (!function_exists('dooroora_get_category_description')) {
    function dooroora_get_category_description($category_slug) {
        $map = dooroora_get_category_mapping();
        return isset($map[$category_slug]['description']) ? $map[$category_slug]['description'] : '';
    }
}

if (!function_exists('dooroora_get_fast_excerpt')) {
    function dooroora_get_fast_excerpt($post_id = 0, $length = 30) {
        $post = get_post($post_id);
        if (!$post) return '';
        if (!empty($post->post_excerpt)) {
            return wp_trim_words($post->post_excerpt, $length, '...');
        }
        $content = preg_replace('/<!--(.|\s)*?-->/', '', $post->post_content);
        $content = strip_shortcodes($content);
        return wp_trim_words(wp_strip_all_tags($content), $length, '...');
    }
}

if (!function_exists('dooroora_excerpt')) {
    function dooroora_excerpt($length = 30) {
        return dooroora_get_fast_excerpt(get_the_ID(), $length);
    }
}

// Global high-priority filter: prevents core get_the_excerpt() from running expensive Gutenberg/oEmbed block parsing
add_filter('get_the_excerpt', function($text, $post = null) {
    if (!empty($text)) return $text;
    $post_obj = get_post($post);
    if (!$post_obj) return $text;
    if (!empty($post_obj->post_excerpt)) return $post_obj->post_excerpt;
    $content = preg_replace('/<!--(.|\s)*?-->/', '', $post_obj->post_content);
    $content = strip_shortcodes($content);
    return wp_trim_words(wp_strip_all_tags($content), 30, '...');
}, 1, 2);

if (!function_exists('dooroora_hero_heading')) {
    function dooroora_hero_heading() {
        $custom = get_option('dooroora_hero_heading', '');
        return !empty($custom) ? $custom : __('Everything About Doors. Answered Properly.', 'dooroora');
    }
}

if (!function_exists('dooroora_hero_description')) {
    function dooroora_hero_description() {
        $custom = get_option('dooroora_hero_description', '');
        return !empty($custom) ? $custom : __('DoorOora provides expert guides, troubleshooting advice, installation tutorials, security recommendations, and buying resources for homeowners and professionals.', 'dooroora');
    }
}

if (!function_exists('dooroora_reading_time')) {
    function dooroora_reading_time($post_id = 0) {
        $content = get_post_field('post_content', $post_id);
        $words   = str_word_count(strip_tags($content));
        $minutes = max(1, ceil($words / 200));
        return sprintf(_n('%d min read', '%d min read', $minutes, 'dooroora'), $minutes);
    }
}

if (!function_exists('dooroora_get_post_updated_date')) {
    function dooroora_get_post_updated_date($post_id = 0) {
        $modified = get_the_modified_date('U', $post_id);
        $published = get_the_date('U', $post_id);
        if ($modified > $published) {
            return get_the_modified_date('', $post_id);
        }
        return false;
    }
}

if (!function_exists('dooroora_related_posts')) {
    function dooroora_related_posts($limit = 5) {
        $categories = wp_get_post_categories(get_the_ID(), array('fields' => 'ids'));
        if (empty($categories)) return;
        $related = new WP_Query(array(
            'category__in'        => $categories,
            'posts_per_page'      => $limit,
            'post__not_in'        => array(get_the_ID()),
            'ignore_sticky_posts' => true,
        ));
        if ($related->have_posts()) :
            echo '<ul class="related-posts-widget-list">';
            while ($related->have_posts()) : $related->the_post();
                echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;
    }
}

if (!function_exists('dooroora_popular_posts')) {
    function dooroora_popular_posts($limit = 5) {
        $popular = new WP_Query(array(
            'posts_per_page'      => $limit,
            'ignore_sticky_posts' => true,
            'orderby'             => 'comment_count',
            'order'               => 'DESC',
        ));
        if ($popular->have_posts()) :
            echo '<ul class="popular-posts-widget-list">';
            while ($popular->have_posts()) : $popular->the_post();
                echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;
    }
}

if (!function_exists('dooroora_extract_faq_blocks')) {
    function dooroora_extract_faq_blocks() {
        $faqs = array();
        $content = get_post_field('post_content', get_the_ID());
        $blocks = parse_blocks($content);
        $faq_start = false;
        $current_q = '';
        $current_a = '';

        foreach ($blocks as $block) {
            // Skip existing dooroora/faq block — it outputs its own schema
            if ($block['blockName'] === 'dooroora/faq') continue;
            if ($block['blockName'] === 'core/heading') {
                $text = strip_tags($block['innerHTML']);
                if (preg_match('/^FAQ|faq|Frequently Asked/i', $text)) {
                    $faq_start = true;
                    continue;
                }
                if ($faq_start) {
                    if (!empty($current_q) && !empty($current_a)) {
                        $faqs[] = array('question' => $current_q, 'answer' => $current_a);
                    }
                    $current_q = $text;
                    $current_a = '';
                    continue;
                }
            }
            if ($faq_start && $block['blockName'] === 'core/paragraph') {
                $current_a .= wp_kses_post($block['innerHTML']);
            }
        }

        if (!empty($current_q) && !empty($current_a)) {
            $faqs[] = array('question' => $current_q, 'answer' => $current_a);
        }

        return $faqs;
    }
}

if (!function_exists('dooroora_fallback_menu')) {
    function dooroora_fallback_menu() {
        $pages = wp_list_pages(array(
            'title_li' => '',
            'echo'     => false,
            'depth'    => 1,
        ));
        if ($pages) {
            echo '<ul id="primary-menu" class="menu">' . $pages . '</ul>';
        }
    }
}

if (!function_exists('dooroora_get_author_role')) {
    function dooroora_get_author_role($author_id = 0) {
        if (!$author_id) $author_id = get_post_field('post_author', get_the_ID());
        $role = get_the_author_meta('dooroora_author_role', $author_id);
        return $role ?: __('Door Hardware & Installation Specialist', 'dooroora');
    }
}

add_action('show_user_profile', 'dooroora_author_role_field');
add_action('edit_user_profile', 'dooroora_author_role_field');
function dooroora_author_role_field($user) {
    $role = get_the_author_meta('dooroora_author_role', $user->ID);
    ?>
    <h3><?php esc_html_e('DoorOora Author Profile', 'dooroora'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="dooroora_author_role"><?php esc_html_e('Author Role / Title', 'dooroora'); ?></label></th>
            <td><input type="text" name="dooroora_author_role" id="dooroora_author_role" value="<?php echo esc_attr($role); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e('e.g. "Door Hardware & Installation Specialist"', 'dooroora'); ?></p></td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'dooroora_save_author_role');
add_action('edit_user_profile_update', 'dooroora_save_author_role');
function dooroora_save_author_role($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;
    if (isset($_POST['dooroora_author_role'])) {
        update_user_meta($user_id, 'dooroora_author_role', sanitize_text_field($_POST['dooroora_author_role']));
    }
}

if (!function_exists('dooroora_is_top_bar_enabled')) {
    function dooroora_is_top_bar_enabled() {
        return get_option('dooroora_top_bar_enabled', '0') === '1';
    }
}

if (!function_exists('dooroora_is_cta_enabled')) {
    function dooroora_is_cta_enabled() {
        return get_option('dooroora_cta_enabled', '0') === '1';
    }
}

if (!class_exists('DoorOora_Mega_Menu_Walker')) {
    class DoorOora_Mega_Menu_Walker extends Walker_Nav_Menu {
        protected $mega_parent_title = '';

        public function start_lvl(&$output, $depth = 0, $args = array()) {
            if ($depth === 0) {
                $output .= '<div class="mega-menu"><div class="container"><div class="mega-menu-grid">';
            } else {
                $output .= '<ul class="sub-menu">';
            }
        }

        public function end_lvl(&$output, $depth = 0, $args = array()) {
            if ($depth === 0) {
                // Always render all categories as the main mega menu content
                $output .= $this->render_mega_categories();
                $output .= '</div></div></div>';
            } else {
                $output .= '</ul>';
            }
        }

        public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
            $classes   = empty($item->classes) ? array() : (array) $item->classes;
            $has_children = in_array('menu-item-has-children', $classes);
            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));

            if ($depth === 0) {
                $this->mega_parent_title = $item->title;
                $output .= '<li class="' . esc_attr($class_names) . '">';
                $atts = array();
                $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
                $atts['target'] = !empty($item->target) ? $item->target : '';
                $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
                $atts['href']   = !empty($item->url) ? $item->url : '';
                $attributes = '';
                foreach ($atts as $attr => $value) {
                    if (!empty($value)) {
                        $value = esc_attr($value);
                        $attributes .= ' ' . $attr . '="' . $value . '"';
                    }
                }
                $title = apply_filters('the_title', $item->title, $item->ID);
                $item_output = $args->before;
                $item_output .= '<a' . $attributes . '>';
                $item_output .= $args->link_before . $title . $args->link_after;
                if ($has_children && $depth === 0) {
                    $item_output .= ' <i class="fa-solid fa-chevron-down nav-arrow"></i>';
                }
                $item_output .= '</a>';
                $item_output .= $args->after;
                $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
            } elseif ($depth === 1) {
                $output .= '<div class="mega-col">';
                $output .= '<span class="mega-col-title">' . esc_html($item->title) . '</span>';
            } else {
                $output .= '<li class="' . esc_attr($class_names) . '">';
                $atts = array();
                $atts['href'] = !empty($item->url) ? $item->url : '';
                $attributes = '';
                foreach ($atts as $attr => $value) {
                    if (!empty($value)) $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
                }
                $title = apply_filters('the_title', $item->title, $item->ID);
                $item_output = $args->before;
                $item_output .= '<a' . $attributes . '>';
                $item_output .= $args->link_before . $title . $args->link_after;
                $item_output .= '</a>';
                $item_output .= $args->after;
                $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
            }
        }

        public function end_el(&$output, $item, $depth = 0, $args = array()) {
            if ($depth === 0) {
                $output .= '</li>';
            } elseif ($depth === 1) {
                $output .= '</div>';
            } else {
                $output .= '</li>';
            }
        }

        private function render_mega_categories() {
            $html = '<div class="mega-col mega-col-categories">';
            $html .= '<span class="mega-col-title">' . esc_html__('Topics', 'dooroora') . '</span>';
            $html .= '<div class="mega-topics">';
            $categories = get_categories(array('hide_empty' => false));
            $map = dooroora_get_category_mapping();
            foreach ($categories as $cat) {
                if (!isset($map[$cat->slug])) continue;
                $data = $map[$cat->slug];
                $html .= '<a href="' . esc_url(get_category_link($cat->term_id)) . '" class="mega-topic-item">';
                $html .= '<i class="' . esc_attr($data['icon']) . '"></i>';
                $html .= '<span class="mega-topic-info">';
                $html .= '<span class="mega-topic-name">' . esc_html($cat->name) . '</span>';
                $html .= '<span class="mega-topic-desc">' . esc_html($data['description']) . '</span>';
                $html .= '</span></a>';
            }
            $html .= '</div></div>';
            return $html;
        }
    }
}
