<?php
defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'dooroora_add_content_scoring_meta_box');
function dooroora_add_content_scoring_meta_box() {
    add_meta_box(
        'dooroora_content_scoring',
        __('Content Scoring (AEO/GEO)', 'dooroora'),
        'dooroora_render_content_scoring',
        'post',
        'side',
        'high'
    );
}

function dooroora_get_content_scores($post) {
    $content = $post->post_content;
    $text = wp_strip_all_tags($content);
    $words = str_word_count($text);
    $sentences = preg_match_all('/[.!?]+/', $text, $m);
    $paragraphs = substr_count($text, "\n\n") + 1;
    $scores = array();

    // 1. Intro Quality (10 pts) — first 100 words
    $first_words = wp_trim_words($text, 100, '');
    $has_hook = strlen($first_words) > 50;
    $intro_score = 0;
    if ($has_hook) $intro_score += 5;
    if ($words > 300) $intro_score += 5;
    $scores[] = array(
        'label' => __('Intro Quality', 'dooroora'),
        'score' => $intro_score,
        'max'   => 10,
        'pass'  => $intro_score >= 7,
        'warn'  => $intro_score >= 4 && $intro_score < 7,
        'note'  => $intro_score >= 7 ? __('Strong intro with good length', 'dooroora') : ($intro_score >= 4 ? __('Add more detail to the opening', 'dooroora') : __('Opening is too short — aim for 50+ words', 'dooroora')),
    );

    // 2. Structure (15 pts) — H2 count, H3 hierarchy, lists, tables
    $h2_count = substr_count($content, '<h2');
    $h3_count = substr_count($content, '<h3');
    $list_count = preg_match_all('/<(ul|ol)>/i', $content, $m);
    $table_count = preg_match_all('/<table>/i', $content, $m);
    $struct_score = 0;
    if ($h2_count >= 3) $struct_score += 5;
    elseif ($h2_count >= 1) $struct_score += 2;
    if ($h3_count >= 3) $struct_score += 4;
    elseif ($h3_count >= 1) $struct_score += 2;
    if ($list_count >= 1) $struct_score += 3;
    if ($table_count >= 1) $struct_score += 3;
    $scores[] = array(
        'label' => __('Content Structure', 'dooroora'),
        'score' => $struct_score,
        'max'   => 15,
        'pass'  => $struct_score >= 11,
        'warn'  => $struct_score >= 6 && $struct_score < 11,
        'note'  => $h2_count < 3 ? __('Add more H2 subheadings (aim for 3+)', 'dooroora') : ($h3_count < 3 ? __('Add H3 subheadings for deeper hierarchy', 'dooroora') : __('Good structure', 'dooroora')),
    );

    // 3. Keyphrase Usage (15 pts) — reads focus keyphrase from seo-manager
    $kw = get_post_meta($post->ID, 'dooroora_focus_keyphrase', true);
    $kw_score = 0;
    $kw_notes = array();
    if (!empty($kw)) {
        $kw_lower = strtolower($kw);
        $title_lower = strtolower(get_post_meta($post->ID, 'dooroora_seo_title', true) ?: $post->post_title);
        $desc_lower = strtolower(get_post_meta($post->ID, 'dooroora_seo_desc', true) ?: '');
        $content_lower = strtolower($text);

        if (strpos($title_lower, $kw_lower) !== false) { $kw_score += 4; } else { $kw_notes[] = __('Not in SEO title', 'dooroora'); }
        if (strpos($desc_lower, $kw_lower) !== false) { $kw_score += 3; } else { $kw_notes[] = __('Not in meta description', 'dooroora'); }
        if (strpos($content_lower, $kw_lower) !== false) { $kw_score += 3; } else { $kw_notes[] = __('Not in body content', 'dooroora'); }
        if (strpos(substr($content_lower, 0, 600), $kw_lower) !== false) { $kw_score += 3; } else { $kw_notes[] = __('Not in first 100 words', 'dooroora'); }

        // H2 presence
        preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/i', $content, $h2_matches);
        $kw_in_h2 = false;
        foreach ($h2_matches[1] as $h2) {
            if (stripos($h2, $kw) !== false) { $kw_in_h2 = true; break; }
        }
        if ($kw_in_h2) { $kw_score += 2; } else { $kw_notes[] = __('Not in any H2', 'dooroora'); }
    }
    $scores[] = array(
        'label' => __('Keyphrase Usage', 'dooroora'),
        'score' => $kw_score,
        'max'   => 15,
        'pass'  => $kw_score >= 11,
        'warn'  => $kw_score >= 6 && $kw_score < 11,
        'note'  => empty($kw) ? __('Set a focus keyphrase in SEO Settings', 'dooroora') : (empty($kw_notes) ? __('Keyphrase well distributed', 'dooroora') : implode('; ', array_slice($kw_notes, 0, 2))),
    );

    // 4. Readability (10 pts)
    $avg_sentence = $sentences > 0 ? $words / $sentences : 0;
    $flesch_score = 206.835 - 1.015 * ($words / max($sentences, 1)) - 84.6 * ($words > 0 ? str_word_count(implode(' ', array_slice(explode(' ', $text), 0, 100))) / 100 * 100 : 0);

    // Simplified readability: check avg sentence length and paragraph length
    $read_score = 0;
    if ($words > 0) {
        if ($avg_sentence <= 20) $read_score += 4;
        elseif ($avg_sentence <= 25) $read_score += 2;

        if ($paragraphs > 0 && ($words / $paragraphs) <= 80) $read_score += 3;

        // Check for transition words
        $transitions = array('therefore', 'however', 'furthermore', 'moreover', 'consequently', 'additionally', 'meanwhile', 'nevertheless', 'nonetheless', 'otherwise', 'thus', 'hence', 'indeed', 'notably', 'specifically', 'in addition', 'for example', 'for instance', 'in conclusion', 'in summary');
        $trans_count = 0;
        foreach ($transitions as $t) {
            $trans_count += substr_count(strtolower($text), $t);
        }
        if ($trans_count >= 3) $read_score += 3;
        elseif ($trans_count >= 1) $read_score += 1;
    }
    $scores[] = array(
        'label' => __('Readability', 'dooroora'),
        'score' => $read_score,
        'max'   => 10,
        'pass'  => $read_score >= 7,
        'warn'  => $read_score >= 4 && $read_score < 7,
        'note'  => $avg_sentence > 25 ? __('Average sentence length exceeds 25 words — aim for 15-20', 'dooroora') : ($trans_count < 3 ? __('Add more transition words', 'dooroora') : __('Good readability', 'dooroora')),
    );

    // 5. Multimedia (10 pts)
    $featured = has_post_thumbnail($post->ID);
    preg_match_all('/<img\s[^>]*>/i', $content, $img_matches);
    $total_imgs = count($img_matches[0]);
    $with_alt = 0;
    foreach ($img_matches[0] as $img) {
        if (preg_match('/alt\s*=\s*["\']\S+["\']/i', $img)) $with_alt++;
    }
    $has_video = preg_match('/<iframe|<video/i', $content);
    $media_score = 0;
    if ($featured) $media_score += 4;
    if ($total_imgs > 0) $media_score += 2;
    if ($total_imgs > 0 && $with_alt === $total_imgs) $media_score += 2;
    elseif ($with_alt > 0) $media_score += 1;
    if ($has_video) $media_score += 2;
    $scores[] = array(
        'label' => __('Multimedia', 'dooroora'),
        'score' => $media_score,
        'max'   => 10,
        'pass'  => $media_score >= 7,
        'warn'  => $media_score >= 4 && $media_score < 7,
        'note'  => !$featured ? __('Add a featured image', 'dooroora') : ($total_imgs === 0 ? __('Add inline images', 'dooroora') : __('Good media usage', 'dooroora')),
    );

    // 6. Internal Links (10 pts)
    $internal_links = dooroora_count_internal_links($content);
    $home_url = home_url('/');
    preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $link_matches);
    $external_links = 0;
    foreach ($link_matches[1] as $link) {
        if (strpos($link, '#') === 0 || strpos($link, 'mailto:') === 0 || strpos($link, 'tel:') === 0) continue;
        if (strpos($link, $home_url) === false && strpos($link, '/') !== 0) $external_links++;
    }
    $link_score = 0;
    if ($internal_links >= 5) $link_score += 6;
    elseif ($internal_links >= 3) $link_score += 4;
    elseif ($internal_links >= 1) $link_score += 2;
    if ($external_links >= 1) $link_score += 4;
    else $link_score += 2; // for having at least nofollow or same-domain links checked
    $scores[] = array(
        'label' => __('Links (Internal / External)', 'dooroora'),
        'score' => $link_score,
        'max'   => 10,
        'pass'  => $link_score >= 7,
        'warn'  => $link_score >= 4 && $link_score < 7,
        'note'  => $internal_links . __(' internal, ', 'dooroora') . $external_links . __(' external links', 'dooroora'),
    );

    // 7. Schema Completeness (15 pts)
    $has_article_schema = is_singular('post');
    $schema_score = 0;
    $schema_notes = array();
    if ($has_article_schema) { $schema_score += 4; } else { $schema_notes[] = __('Article schema auto', 'dooroora'); }
    if (has_block('dooroora/faq', $post->ID) || has_block('core/heading', $post->ID) && stripos($content, 'FAQ') !== false) { $schema_score += 4; } else { $schema_notes[] = __('FAQ schema missing', 'dooroora'); }
    if (has_block('dooroora/howto', $post->ID)) { $schema_score += 4; } else { $schema_notes[] = __('HowTo schema missing', 'dooroora'); }
    $has_breadcrumbs = true; // theme auto-outputs breadcrumbs
    if ($has_breadcrumbs) $schema_score += 3;
    $scores[] = array(
        'label' => __('Schema Completeness', 'dooroora'),
        'score' => $schema_score,
        'max'   => 15,
        'pass'  => $schema_score >= 11,
        'warn'  => $schema_score >= 6 && $schema_score < 11,
        'note'  => empty($schema_notes) ? __('All schemas present', 'dooroora') : implode('; ', $schema_notes),
    );

    // 8. AEO Readiness (10 pts) — question coverage, direct answers
    $aeo_score = 0;
    $aeo_notes = array();
    // FAQ/QA coverage
    $faq_blocks = dooroora_extract_faq_blocks();
    $faq_count = count($faq_blocks);
    $has_faq_block = has_block('dooroora/faq', $post->ID);
    if ($has_faq_block || $faq_count > 0) {
        $aeo_score += 4;
    } else {
        $aeo_notes[] = __('Add FAQ section', 'dooroora');
    }
    // Direct answer in first paragraph
    $first_para = wp_trim_words($text, 50, '');
    if (strlen($first_para) > 80) $aeo_score += 3;
    else $aeo_notes[] = __('Short first paragraph — expand', 'dooroora');
    // Question phrasing usage (who, what, where, when, why, how)
    $question_words = array('what is', 'how to', 'how do', 'why does', 'what are', 'when should', 'where can', 'which is');
    $has_questions = false;
    foreach ($question_words as $qw) {
        if (stripos($text, $qw) !== false) { $has_questions = true; break; }
    }
    if ($has_questions) $aeo_score += 3;
    else $aeo_notes[] = __('Add question phrases (what, how, why)', 'dooroora');
    $scores[] = array(
        'label' => __('AEO Readiness (Answer Engine)', 'dooroora'),
        'score' => $aeo_score,
        'max'   => 10,
        'pass'  => $aeo_score >= 7,
        'warn'  => $aeo_score >= 4 && $aeo_score < 7,
        'note'  => empty($aeo_notes) ? __('Good answer engine coverage', 'dooroora') : implode('; ', $aeo_notes),
    );

    // 9. GEO Signals (10 pts) — entity depth, semantic coverage
    $geo_score = 0;
    $geo_notes = array();
    // Check for statistics/data
    $has_stats = preg_match('/\d+%|\d+ in \d+|\d+ out of \d+|statistic|according to|study show|research found/i', $text);
    if ($has_stats) { $geo_score += 3; } else { $geo_notes[] = __('Add relevant statistics', 'dooroora'); }
    // Word count for depth (aim 1500+)
    if ($words >= 1500) $geo_score += 3;
    elseif ($words >= 800) $geo_score += 1;
    else $geo_notes[] = __('Aim for 1500+ words', 'dooroora');
    // Entity diversity (check for schema.org entity types)
    $entity_terms = array('product', 'service', 'tool', 'guide', 'solution', 'system', 'brand', 'material', 'type');
    $entity_count = 0;
    foreach ($entity_terms as $et) {
        if (stripos($text, $et) !== false) $entity_count++;
    }
    if ($entity_count >= 5) $geo_score += 4;
    elseif ($entity_count >= 3) $geo_score += 2;
    else $geo_notes[] = __('Add more domain entities', 'dooroora');
    $scores[] = array(
        'label' => __('GEO Signals (Generative Engine)', 'dooroora'),
        'score' => $geo_score,
        'max'   => 10,
        'pass'  => $geo_score >= 7,
        'warn'  => $geo_score >= 4 && $geo_score < 7,
        'note'  => empty($geo_notes) ? __('Strong GEO signals', 'dooroora') : implode('; ', $geo_notes),
    );

    // Total score
    $total_score = 0;
    $total_max = 0;
    foreach ($scores as $s) {
        $total_score += $s['score'];
        $total_max += $s['max'];
    }

    return array('scores' => $scores, 'total' => $total_score, 'max' => $total_max);
}

function dooroora_render_content_scoring($post) {
    $result = dooroora_get_content_scores($post);
    $scores = $result['scores'];
    $total = $result['total'];
    $max = $result['max'];

    $pct = $max > 0 ? round(($total / $max) * 100) : 0;
    if ($pct >= 80) $grade = __('Excellent', 'dooroora');
    elseif ($pct >= 60) $grade = __('Good', 'dooroora');
    elseif ($pct >= 40) $grade = __('Average', 'dooroora');
    else $grade = __('Needs Work', 'dooroora');
    ?>
    <div class="dooroora-scoring">
        <ul class="dooroora-scoring-list">
            <?php foreach ($scores as $s) : ?>
                <li class="dooroora-scoring-item">
                    <span class="dooroora-scoring-icon <?php echo $s['pass'] ? 'pass' : ($s['warn'] ? 'warn' : 'fail'); ?>">
                        <?php echo $s['pass'] ? '&#10003;' : ($s['warn'] ? '&#9888;' : '&#10007;'); ?>
                    </span>
                    <span class="dooroora-scoring-label">
                        <strong><?php echo esc_html($s['label']); ?>:</strong>
                        <?php echo intval($s['score']); ?>/<?php echo intval($s['max']); ?>
                        <span class="dooroora-scoring-suggestion"><?php echo esc_html($s['note']); ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="dooroora-scoring-footer">
            <span class="dooroora-scoring-total"><?php printf(esc_html__('Score: %d / %d (%d%%)', 'dooroora'), $total, $max, $pct); ?></span>
            <span class="dooroora-scoring-grade
                <?php echo $pct >= 80 ? 'excellent' : ($pct >= 60 ? 'good' : ($pct >= 40 ? 'average' : 'poor')); ?>">
                <?php echo esc_html($grade); ?>
            </span>
        </div>
    </div>
    <?php
}

if (!function_exists('dooroora_count_internal_links')) {
function dooroora_count_internal_links($content) {
    $count = 0;
    $home_url = home_url('/');
    preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $link) {
            if (strpos($link, '#') === 0 || strpos($link, 'mailto:') === 0 || strpos($link, 'tel:') === 0) continue;
            if (strpos($link, $home_url) !== false || strpos($link, '/') === 0) $count++;
        }
    }
    return $count;
}
}
