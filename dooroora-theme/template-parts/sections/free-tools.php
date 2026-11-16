<section class="free-tools-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Free Tools', 'dooroora'); ?></h2>
            <p class="section-subtitle"><?php esc_html_e('Calculate Before You Buy Or Install', 'dooroora'); ?></p>
            <p class="section-description"><?php esc_html_e('Use our upcoming calculators and planning tools to make informed purchasing and installation decisions.', 'dooroora'); ?></p>
        </div>

        <div class="tools-grid" id="tools-grid">
            <?php
            $tools_query = new WP_Query(array(
                'post_type'      => 'tool',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ));

            if ($tools_query->have_posts()) :
                while ($tools_query->have_posts()) : $tools_query->the_post();
                    $icon = get_post_meta(get_the_ID(), 'tool_icon', true) ?: 'fa-solid fa-toolbox';
            ?>
                    <a href="<?php the_permalink(); ?>" class="tool-card">
                        <span class="tool-card-icon"><i class="<?php echo esc_attr($icon); ?>"></i></span>
                        <span class="tool-card-title"><?php the_title(); ?></span>
                        <?php if (has_excerpt()) : ?>
                            <span class="tool-card-desc"><?php echo esc_html(get_the_excerpt()); ?></span>
                        <?php endif; ?>
                    </a>
                <?php
                endwhile;
                wp_reset_postdata();
            else :
                // Placeholder tools
                $placeholder_tools = array(
                    array(
                        'title' => __('Door Size Calculator', 'dooroora'),
                        'desc'  => __('Calculate the perfect door size for any opening.', 'dooroora'),
                        'icon'  => 'fa-solid fa-ruler-combined',
                        'slug'  => 'door-size-calculator',
                    ),
                    array(
                        'title' => __('Lock Security Matcher', 'dooroora'),
                        'desc'  => __('Find the best lock for your security needs.', 'dooroora'),
                        'icon'  => 'fa-solid fa-lock',
                        'slug'  => 'lock-security-matcher',
                    ),
                    array(
                        'title' => __('Garage Wire Size Guide', 'dooroora'),
                        'desc'  => __('Determine the correct wire size for your garage.', 'dooroora'),
                        'icon'  => 'fa-solid fa-bolt',
                        'slug'  => 'garage-wire-size-guide',
                    ),
                );
                foreach ($placeholder_tools as $tool) :
            ?>
                    <div class="tool-card tool-card-placeholder">
                        <span class="tool-card-icon"><i class="<?php echo esc_attr($tool['icon']); ?>"></i></span>
                        <span class="tool-card-title"><?php echo esc_html($tool['title']); ?></span>
                        <span class="tool-card-desc"><?php echo esc_html($tool['desc']); ?></span>
                        <span class="tool-card-badge"><?php esc_html_e('Coming Soon', 'dooroora'); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
