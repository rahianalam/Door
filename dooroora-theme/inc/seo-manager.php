<?php
defined('ABSPATH') || exit;

/* ----------------------------------------------------------
   Per-post / per-page SEO meta box
   ---------------------------------------------------------- */
add_action('add_meta_boxes', 'dooroora_add_seo_meta_box');
function dooroora_add_seo_meta_box() {
    $post_types = array('post', 'page');
    foreach ($post_types as $pt) {
        add_meta_box(
            'dooroora_seo_box',
            __('DoorOora SEO Settings', 'dooroora'),
            'dooroora_render_seo_meta_box',
            $pt,
            'normal',
            'high'
        );
    }
}

function dooroora_render_seo_meta_box($post) {
    wp_nonce_field('dooroora_seo_box', 'dooroora_seo_box_nonce');

    $focus_kw = get_post_meta($post->ID, 'dooroora_focus_keyphrase', true);

    // Count internal/external links
    $link_counts = dooroora_count_post_links($post->post_content);
    $img_counts  = dooroora_count_images($post->post_content);

    // Content for analysis (used by JS)
    $content_for_analysis = wp_strip_all_tags($post->post_content);
    $title_for_analysis   = get_post_meta($post->ID, 'dooroora_seo_title', true) ?: $post->post_title;
    $desc_for_analysis    = get_post_meta($post->ID, 'dooroora_seo_desc', true) ?: '';

    $meta_desc_stored = esc_attr(get_post_meta($post->ID, 'dooroora_seo_desc', true));
    $seo_title_stored = esc_attr(get_post_meta($post->ID, 'dooroora_seo_title', true));
    $canonical_val    = esc_attr(get_post_meta($post->ID, 'dooroora_seo_canonical', true));
    $og_title_val     = esc_attr(get_post_meta($post->ID, 'dooroora_og_title', true));
    $og_desc_val      = esc_textarea(get_post_meta($post->ID, 'dooroora_og_desc', true));
    $og_image_val     = esc_attr(get_post_meta($post->ID, 'dooroora_og_image', true));
    $robots_val       = get_post_meta($post->ID, 'dooroora_robots', true);
    $custom_schema    = get_post_meta($post->ID, 'dooroora_custom_schema', true);
    ?>
    <div class="dooroora-seo-wrapper">
        <div class="dooroora-seo-columns">
            <div class="dooroora-seo-main">
                <!-- Focus Keyphrase -->
                <p>
                    <label for="dooroora_focus_keyphrase"><strong><?php esc_html_e('Focus Keyphrase', 'dooroora'); ?></strong></label><br>
                    <input type="text" name="dooroora_focus_keyphrase" id="dooroora_focus_keyphrase" value="<?php echo esc_attr($focus_kw); ?>" style="width:100%;max-width:600px;" placeholder="<?php esc_attr_e('e.g. steel entry door installation', 'dooroora'); ?>" />
                    <span class="description"><?php esc_html_e('Enter a keyphrase to analyze content presence.', 'dooroora'); ?></span>
                </p>

                <!-- Keyphrase Analysis -->
                <div class="dooroora-keyphrase-analysis" id="dooroora-keyphrase-analysis">
                    <strong><?php esc_html_e('Keyphrase Presence', 'dooroora'); ?></strong>
                    <ul class="dooroora-analysis-checks" id="dooroora-analysis-checks"></ul>
                </div>

                <!-- SERP Preview -->
                <div class="dooroora-serp-preview" id="dooroora-serp-preview">
                    <strong><?php esc_html_e('Google SERP Preview', 'dooroora'); ?></strong>
                    <div class="dooroora-serp-box">
                        <div class="dooroora-serp-url" id="dooroora-serp-url"><?php echo esc_url(home_url('/')); ?></div>
                        <div class="dooroora-serp-title" id="dooroora-serp-title">
                            <?php echo esc_html(wp_trim_words($title_for_analysis, 10, '')); ?>
                        </div>
                        <div class="dooroora-serp-desc" id="dooroora-serp-desc">
                            <?php echo esc_html(wp_trim_words($desc_for_analysis ?: wp_strip_all_tags($post->post_content), 25, '...')); ?>
                        </div>
                    </div>
                </div>

                <!-- SEO Title + Meta Desc in columns -->
                <div class="dooroora-seo-row">
                    <div class="dooroora-seo-col">
                        <label for="dooroora_seo_title"><strong><?php esc_html_e('SEO Title', 'dooroora'); ?></strong></label>
                        <input type="text" name="dooroora_seo_title" id="dooroora_seo_title" value="<?php echo $seo_title_stored; ?>" style="width:100%;max-width:100%;" />
                        <div class="dooroora-char-count"><span id="dooroora-title-count"><?php echo strlen($seo_title_stored ?: $title_for_analysis); ?></span> / 60</div>
                    </div>
                    <div class="dooroora-seo-col">
                        <label for="dooroora_seo_desc"><strong><?php esc_html_e('Meta Description', 'dooroora'); ?></strong></label>
                        <textarea name="dooroora_seo_desc" id="dooroora_seo_desc" rows="3" style="width:100%;"><?php echo $meta_desc_stored; ?></textarea>
                        <div class="dooroora-char-count"><span id="dooroora-desc-count"><?php echo strlen($meta_desc_stored ?: $desc_for_analysis); ?></span> / 160</div>
                    </div>
                </div>

                <!-- Link + Image stats -->
                <div class="dooroora-seo-stats">
                    <span class="dooroora-stat">🔗 <?php esc_html_e('Internal:', 'dooroora'); ?> <strong id="dooroora-internal-count"><?php echo intval($link_counts['internal']); ?></strong></span>
                    <span class="dooroora-stat">🔗 <?php esc_html_e('External:', 'dooroora'); ?> <strong id="dooroora-external-count"><?php echo intval($link_counts['external']); ?></strong></span>
                    <span class="dooroora-stat">🖼 <?php esc_html_e('Images:', 'dooroora'); ?> <strong id="dooroora-image-count"><?php echo intval($img_counts['total']); ?></strong></span>
                    <span class="dooroora-stat">🖼 <?php esc_html_e('With Alt:', 'dooroora'); ?> <strong id="dooroora-alt-count"><?php echo intval($img_counts['with_alt']); ?></strong></span>
                    <span class="dooroora-stat">📝 <?php esc_html_e('Words:', 'dooroora'); ?> <strong id="dooroora-word-count"><?php echo str_word_count($content_for_analysis); ?></strong></span>
                </div>
            </div>

            <div class="dooroora-seo-side">
                <!-- Canonical -->
                <p>
                    <label for="dooroora_seo_canonical"><strong><?php esc_html_e('Canonical URL', 'dooroora'); ?></strong></label>
                    <input type="url" name="dooroora_seo_canonical" id="dooroora_seo_canonical" value="<?php echo $canonical_val; ?>" style="width:100%;" />
                </p>

                <!-- Robots -->
                <p>
                    <label for="dooroora_robots"><strong><?php esc_html_e('Robots', 'dooroora'); ?></strong></label>
                    <select name="dooroora_robots" id="dooroora_robots" style="width:100%;">
                        <option value=""><?php esc_html_e('Default (index,follow)', 'dooroora'); ?></option>
                        <option value="index,follow" <?php selected($robots_val, 'index,follow'); ?>><?php esc_html_e('Index, Follow', 'dooroora'); ?></option>
                        <option value="noindex,follow" <?php selected($robots_val, 'noindex,follow'); ?>><?php esc_html_e('No Index, Follow', 'dooroora'); ?></option>
                        <option value="index,nofollow" <?php selected($robots_val, 'index,nofollow'); ?>><?php esc_html_e('Index, No Follow', 'dooroora'); ?></option>
                        <option value="noindex,nofollow" <?php selected($robots_val, 'noindex,nofollow'); ?>><?php esc_html_e('No Index, No Follow', 'dooroora'); ?></option>
                    </select>
                </p>

                <!-- Open Graph -->
                <p><strong><?php esc_html_e('Open Graph', 'dooroora'); ?></strong></p>
                <p>
                    <label for="dooroora_og_title"><?php esc_html_e('OG Title', 'dooroora'); ?></label>
                    <input type="text" name="dooroora_og_title" id="dooroora_og_title" value="<?php echo $og_title_val; ?>" style="width:100%;" />
                </p>
                <p>
                    <label for="dooroora_og_desc"><?php esc_html_e('OG Description', 'dooroora'); ?></label>
                    <textarea name="dooroora_og_desc" id="dooroora_og_desc" rows="2" style="width:100%;"><?php echo $og_desc_val; ?></textarea>
                </p>
                <p>
                    <label for="dooroora_og_image"><?php esc_html_e('OG Image URL', 'dooroora'); ?></label>
                    <input type="url" name="dooroora_og_image" id="dooroora_og_image" value="<?php echo $og_image_val; ?>" style="width:100%;" />
                </p>

                <!-- Custom Schema -->
                <p>
                    <label for="dooroora_custom_schema"><strong><?php esc_html_e('Custom Schema (JSON-LD)', 'dooroora'); ?></strong></label>
                    <textarea name="dooroora_custom_schema" id="dooroora_custom_schema" rows="4" style="width:100%;font-family:monospace;font-size:0.8125rem;"><?php echo esc_textarea($custom_schema); ?></textarea>
                </p>
            </div>
        </div>

        <!-- Data attributes for JS analysis -->
        <div id="dooroora-seo-data"
             data-title="<?php echo esc_attr($post->post_title); ?>"
             data-content="<?php echo esc_attr(wp_trim_words($content_for_analysis, 500)); ?>"
             data-permalink="<?php echo esc_url(get_permalink($post->ID)); ?>"
             data-site-name="<?php echo esc_attr(get_bloginfo('name')); ?>"></div>
    </div>
    <?php
}

add_action('save_post', 'dooroora_save_seo_meta');
function dooroora_save_seo_meta($post_id) {
    if (!isset($_POST['dooroora_seo_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['dooroora_seo_box_nonce'], 'dooroora_seo_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = array('dooroora_seo_title', 'dooroora_seo_desc', 'dooroora_seo_canonical',
                    'dooroora_og_title', 'dooroora_og_desc', 'dooroora_og_image', 'dooroora_robots',
                    'dooroora_custom_schema', 'dooroora_focus_keyphrase');
    foreach ($fields as $key) {
        if (isset($_POST[$key])) {
            $val = $key === 'dooroora_custom_schema' ? wp_kses_post($_POST[$key]) : sanitize_text_field($_POST[$key]);
            update_post_meta($post_id, $key, $val);
        }
    }
}

/* ----------------------------------------------------------
   Helper: count links in content
   ---------------------------------------------------------- */
function dooroora_count_post_links($content) {
    $internal = 0;
    $external = 0;
    $home_url = home_url('/');
    preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $link) {
            if (strpos($link, '#') === 0 || strpos($link, 'mailto:') === 0 || strpos($link, 'tel:') === 0) continue;
            if (strpos($link, $home_url) !== false || strpos($link, '/') === 0) {
                $internal++;
            } else {
                $external++;
            }
        }
    }
    return array('internal' => $internal, 'external' => $external);
}

function dooroora_count_images($content) {
    $total = 0;
    $with_alt = 0;
    preg_match_all('/<img\s[^>]*>/i', $content, $matches);
    foreach ($matches[0] as $img) {
        $total++;
        if (preg_match('/alt\s*=\s*["\'].+["\']/i', $img)) {
            $with_alt++;
        }
    }
    return array('total' => $total, 'with_alt' => $with_alt);
}

/* ----------------------------------------------------------
   Override document title / meta with per-post SEO values
   ---------------------------------------------------------- */
add_filter('pre_get_document_title', 'dooroora_seo_override_title');
function dooroora_seo_override_title($title) {
    if (is_singular()) {
        $seo_title = get_post_meta(get_the_ID(), 'dooroora_seo_title', true);
        if (!empty($seo_title)) {
            return esc_html($seo_title);
        }
    }
    return $title;
}

add_action('wp_head', 'dooroora_seo_override_meta', 0);
function dooroora_seo_override_meta() {
    if (!is_singular()) return;
    $post_id = get_the_ID();

    $desc = get_post_meta($post_id, 'dooroora_seo_desc', true);
    if (!empty($desc)) {
        echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
    }

    $canonical = get_post_meta($post_id, 'dooroora_seo_canonical', true);
    if (!empty($canonical)) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";
    }

    $robots = get_post_meta($post_id, 'dooroora_robots', true);
    if (!empty($robots)) {
        echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";
    }

    $og_title = get_post_meta($post_id, 'dooroora_og_title', true);
    $og_desc  = get_post_meta($post_id, 'dooroora_og_desc', true);
    $og_image = get_post_meta($post_id, 'dooroora_og_image', true);

    if (!empty($og_title)) {
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '" />' . "\n";
    }
    if (!empty($og_desc)) {
        echo '<meta property="og:description" content="' . esc_attr($og_desc) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($og_desc) . '" />' . "\n";
    }
    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '" />' . "\n";
    }

    $custom_schema = get_post_meta($post_id, 'dooroora_custom_schema', true);
    if (!empty($custom_schema)) {
        echo $custom_schema . "\n";
    }
}

/* ----------------------------------------------------------
   Category SEO fields
   ---------------------------------------------------------- */
add_action('category_add_form_fields', 'dooroora_category_seo_fields_add');
function dooroora_category_seo_fields_add() {
    ?>
    <div class="form-field">
        <label for="dooroora_cat_seo_title"><?php esc_html_e('SEO Title', 'dooroora'); ?></label>
        <input type="text" name="dooroora_cat_seo_title" id="dooroora_cat_seo_title" value="" />
    </div>
    <div class="form-field">
        <label for="dooroora_cat_seo_desc"><?php esc_html_e('Meta Description', 'dooroora'); ?></label>
        <textarea name="dooroora_cat_seo_desc" id="dooroora_cat_seo_desc" rows="3"></textarea>
    </div>
    <div class="form-field">
        <label for="dooroora_cat_canonical"><?php esc_html_e('Canonical URL', 'dooroora'); ?></label>
        <input type="url" name="dooroora_cat_canonical" id="dooroora_cat_canonical" value="" />
    </div>
    <div class="form-field">
        <label for="dooroora_cat_og_title"><?php esc_html_e('OG Title', 'dooroora'); ?></label>
        <input type="text" name="dooroora_cat_og_title" id="dooroora_cat_og_title" value="" />
    </div>
    <div class="form-field">
        <label for="dooroora_cat_og_desc"><?php esc_html_e('OG Description', 'dooroora'); ?></label>
        <textarea name="dooroora_cat_og_desc" id="dooroora_cat_og_desc" rows="2"></textarea>
    </div>
    <div class="form-field">
        <label for="dooroora_cat_robots"><?php esc_html_e('Robots Setting', 'dooroora'); ?></label>
        <select name="dooroora_cat_robots" id="dooroora_cat_robots">
            <option value=""><?php esc_html_e('Default (index,follow)', 'dooroora'); ?></option>
            <option value="index,follow"><?php esc_html_e('Index, Follow', 'dooroora'); ?></option>
            <option value="noindex,follow"><?php esc_html_e('No Index, Follow', 'dooroora'); ?></option>
            <option value="index,nofollow"><?php esc_html_e('Index, No Follow', 'dooroora'); ?></option>
            <option value="noindex,nofollow"><?php esc_html_e('No Index, No Follow', 'dooroora'); ?></option>
        </select>
    </div>
    <?php
}

add_action('category_edit_form_fields', 'dooroora_category_seo_fields_edit');
function dooroora_category_seo_fields_edit($term) {
    $seo_title    = get_term_meta($term->term_id, 'dooroora_cat_seo_title', true);
    $seo_desc     = get_term_meta($term->term_id, 'dooroora_cat_seo_desc', true);
    $canonical    = get_term_meta($term->term_id, 'dooroora_cat_canonical', true);
    $og_title     = get_term_meta($term->term_id, 'dooroora_cat_og_title', true);
    $og_desc      = get_term_meta($term->term_id, 'dooroora_cat_og_desc', true);
    $robots       = get_term_meta($term->term_id, 'dooroora_cat_robots', true);
    ?>
    <tr class="form-field">
        <th><label for="dooroora_cat_seo_title"><?php esc_html_e('SEO Title', 'dooroora'); ?></label></th>
        <td><input type="text" name="dooroora_cat_seo_title" id="dooroora_cat_seo_title" value="<?php echo esc_attr($seo_title); ?>" class="regular-text" /></td>
    </tr>
    <tr class="form-field">
        <th><label for="dooroora_cat_seo_desc"><?php esc_html_e('Meta Description', 'dooroora'); ?></label></th>
        <td><textarea name="dooroora_cat_seo_desc" id="dooroora_cat_seo_desc" rows="3" class="regular-text"><?php echo esc_textarea($seo_desc); ?></textarea></td>
    </tr>
    <tr class="form-field">
        <th><label for="dooroora_cat_canonical"><?php esc_html_e('Canonical URL', 'dooroora'); ?></label></th>
        <td><input type="url" name="dooroora_cat_canonical" id="dooroora_cat_canonical" value="<?php echo esc_attr($canonical); ?>" class="regular-text" /></td>
    </tr>
    <tr class="form-field">
        <th><label for="dooroora_cat_og_title"><?php esc_html_e('OG Title', 'dooroora'); ?></label></th>
        <td><input type="text" name="dooroora_cat_og_title" id="dooroora_cat_og_title" value="<?php echo esc_attr($og_title); ?>" class="regular-text" /></td>
    </tr>
    <tr class="form-field">
        <th><label for="dooroora_cat_og_desc"><?php esc_html_e('OG Description', 'dooroora'); ?></label></th>
        <td><textarea name="dooroora_cat_og_desc" id="dooroora_cat_og_desc" rows="2" class="regular-text"><?php echo esc_textarea($og_desc); ?></textarea></td>
    </tr>
    <tr class="form-field">
        <th><label for="dooroora_cat_robots"><?php esc_html_e('Robots Setting', 'dooroora'); ?></label></th>
        <td>
            <select name="dooroora_cat_robots" id="dooroora_cat_robots">
                <option value=""><?php esc_html_e('Default (index,follow)', 'dooroora'); ?></option>
                <option value="index,follow" <?php selected($robots, 'index,follow'); ?>><?php esc_html_e('Index, Follow', 'dooroora'); ?></option>
                <option value="noindex,follow" <?php selected($robots, 'noindex,follow'); ?>><?php esc_html_e('No Index, Follow', 'dooroora'); ?></option>
                <option value="index,nofollow" <?php selected($robots, 'index,nofollow'); ?>><?php esc_html_e('Index, No Follow', 'dooroora'); ?></option>
                <option value="noindex,nofollow" <?php selected($robots, 'noindex,nofollow'); ?>><?php esc_html_e('No Index, No Follow', 'dooroora'); ?></option>
            </select>
        </td>
    </tr>
    <?php
}

add_action('created_category', 'dooroora_save_category_seo');
add_action('edited_category', 'dooroora_save_category_seo');
function dooroora_save_category_seo($term_id) {
    $fields = array('dooroora_cat_seo_title', 'dooroora_cat_seo_desc', 'dooroora_cat_canonical',
                    'dooroora_cat_og_title', 'dooroora_cat_og_desc', 'dooroora_cat_robots');
    foreach ($fields as $key) {
        if (isset($_POST[$key])) {
            $val = in_array($key, array('dooroora_cat_seo_desc', 'dooroora_cat_og_desc'))
                ? sanitize_textarea_field($_POST[$key])
                : sanitize_text_field($_POST[$key]);
            update_term_meta($term_id, $key, $val);
        }
    }
}

add_filter('pre_get_document_title', 'dooroora_category_seo_title');
function dooroora_category_seo_title($title) {
    if (is_category()) {
        $cat = get_queried_object();
        $seo_title = get_term_meta($cat->term_id, 'dooroora_cat_seo_title', true);
        if (!empty($seo_title)) {
            return esc_html($seo_title);
        }
    }
    return $title;
}

add_action('wp_head', 'dooroora_category_seo_meta', 0);
function dooroora_category_seo_meta() {
    if (!is_category()) return;
    $cat = get_queried_object();

    $desc = get_term_meta($cat->term_id, 'dooroora_cat_seo_desc', true);
    if (!empty($desc)) {
        echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
    }
    $canonical = get_term_meta($cat->term_id, 'dooroora_cat_canonical', true);
    if (!empty($canonical)) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";
    }
    $robots = get_term_meta($cat->term_id, 'dooroora_cat_robots', true);
    if (!empty($robots)) {
        echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";
    }
    $og_title = get_term_meta($cat->term_id, 'dooroora_cat_og_title', true);
    $og_desc  = get_term_meta($cat->term_id, 'dooroora_cat_og_desc', true);
    if (!empty($og_title)) {
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
    }
    if (!empty($og_desc)) {
        echo '<meta property="og:description" content="' . esc_attr($og_desc) . '" />' . "\n";
    }
}

/* ----------------------------------------------------------
   Advanced Robots — per-template settings in wp_head
   ---------------------------------------------------------- */
add_action('wp_head', 'dooroora_advanced_robots', 0);
function dooroora_advanced_robots() {
    // Skip if per-post robots already set
    if (is_singular()) {
        $post_robots = get_post_meta(get_the_ID(), 'dooroora_robots', true);
        if (!empty($post_robots)) return;
    }
    if (is_category()) {
        $cat_robots = get_term_meta(get_queried_object_id(), 'dooroora_cat_robots', true);
        if (!empty($cat_robots)) return;
    }

    $robots = '';
    if (is_tag() && get_option('dooroora_robots_tag', '') === 'noindex') {
        $robots = 'noindex,follow';
    } elseif (is_author() && get_option('dooroora_robots_author', '') === 'noindex') {
        $robots = 'noindex,follow';
    } elseif (is_date() && get_option('dooroora_robots_date', '') === 'noindex') {
        $robots = 'noindex,follow';
    } elseif (is_search() && get_option('dooroora_robots_search', '') === 'noindex') {
        $robots = 'noindex,follow';
    }

    if (!empty($robots)) {
        echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";
    }
}

/* ----------------------------------------------------------
   XML Sitemap
   ---------------------------------------------------------- */
add_filter('wp_sitemaps_enabled', 'dooroora_toggle_native_sitemap');
function dooroora_toggle_native_sitemap($enabled) {
    if (!get_option('dooroora_sitemap_enabled', '1')) return false;
    return $enabled;
}

add_action('template_redirect', 'dooroora_redirect_old_sitemap');
function dooroora_redirect_old_sitemap() {
    if (!get_option('dooroora_sitemap_enabled', '1')) return;
    $request = trailingslashit(strtok($_SERVER['REQUEST_URI'], '?'));
    if ($request === '/sitemap.xml' || $request === '/sitemap.xml/') {
        wp_safe_redirect(home_url('/wp-sitemap.xml'), 301);
        exit;
    }
}

add_action('after_switch_theme', 'dooroora_flush_on_activation');
function dooroora_flush_on_activation() {
    flush_rewrite_rules();
    dooroora_create_newsletter_table();
}

add_action('admin_init', 'dooroora_check_newsletter_table');
function dooroora_check_newsletter_table() {
    if (!get_option('dooroora_newsletter_db_version')) {
        dooroora_create_newsletter_table();
    }
}

function dooroora_create_newsletter_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dooroora_newsletter';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('dooroora_newsletter_db_version', '1.0');
}
