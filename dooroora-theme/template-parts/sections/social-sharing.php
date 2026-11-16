<div class="social-sharing" id="social-sharing">
    <span class="social-sharing-label"><?php esc_html_e('Share', 'dooroora'); ?></span>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="social-sharing-link social-sharing-facebook" aria-label="<?php esc_attr_e('Share on Facebook', 'dooroora'); ?>">
        <i class="fa-brands fa-facebook-f"></i>
    </a>
    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="social-sharing-link social-sharing-twitter" aria-label="<?php esc_attr_e('Share on X', 'dooroora'); ?>">
        <i class="fa-brands fa-x-twitter"></i>
    </a>
    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="social-sharing-link social-sharing-linkedin" aria-label="<?php esc_attr_e('Share on LinkedIn', 'dooroora'); ?>">
        <i class="fa-brands fa-linkedin-in"></i>
    </a>
    <a href="mailto:?subject=<?php echo esc_attr(get_the_title()); ?>&body=<?php echo esc_url(get_permalink()); ?>" class="social-sharing-link social-sharing-email" aria-label="<?php esc_attr_e('Share via Email', 'dooroora'); ?>">
        <i class="fa-regular fa-envelope"></i>
    </a>
</div>
