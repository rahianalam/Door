/**
 * DoorOora Theme JavaScript
 *
 * @package DoorOora
 * @since 1.0.0
 */

(function () {
    'use strict';

    var body = document.body;
    var siteHeader = document.querySelector('.site-header');
    var ajaxUrl = (typeof dooroora_ajax !== 'undefined') ? dooroora_ajax.ajax_url : '/wp-admin/admin-ajax.php';

    /* ----------------------------------------------------------
        1. Sticky header shrink on scroll
        ---------------------------------------------------------- */
    if (siteHeader) {
        var headerThreshold = window.innerWidth <= 768 ? 10 : 20;
        window.addEventListener('scroll', function () {
            if (window.scrollY > headerThreshold) {
                siteHeader.classList.add('header-scrolled');
            } else {
                siteHeader.classList.remove('header-scrolled');
            }
        }, { passive: true });
    }

    /* ----------------------------------------------------------
       2. Off-canvas mobile menu
       ---------------------------------------------------------- */
    var mobileToggles = document.querySelectorAll('.mobile-menu-toggle');
    var offCanvas = document.querySelector('.off-canvas');
    var offCanvasOverlay = document.querySelector('.off-canvas-overlay');
    var offCanvasClose = document.querySelector('.off-canvas-close');

    // Move off-canvas and overlay outside of header so backdrop-filter does not clip fixed positioning
    if (offCanvas && siteHeader && siteHeader.contains(offCanvas)) {
        document.body.appendChild(offCanvas);
    }
    if (offCanvasOverlay && siteHeader && siteHeader.contains(offCanvasOverlay)) {
        document.body.appendChild(offCanvasOverlay);
    }

    function openOffCanvas() {
        if (!offCanvas || !offCanvasOverlay) return;
        offCanvas.classList.add('open');
        offCanvasOverlay.classList.add('active');
        body.style.overflow = 'hidden';
        mobileToggles.forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'true');
        });
    }

    function closeOffCanvas() {
        if (!offCanvas || !offCanvasOverlay) return;
        offCanvas.classList.remove('open');
        offCanvasOverlay.classList.remove('active');
        body.style.overflow = '';
        mobileToggles.forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    mobileToggles.forEach(function (btn) {
        btn.addEventListener('click', function (e) { e.preventDefault(); openOffCanvas(); });
    });
    if (offCanvasClose) offCanvasClose.addEventListener('click', closeOffCanvas);
    if (offCanvasOverlay) offCanvasOverlay.addEventListener('click', closeOffCanvas);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeOffCanvas(); closeSearch(); }
    });

    /* ----------------------------------------------------------
       3. Search overlay with live AJAX search
       ---------------------------------------------------------- */
    var searchToggle = document.querySelector('.search-toggle');
    var searchOverlay = document.querySelector('.search-overlay');
    var searchClose = document.querySelector('.search-overlay-close');
    var searchInput = document.querySelector('.search-overlay-input');
    var liveResults = document.getElementById('live-search-results');
    var liveList = liveResults ? liveResults.querySelector('.live-search-list') : null;
    var searchTimer = null;

    function openSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.add('active');
        body.style.overflow = 'hidden';
        if (searchInput) setTimeout(function () { searchInput.focus(); }, 100);
    }

    function closeSearch() {
        if (!searchOverlay) return;
        searchOverlay.classList.remove('active');
        body.style.overflow = '';
        if (liveResults) liveResults.classList.remove('has-results');
        if (liveList) liveList.innerHTML = '';
    }

    if (searchToggle) searchToggle.addEventListener('click', function (e) { e.preventDefault(); openSearch(); });
    if (searchClose) searchClose.addEventListener('click', closeSearch);
    if (searchOverlay) searchOverlay.addEventListener('click', function (e) {
        if (e.target === searchOverlay) closeSearch();
    });

    if (searchInput && liveResults) {
        searchInput.addEventListener('input', function () {
            var query = this.value.trim();
            if (searchTimer) clearTimeout(searchTimer);
            if (query.length < 2) {
                liveResults.classList.remove('has-results');
                if (liveList) liveList.innerHTML = '';
                return;
            }
            searchTimer = setTimeout(function () {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxUrl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success && liveList) {
                                liveList.innerHTML = response.data.html;
                                liveResults.classList.add('has-results');
                            }
                        } catch (err) {}
                    }
                };
                xhr.send('action=dooroora_live_search&query=' + encodeURIComponent(query));
            }, 300);
        });
    }

    /* ----------------------------------------------------------
       4. Mega menu: close on outside click
       ---------------------------------------------------------- */
    document.addEventListener('click', function (e) {
        var megaMenus = document.querySelectorAll('.mega-menu');
        megaMenus.forEach(function (mm) {
            var parent = mm.closest('li');
            if (parent && !parent.contains(e.target)) {
                // keep visible on hover, no action needed for click
            }
        });
    });

    /* ----------------------------------------------------------
       5. AJAX category tabs (front page browse guides)
       ---------------------------------------------------------- */
    var tabsContainer = document.getElementById('guides-tabs');
    var guidesGrid = document.getElementById('guides-grid');
    var guidesLoading = document.getElementById('guides-loading');

    if (tabsContainer && guidesGrid) {
        tabsContainer.addEventListener('click', function (e) {
            var tab = e.target.closest('.guides-tab');
            if (!tab) return;
            var category = tab.getAttribute('data-category');
            tabsContainer.querySelectorAll('.guides-tab').forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            if (guidesLoading) guidesLoading.style.display = 'block';
            guidesGrid.style.opacity = '0.4';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (guidesLoading) guidesLoading.style.display = 'none';
                guidesGrid.style.opacity = '1';
                if (xhr.status >= 200 && xhr.status < 400) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            guidesGrid.innerHTML = response.data.html;
                        } else if (response.data && response.data.html) {
                            guidesGrid.innerHTML = response.data.html;
                        }
                    } catch (err) {}
                }
            };
            xhr.send('action=dooroora_get_category_guides&category=' + encodeURIComponent(category));
        });
    }

    /* ----------------------------------------------------------
       6. Blog archive AJAX filtering
       ---------------------------------------------------------- */
    var filterTags = document.querySelectorAll('.blog-filter-tag');
    var sortSelect = document.getElementById('blog-sort-select');
    var blogGrid = document.getElementById('blog-grid');
    var blogLoading = document.getElementById('blog-loading');
    var blogPagination = document.querySelector('.blog-pagination');

    if (filterTags.length > 0 && blogGrid) {
        function loadBlogPosts() {
            var activeTag = document.querySelector('.blog-filter-tag.active');
            var category = activeTag ? activeTag.getAttribute('data-filter') : 'all';
            var sort = sortSelect ? sortSelect.value : 'latest';
            if (blogLoading) blogLoading.style.display = 'block';
            blogGrid.style.opacity = '0.4';
            if (blogPagination) blogPagination.style.display = 'none';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (blogLoading) blogLoading.style.display = 'none';
                blogGrid.style.opacity = '1';
                if (xhr.status >= 200 && xhr.status < 400) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            blogGrid.innerHTML = response.data.html;
                        } else if (response.data && response.data.html) {
                            blogGrid.innerHTML = response.data.html;
                        }
                    } catch (err) {}
                }
                if (category === 'all' && blogPagination) blogPagination.style.display = '';
            };
            xhr.send('action=dooroora_filter_blog&category=' + encodeURIComponent(category) + '&sort=' + encodeURIComponent(sort));
        }

        filterTags.forEach(function (tag) {
            tag.addEventListener('click', function () {
                filterTags.forEach(function (t) { t.classList.remove('active'); });
                tag.classList.add('active');
                loadBlogPosts();
            });
        });
        if (sortSelect) sortSelect.addEventListener('change', loadBlogPosts);
    }

    /* ----------------------------------------------------------
       7. FAQ accordion
       ---------------------------------------------------------- */
    document.querySelectorAll('.faq-question').forEach(function (q) {
        q.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            if (item) item.classList.toggle('active');
            var expanded = item.classList.contains('active');
            this.setAttribute('aria-expanded', expanded);
        });
    });

    /* ----------------------------------------------------------
       8. Table of Contents (H2 only, collapsed default)
       ---------------------------------------------------------- */
    var tocSection = document.getElementById('single-toc');
    var tocList = document.getElementById('toc-list');
    var contentArea = document.getElementById('single-content');

    if (tocSection && tocList && contentArea) {
        var headings = contentArea.querySelectorAll('h2');
        if (headings.length > 0) {
            headings.forEach(function (heading, index) {
                var id = 'toc-heading-' + index;
                heading.setAttribute('id', id);
                var li = document.createElement('li');
                var a = document.createElement('a');
                a.href = '#' + id;
                a.textContent = heading.textContent;
                li.appendChild(a);
                tocList.appendChild(li);
            });
        } else {
            tocSection.style.display = 'none';
        }

        // Toggle collapse
        var tocToggle = tocSection.querySelector('.toc-toggle');
        if (tocToggle) {
            tocToggle.addEventListener('click', function () {
                var isCollapsed = tocSection.classList.contains('collapsed');
                tocSection.classList.toggle('collapsed');
                this.setAttribute('aria-expanded', isCollapsed);
            });
        }

        // Scroll-spy: highlight active TOC entry via IntersectionObserver
        if (headings.length > 0 && 'IntersectionObserver' in window) {
            var tocLinks = tocList.querySelectorAll('a');
            var headingElements = [];
            headings.forEach(function (h) { headingElements.push(h); });
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        tocLinks.forEach(function (link) {
                            link.classList.remove('toc-active');
                            if (link.getAttribute('href') === '#' + entry.target.getAttribute('id')) {
                                link.classList.add('toc-active');
                            }
                        });
                    }
                });
            }, { rootMargin: '-80px 0px -60% 0px', threshold: 0 });
            headingElements.forEach(function (h) { observer.observe(h); });
        }
    }

    /* ----------------------------------------------------------
       9. Smooth scroll for all anchor links (with header offset)
       ---------------------------------------------------------- */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#' || !targetId) return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                var headerOffset = siteHeader ? siteHeader.offsetHeight + 20 : 100;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    /* ----------------------------------------------------------
        10. Newsletter form submission
        ---------------------------------------------------------- */
    var newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var input = this.querySelector('.newsletter-input');
            var submitBtn = this.querySelector('.newsletter-submit');
            var email = input ? input.value.trim() : '';
            if (!email) return;

            var originalText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) { submitBtn.textContent = '...'; submitBtn.disabled = true; }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (submitBtn) { submitBtn.textContent = originalText; submitBtn.disabled = false; }
                try {
                    var response = JSON.parse(xhr.responseText);
                    var msg = response.data && response.data.message ? response.data.message : '';

                    var existingMsg = newsletterForm.querySelector('.newsletter-message');
                    if (existingMsg) existingMsg.remove();

                    var msgEl = document.createElement('p');
                    msgEl.className = 'newsletter-message';
                    msgEl.textContent = msg;
                    msgEl.style.cssText = 'margin-top:12px;font-size:0.9375rem;' +
                        (response.success ? 'color:#4ade80;' : 'color:#f87171;');
                    newsletterForm.appendChild(msgEl);

                    if (response.success && input) input.value = '';
                } catch (err) {}
            };
            xhr.onerror = function () {
                if (submitBtn) { submitBtn.textContent = originalText; submitBtn.disabled = false; }
            };
            xhr.send('action=dooroora_newsletter_subscribe&email=' + encodeURIComponent(email) + '&nonce=' + encodeURIComponent(dooroora_ajax.nonce));
        });
    }

    /* ----------------------------------------------------------
        11. Scroll to Top
        ---------------------------------------------------------- */
    var scrollBtn = document.getElementById('scroll-to-top');
    if (scrollBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        }, { passive: true });
        scrollBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    })();



/* ==========================================================
   DoorOora Interactive Tools Logic
   ========================================================== */
document.addEventListener('DOMContentLoaded', function() {
            // Tab switcher
            const tabs = document.querySelectorAll('.calc-tab-btn');
            tabs.forEach(btn => {
                btn.addEventListener('click', function() {
                    const parent = this.closest('.dooroora-calculator-card');
                    parent.querySelectorAll('.calc-tab-btn').forEach(b => b.classList.remove('active'));
                    parent.querySelectorAll('.calc-tab-pane').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    const target = parent.querySelector('#' + this.getAttribute('data-tab'));
                    if (target) target.classList.add('active');
                    if (window.runDoorCalc) window.runDoorCalc();
                });
            });

            // 1. Door Size Calculator Logic
            const doorCalcApp = document.getElementById('door-size-calc-app');
            if (doorCalcApp) {
                window.runDoorCalc = function() {
                    const activeTab = doorCalcApp.querySelector('.calc-tab-btn.active').getAttribute('data-tab');
                    const resRO = document.getElementById('res-ro-dim');
                    const resSlab = document.getElementById('res-slab-dim');
                    const resJamb = document.getElementById('res-jamb-depth');
                    const resBanner = document.getElementById('res-summary-banner');

                    if (activeTab === 'tab-slab-to-ro') {
                        const type = document.getElementById('ds-door-type').value;
                        const width = parseFloat(document.getElementById('ds-slab-width').value);
                        const height = parseFloat(document.getElementById('ds-slab-height').value);
                        const framing = document.getElementById('ds-wall-framing').value;

                        let roWidth = width + 2;
                        let roHeight = height + 2.5;

                        if (type === 'pocket-door') {
                            roWidth = (width * 2) + 2;
                            roHeight = height + 4.5;
                        } else if (type === 'exterior-entry') {
                            roHeight = height + 3.0; // Sill allowance
                        } else if (type === 'bifold-door') {
                            roWidth = width;
                            roHeight = height + 2.0;
                        } else if (type === 'barn-door') {
                            roWidth = width - 2; // Opening is smaller than slab
                            roHeight = height - 1.5;
                        }

                        let jamb = '4-9/16"';
                        if (framing === '2x6') jamb = '6-9/16"';
                        else if (framing === 'plaster') jamb = '5-1/4"';

                        resRO.textContent = roWidth + '" W × ' + roHeight + '" H';
                        resSlab.textContent = width + '" W × ' + height + '" H';
                        resJamb.textContent = jamb;

                        let note = '<strong>Pro Carpenter Tip:</strong> For an 80" pre-hung door, frame your rough opening to <strong>' + roWidth + '" wide by ' + roHeight + '" tall</strong> with a ' + jamb + ' jamb. Always level the subfloor before shimming.';
                        if (type === 'pocket-door') {
                            note = '<strong>Pocket Door Notice:</strong> Rough opening requires <strong>' + roWidth + '" wide by ' + roHeight + '" tall</strong> to house the track and pocket frame studs.';
                        }
                        resBanner.innerHTML = note;
                    } else {
                        const mWidth = parseFloat(document.getElementById('ds-measured-width').value) || 34;
                        const mHeight = parseFloat(document.getElementById('ds-measured-height').value) || 82.5;
                        const floor = document.getElementById('ds-flooring-type').value;

                        let floorOffset = 2.5;
                        if (floor === 'carpet') floorOffset = 3.0;
                        else if (floor === 'subfloor') floorOffset = 3.25;

                        const calcWidth = Math.round(mWidth - 2);
                        const calcHeight = Math.round(mHeight - floorOffset);

                        resRO.textContent = mWidth + '" W × ' + mHeight + '" H (Measured)';
                        resSlab.textContent = calcWidth + '" W × ' + calcHeight + '" H (Recommended)';
                        resBanner.innerHTML = '<strong>Sizing Assessment:</strong> Based on your measured opening of ' + mWidth + '" × ' + mHeight + '", select a standard <strong>' + calcWidth + '" × 80"</strong> door slab.';
                    }
                };

                doorCalcApp.querySelectorAll('select, input').forEach(el => {
                    el.addEventListener('change', window.runDoorCalc);
                    el.addEventListener('input', window.runDoorCalc);
                });
                window.runDoorCalc();
            }

            // 2. Lock Security Matcher Logic
            const lockApp = document.getElementById('lock-matcher-app');
            if (lockApp) {
                window.runLockMatcher = function() {
                    const loc = document.getElementById('lsm-location').value;
                    const mat = document.getElementById('lsm-material').value;
                    const thick = parseFloat(document.getElementById('lsm-thickness').value);
                    const bset = document.getElementById('lsm-backset').value;
                    const priority = document.getElementById('lsm-priority').value;

                    const resGrade = document.getElementById('lsm-res-grade');
                    const resType = document.getElementById('lsm-res-type');
                    const resStrike = document.getElementById('lsm-res-strike');
                    const resScore = document.getElementById('lsm-res-score');
                    const resBanner = document.getElementById('lsm-summary-banner');

                    let grade = 'ANSI Grade 1';
                    let type = 'Single-Cylinder Heavy Deadbolt';
                    let strike = 'Reinforced Box Strike with 3" Screws';
                    let score = '96 / 100';
                    let tip = 'Install an <strong>ANSI Grade 1 Deadbolt</strong> (e.g. Schlage B60N) with four 3-inch hardened steel screws into wall framing.';

                    if (priority === 'smart-connected') {
                        grade = 'ANSI Grade 1 or 2 Smart Deadbolt';
                        type = 'Smart Lock with Keypad & Keyway Backup';
                        score = '94 / 100';
                        tip = 'Select an <strong>ANSI Grade 1 certified smart lock</strong> (like Schlage Encode Plus) to combine Apple HomeKey/keypad access with commercial-grade physical brute-force defense.';
                    } else if (loc === 'interior-office' || priority === 'child-safety') {
                        grade = 'ANSI Grade 2 or 3';
                        type = 'Privacy / Storeroom Keyed Lever';
                        strike = 'Standard Full-Lip Strike Plate';
                        score = '84 / 100';
                        tip = 'For interior access control, a Grade 2 keyed lever or keypad handle provides reliable access control without requiring a separate deadbolt hole.';
                    } else if (mat === 'glass-panel') {
                        grade = 'ANSI Grade 1 High-Security';
                        type = 'Double-Cylinder Deadbolt or Security Film';
                        score = '92 / 100';
                        tip = 'Because glass within 40" of the lock can be broken to reach the interior latch, apply 8-mil clear shatter-resistant security window film over the glass panel.';
                    }

                    resGrade.textContent = grade;
                    resType.textContent = type;
                    resStrike.textContent = strike;
                    resScore.textContent = score;
                    resBanner.innerHTML = '<strong>Expert Locksmith Recommendation:</strong> ' + tip;
                };

                lockApp.querySelectorAll('select').forEach(el => {
                    el.addEventListener('change', window.runLockMatcher);
                });
                window.runLockMatcher();
            }

            // 3. Garage Wire Size Calculator Logic
            const wireApp = document.getElementById('garage-wire-calc-app');
            if (wireApp) {
                window.runWireCalc = function() {
                    const eq = document.getElementById('gwc-equipment').value;
                    const volt = parseFloat(document.getElementById('gwc-voltage').value);
                    const dist = parseFloat(document.getElementById('gwc-distance').value) || 50;
                    const mat = document.getElementById('gwc-material').value;
                    const dropLimit = parseFloat(document.getElementById('gwc-drop-limit').value);

                    let baseAmps = 6.0;
                    let breaker = '20 Amp Breaker';
                    let baseGauge = 12; // 12 AWG
                    let cable = '12/2 with Ground NM-B';

                    if (eq === 'opener-standard') {
                        baseAmps = 6.0;
                        breaker = '20 Amp Breaker';
                        baseGauge = 12;
                        cable = '12/2 NM-B or THHN in Conduit';
                    } else if (eq === 'opener-heavy' || eq === 'garage-receptacles') {
                        baseAmps = 16.0;
                        breaker = '20 Amp Breaker';
                        baseGauge = 12;
                        cable = '12/2 with Ground NM-B';
                    } else if (eq === 'ev-charger-32') {
                        baseAmps = 32.0;
                        breaker = '40 Amp 240V Double-Pole Breaker';
                        baseGauge = 8;
                        cable = '8/3 NM-B or 8 AWG THHN in Conduit';
                    } else if (eq === 'ev-charger-48') {
                        baseAmps = 48.0;
                        breaker = '60 Amp 240V Double-Pole Breaker';
                        baseGauge = 6;
                        cable = '6 AWG THHN Copper in 3/4" Conduit (or 4 AWG NM-B)';
                    } else if (eq === 'garage-heater') {
                        baseAmps = 21.0;
                        breaker = '30 Amp 240V Double-Pole Breaker';
                        baseGauge = 10;
                        cable = '10/2 with Ground NM-B';
                    } else if (eq === 'detached-subpanel-60') {
                        baseAmps = 48.0;
                        breaker = '60 Amp 240V Feeder Breaker';
                        baseGauge = mat === 'aluminum' ? 4 : 6;
                        cable = (mat === 'aluminum' ? '#4 Aluminum MHF' : '#6 Copper THHN') + ' in 1" Conduit';
                    } else if (eq === 'detached-subpanel-100') {
                        baseAmps = 80.0;
                        breaker = '100 Amp 240V Main Panel Feeder';
                        baseGauge = mat === 'aluminum' ? 1 : 2;
                        cable = (mat === 'aluminum' ? '#1/0 Aluminum' : '#2 Copper THHN') + ' in 1.5" Conduit';
                    }

                    // Circular mils table for copper:
                    const cmTable = {
                        14: 4110, 12: 6530, 10: 10380, 8: 16510, 6: 26240, 4: 41740, 3: 52620, 2: 66360, 1: 83690
                    };
                    const k = mat === 'aluminum' ? 21.2 : 12.9;

                    let finalGauge = baseGauge;
                    let cm = cmTable[finalGauge] || 6530;
                    let vd = (2 * k * baseAmps * dist) / cm;
                    let vDropPct = (vd / volt) * 100;

                    // Upsize if voltage drop exceeds limit
                    const gauges = [14, 12, 10, 8, 6, 4, 3, 2, 1];
                    let gIndex = gauges.indexOf(finalGauge);
                    while (vDropPct > dropLimit && gIndex < gauges.length - 1) {
                        gIndex++;
                        finalGauge = gauges[gIndex];
                        cm = cmTable[finalGauge] || 66360;
                        vd = (2 * k * baseAmps * dist) / cm;
                        vDropPct = (vd / volt) * 100;
                    }

                    const resGauge = document.getElementById('gwc-res-gauge');
                    const resBreaker = document.getElementById('gwc-res-breaker');
                    const resDrop = document.getElementById('gwc-res-drop');
                    const resDelivered = document.getElementById('gwc-res-delivered');
                    const resCable = document.getElementById('gwc-res-cable');

                    resGauge.textContent = finalGauge + ' AWG ' + (mat === 'aluminum' ? 'Aluminum' : 'Copper');
                    resBreaker.textContent = breaker;
                    resDrop.textContent = vDropPct.toFixed(1) + '% (' + vd.toFixed(1) + 'V Drop)';
                    resDelivered.textContent = 'Delivering ' + (volt - vd).toFixed(1) + 'V at garage equipment (Target: <' + dropLimit + '%)';
                    resCable.textContent = cable;
                };

                wireApp.querySelectorAll('select, input').forEach(el => {
                    el.addEventListener('change', window.runWireCalc);
                    el.addEventListener('input', window.runWireCalc);
                });
                window.runWireCalc();
            }
        });



/* ==========================================================
   DoorOora Interactive Tools: Modern Presets & DOM Cleaner
   ========================================================== */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Purge all blog clutter from tool pages in the DOM
    if (document.body.classList.contains('single-tool')) {
        const clutterSelectors = [
            '.single-author-info',
            '.single-dates',
            '.single-trust-block',
            '.social-sharing',
            '.single-toc',
            '#single-sidebar',
            '.single-sidebar',
            '.single-tags',
            '.single-author-box',
            '.author-box',
            '.single-related-posts',
            '#comments',
            '.single-faqs'
        ];
        clutterSelectors.forEach(function(selector) {
            document.querySelectorAll(selector).forEach(function(el) {
                el.remove();
            });
        });

        // Insert Modern Subtitle & Meta under title if missing
        const singleHeader = document.querySelector('.single-header');
        if (singleHeader && !singleHeader.querySelector('.tool-hero-badge-row')) {
            const badgeRow = document.createElement('div');
            badgeRow.className = 'tool-hero-badge-row';
            badgeRow.innerHTML = `
                <span class="tool-app-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Free Interactive Tool</span>
                <span class="tool-app-meta-pill"><i class="fa-solid fa-bolt"></i> Instant Calculations</span>
                <span class="tool-app-meta-pill"><i class="fa-solid fa-shield-halved"></i> Engineering Verified</span>
                <span class="tool-app-meta-pill"><i class="fa-solid fa-mobile-screen-button"></i> Mobile Ready</span>
            `;
            singleHeader.appendChild(badgeRow);
        }
    }

    // 2. Insert Quick Presets for Door Size Calculator
    const doorApp = document.getElementById('door-size-calc-app');
    if (doorApp && !doorApp.querySelector('.calc-presets-bar')) {
        const presetsBar = document.createElement('div');
        presetsBar.className = 'calc-presets-bar';
        presetsBar.innerHTML = `
            <span class="presets-label"><i class="fa-solid fa-sliders"></i> Quick Sizes:</span>
            <button type="button" class="preset-chip active" data-ro-type="interior-prehung" data-width="32" data-height="80">32" × 80" Bedroom (Standard)</button>
            <button type="button" class="preset-chip" data-ro-type="exterior-entry" data-width="36" data-height="80">36" × 80" Front Entry</button>
            <button type="button" class="preset-chip" data-ro-type="interior-prehung" data-width="30" data-height="80">30" × 80" Bath / Office</button>
            <button type="button" class="preset-chip" data-ro-type="interior-prehung" data-width="28" data-height="80">28" × 80" Closet</button>
            <button type="button" class="preset-chip" data-ro-type="interior-prehung" data-width="36" data-height="80">36" × 80" ADA Accessible</button>
            <button type="button" class="preset-chip" data-ro-type="pocket-door" data-width="32" data-height="80">32" Pocket Door</button>
        `;

        const tabs = doorApp.querySelector('.calc-tabs');
        if (tabs) {
            tabs.insertAdjacentElement('afterend', presetsBar);
        }

        presetsBar.querySelectorAll('.preset-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                presetsBar.querySelectorAll('.preset-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');

                // Switch to Tab 1 if not active
                const tab1Btn = doorApp.querySelector('[data-tab="tab-slab-to-ro"]');
                if (tab1Btn && !tab1Btn.classList.contains('active')) {
                    tab1Btn.click();
                }

                const doorType = document.getElementById('ds-door-type');
                const slabWidth = document.getElementById('ds-slab-width');
                const slabHeight = document.getElementById('ds-slab-height');

                if (doorType) doorType.value = this.getAttribute('data-ro-type');
                if (slabWidth) slabWidth.value = this.getAttribute('data-width');
                if (slabHeight) slabHeight.value = this.getAttribute('data-height');

                if (window.runDoorCalc) window.runDoorCalc();
            });
        });
    }

    // 3. Insert Quick Presets for Lock Security Matcher
    const lockApp = document.getElementById('lock-matcher-app');
    if (lockApp && !lockApp.querySelector('.calc-presets-bar')) {
        const presetsBar = document.createElement('div');
        presetsBar.className = 'calc-presets-bar';
        presetsBar.innerHTML = `
            <span class="presets-label"><i class="fa-solid fa-shield"></i> Scenarios:</span>
            <button type="button" class="preset-chip active" data-loc="front-entry" data-mat="steel-clad" data-thick="1.75" data-pri="max-security">Front Entry Max Security</button>
            <button type="button" class="preset-chip" data-loc="rear-patio" data-mat="fiberglass" data-thick="1.75" data-pri="max-security">Back / Patio Entry</button>
            <button type="button" class="preset-chip" data-loc="rental-airbnb" data-mat="steel-clad" data-thick="1.75" data-pri="keyless-keypad">Rental / Airbnb Keyless</button>
            <button type="button" class="preset-chip" data-loc="front-entry" data-mat="solid-wood" data-thick="1.75" data-pri="smart-connected">Smart Home Connected</button>
            <button type="button" class="preset-chip" data-loc="commercial-suite" data-mat="steel-clad" data-thick="1.75" data-pri="max-security">Commercial Suite</button>
        `;

        const header = lockApp.querySelector('.calc-header');
        if (header) {
            header.insertAdjacentElement('afterend', presetsBar);
        }

        presetsBar.querySelectorAll('.preset-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                presetsBar.querySelectorAll('.preset-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');

                const loc = document.getElementById('lsm-location');
                const mat = document.getElementById('lsm-material');
                const thick = document.getElementById('lsm-thickness');
                const pri = document.getElementById('lsm-priority');

                if (loc) loc.value = this.getAttribute('data-loc');
                if (mat) mat.value = this.getAttribute('data-mat');
                if (thick) thick.value = this.getAttribute('data-thick');
                if (pri) pri.value = this.getAttribute('data-pri');

                if (window.runLockMatcher) window.runLockMatcher();
            });
        });
    }

    // 4. Insert Quick Presets for Garage Wire Size Guide
    const wireApp = document.getElementById('wire-calc-app');
    if (wireApp && !wireApp.querySelector('.calc-presets-bar')) {
        const presetsBar = document.createElement('div');
        presetsBar.className = 'calc-presets-bar';
        presetsBar.innerHTML = `
            <span class="presets-label"><i class="fa-solid fa-bolt"></i> Presets:</span>
            <button type="button" class="preset-chip active" data-eq="opener-half-hp" data-dist="50" data-mat="copper">1/2 HP Opener (120V 50ft)</button>
            <button type="button" class="preset-chip" data-eq="ev-charger-level2-50a" data-dist="75" data-mat="copper">Level 2 EV Charger (240V 50A)</button>
            <button type="button" class="preset-chip" data-eq="garage-heater-30a" data-dist="50" data-mat="copper">240V Garage Heater (30A)</button>
            <button type="button" class="preset-chip" data-eq="detached-subpanel-100" data-dist="100" data-mat="aluminum">100A Detached Subpanel (Aluminum)</button>
        `;

        const header = wireApp.querySelector('.calc-header');
        if (header) {
            header.insertAdjacentElement('afterend', presetsBar);
        }

        presetsBar.querySelectorAll('.preset-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                presetsBar.querySelectorAll('.preset-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');

                const eq = document.getElementById('gwc-equipment');
                const dist = document.getElementById('gwc-distance');
                const distVal = document.getElementById('gwc-dist-val');
                const mat = document.getElementById('gwc-conductor');

                if (eq) eq.value = this.getAttribute('data-eq');
                if (dist) {
                    dist.value = this.getAttribute('data-dist');
                    if (distVal) distVal.textContent = this.getAttribute('data-dist') + ' ft';
                }
                if (mat) mat.value = this.getAttribute('data-mat');

                if (window.runWireCalc) window.runWireCalc();
            });
        });
    }
});



/* ----------------------------------------------------------
   Mobile Blog Optimization: Tables & Touch Experience
   ---------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    var content = document.getElementById('single-content');
    if (!content) return;

    // Ensure all tables in content have responsive scroll wrappers
    var tables = content.querySelectorAll('table');
    tables.forEach(function (tbl) {
        if (!tbl.closest('.dooroora-table-scroll')) {
            var wrapper = document.createElement('div');
            wrapper.className = 'dooroora-table-wrapper';
            var scrollDiv = document.createElement('div');
            scrollDiv.className = 'dooroora-table-scroll';
            
            tbl.parentNode.insertBefore(wrapper, tbl);
            scrollDiv.appendChild(tbl);
            wrapper.appendChild(scrollDiv);

            var hint = document.createElement('div');
            hint.className = 'dooroora-table-hint';
            hint.innerHTML = '<i class="fa-solid fa-arrows-left-right"></i> Scroll to view full table &rarr;';
            wrapper.appendChild(hint);
        }
    });
});
