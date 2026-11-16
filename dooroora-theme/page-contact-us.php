<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 *
 * Dedicated Contact Page Template for DoorOora Blog
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main contact-page-wrapper">
    <div class="container">
        <!-- Hero & Breadcrumbs -->
        <header class="contact-hero">
            <nav class="contact-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'dooroora'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'dooroora'); ?></a>
                <span class="sep">&rsaquo;</span>
                <a href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Blog', 'dooroora'); ?></a>
                <span class="sep">&rsaquo;</span>
                <span class="current"><?php esc_html_e('Contact Editorial', 'dooroora'); ?></span>
            </nav>
            <h1 class="contact-title"><?php esc_html_e('Contact the DoorOora Editorial Team', 'dooroora'); ?></h1>
            <p class="contact-lead">
                <?php esc_html_e('DoorOora is an independent home improvement blog dedicated to practical door maintenance, lock hardware reviews, weatherproofing solutions, and step-by-step DIY installation guides. We love hearing from our readers, fellow DIYers, and industry partners.', 'dooroora'); ?>
            </p>
        </header>

        <!-- Quick Contact Channels for Blog -->
        <section class="contact-channels-grid" aria-label="<?php esc_attr_e('Blog Contact Channels', 'dooroora'); ?>">
            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h2 class="contact-channel-title"><?php esc_html_e('Reader Questions & Feedback', 'dooroora'); ?></h2>
                <p class="contact-channel-desc">
                    <?php esc_html_e('Have a question about a tutorial, or facing a tricky door problem we haven\'t covered yet? Share your project details with our writers.', 'dooroora'); ?>
                </p>
                <a href="#contact-form-section" class="contact-channel-link">
                    <?php esc_html_e('Ask Our Writers', 'dooroora'); ?> &darr;
                </a>
            </div>

            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <i class="fa-solid fa-pen-ruler"></i>
                </div>
                <h2 class="contact-channel-title"><?php esc_html_e('Editorial & Fact-Checking', 'dooroora'); ?></h2>
                <p class="contact-channel-desc">
                    <?php esc_html_e('We strive for 100% technical accuracy. Notice an outdated building code, manufacturer spec change, or typo in any of our articles?', 'dooroora'); ?>
                </p>
                <a href="mailto:editorial@dooroora.com" class="contact-channel-link">
                    editorial@dooroora.com &rarr;
                </a>
            </div>

            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h2 class="contact-channel-title"><?php esc_html_e('Product Reviews & Testing', 'dooroora'); ?></h2>
                <p class="contact-channel-desc">
                    <?php esc_html_e('Represent a door hardware, smart lock, or weatherproofing brand? Inquire about editorial product testing and hands-on reviews.', 'dooroora'); ?>
                </p>
                <a href="mailto:partnerships@dooroora.com" class="contact-channel-link">
                    partnerships@dooroora.com &rarr;
                </a>
            </div>
        </section>

        <!-- Main Form & Information Grid -->
        <div class="contact-main-grid" id="contact-form-section">
            <!-- Left Column: Interactive Form -->
            <div class="contact-form-card">
                <div class="contact-form-header">
                    <h2 class="contact-form-title"><?php esc_html_e('Send a Message to the Editors', 'dooroora'); ?></h2>
                    <p class="contact-form-subtitle">
                        <?php esc_html_e('Whether you have a tutorial suggestion, a hardware question, or partnership inquiry, we read every note and typically reply within 1 to 2 business days.', 'dooroora'); ?>
                    </p>
                </div>

                <form id="dooroora-contact-form" class="dooroora-contact-form" method="post" novalidate>
                    <!-- Antispam Honeypot (hidden from human visitors) -->
                    <div class="contact-hp" aria-hidden="true" style="display:none !important; visibility:hidden !important;">
                        <label for="contact-hp-field">Website URL (Leave blank)</label>
                        <input type="text" id="contact-hp-field" name="dooroora_website_url" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Nonce & Action -->
                    <input type="hidden" name="action" value="dooroora_contact_form">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('dooroora_nonce')); ?>">

                    <div class="contact-form-row two-cols">
                        <div>
                            <label for="contact-name" class="contact-label">
                                <?php esc_html_e('Your Name', 'dooroora'); ?> <span class="required">*</span>
                            </label>
                            <input type="text" id="contact-name" name="name" class="contact-input" placeholder="<?php esc_attr_e('e.g., Alex Miller', 'dooroora'); ?>" required>
                        </div>
                        <div>
                            <label for="contact-email" class="contact-label">
                                <?php esc_html_e('Email Address', 'dooroora'); ?> <span class="required">*</span>
                            </label>
                            <input type="email" id="contact-email" name="email" class="contact-input" placeholder="<?php esc_attr_e('alex@example.com', 'dooroora'); ?>" required>
                        </div>
                    </div>

                    <div class="contact-form-row two-cols">
                        <div>
                            <label for="contact-inquiry-type" class="contact-label">
                                <?php esc_html_e('Inquiry Topic', 'dooroora'); ?> <span class="required">*</span>
                            </label>
                            <select id="contact-inquiry-type" name="inquiry_type" class="contact-select">
                                <option value="reader-question"><?php esc_html_e('Reader Question / Guide Feedback', 'dooroora'); ?></option>
                                <option value="topic-suggestion"><?php esc_html_e('Tutorial / Topic Suggestion', 'dooroora'); ?></option>
                                <option value="editorial"><?php esc_html_e('Editorial Correction / Fact-Check', 'dooroora'); ?></option>
                                <option value="product-testing"><?php esc_html_e('Product Review / Brand Collaboration', 'dooroora'); ?></option>
                                <option value="general"><?php esc_html_e('General Inquiry / Other', 'dooroora'); ?></option>
                            </select>
                        </div>
                        <div>
                            <label for="contact-subject" class="contact-label">
                                <?php esc_html_e('Subject', 'dooroora'); ?> <span class="required">*</span>
                            </label>
                            <input type="text" id="contact-subject" name="subject" class="contact-input" placeholder="<?php esc_attr_e('e.g., Question about storm door closer tension guide', 'dooroora'); ?>" required>
                        </div>
                    </div>

                    <div class="contact-form-row">
                        <label for="contact-message" class="contact-label">
                            <?php esc_html_e('Your Message', 'dooroora'); ?> <span class="required">*</span>
                        </label>
                        <textarea id="contact-message" name="message" class="contact-textarea" rows="6" placeholder="<?php esc_attr_e('Share your thoughts, DIY project details, or article feedback. If referencing a published guide, feel free to include the topic or article link...', 'dooroora'); ?>" required></textarea>
                    </div>

                    <div class="contact-form-row">
                        <button type="submit" class="contact-submit-btn" id="contact-submit-btn">
                            <span class="btn-text"><?php esc_html_e('Send Message to Editorial', 'dooroora'); ?></span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>

                    <div id="contact-form-feedback" class="contact-form-feedback" style="display:none;" role="alert"></div>
                </form>
            </div>

            <!-- Right Column: Direct Info & Sidebar -->
            <aside class="contact-sidebar" aria-label="<?php esc_attr_e('Editorial Contact Information', 'dooroora'); ?>">
                <!-- Direct Editorial Channels Card -->
                <div class="contact-info-card">
                    <div class="contact-info-card-header">
                        <i class="fa-solid fa-envelope-open-text"></i>
                        <h3 class="contact-info-card-title"><?php esc_html_e('Editorial Inboxes', 'dooroora'); ?></h3>
                    </div>
                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Reader Mail & General', 'dooroora'); ?></span>
                            <span class="contact-info-value"><a href="mailto:contact@dooroora.com">contact@dooroora.com</a></span>
                        </div>
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Editorial & Fact-Checking', 'dooroora'); ?></span>
                            <span class="contact-info-value"><a href="mailto:editorial@dooroora.com">editorial@dooroora.com</a></span>
                        </div>
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Press & Product Testing', 'dooroora'); ?></span>
                            <span class="contact-info-value"><a href="mailto:partnerships@dooroora.com">partnerships@dooroora.com</a></span>
                        </div>
                    </div>
                </div>

                <!-- About the Blog Card -->
                <div class="contact-info-card">
                    <div class="contact-info-card-header">
                        <i class="fa-solid fa-book-open"></i>
                        <h3 class="contact-info-card-title"><?php esc_html_e('About DoorOora Blog', 'dooroora'); ?></h3>
                    </div>
                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Our Mission', 'dooroora'); ?></span>
                            <span class="contact-info-value"><?php esc_html_e('Independent, step-by-step door maintenance guides and unbiased hardware testing.', 'dooroora'); ?></span>
                        </div>
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Response Time', 'dooroora'); ?></span>
                            <span class="contact-info-value"><?php esc_html_e('Usually within 1 to 2 business days', 'dooroora'); ?></span>
                        </div>
                        <div class="contact-info-item">
                            <span class="contact-info-label"><?php esc_html_e('Editorial Integrity', 'dooroora'); ?></span>
                            <span class="contact-info-value"><?php esc_html_e('All guides are written and fact-checked in-house by our own team.', 'dooroora'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Editorial Policy & Guest Posts Notice -->
                <div class="contact-notice-card">
                    <div class="contact-notice-header">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span><?php esc_html_e('Guest Post & Link Policy', 'dooroora'); ?></span>
                    </div>
                    <p class="contact-notice-text">
                        <?php esc_html_e('To maintain the highest level of authenticity and technical rigor, DoorOora does not accept unsolicited guest articles, paid backlink insertions, or automated content submissions. All published guides are produced exclusively by our in-house testing staff.', 'dooroora'); ?>
                    </p>
                </div>
            </aside>
        </div>

        <!-- FAQ Section -->
        <section class="contact-faq-section" aria-label="<?php esc_attr_e('Frequently Asked Questions', 'dooroora'); ?>">
            <div class="contact-faq-header">
                <h2><?php esc_html_e('Frequently Asked Questions', 'dooroora'); ?></h2>
                <p><?php esc_html_e('Quick answers to common questions about our blog, guides, and testing process.', 'dooroora'); ?></p>
            </div>

            <div class="contact-faq-grid">
                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('Can I suggest a door topic or tutorial for you to write about?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php esc_html_e('Yes, please! Many of our most helpful guides began as questions from readers facing tricky DIY problems. If you\'re dealing with a sticking door, a drafty threshold, or an unusual lock issue, tell us about it. Our editorial team regularly turns reader challenges into detailed, step-by-step guides.', 'dooroora'); ?>
                    </p>
                </div>

                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('How do you review door hardware and tools on the blog?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php esc_html_e('Every lock, door seal, closer, and tool we feature is evaluated for real-world durability, ease of installation, and value. We never accept payment in exchange for positive reviews or altered testing conclusions. Our editorial recommendations remain 100% independent.', 'dooroora'); ?>
                    </p>
                </div>

                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('Can I quote or reference DoorOora guides on my own website?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php esc_html_e('You are welcome to reference brief excerpts, statistics, or recommendations from our tutorials, provided you include clear, clickable attribution linking directly back to the original article on DoorOora. Full article duplication or automated syndication is strictly prohibited.', 'dooroora'); ?>
                    </p>
                </div>

                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('Do you accept guest contributions or sponsored links?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php esc_html_e('No. To protect the technical quality and trustworthiness of our blog, we do not accept third-party guest posts, sponsored link insertions, or syndicated content. Everything on DoorOora is written and verified by our in-house team.', 'dooroora'); ?>
                    </p>
                </div>

                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('How quickly do you respond to reader emails?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php esc_html_e('We read every reader message! Because our writers spend time in the workshop and on testing benches, we typically respond to inquiries within 1 to 2 business days (Monday through Friday).', 'dooroora'); ?>
                    </p>
                </div>

                <div class="contact-faq-item">
                    <h3 class="contact-faq-q">
                        <i class="fa-solid fa-circle-question"></i>
                        <?php esc_html_e('Where can I find immediate answers or DIY calculators?', 'dooroora'); ?>
                    </h3>
                    <p class="contact-faq-a">
                        <?php echo sprintf(
                            /* translators: 1: blog url, 2: tools url */
                            esc_html__('Check out our complete library of articles on the %1$s, or use our interactive calculators on the %2$s page to size thresholds and check hinge requirements.', 'dooroora'),
                            '<a href="' . esc_url(home_url('/blog')) . '"><strong>' . esc_html__('DoorOora Blog', 'dooroora') . '</strong></a>',
                            '<a href="' . esc_url(home_url('/tools')) . '"><strong>' . esc_html__('Free Tools', 'dooroora') . '</strong></a>'
                        ); ?>
                    </p>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('dooroora-contact-form');
    if (!form) return;

    var submitBtn = document.getElementById('contact-submit-btn');
    var feedback = document.getElementById('contact-form-feedback');
    var ajaxUrl = (typeof dooroora_ajax !== 'undefined') ? dooroora_ajax.ajax_url : '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous state
        feedback.style.display = 'none';
        feedback.className = 'contact-form-feedback';
        feedback.textContent = '';

        // Basic client validation
        var nameInput = document.getElementById('contact-name');
        var emailInput = document.getElementById('contact-email');
        var messageInput = document.getElementById('contact-message');

        if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
            showFeedback('<?php echo esc_js(__('Please enter your full name.', 'dooroora')); ?>', 'error');
            nameInput.focus();
            return;
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value.trim())) {
            showFeedback('<?php echo esc_js(__('Please enter a valid email address.', 'dooroora')); ?>', 'error');
            emailInput.focus();
            return;
        }

        if (!messageInput.value.trim() || messageInput.value.trim().length < 10) {
            showFeedback('<?php echo esc_js(__('Please write a message (at least 10 characters).', 'dooroora')); ?>', 'error');
            messageInput.focus();
            return;
        }

        // Set loading state
        submitBtn.disabled = true;
        var originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <?php echo esc_js(__("Sending message...", "dooroora")); ?>';

        var formData = new FormData(form);

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (res) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;

            if (res && res.success) {
                showFeedback(res.data.message || '<?php echo esc_js(__("Thank you! Your message has been sent to our editorial team.", "dooroora")); ?>', 'success');
                form.reset();
            } else {
                var err = (res && res.data && res.data.message) ? res.data.message : '<?php echo esc_js(__("An unexpected error occurred. Please try again or email us directly.", "dooroora")); ?>';
                showFeedback(err, 'error');
            }
        })
        .catch(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            showFeedback('<?php echo esc_js(__("Network error occurred. Please try again or email contact@dooroora.com.", "dooroora")); ?>', 'error');
        });
    });

    function showFeedback(msg, type) {
        feedback.textContent = msg;
        feedback.className = 'contact-form-feedback ' + type;
        feedback.style.display = 'block';
        feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
</script>

<?php
get_footer();
