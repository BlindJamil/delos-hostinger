/*
 * Delos International — Floating Globe Language Switcher
 * Injected via index.php output buffer. 100% static, zero Blade dependency.
 *
 * Uses <div> elements (not <a> tags) to avoid the site's global CSS
 * rules on anchor tags that cause position/layout collisions.
 */
(function() {
    var path = window.location.pathname;
    var currentLocale = 'en';
    var m = path.match(/^\/(en|ar|it)(\/|$)/);
    if (m) currentLocale = m[1];

    var isFirstVisit = document.cookie.indexOf('delos_locale_seen') === -1;
    var pathWithoutLocale = path.replace(/^\/(en|ar|it)(\/|$)/, '/');
    if (pathWithoutLocale === '/') pathWithoutLocale = '';

    var locales = [
        { code: 'en', label: 'EN', native: 'English' },
        { code: 'ar', label: 'AR', native: 'العربية' },
        { code: 'it', label: 'IT', native: 'Italiano' }
    ];

    // ── Wrapper: fixed bottom-right ──
    var wrap = document.createElement('div');
    wrap.id = 'delos-globe-wrap';
    wrap.style.cssText = [
        'position:fixed',
        'bottom:24px',
        'right:24px',
        'z-index:10000',
        'font-family:Inter,-apple-system,Helvetica,sans-serif',
        'line-height:1.4'
    ].join(';');

    // ── Panel: sits above the button ──
    var panel = document.createElement('div');
    panel.id = 'delos-globe-panel';
    panel.style.cssText = [
        'position:absolute',
        'bottom:58px',
        'right:0',
        'width:220px',
        'border-radius:12px',
        'background:#2C2220',
        'border:1px solid rgba(196,154,122,0.25)',
        'box-shadow:0 12px 40px rgba(0,0,0,0.4)',
        'padding:0',
        'margin:0',
        'opacity:0',
        'visibility:hidden',
        'transform:translateY(8px)',
        'transform-origin:bottom right',
        'transition:opacity 0.25s,visibility 0.25s,transform 0.25s',
        'pointer-events:none',
        'overflow:visible'
    ].join(';');

    // Panel header
    var hdr = document.createElement('div');
    hdr.style.cssText = 'display:block;padding:14px 18px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,0.06);';
    hdr.innerHTML = '<span style="font-size:9px;letter-spacing:0.4em;text-transform:uppercase;color:rgba(196,154,122,0.7);font-weight:500;display:inline;">Language</span>';
    panel.appendChild(hdr);

    // ── Language items (using divs, not anchors) ──
    for (var i = 0; i < locales.length; i++) {
        var loc = locales[i];
        var isCurrent = loc.code === currentLocale;
        var url = '/' + loc.code + pathWithoutLocale;

        var item = document.createElement('div');
        item.setAttribute('role', 'menuitem');
        item.setAttribute('data-url', url);
        item.setAttribute('data-code', loc.code);
        item.style.cssText = [
            'display:block',
            'position:static',
            'padding:13px 18px',
            'margin:0',
            'cursor:' + (isCurrent ? 'default' : 'pointer'),
            'background:' + (isCurrent ? 'rgba(196,154,122,0.1)' : 'transparent'),
            'border-bottom:' + (i < locales.length - 1 ? '1px solid rgba(255,255,255,0.05)' : 'none'),
            'transition:background 0.2s',
            'box-sizing:border-box',
            'width:100%',
            'text-decoration:none',
            'float:none',
            'clear:both'
        ].join(';');

        // Code label
        var codeEl = document.createElement('span');
        codeEl.style.cssText = 'display:inline;position:static;font-size:13px;font-weight:600;letter-spacing:0.15em;margin-right:10px;color:' + (isCurrent ? '#C49A7A' : '#F8F4EF') + ';';
        codeEl.textContent = loc.label;

        // Native name
        var nativeEl = document.createElement('span');
        nativeEl.style.cssText = 'display:inline;position:static;font-size:12px;color:' + (isCurrent ? 'rgba(196,154,122,0.7)' : 'rgba(248,244,239,0.45)') + ';';
        if (loc.code === 'ar') nativeEl.style.fontFamily = "'Cairo',sans-serif";
        nativeEl.textContent = loc.native;

        item.appendChild(codeEl);
        item.appendChild(nativeEl);

        // Checkmark for current
        if (isCurrent) {
            var chk = document.createElement('span');
            chk.style.cssText = 'display:inline;position:static;float:right;color:#C49A7A;font-size:14px;margin-left:8px;';
            chk.textContent = '\u2713';
            item.appendChild(chk);
        }

        // Click + hover (only for non-current)
        if (!isCurrent) {
            (function(code, href, el) {
                el.onmouseover = function() { el.style.background = 'rgba(196,154,122,0.12)'; };
                el.onmouseout = function() { el.style.background = 'transparent'; };
                el.onclick = function() {
                    var d = 365 * 24 * 60 * 60;
                    var s = location.protocol === 'https:' ? ';Secure' : '';
                    document.cookie = 'delos_locale=' + code + ';Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
                    document.cookie = 'delos_locale_seen=1;Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
                    window.location.href = href;
                };
            })(loc.code, url, item);
        }

        panel.appendChild(item);
    }

    // ── Globe button ──
    var btn = document.createElement('div');
    btn.id = 'delos-globe-btn';
    btn.setAttribute('role', 'button');
    btn.setAttribute('aria-label', 'Change language');
    btn.setAttribute('tabindex', '0');
    btn.style.cssText = [
        'width:48px',
        'height:48px',
        'border-radius:50%',
        'border:1.5px solid rgba(196,154,122,0.6)',
        'background:rgba(44,34,32,0.85)',
        'cursor:pointer',
        'display:flex',
        'align-items:center',
        'justify-content:center',
        'box-shadow:0 4px 20px rgba(0,0,0,0.25)',
        'position:static',
        'transition:all 0.3s',
        'margin:0',
        'padding:0'
    ].join(';');
    btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C49A7A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>';
    btn.onmouseover = function() { btn.style.borderColor = '#C49A7A'; btn.style.transform = 'scale(1.08)'; };
    btn.onmouseout = function() { btn.style.borderColor = 'rgba(196,154,122,0.6)'; btn.style.transform = 'scale(1)'; };

    // Assemble: panel first (above), then button (below)
    wrap.appendChild(panel);
    wrap.appendChild(btn);
    document.body.appendChild(wrap);

    // ── Toggle ──
    var isOpen = false;
    function setOpen(open) {
        isOpen = open;
        panel.style.opacity = open ? '1' : '0';
        panel.style.visibility = open ? 'visible' : 'hidden';
        panel.style.transform = open ? 'translateY(0) scale(1)' : 'translateY(8px) scale(0.95)';
        panel.style.pointerEvents = open ? 'auto' : 'none';
    }

    btn.addEventListener('click', function(e) { e.stopPropagation(); setOpen(!isOpen); });
    document.addEventListener('click', function(e) { if (isOpen && !wrap.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && isOpen) setOpen(false); });

    // Auto-expand on first visit
    if (isFirstVisit) {
        setTimeout(function() { setOpen(true); }, 800);
    }
})();

/*
 * RTL Fix: Keep marquee animations identical to English.
 * The rtl.css rule reverses marquee direction which breaks the layout.
 * Brand names are the same in every language — no direction change needed.
 */
(function() {
    if (document.documentElement.getAttribute('dir') !== 'rtl') return;

    function fixRtlMarquees() {
        var marquees = document.querySelectorAll('.marquee-track, .brand-marquee');
        for (var i = 0; i < marquees.length; i++) {
            marquees[i].style.animationDirection = 'normal';
        }
    }

    if (document.readyState === 'complete') {
        fixRtlMarquees();
    } else {
        window.addEventListener('load', fixRtlMarquees);
    }
    // Also run immediately in case elements already exist
    fixRtlMarquees();
})();
