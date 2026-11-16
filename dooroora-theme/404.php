<?php
/**
 * 404 template
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<div class="content-area error-404">
    <div class="container">
        <section class="error-section">
            <h1 class="error-code">404</h1>
            <p class="error-message">
                <?php esc_html_e('The page you are looking for does not exist.', 'dooroora'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                <?php esc_html_e('Back to Home', 'dooroora'); ?>
            </a>
        </section>
    </div>
</div>

<?php
get_footer();
