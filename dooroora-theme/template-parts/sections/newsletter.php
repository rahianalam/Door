<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-inner">
            <h2 class="newsletter-title"><?php esc_html_e('Get The Right Guide, The Moment You Need It', 'dooroora'); ?></h2>
            <p class="newsletter-text"><?php echo esc_html(get_option('dooroora_newsletter_text', __('Join homeowners and professionals receiving door maintenance tips, buying advice, and expert guides.', 'dooroora'))); ?></p>
            <form class="newsletter-form" action="#" method="post">
                <label for="newsletter-email" class="screen-reader-text">
                    <?php esc_html_e('Your email address', 'dooroora'); ?>
                </label>
                <div class="newsletter-form-row">
                    <input type="email" id="newsletter-email" class="newsletter-input" placeholder="<?php esc_attr_e('Enter your email', 'dooroora'); ?>" required>
                    <button type="submit" class="btn btn-primary newsletter-submit">
                        <?php esc_html_e('Subscribe', 'dooroora'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
