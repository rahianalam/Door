<section class="browse-topics-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Browse by Topic', 'dooroora'); ?></h2>
            <p class="section-subtitle"><?php esc_html_e('Explore expert guides organized by category.', 'dooroora'); ?></p>
        </div>

        <div class="topics-grid">
            <?php
            $categories = get_categories(array('hide_empty' => false));
            $map = dooroora_get_category_mapping();

            foreach ($categories as $category) :
                $slug  = $category->slug;
                $cat   = isset($map[$slug]) ? $map[$slug] : array();
                $icon  = isset($cat['icon']) ? $cat['icon'] : 'fa-solid fa-tag';
                $color = isset($cat['color']) ? $cat['color'] : '#333';
                $desc  = isset($cat['description']) ? $cat['description'] : '';
                $count = $category->count;
            ?>
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="topic-card" style="--cat-color: <?php echo esc_attr($color); ?>">
                    <span class="topic-card-icon"><i class="<?php echo esc_attr($icon); ?>"></i></span>
                    <span class="topic-card-name"><?php echo esc_html($category->name); ?></span>
                    <span class="topic-card-count"><?php echo esc_html(sprintf(_n('%d Guide', '%d Guides', $count, 'dooroora'), $count)); ?></span>
                    <?php if ($desc) : ?>
                        <span class="topic-card-desc"><?php echo esc_html($desc); ?></span>
                    <?php endif; ?>
                    <span class="topic-card-cta"><?php esc_html_e('Explore Category', 'dooroora'); ?> &rarr;</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
