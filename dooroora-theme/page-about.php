<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 *
 * Dedicated About Page Template for DoorOora Blog
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main about-page-wrapper">
    <div class="container">
        <!-- Hero Section -->
        <header class="about-hero">
            <nav class="about-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'dooroora'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'dooroora'); ?></a>
                <span class="sep">&rsaquo;</span>
                <span class="current"><?php esc_html_e('About', 'dooroora'); ?></span>
            </nav>
            <div class="about-badge">
                <i class="fa-solid fa-door-open"></i>
                <span><?php esc_html_e('Independent DIY Publication & Hardware Lab', 'dooroora'); ?></span>
            </div>
            <h1 class="about-title"><?php esc_html_e('About', 'dooroora'); ?></h1>
            <p class="about-lead">
                <?php esc_html_e('DoorOora is an independent home improvement blog dedicated to practical door maintenance, lock hardware reviews, weatherproofing solutions, and step-by-step DIY installation guides. We help homeowners, woodworkers, and DIY enthusiasts troubleshoot, repair, and upgrade their doors with confidence.', 'dooroora'); ?>
            </p>
        </header>

        <!-- Our Story & Mission Grid -->
        <section class="about-story-grid" aria-label="<?php esc_attr_e('Our Mission and Story', 'dooroora'); ?>">
            <div class="about-story-content">
                <h2><?php esc_html_e('Because Doors Shouldn\'t Be a Mystery', 'dooroora'); ?></h2>
                <p>
                    <?php esc_html_e('Every home has dozens of doors—exterior entry doors, storm doors, interior passage doors, sliding patio doors, garage entries, and pocket doors. Yet when a latch refuses to catch, winter drafts whistle under the threshold, a hinge squeaks relentlessly, or a storm door closer slams shut, finding clear, actionable advice can be surprisingly difficult.', 'dooroora'); ?>
                </p>
                <p>
                    <?php esc_html_e('Generic home improvement websites often churn out vague, high-level summaries without real-world measurements or hardware specifications. Meanwhile, local contractor directories exist primarily to capture your phone number for an expensive service visit—often for an issue you could easily fix yourself with a five-dollar shim and a screwdriver.', 'dooroora'); ?>
                </p>
                <p>
                    <?php esc_html_e('DoorOora was founded to bridge that gap. We believe in empowering homeowners and DIYers with transparent diagnostic walkthroughs, cross-sectional diagrams, and honest hardware testing grounded in real workshop experience.', 'dooroora'); ?>
                </p>
                <div class="about-story-highlight">
                    "<?php esc_html_e('Our goal is simple: provide clear, practical, and technically trustworthy information so you can solve problems, boost home security, and tackle DIY door projects with complete confidence.', 'dooroora'); ?>"
                </div>
            </div>

            <!-- Stats & Milestones Card -->
            <div class="about-stats-card">
                <div class="about-stats-card-header">
                    <h3><?php esc_html_e('DoorOora at a Glance', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('A dedicated home improvement resource', 'dooroora'); ?></p>
                </div>
                <div class="about-stats-grid">
                    <div class="about-stat-item">
                        <span class="about-stat-number">50+</span>
                        <span class="about-stat-label"><?php esc_html_e('In-Depth DIY Guides & Tutorials', 'dooroora'); ?></span>
                    </div>
                    <div class="about-stat-item">
                        <span class="about-stat-number">8</span>
                        <span class="about-stat-label"><?php esc_html_e('Core Door Hardware Categories', 'dooroora'); ?></span>
                    </div>
                    <div class="about-stat-item">
                        <span class="about-stat-number">100%</span>
                        <span class="about-stat-label"><?php esc_html_e('In-House Research & Testing', 'dooroora'); ?></span>
                    </div>
                    <div class="about-stat-item">
                        <span class="about-stat-number">0</span>
                        <span class="about-stat-label"><?php esc_html_e('Paid Positive Ratings Accepted', 'dooroora'); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Topic Pillars Section -->
        <section class="about-pillars-section" aria-label="<?php esc_attr_e('What We Cover', 'dooroora'); ?>">
            <div class="about-section-header">
                <h2><?php esc_html_e('What We Cover on the Blog', 'dooroora'); ?></h2>
                <p><?php esc_html_e('From basic weatherstripping to heavy-duty security deadbolts, our tutorials focus on practical, cost-effective DIY solutions across six core specialties.', 'dooroora'); ?></p>
            </div>

            <div class="about-pillars-grid">
                <!-- Pillar 1: Locks & Security -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3><?php esc_html_e('Locks & Entry Security', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Deadbolt mechanics, ANSI/BHMA grading standards, cylinder rekeying, reinforced strike plates, and physical kick-in protection.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/locks-security/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Lock Guides', 'dooroora'); ?> &rarr;
                    </a>
                </div>

                <!-- Pillar 2: Sealing & Weatherproofing -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-wind"></i>
                    </div>
                    <h3><?php esc_html_e('Sealing & Weatherproofing', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Threshold gap elimination, silicone vs rubber sweeps, kerf weatherstripping, draft exclusion, and acoustic soundproofing.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/sealing-weatherproofing/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Weatherproofing', 'dooroora'); ?> &rarr;
                    </a>
                </div>

                <!-- Pillar 3: Door Types & Materials -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <h3><?php esc_html_e('Door Types & Materials', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Comparing solid wood, fiberglass, steel slabs, hollow-core interior doors, pocket doors, barn doors, and storm doors.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/door-types-materials/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Door Types', 'dooroora'); ?> &rarr;
                    </a>
                </div>

                <!-- Pillar 4: Installation & Framing -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-hammer"></i>
                    </div>
                    <h3><?php esc_html_e('Installation & Framing', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Hinge shimming, plumb jamb alignment, rough opening framing, threshold concrete bonding, and settling adjustments.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/installation-framing/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Installation', 'dooroora'); ?> &rarr;
                    </a>
                </div>

                <!-- Pillar 5: Garage Doors & Hardware -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <h3><?php esc_html_e('Garage Doors & Openers', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Torsion spring safety, track alignment, rail bowing, bottom weather seals, and opener sensor calibration.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/garage-doors/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Garage Guides', 'dooroora'); ?> &rarr;
                    </a>
                </div>

                <!-- Pillar 6: Smart Doorbells & Sensors -->
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <h3><?php esc_html_e('Smart Doorbells & Sensors', 'dooroora'); ?></h3>
                    <p><?php esc_html_e('Video doorbell installation, AC vs DC chime compatibility, 16V/24V transformers, and smart contact sensor setups.', 'dooroora'); ?></p>
                    <a href="<?php echo esc_url(home_url('/category/smart-doorbells-sensors/')); ?>" class="about-pillar-link">
                        <?php esc_html_e('Explore Smart Tech', 'dooroora'); ?> &rarr;
                    </a>
                </div>
            </div>
        </section>

        <!-- Our Editorial & Testing Standards -->
        <section class="about-standards-section" aria-label="<?php esc_attr_e('Editorial Standards', 'dooroora'); ?>">
            <div class="about-section-header">
                <h2><?php esc_html_e('Our Editorial & Testing Standards', 'dooroora'); ?></h2>
                <p><?php esc_html_e('We believe trust is earned through rigor, transparency, and technical precision. Here is how we produce and verify every guide.', 'dooroora'); ?></p>
            </div>

            <div class="about-standards-grid">
                <div class="about-standard-card">
                    <div class="about-standard-num">1</div>
                    <div class="about-standard-content">
                        <h3><?php esc_html_e('Hands-On Workshop Testing', 'dooroora'); ?></h3>
                        <p><?php esc_html_e('We don\'t just rewrite manufacturer marketing claims. When we test a door sweep, closer, or deadbolt, we install it on real door slabs to verify clearance, screw holding strength, and real-world durability.', 'dooroora'); ?></p>
                    </div>
                </div>

                <div class="about-standard-card">
                    <div class="about-standard-num">2</div>
                    <div class="about-standard-content">
                        <h3><?php esc_html_e('Codes & Hardware Standards', 'dooroora'); ?></h3>
                        <p><?php esc_html_e('Our tutorials reference recognized benchmarks including the International Residential Code (IRC), International Building Code (IBC), ADA accessibility guidelines, and ANSI/BHMA hardware performance grades.', 'dooroora'); ?></p>
                    </div>
                </div>

                <div class="about-standard-card">
                    <div class="about-standard-num">3</div>
                    <div class="about-standard-content">
                        <h3><?php esc_html_e('Strict Editorial Independence', 'dooroora'); ?></h3>
                        <p><?php esc_html_e('We never accept compensation in exchange for positive reviews or altered recommendations. If a lock has a design flaw or a weatherstrip adhesive fails in cold weather, our readers will hear about it honestly.', 'dooroora'); ?></p>
                    </div>
                </div>

                <div class="about-standard-card">
                    <div class="about-standard-num">4</div>
                    <div class="about-standard-content">
                        <h3><?php esc_html_e('Continuous Maintenance & Updates', 'dooroora'); ?></h3>
                        <p><?php esc_html_e('Building codes, tool models, and smart home standards evolve. Our editorial staff regularly revisits published guides to update measurements, refresh product recommendations, and keep citations accurate.', 'dooroora'); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Free Tools Banner -->
        <section class="about-tools-banner" aria-label="<?php esc_attr_e('Free Tools', 'dooroora'); ?>">
            <div class="about-tools-text">
                <h3><i class="fa-solid fa-calculator" style="margin-right: 8px; color: var(--color-primary);"></i> <?php esc_html_e('Interactive DIY Tools & Calculators', 'dooroora'); ?></h3>
                <p><?php esc_html_e('Need to calculate the exact threshold gap height or determine how many hinges your solid core door needs? Try our free online calculators.', 'dooroora'); ?></p>
            </div>
            <a href="<?php echo esc_url(home_url('/tools')); ?>" class="about-tools-btn">
                <span><?php esc_html_e('Explore Free Tools', 'dooroora'); ?></span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </section>

        <!-- Call to Action Section -->
        <section class="about-cta-section" aria-label="<?php esc_attr_e('Get in Touch', 'dooroora'); ?>">
            <div class="about-cta-content">
                <h2><?php esc_html_e('Have a Question or an Idea for a Guide?', 'dooroora'); ?></h2>
                <p>
                    <?php esc_html_e('We love hearing from our readers! If you are facing a stubborn door issue we haven\'t covered yet, or have feedback on a published tutorial, our editorial team is just a message away.', 'dooroora'); ?>
                </p>
                <div class="about-cta-buttons">
                    <a href="<?php echo esc_url(home_url('/contact-us')); ?>" class="about-btn-primary">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?php esc_html_e('Contact Our Editorial Team', 'dooroora'); ?></span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="about-btn-secondary">
                        <i class="fa-solid fa-newspaper"></i>
                        <span><?php esc_html_e('Browse All Blog Guides', 'dooroora'); ?></span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
