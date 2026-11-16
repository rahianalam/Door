<?php
defined('ABSPATH') || exit;

add_action('wp_dashboard_setup', 'dooroora_dashboard_widgets');
function dooroora_dashboard_widgets() {
    wp_add_dashboard_widget(
        'dooroora_content_health',
        __('DoorOora Content Health', 'dooroora'),
        'dooroora_render_dashboard_widget'
    );
}

function dooroora_render_dashboard_widget() {
    $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'post_type'      => 'post',
        'fields'         => 'ids',
    ));

    $total = count($posts);
    $scored = 0;
    $total_score = 0;

    $needs_improvement = 0;
    $no_keyphrase = 0;
    $no_faq = 0;
    $no_internal_links = 0;

    foreach ($posts as $post_id) {
        $kw = get_post_meta($post_id, 'dooroora_focus_keyphrase', true);
        if (empty($kw)) $no_keyphrase++;

        $content = get_post_field('post_content', $post_id);
        $text = wp_strip_all_tags($content);
        $words = str_word_count($text);

        if ($words > 0) {
            $scored++;
            $post_obj = get_post($post_id);
            $result = dooroora_get_content_scores($post_obj);
            $total_score += $result['total'];

            if ($result['total'] < ($result['max'] * 0.5)) {
                $needs_improvement++;
            }

            if (dooroora_count_internal_links($content) < 3) {
                $no_internal_links++;
            }
        }

        $faq_blocks = dooroora_extract_faq_blocks_for_post($post_id, $content);
        if (empty($faq_blocks) && !has_block('dooroora/faq', $post_id)) {
            $no_faq++;
        }
    }

    $avg_score = $scored > 0 ? round($total_score / $scored) : 0;
    $max_possible = $scored > 0 ? 100 : 0;
    $pct = $scored > 0 ? round(($total_score / ($scored * 100)) * 100) : 0;

    ?>
    <div class="dooroora-dashboard-widget">
        <div class="dooroora-dashboard-grid">
            <div class="dooroora-dashboard-card">
                <div class="dooroora-dashboard-number"><?php echo intval($total); ?></div>
                <div class="dooroora-dashboard-label"><?php esc_html_e('Published Posts', 'dooroora'); ?></div>
            </div>
            <div class="dooroora-dashboard-card">
                <div class="dooroora-dashboard-number"><?php echo intval($avg_score); ?>%</div>
                <div class="dooroora-dashboard-label"><?php esc_html_e('Avg Content Score', 'dooroora'); ?></div>
            </div>
            <div class="dooroora-dashboard-card">
                <div class="dooroora-dashboard-number"><?php echo intval($needs_improvement); ?></div>
                <div class="dooroora-dashboard-label"><?php esc_html_e('Need Improvement', 'dooroora'); ?></div>
            </div>
        </div>

        <p><strong><?php esc_html_e('Quick Stats:', 'dooroora'); ?></strong></p>
        <ul style="margin:0 0 0 16px;list-style:disc;">
            <li><?php echo intval($no_keyphrase); ?>/<?php echo intval($total); ?> <?php esc_html_e('posts missing a focus keyphrase', 'dooroora'); ?></li>
            <li><?php echo intval($no_faq); ?>/<?php echo intval($total); ?> <?php esc_html_e('posts without an FAQ section', 'dooroora'); ?></li>
            <li><?php echo intval($no_internal_links); ?>/<?php echo intval($total); ?> <?php esc_html_e('posts with fewer than 3 internal links', 'dooroora'); ?></li>
        </ul>

        <p style="margin-top:12px;font-size:12px;color:#6b7280;">
            <?php esc_html_e('Edit a post to see detailed AEO/GEO content scoring in the sidebar.', 'dooroora'); ?>
        </p>
    </div>
    <?php
}

function dooroora_extract_faq_blocks_for_post($post_id, $content) {
    $faqs = array();
    $blocks = parse_blocks($content);
    $faq_start = false;
    $current_q = '';
    $current_a = '';

    foreach ($blocks as $block) {
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
