    </main>

    <footer id="colophon" class="site-footer" itemscope itemtype="https://schema.org/WPFooter">
        <div class="container">
            <div class="footer-grid">

                <!-- Column 1: About + Social -->
                <div class="footer-col footer-col-about">
                    <div class="footer-logo">
                        <?php if (has_custom_logo()) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <span class="footer-site-title"><?php bloginfo('name'); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="footer-desc">
                        <?php echo esc_html(get_option('dooroora_footer_description', __('DoorOora is an independent resource covering doors, locks, garage systems, installation guides, hardware, smart entry solutions, and home security advice.', 'dooroora'))); ?>
                    </p>
                    <div class="footer-social">
                        <?php
                        $social_links = dooroora_get_social_links();
                        $social_icons = array(
                            'facebook'  => 'fa-brands fa-facebook-f',
                            'x'         => 'fa-brands fa-x-twitter',
                            'twitter'   => 'fa-brands fa-x-twitter',
                            'linkedin'  => 'fa-brands fa-linkedin-in',
                            'pinterest' => 'fa-brands fa-pinterest-p',
                            'youtube'   => 'fa-brands fa-youtube',
                        );
                        foreach ($social_links as $platform => $url) :
                            $icon = isset($social_icons[$platform]) ? $social_icons[$platform] : 'fa-brands fa-' . $platform;
                        ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($platform); ?>" class="footer-social-link"><i class="<?php echo esc_attr($icon); ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 2: Categories with icons -->
                <div class="footer-col footer-col-categories">
                    <h4 class="footer-col-title"><?php esc_html_e('Categories', 'dooroora'); ?></h4>
                    <ul class="footer-category-list">
                        <?php
                        $categories = get_categories(array('hide_empty' => false));
                        $map = dooroora_get_category_mapping();
                        foreach ($categories as $cat) :
                            if (!isset($map[$cat->slug])) continue;
                            $icon = esc_attr($map[$cat->slug]['icon']);
                        ?>
                            <li><a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><i class="<?php echo $icon; ?>"></i> <?php echo esc_html($cat->name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Column 3: Quick Links (hardcoded) -->
                <div class="footer-col footer-col-links">
                    <h4 class="footer-col-title"><?php esc_html_e('Quick Links', 'dooroora'); ?></h4>
                    <ul class="footer-quick-links">
                        <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About Us', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms-conditions/')); ?>"><?php esc_html_e('Terms & Conditions', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/editorial-policy/')); ?>"><?php esc_html_e('Editorial Policy', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>"><?php esc_html_e('HTML Sitemap', 'dooroora'); ?></a></li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-inner">
                    <p class="footer-copyright">
                        <?php $copyright = get_option('dooroora_copyright_text', ''); ?>
                        <?php if (!empty($copyright)) : ?>
                            <?php echo esc_html(str_replace('{year}', gmdate('Y'), $copyright)); ?>
                        <?php else : ?>
                            &copy; <?php echo esc_html(gmdate('Y')); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>. <?php esc_html_e('All rights reserved.', 'dooroora'); ?>
                        <?php endif; ?>
                    </p>
                    <ul class="footer-bottom-links">
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'dooroora'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms-conditions/')); ?>"><?php esc_html_e('Terms of Use', 'dooroora'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>

<button class="scroll-to-top" id="scroll-to-top" aria-label="<?php esc_attr_e('Scroll to top', 'dooroora'); ?>">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php wp_footer(); ?>
</body>
</html>
