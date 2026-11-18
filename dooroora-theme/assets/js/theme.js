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

            // Shared helpers
            const ddSet = function(id, val, html) {
                const el = document.getElementById(id);
                if (!el) return;
                if (html) el.innerHTML = val; else el.textContent = val;
            };
            const ddFrac = function(n) {
                // Format inches to nearest 1/8 as a fraction, e.g. 82.5 -> 82-1/2
                const whole = Math.floor(n + 1e-9);
                const eighths = Math.round((n - whole) * 8);
                if (eighths === 0) return String(whole);
                if (eighths === 8) return String(whole + 1);
                let num = eighths, den = 8;
                while (num % 2 === 0) { num /= 2; den /= 2; }
                return whole + '-' + num + '/' + den;
            };
            const ddCallout = function(inches) {
                return Math.floor(inches / 12) + '/' + (inches % 12);
            };

            // 1. Door Size Calculator Logic
            const doorCalcApp = document.getElementById('door-size-calc-app');
            if (doorCalcApp) {
                const STD_WIDTHS = [18, 20, 24, 28, 30, 32, 34, 36];
                const STD_HEIGHTS = [80, 84, 96];
                window.runDoorCalc = function() {
                    const activeBtn = doorCalcApp.querySelector('.calc-tab-btn.active');
                    const activeTab = activeBtn ? activeBtn.getAttribute('data-tab') : 'tab-slab-to-ro';

                    if (activeTab === 'tab-slab-to-ro') {
                        const type = document.getElementById('ds-door-type').value;
                        const width = parseFloat(document.getElementById('ds-slab-width').value);
                        const height = parseFloat(document.getElementById('ds-slab-height').value);
                        const framing = document.getElementById('ds-wall-framing').value;

                        let jamb = '4-9/16"', jambNote = 'Fits a 3-1/2" stud plus 1/2" drywall on both sides';
                        if (framing === '2x6') { jamb = '6-9/16"'; jambNote = 'Fits a 5-1/2" stud plus 1/2" drywall on both sides'; }
                        else if (framing === 'plaster') { jamb = '~5-1/4"'; jambNote = 'Measure your actual wall thickness; plaster walls vary'; }

                        let roText, roNote, note;
                        if (type === 'pocket-door') {
                            const w = width * 2 + 1, h = height + 4.5;
                            roText = ddFrac(w) + '" W × ' + ddFrac(h) + '" H';
                            roNote = 'Typical rule: (2 × door width) + 1", height + 4-1/2"';
                            note = '<strong>Pocket Door:</strong> A ' + width + '" × ' + height + '" pocket door typically needs a rough opening of about <strong>' + ddFrac(w) + '" wide by ' + ddFrac(h) + '" tall</strong>. Pocket frame kits vary, so always confirm with your kit\'s instructions before framing.';
                        } else if (type === 'bifold-door') {
                            const fw = width, fh = height + 0.5, rw = width + 1.5, rh = height + 2;
                            roText = ddFrac(rw) + '" W × ' + ddFrac(rh) + '" H';
                            roNote = 'Finished opening: ' + ddFrac(fw) + '" × ' + ddFrac(fh) + '" (inside the jambs)';
                            note = '<strong>Bi-Fold Door:</strong> Bi-fold doors are sized to the <strong>finished opening</strong>. A ' + width + '" unit fits a finished opening of about ' + ddFrac(fw) + '" × ' + ddFrac(fh) + '". Framed with 3/4" jambs, that is a rough opening of about <strong>' + ddFrac(rw) + '" × ' + ddFrac(rh) + '"</strong>. Check the manufacturer\'s sizing chart.';
                        } else if (type === 'barn-door') {
                            const ow = width - 2, oh = height - 1;
                            roText = ddFrac(ow) + '" W × ' + ddFrac(oh) + '" H (max finished opening)';
                            roNote = 'Barn doors overlap the opening; no rough opening is framed for the slab';
                            note = '<strong>Barn Door:</strong> A surface-mounted barn door should be at least 2" wider and 1" taller than the finished opening. A ' + width + '" × ' + height + '" door suits a finished opening up to about <strong>' + ddFrac(ow) + '" × ' + ddFrac(oh) + '"</strong>. Add solid blocking or a header board for the track.';
                        } else if (type === 'interior-slab') {
                            roText = 'Not needed (existing frame)';
                            roNote = 'Slab replacement reuses the existing jamb';
                            note = '<strong>Slab Replacement:</strong> Measure the old slab (width, height and thickness) and the hinge locations, then buy a ' + width + '" × ' + height + '" slab to match. You may need to trim it and cut new hinge mortises.';
                        } else if (type === 'exterior-entry') {
                            const w = width + 2, h = height + 2.5;
                            roText = ddFrac(w) + '" W × ' + ddFrac(h) + '" H';
                            roNote = 'Typical rule: width + 2", height + 2-1/2" (sill units vary)';
                            note = '<strong>Exterior Entry Door:</strong> Frame the rough opening to about <strong>' + ddFrac(w) + '" wide by ' + ddFrac(h) + '" tall</strong> for a ' + width + '" × ' + height + '" pre-hung unit. Exterior units vary by brand and sill height, so confirm the manufacturer\'s rough opening before framing, and install a sill pan.';
                        } else {
                            const w = width + 2, h = height + 2.5;
                            roText = ddFrac(w) + '" W × ' + ddFrac(h) + '" H';
                            roNote = 'Standard rule: width + 2", height + 2-1/2"';
                            note = '<strong>Pro Carpenter Tip:</strong> For a ' + width + '" × ' + height + '" pre-hung door, frame your rough opening to <strong>' + ddFrac(w) + '" wide by ' + ddFrac(h) + '" tall</strong> with a ' + jamb + ' jamb. Check the floor with a level before setting the jamb.';
                        }

                        ddSet('res-ro-dim', roText);
                        ddSet('res-ro-notes', roNote);
                        ddSet('res-slab-dim', width + '" W × ' + height + '" H');
                        ddSet('res-callout', 'Architectural callout: ' + ddCallout(width) + ' × ' + ddCallout(height));
                        ddSet('res-jamb-depth', (type === 'barn-door') ? 'N/A (surface mount)' : jamb);
                        ddSet('res-jamb-notes', (type === 'barn-door') ? 'Barn doors hang on a wall-mounted track' : jambNote);
                        ddSet('res-summary-banner', note, true);
                    } else {
                        const mWidth = parseFloat(document.getElementById('ds-measured-width').value);
                        const mHeight = parseFloat(document.getElementById('ds-measured-height').value);
                        const floor = document.getElementById('ds-flooring-type').value;

                        if (!(mWidth > 0) || !(mHeight > 0)) {
                            ddSet('res-summary-banner', '<strong>Enter your measurements:</strong> type the rough opening width and height in inches.', true);
                            return;
                        }

                        // Floor allowance: finished floor needs least, bare subfloor needs room for future flooring
                        let floorOffset = 2.5;
                        if (floor === 'carpet') floorOffset = 3.0;
                        else if (floor === 'subfloor') floorOffset = 3.25;

                        const maxW = mWidth - 2;
                        const maxH = mHeight - floorOffset;
                        const fitW = STD_WIDTHS.filter(w => w <= maxW + 0.125).pop();
                        const fitH = STD_HEIGHTS.filter(h => h <= maxH + 0.125).pop();

                        ddSet('res-ro-dim', ddFrac(mWidth) + '" W × ' + ddFrac(mHeight) + '" H (measured)');
                        ddSet('res-ro-notes', 'Measured between jack studs and from subfloor to header');
                        ddSet('res-jamb-depth', 'Match wall thickness');
                        ddSet('res-jamb-notes', '4-9/16" for 2x4 walls, 6-9/16" for 2x6 walls');

                        if (!fitW || !fitH) {
                            ddSet('res-slab-dim', 'Custom size needed');
                            ddSet('res-callout', 'Opening is smaller than standard pre-hung sizes');
                            ddSet('res-summary-banner', '<strong>Sizing Assessment:</strong> An opening of ' + ddFrac(mWidth) + '" × ' + ddFrac(mHeight) + '" is too small for a standard pre-hung door (smallest standard is 18" × 80", needing about 20" × 82-1/2"). You will need to reframe the opening or order a custom-size door.', true);
                            return;
                        }

                        ddSet('res-slab-dim', fitW + '" W × ' + fitH + '" H (standard pre-hung)');
                        ddSet('res-callout', 'Architectural callout: ' + ddCallout(fitW) + ' × ' + ddCallout(fitH));

                        let msg = '<strong>Sizing Assessment:</strong> Your ' + ddFrac(mWidth) + '" × ' + ddFrac(mHeight) + '" rough opening fits a standard <strong>' + fitW + '" × ' + fitH + '"</strong> pre-hung door.';
                        const extraW = maxW - fitW, extraH = maxH - fitH;
                        if (extraW > 1 || extraH > 1) {
                            msg += ' The opening is larger than ideal (about ' + ddFrac(Math.max(0, extraW)) + '" extra width and ' + ddFrac(Math.max(0, extraH)) + '" extra height), so add filler studs or a cripple to tighten it before installing.';
                        }
                        ddSet('res-summary-banner', msg, true);
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
                    const keysEl = document.getElementById('lsm-keys');
                    const keys = keysEl ? keysEl.value : 'rekeyable';

                    const exterior = ['front-entry', 'rear-patio', 'garage-service', 'rental-airbnb', 'commercial-suite'].indexOf(loc) !== -1;
                    const tips = [];
                    let grade, gradeSub, type, typeSub, strike, strikeSub, level, levelSub;

                    // Base recommendation by location
                    if (loc === 'commercial-suite') {
                        grade = 'ANSI/BHMA Grade 1';
                        gradeSub = 'Commercial duty for high-traffic doors';
                        type = 'Commercial Lever or Mortise Lock (Office/Storeroom Function)';
                        typeSub = 'Inside lever must always open the door for egress';
                        tips.push('Commercial doors must allow free exit from inside and usually require ADA-compliant lever handles. Use a commercial locksmith for master keying, and do not modify fire-rated doors.');
                    } else if (loc === 'interior-office') {
                        grade = 'ANSI/BHMA Grade 2';
                        gradeSub = 'Strong interior duty';
                        type = 'Keyed Entry or Storeroom Lever';
                        typeSub = 'Add a single-cylinder deadbolt on a solid door for valuables';
                        tips.push('For a home office or wine room, a Grade 2 keyed lever is usually enough. For a gun room, use a solid-core or steel door with a Grade 1 deadbolt; a lock on a weak door adds little.');
                    } else if (loc === 'garage-service') {
                        grade = 'ANSI/BHMA Grade 1 or 2';
                        gradeSub = 'Garage-to-house doors are a common entry point';
                        type = 'Single-Cylinder Deadbolt + Keyed Lever';
                        typeSub = 'Keep the door self-closing if your code requires it';
                        tips.push('Many building codes require the garage-to-house door to be solid-core, steel or fire-rated (often 20-minute) and sometimes self-closing. Use hardware suitable for that door and avoid cutting extra holes in a labeled fire door.');
                    } else if (loc === 'rental-airbnb') {
                        grade = 'ANSI/BHMA Grade 2 or better';
                        gradeSub = 'Balanced durability for frequent guest use';
                        type = 'Keypad or Smart Deadbolt with Key Backup';
                        typeSub = 'Unique codes per guest; no physical keys to lose';
                        tips.push('Change guest codes between stays and keep a mechanical key backup. Check local laws on tenant access and lock changes.');
                    } else if (loc === 'rear-patio') {
                        grade = 'ANSI/BHMA Grade 1';
                        gradeSub = 'Highest residential protection for hidden doors';
                        type = 'Single-Cylinder Grade 1 Deadbolt';
                        typeSub = 'Full 1" bolt throw with hardened insert';
                        tips.push('Back and side doors are out of sight, so they are targeted more often. Add a door reinforcement plate and 3" screws in the top hinge as well as the strike.');
                    } else {
                        grade = 'ANSI/BHMA Grade 1 or 2';
                        gradeSub = 'Grade 1 is best for primary entry doors';
                        type = 'Single-Cylinder Deadbolt + Keyed Entry Handle';
                        typeSub = 'Full 1" bolt throw; single cylinder for fast exit';
                    }

                    strike = exterior ? 'Reinforced Box Strike' : 'Standard Strike Plate';
                    strikeSub = exterior ? 'Secure with 3" screws into the wall studs' : 'Standard screws are fine for interior doors';
                    if (exterior) tips.push('Replace the short strike screws with 3" screws that reach the framing. This is the single biggest defense against kick-ins.');

                    // Priority adjustments
                    if (priority === 'smart-connected') {
                        type = 'Smart Deadbolt with Key Backup';
                        typeSub = 'Choose a BHMA-graded smart lock with a physical keyway or backup';
                        tips.push('Pick a smart lock that carries a BHMA grade, supports your platform (Apple Home, Google Home, Alexa or Matter) and has a backup if batteries die.');
                    } else if (priority === 'keyless-keypad') {
                        type = 'Keypad Deadbolt';
                        typeSub = 'Code entry; choose a graded model';
                        tips.push('A keypad deadbolt removes hidden spare keys. Choose a Grade 1 or 2 model and change codes periodically.');
                    } else if (priority === 'child-safety') {
                        if (exterior) {
                            typeSub = 'Single-cylinder only: never require a key to exit';
                        } else {
                            type = 'Privacy Lever with Emergency Release';
                            typeSub = 'Can be opened from outside with a pin or tool';
                            grade = 'ANSI/BHMA Grade 2 or 3';
                            gradeSub = 'Interior privacy duty';
                        }
                        tips.push('For child safety, every lock must open from inside without a key, and interior privacy locks should have an outside emergency release.');
                    } else if (priority === 'budget-standard') {
                        if (exterior && loc !== 'commercial-suite') { grade = 'ANSI/BHMA Grade 2'; gradeSub = 'Reliable residential protection at lower cost'; }
                        tips.push('A Grade 2 deadbolt with a reinforced strike and 3" screws gives better real-world protection than an expensive lock with a weak strike.');
                    } else if (priority === 'max-security' && exterior) {
                        grade = 'ANSI/BHMA Grade 1';
                        gradeSub = 'Highest residential/commercial rating';
                        tips.push('For maximum security, combine a Grade 1 deadbolt with a pick- and bump-resistant cylinder and a full door reinforcement kit.');
                    }

                    // Material checks
                    if (mat === 'hollow-core') {
                        if (exterior) {
                            grade = 'Replace the door first';
                            gradeSub = 'A hollow-core door cannot hold a deadbolt securely';
                            type = 'Upgrade to a Solid-Core or Steel Exterior Door';
                            typeSub = 'Then fit a Grade 1 or 2 deadbolt';
                            tips.unshift('<strong>Important:</strong> hollow-core doors are for interiors only. Any lock on one can be defeated by breaking the door itself. Replace it with a solid-core, steel or fiberglass exterior door.');
                        } else {
                            if (type.indexOf('Deadbolt') !== -1) { type = 'Keyed or Privacy Lever (no deadbolt)'; }
                            typeSub = 'Hollow-core doors only have solid blocking at the handle';
                            tips.push('Hollow-core doors only have a solid block where the handle goes. Use a lever or knob lock there rather than adding a deadbolt.');
                        }
                    } else if (mat === 'glass-panel') {
                        tips.push('Glass within about 40" of the lock can be broken to reach the thumb turn. Use laminated or tempered security glass or security film. Double-cylinder deadbolts are restricted by many fire and building codes on exit doors because they slow escape, so check local code before using one.');
                    } else if (mat === 'steel-clad' || mat === 'fiberglass') {
                        tips.push('On steel and fiberglass doors, use the reinforcer sleeve supplied with the lock so the through-bolts do not crush the door skin.');
                    }

                    // Thickness and backset
                    if (thick < 1.5 && exterior) {
                        tips.push('A 1-3/8" door is an interior thickness. Exterior doors should be 1-3/4" thick for strength and weather sealing.');
                    } else if (thick > 2) {
                        tips.push('Doors thicker than 1-3/4" need a thick-door extension kit. Check the lock\'s supported door thickness before buying.');
                    }
                    tips.push('Your ' + (bset === '2.75' ? '2-3/4"' : '2-3/8"') + ' backset is standard; most locks adjust between 2-3/8" and 2-3/4".');

                    // Key management
                    if (keys === 'rekeyable') {
                        tips.push('Choose a DIY-rekeyable cylinder (such as Kwikset SmartKey) or interchangeable core so you can change keys in minutes.');
                    } else if (keys === 'master-keyed') {
                        tips.push('A master key system is set up by a locksmith; buy all cylinders on the same keyway.');
                    } else if (keys === 'restricted-key') {
                        tips.push('Restricted keyways (such as Medeco or Schlage Primus) prevent unauthorized key copies and add strong pick resistance.');
                    } else if (keys === 'fully-keyless') {
                        tips.push('Fully keyless locks need a reliable backup, such as an external battery jump terminal, so you are not locked out when batteries die.');
                    }

                    // Overall protection level (qualitative, not a score)
                    if (grade.indexOf('Replace') === 0) { level = 'Low until door is upgraded'; levelSub = 'The door itself is the weak point'; }
                    else if (grade.indexOf('Grade 1') !== -1 && exterior) { level = 'High'; levelSub = 'With reinforced strike and 3" screws'; }
                    else if (exterior) { level = 'Good'; levelSub = 'Upgrade to Grade 1 for more protection'; }
                    else { level = 'Appropriate for interior use'; levelSub = 'Interior locks provide privacy, not forced-entry protection'; }

                    ddSet('lsm-res-grade', grade);
                    ddSet('lsm-res-cycles', gradeSub);
                    ddSet('lsm-res-type', type);
                    ddSet('lsm-res-type-sub', typeSub);
                    ddSet('lsm-res-strike', strike);
                    ddSet('lsm-res-strike-sub', strikeSub);
                    ddSet('lsm-res-score', level);
                    ddSet('lsm-res-score-sub', levelSub);
                    ddSet('lsm-summary-banner', '<strong>Recommendation:</strong><ul style="margin:8px 0 0 18px;padding:0">' + tips.map(t => '<li style="margin:4px 0">' + t + '</li>').join('') + '</ul>', true);
                };

                lockApp.querySelectorAll('select').forEach(el => {
                    el.addEventListener('change', window.runLockMatcher);
                });
                window.runLockMatcher();
            }

            // 3. Garage Wire Size Calculator Logic
            const wireApp = document.getElementById('garage-wire-calc-app');
            if (wireApp) {
                // Conductor sizes, smallest to largest, with circular mils (NEC Chapter 9, Table 8)
                const SIZES = ['14', '12', '10', '8', '6', '4', '3', '2', '1', '1/0', '2/0', '3/0', '4/0'];
                const CMILS = { '14': 4110, '12': 6530, '10': 10380, '8': 16510, '6': 26240, '4': 41740, '3': 52620, '2': 66360, '1': 83690, '1/0': 105600, '2/0': 133100, '3/0': 167800, '4/0': 211600 };
                // NEC Table 310.16 allowable ampacities (60°C and 75°C columns)
                const AMP = {
                    copper: {
                        60: { '14': 15, '12': 20, '10': 30, '8': 40, '6': 55, '4': 70, '3': 85, '2': 95, '1': 110, '1/0': 125, '2/0': 145, '3/0': 165, '4/0': 195 },
                        75: { '14': 20, '12': 25, '10': 35, '8': 50, '6': 65, '4': 85, '3': 100, '2': 115, '1': 130, '1/0': 150, '2/0': 175, '3/0': 200, '4/0': 230 }
                    },
                    aluminum: {
                        60: { '12': 15, '10': 25, '8': 35, '6': 40, '4': 55, '3': 65, '2': 75, '1': 85, '1/0': 100, '2/0': 115, '3/0': 130, '4/0': 150 },
                        75: { '12': 20, '10': 30, '8': 40, '6': 50, '4': 65, '3': 75, '2': 90, '1': 100, '1/0': 120, '2/0': 135, '3/0': 155, '4/0': 180 }
                    }
                };
                // NEC 240.4(D) small-conductor overcurrent limits
                const SMALL_MAX = { copper: { '14': 15, '12': 20, '10': 30 }, aluminum: { '12': 15, '10': 25 } };

                const EQUIP = {
                    'opener-standard':       { load: 6,  breaker: 20,  volts: 120, feeder: false, label: 'garage door opener', wires: '2 conductors + ground' },
                    'opener-heavy':          { load: 12, breaker: 20,  volts: 120, feeder: false, label: 'heavy-duty opener', wires: '2 conductors + ground' },
                    'garage-receptacles':    { load: 16, breaker: 20,  volts: 120, feeder: false, label: 'garage receptacle circuit', wires: '2 conductors + ground' },
                    'ev-charger-32':         { load: 32, breaker: 40,  volts: 240, feeder: false, label: '32A EV charger', wires: '2 hots + ground (neutral only if the charger requires it)' },
                    'ev-charger-48':         { load: 48, breaker: 60,  volts: 240, feeder: false, label: '48A EV charger', wires: '2 hots + ground (neutral only if the charger requires it)' },
                    'garage-heater':         { load: 21, breaker: 30,  volts: 240, feeder: false, label: '5,000W heater', wires: '2 hots + ground' },
                    'detached-subpanel-60':  { load: 48, breaker: 60,  volts: 240, feeder: true,  label: '60A subpanel feeder', wires: '2 hots + neutral + ground (4-wire)' },
                    'detached-subpanel-100': { load: 80, breaker: 100, volts: 240, feeder: true,  label: '100A subpanel feeder', wires: '2 hots + neutral + ground (4-wire)' }
                };

                const voltSel = document.getElementById('gwc-voltage');
                const matSel = document.getElementById('gwc-material');

                window.runWireCalc = function() {
                    const eqKey = document.getElementById('gwc-equipment').value;
                    const eq = EQUIP[eqKey] || EQUIP['opener-standard'];
                    const distRaw = parseFloat(document.getElementById('gwc-distance').value);
                    const dist = distRaw > 0 ? distRaw : 50;
                    const dropLimit = parseFloat(document.getElementById('gwc-drop-limit').value) || 3;
                    const methodEl = document.getElementById('gwc-install-method');
                    const method = methodEl ? methodEl.value : 'nmb';
                    const notes = [];

                    // Voltage is set by the equipment, not chosen freely
                    const volt = eq.volts;
                    if (voltSel) { voltSel.value = String(volt); voltSel.disabled = true; voltSel.title = 'Set automatically by the equipment you selected'; }

                    // Aluminum is only recommended for feeders (#8 and larger in practice)
                    let mat = matSel ? matSel.value : 'copper';
                    if (mat === 'aluminum' && !eq.feeder) {
                        mat = 'copper';
                        notes.push('Aluminum is not recommended for small branch circuits like this, so the result uses copper. Aluminum is typically used only for larger feeders such as subpanels.');
                    }

                    // Temperature column: NM-B and UF-B are 60°C; THHN/THWN-2 in conduit, SER and URD feeders use 75°C terminals
                    let temp = 75;
                    if (!eq.feeder && (method === 'nmb' || method === 'direct-burial')) temp = 60;

                    // 1) Smallest conductor whose ampacity covers the breaker rating
                    const table = AMP[mat][temp];
                    let idx = -1;
                    for (let i = 0; i < SIZES.length; i++) {
                        const s = SIZES[i];
                        if (!(s in table)) continue;
                        const small = SMALL_MAX[mat][s];
                        const limit = small ? Math.min(table[s], small) : table[s];
                        if (limit >= eq.breaker) { idx = i; break; }
                    }
                    if (idx === -1) idx = SIZES.length - 1;
                    const ampacitySize = SIZES[idx];

                    // 2) Upsize for voltage drop: VD = 2 × K × I × L / CM (single-phase)
                    const k = mat === 'aluminum' ? 21.2 : 12.9;
                    const vdFor = function(size) { return (2 * k * eq.load * dist) / CMILS[size]; };
                    let vd = vdFor(SIZES[idx]);
                    while ((vd / volt) * 100 > dropLimit && idx < SIZES.length - 1) {
                        idx++;
                        vd = vdFor(SIZES[idx]);
                    }
                    const size = SIZES[idx];
                    const pct = (vd / volt) * 100;
                    const overLimit = pct > dropLimit;
                    const upsized = size !== ampacitySize;

                    // 3) Cable/conductor type by installation method
                    const matName = mat === 'aluminum' ? 'Aluminum' : 'Copper';
                    let cable;
                    if (eq.feeder) {
                        if (method === 'nmb') cable = size + ' AWG ' + matName + ' SER cable (4-wire) or THHN in conduit';
                        else if (method === 'direct-burial') cable = size + ' AWG ' + matName + ' URD/USE-2 direct burial (24" deep) or THWN-2 in conduit';
                        else cable = size + ' AWG ' + matName + ' THWN-2 in conduit (4 conductors)';
                    } else {
                        const n = '2';
                        if (method === 'nmb') cable = size + '/' + n + ' with ground NM-B (Romex), indoor dry locations';
                        else if (method === 'direct-burial') cable = size + '/' + n + ' with ground UF-B (24" deep, or 12" under 120V GFCI 20A circuits) or THWN-2 in conduit';
                        else cable = size + ' AWG ' + matName + ' THHN/THWN-2 in conduit';
                    }
                    if (method === 'direct-burial') notes.push('Underground runs must use wet-rated cable or conductors (UF-B, URD or THWN-2). Standard NM-B (Romex) is not allowed underground or in conduit outdoors.');

                    if (upsized) notes.push('Wire upsized from ' + ampacitySize + ' AWG to ' + size + ' AWG to keep voltage drop under ' + dropLimit + '% over ' + dist + ' ft. Check that the larger wire fits your breaker and device terminals.');
                    if (!eq.feeder && eq.breaker <= 30 && SIZES.indexOf(size) > SIZES.indexOf('10')) notes.push('Most breakers, outlets and openers only accept wire up to 10 AWG. For a run this long, a small subpanel in the garage is usually the better solution.');
                    if (overLimit) notes.push('<strong>Warning:</strong> even ' + size + ' AWG exceeds the ' + dropLimit + '% target over ' + dist + ' ft. Have an electrician design this run (larger conductors, a closer subpanel or a different layout).');
                    if (eq.label.indexOf('EV') !== -1) notes.push('EV chargers are continuous loads: the breaker must be at least 125% of the charger current (NEC 625). Follow the charger manufacturer\'s wiring instructions.');
                    if (!eq.feeder) notes.push('Garage receptacles require GFCI protection under NEC 210.8; many areas also require AFCI.');
                    if (eq.feeder) notes.push('Detached buildings need a 4-wire feeder, a separate ground rod/electrode system at the subpanel, and neutrals isolated from grounds in the subpanel.');
                    notes.push('These are estimates based on NEC tables for typical conditions. Local codes, ambient temperature, conduit fill and terminal ratings can change the result, so confirm with a licensed electrician and get a permit where required.');

                    ddSet('gwc-res-gauge', size + ' AWG ' + matName);
                    ddSet('gwc-res-gauge-notes', 'Ampacity minimum: ' + ampacitySize + ' AWG (' + temp + '°C column)' + (upsized ? '; upsized for voltage drop' : ''));
                    ddSet('gwc-res-breaker', eq.breaker + ' A ' + (eq.volts === 240 ? '240V double-pole' : '120V single-pole') + ' breaker');
                    ddSet('gwc-res-load-notes', 'Design load: ' + eq.load + ' A for ' + eq.label);
                    ddSet('gwc-res-drop', pct.toFixed(1) + '% (' + vd.toFixed(1) + ' V drop)');
                    ddSet('gwc-res-delivered', 'About ' + (volt - vd).toFixed(1) + ' V at the equipment' + (overLimit ? ' (exceeds ' : ' (within ') + dropLimit + '% target)');
                    ddSet('gwc-res-cable', cable);
                    ddSet('gwc-summary-banner', '<strong>Notes (' + eq.wires + '):</strong><ul style="margin:8px 0 0 18px;padding:0">' + notes.map(t => '<li style="margin:4px 0">' + t + '</li>').join('') + '</ul>', true);

                    const badge = wireApp.querySelector('.calc-status-badge');
                    if (badge) badge.textContent = 'Estimate';
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
