<?php
/**
 * Front page template
 *
 * Sections:
 * 1. Header
 * 2. Hero
 * 3. Browse by Topics
 * 4. Who We Are
 * 5. Browse Guides by Category
 * 6. Free Tools
 * 7. Latest Guides
 * 8. Newsletter CTA
 * 9. Footer
 *
 * @package DoorOora
 * @since 1.0.0
 */

get_header();

get_template_part('template-parts/sections/hero');
get_template_part('template-parts/sections/browse-topics');
get_template_part('template-parts/sections/who-we-are');
get_template_part('template-parts/sections/browse-guides');
get_template_part('template-parts/sections/free-tools');
get_template_part('template-parts/sections/latest-guides');
get_template_part('template-parts/sections/newsletter');

get_footer();
