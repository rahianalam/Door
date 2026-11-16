(function () {
    'use strict';

    var kwInput = document.getElementById('dooroora_focus_keyphrase');
    var titleInput = document.getElementById('dooroora_seo_title');
    var descInput = document.getElementById('dooroora_seo_desc');
    var checksContainer = document.getElementById('dooroora-analysis-checks');
    var serpTitle = document.getElementById('dooroora-serp-title');
    var serpDesc = document.getElementById('dooroora-serp-desc');
    var serpUrl = document.getElementById('dooroora-serp-url');
    var titleCount = document.getElementById('dooroora-title-count');
    var descCount = document.getElementById('dooroora-desc-count');
    var dataEl = document.getElementById('dooroora-seo-data');

    if (!dataEl) return;

    var postTitle = dataEl.getAttribute('data-title') || '';
    var postContent = dataEl.getAttribute('data-content') || '';
    var permalink = dataEl.getAttribute('data-permalink') || '';
    var siteName = dataEl.getAttribute('data-site-name') || '';

    function getKeyphrase() {
        return kwInput ? kwInput.value.trim() : '';
    }

    function getTitle() {
        return titleInput ? titleInput.value.trim() : postTitle;
    }

    function getDesc() {
        return descInput ? descInput.value.trim() : '';
    }

    function updateSERP() {
        var title = getTitle();
        var desc = getDesc() || postContent.substring(0, 160);

        if (serpTitle) serpTitle.textContent = title.substring(0, 60);
        if (serpDesc) serpDesc.textContent = desc.substring(0, 160);
        if (serpUrl) serpUrl.textContent = permalink.substring(0, 70);

        if (titleCount) titleCount.textContent = title.length;
        if (descCount) descCount.textContent = desc.length;
    }

    function updateAnalysis() {
        var kw = getKeyphrase();
        if (!checksContainer) return;

        checksContainer.innerHTML = '';

        if (!kw) {
            var li = document.createElement('li');
            li.className = 'dooroora-check dooroora-check-neutral';
            li.textContent = 'Enter a focus keyphrase to analyze.';
            checksContainer.appendChild(li);
            return;
        }

        var kws = kw.toLowerCase();
        var checks = [
            { label: 'In SEO Title', pass: getTitle().toLowerCase().indexOf(kws) !== -1 },
            { label: 'In Meta Description', pass: getDesc().toLowerCase().indexOf(kws) !== -1 },
            { label: 'In Post Title', pass: postTitle.toLowerCase().indexOf(kws) !== -1 },
            { label: 'In URL slug', pass: permalink.toLowerCase().indexOf(kws.replace(/\s+/g, '-')) !== -1 || permalink.toLowerCase().indexOf(kws.replace(/\s+/g, '')) !== -1 },
            { label: 'In First 100 Words', pass: postContent.substring(0, 600).toLowerCase().indexOf(kws) !== -1 },
            { label: 'In H2 Headings', pass: doorooraHasKeyphraseInH2s(kw) },
            { label: 'Keyphrase Density > 0.5%', pass: doorooraKeyphraseDensity(kw) >= 0.5 },
        ];

        var passed = 0;
        checks.forEach(function (check) {
            var li = document.createElement('li');
            li.className = 'dooroora-check dooroora-check-' + (check.pass ? 'pass' : 'fail');
            li.textContent = (check.pass ? '\u2713 ' : '\u2717 ') + check.label;
            checksContainer.appendChild(li);
            if (check.pass) passed++;
        });

        var score = document.createElement('li');
        score.className = 'dooroora-check dooroora-check-score';
        score.textContent = 'Keyphrase Score: ' + passed + ' / ' + checks.length;
        checksContainer.appendChild(score);
    }

    function doorooraHasKeyphraseInH2s(kw) {
        var editor = wp && wp.data ? wp.data.select('core/editor') : null;
        if (editor) {
            var blocks = editor.getBlocks();
            for (var i = 0; i < blocks.length; i++) {
                if (blocks[i].name === 'core/heading' && blocks[i].attributes.level === 2) {
                    if ((blocks[i].attributes.content || '').toLowerCase().indexOf(kw.toLowerCase()) !== -1) {
                        return true;
                    }
                }
            }
        }
        // Fallback to content check
        var h2Regex = /<h2[^>]*>([^<]+)<\/h2>/gi;
        var match;
        while ((match = h2Regex.exec(postContent)) !== null) {
            if (match[1].toLowerCase().indexOf(kw.toLowerCase()) !== -1) return true;
        }
        return false;
    }

    function doorooraKeyphraseDensity(kw) {
        var words = postContent.split(/\s+/).length;
        if (words === 0) return 0;
        var kwWords = kw.split(/\s+/);
        var count = 0;
        var idx = postContent.toLowerCase().indexOf(kw.toLowerCase());
        while (idx !== -1) {
            count++;
            idx = postContent.toLowerCase().indexOf(kw.toLowerCase(), idx + 1);
        }
        return (count / words) * 100;
    }

    // Event listeners
    if (kwInput) kwInput.addEventListener('input', function () { updateAnalysis(); updateSERP(); });
    if (titleInput) titleInput.addEventListener('input', updateSERP);
    if (descInput) descInput.addEventListener('input', updateSERP);

    // Initial run
    updateSERP();
    updateAnalysis();

    // Re-run on Gutenberg block changes
    if (wp && wp.data) {
        wp.data.subscribe(function () {
            var editor = wp.data.select('core/editor');
            if (editor) {
                var blocks = editor.getBlocks();
                var newContent = blocks.map(function (b) {
                    return b.attributes.content || b.attributes.text || '';
                }).join(' ');
                if (newContent !== postContent) {
                    postContent = newContent || postContent;
                    updateAnalysis();
                }
            }
        });
    }
})();
