<?php
defined('ABSPATH') || exit;

add_action('init', 'dooroora_register_blocks');
function dooroora_register_blocks() {
    if (!function_exists('register_block_type')) return;

    // Entity List block
    register_block_type('dooroora/entity-list', array(
        'title'           => __('Related Components', 'dooroora'),
        'description'     => __('Display related entities/components for semantic SEO.', 'dooroora'),
        'category'        => 'widgets',
        'icon'            => 'list-view',
        'keywords'        => array('entity', 'components', 'seo'),
        'attributes'      => array(
            'title'   => array('type' => 'string', 'default' => __('Components Mentioned', 'dooroora')),
            'entities' => array('type' => 'array', 'default' => array()),
        ),
        'render_callback' => 'dooroora_render_entity_list_block',
        'editor_script'   => 'dooroora-blocks-editor',
    ));

    // FAQ Schema block
    register_block_type('dooroora/faq', array(
        'title'           => __('FAQ Schema', 'dooroora'),
        'description'     => __('FAQ with JSON-LD schema for SEO.', 'dooroora'),
        'category'        => 'widgets',
        'icon'            => 'editor-help',
        'keywords'        => array('faq', 'schema', 'seo', 'questions'),
        'attributes'      => array(
            'title'     => array('type' => 'string', 'default' => __('Frequently Asked Questions', 'dooroora')),
            'items'     => array('type' => 'array', 'default' => array()),
        ),
        'render_callback' => 'dooroora_render_faq_block',
        'editor_script'   => 'dooroora-blocks-editor',
    ));

    // How-To Schema block
    register_block_type('dooroora/howto', array(
        'title'           => __('How-To Schema', 'dooroora'),
        'description'     => __('Step-by-step instructions with HowTo JSON-LD schema.', 'dooroora'),
        'category'        => 'widgets',
        'icon'            => 'editor-ol',
        'keywords'        => array('howto', 'steps', 'schema', 'seo', 'tutorial'),
        'attributes'      => array(
            'title'       => array('type' => 'string', 'default' => __('How To', 'dooroora')),
            'description' => array('type' => 'string', 'default' => ''),
            'steps'       => array('type' => 'array', 'default' => array()),
        ),
        'render_callback' => 'dooroora_render_howto_block',
        'editor_script'   => 'dooroora-blocks-editor',
    ));
}

add_action('enqueue_block_editor_assets', 'dooroora_enqueue_block_editor_assets');
function dooroora_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'dooroora-blocks-editor',
        DOOROORA_THEME_URI . '/assets/js/blocks.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        DOOROORA_THEME_VERSION,
        true
    );
}

/* ---- Entity List ---- */
function dooroora_render_entity_list_block($attributes) {
    $title    = !empty($attributes['title']) ? $attributes['title'] : __('Components Mentioned', 'dooroora');
    $entities = !empty($attributes['entities']) ? $attributes['entities'] : array();
    if (empty($entities)) return '';

    ob_start();
    ?>
    <div class="wp-block-dooroora-entity-list entity-list">
        <h3 class="entity-list-title"><?php echo esc_html($title); ?></h3>
        <ul class="entity-list-items">
            <?php foreach ($entities as $entity) : ?>
                <?php
                $label = is_array($entity) ? ($entity['label'] ?? $entity['text'] ?? '') : $entity;
                $url   = is_array($entity) ? ($entity['url'] ?? '') : '';
                ?>
                <li class="entity-list-item">
                    <i class="fa-solid fa-check-circle entity-list-icon"></i>
                    <?php if (!empty($url)) : ?>
                        <a href="<?php echo esc_url($url); ?>" class="entity-list-link"><?php echo esc_html($label); ?></a>
                    <?php else : ?>
                        <span class="entity-list-text"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

/* ---- FAQ Schema Block ---- */
function dooroora_render_faq_block($attributes) {
    $title = !empty($attributes['title']) ? $attributes['title'] : __('Frequently Asked Questions', 'dooroora');
    $items = !empty($attributes['items']) ? $attributes['items'] : array();
    if (empty($items)) return '';

    $schema_items = array();
    ob_start();
    ?>
    <div class="wp-block-dooroora-faq" itemscope itemtype="https://schema.org/FAQPage">
        <h2 class="faq-section-title"><?php echo esc_html($title); ?></h2>
        <div class="faq-list">
            <?php foreach ($items as $item) : ?>
                <?php
                $question = is_array($item) ? ($item['question'] ?? '') : '';
                $answer   = is_array($item) ? ($item['answer'] ?? '') : '';
                if (empty($question) && empty($answer)) continue;
                $schema_items[] = array(
                    '@type'          => 'Question',
                    'name'           => $question,
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => $answer,
                    ),
                );
                ?>
                <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="faq-question" itemprop="name" aria-expanded="false">
                        <?php echo esc_html($question); ?>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text"><?php echo wp_kses_post(wpautop($answer)); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script type="application/ld+json">
    <?php
    echo wp_json_encode(array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $schema_items,
    ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    ?>
    </script>
    <?php
    return ob_get_clean();
}

/* ---- How-To Schema Block ---- */
function dooroora_render_howto_block($attributes) {
    $title       = !empty($attributes['title']) ? $attributes['title'] : __('How To', 'dooroora');
    $description = !empty($attributes['description']) ? $attributes['description'] : '';
    $steps       = !empty($attributes['steps']) ? $attributes['steps'] : array();
    if (empty($steps)) return '';

    $schema_steps = array();
    $position = 1;
    ob_start();
    ?>
    <div class="wp-block-dooroora-howto">
        <h2 class="howto-section-title"><?php echo esc_html($title); ?></h2>
        <?php if (!empty($description)) : ?>
            <p class="howto-description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <ol class="howto-steps">
            <?php foreach ($steps as $step) : ?>
                <?php
                $step_title = is_array($step) ? ($step['title'] ?? '') : '';
                $step_desc  = is_array($step) ? ($step['description'] ?? '') : '';
                if (empty($step_title) && empty($step_desc)) continue;
                $schema_steps[] = array(
                    '@type'    => 'HowToStep',
                    'position' => $position,
                    'name'     => $step_title,
                    'text'     => $step_desc,
                );
                $position++;
                ?>
                <li class="howto-step">
                    <div class="howto-step-number"><?php echo esc_html($position - 1); ?></div>
                    <div class="howto-step-content">
                        <h3 class="howto-step-title"><?php echo esc_html($step_title); ?></h3>
                        <p class="howto-step-desc"><?php echo wp_kses_post($step_desc); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
    <script type="application/ld+json">
    <?php
    echo wp_json_encode(array(
        '@context'    => 'https://schema.org',
        '@type'       => 'HowTo',
        'name'        => $title,
        'description' => $description,
        'step'        => $schema_steps,
    ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    ?>
    </script>
    <?php
    return ob_get_clean();
}
