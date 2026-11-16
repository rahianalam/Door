<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content-col">
                <h1 class="hero-heading"><?php echo esc_html(dooroora_hero_heading()); ?></h1>
                <p class="hero-description"><?php echo esc_html(dooroora_hero_description()); ?></p>

                <form role="search" method="get" class="hero-search" action="<?php echo esc_url(home_url('/')); ?>">
                    <label for="hero-search-input" class="screen-reader-text">
                        <?php esc_html_e('Search', 'dooroora'); ?>
                    </label>
                    <input type="search" id="hero-search-input" class="hero-search-input" placeholder="<?php esc_attr_e('Search guides, repairs, locks, garage doors...', 'dooroora'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="btn btn-primary hero-search-btn">
                        <?php esc_html_e('Search Guides', 'dooroora'); ?>
                    </button>
                </form>

                <ul class="hero-trust">
                    <li><i class="fa-solid fa-circle-check"></i> <?php esc_html_e('Expert Written Guides', 'dooroora'); ?></li>
                    <li><i class="fa-solid fa-circle-check"></i> <?php esc_html_e('Fact Checked Content', 'dooroora'); ?></li>
                    <li><i class="fa-solid fa-circle-check"></i> <?php esc_html_e('Updated Regularly', 'dooroora'); ?></li>
                    <li><i class="fa-solid fa-circle-check"></i> <?php esc_html_e('Practical Real-World Advice', 'dooroora'); ?></li>
                </ul>
            </div>

            <div class="hero-image-col">
                <img src="https://dooroora.com/wp-content/uploads/2026/06/image-removebg-preview-5.png" alt="<?php esc_attr_e('DoorOora — Everything about doors', 'dooroora'); ?>" class="hero-image" loading="eager" />
            </div>
        </div>
    </div>
</section>
