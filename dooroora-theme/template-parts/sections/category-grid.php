<section id="categories" class="category-grid-section">
    <div class="container">
        <h2 class="section-title">
            <?php esc_html_e('Explore by Category', 'dooroora'); ?>
        </h2>
        <div class="category-grid">
            <?php
            $categories = dooroora_get_category_mapping();
            foreach ($categories as $slug => $cat) :
                $cat_url = get_category_link(get_category_by_slug($slug));
                ?>
                <a href="<?php echo esc_url($cat_url); ?>" class="category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <span class="category-card-icon">
                        <i class="<?php echo esc_attr($cat['icon']); ?>"></i>
                    </span>
                    <span class="category-card-label"><?php echo esc_html($cat['label']); ?></span>
                    <span class="category-card-desc"><?php echo esc_html($cat['description']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
