/*
 * Delos International — Floating Globe Language Switcher
 * Injected via index.php output buffer. 100% static, zero Blade dependency.
 */
(function() {
    var path = window.location.pathname;
    var currentLocale = 'en';
    var m = path.match(/^\/(en|ar|it)(\/|$)/);
    if (m) currentLocale = m[1];

    var isFirstVisit = document.cookie.indexOf('delos_locale_seen') === -1;

    var locales = [
        { code: 'en', label: 'EN', native: 'English' },
        { code: 'ar', label: 'AR', native: 'العربية' },
        { code: 'it', label: 'IT', native: 'Italiano' }
    ];

    var pathWithoutLocale = path.replace(/^\/(en|ar|it)(\/|$)/, '/');
    if (pathWithoutLocale === '/') pathWithoutLocale = '';

    // Container
    var container = document.createElement('div');
    container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:10000;font-family:Inter,-apple-system,sans-serif;';

    // Globe button
    var btn = document.createElement('button');
    btn.setAttribute('aria-label', 'Change language');
    btn.style.cssText = 'width:48px;height:48px;border-radius:50%;border:1.5px solid rgba(196,154,122,0.6);background:rgba(44,34,32,0.85);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,0.25);position:relative;z-index:2;transition:all 0.3s;outline:none;';
    btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C49A7A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>';
    btn.onmouseover = function() { this.style.borderColor='#C49A7A'; this.style.transform='scale(1.08)'; };
    btn.onmouseout = function() { this.style.borderColor='rgba(196,154,122,0.6)'; this.style.transform='scale(1)'; };

    // Panel
    var panel = document.createElement('div');
    panel.style.cssText = 'position:absolute;bottom:60px;right:0;width:210px;border-radius:12px;background:rgba(44,34,32,0.94);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(196,154,122,0.2);box-shadow:0 12px 40px rgba(0,0,0,0.35);opacity:0;visibility:hidden;transform:translateY(8px) scale(0.95);transform-origin:bottom right;transition:all 0.25s cubic-bezier(0.16,1,0.3,1);pointer-events:none;';

    // Header
    var hdr = document.createElement('div');
    hdr.style.cssText = 'padding:14px 16px 10px;border-bottom:1px solid rgba(255,255,255,0.06);';
    hdr.innerHTML = '<span style="font-size:9px;letter-spacing:0.4em;text-transform:uppercase;color:rgba(196,154,122,0.7);font-weight:500;">Language</span>';
    panel.appendChild(hdr);

    // Language links
    for (var i = 0; i < locales.length; i++) {
        var loc = locales[i];
        var isCurrent = loc.code === currentLocale;

        var a = document.createElement('a');
        a.href = '/' + loc.code + pathWithoutLocale;
        a.setAttribute('data-page-transition', 'false');
        a.style.cssText = 'cursor:pointer;display:block;padding:13px 16px;text-decoration:none;color:#F8F4EF;transition:background 0.2s;' + (isCurrent ? 'background:rgba(196,154,122,0.1);' : 'background:transparent;');

        // Content row
        var row = document.createElement('div');
        row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;';

        var left = document.createElement('div');
        left.style.cssText = 'display:flex;align-items:center;gap:10px;';

        var codeSpan = document.createElement('span');
        codeSpan.style.cssText = 'font-size:13px;font-weight:600;letter-spacing:0.15em;color:' + (isCurrent ? '#C49A7A' : '#F8F4EF') + ';';
        codeSpan.textContent = loc.label;

        var nativeSpan = document.createElement('span');
        nativeSpan.style.cssText = 'font-size:12px;color:' + (isCurrent ? 'rgba(196,154,122,0.7)' : 'rgba(248,244,239,0.45)') + ';';
        if (loc.code === 'ar') nativeSpan.style.fontFamily = "'Cairo',sans-serif";
        nativeSpan.textContent = loc.native;

        left.appendChild(codeSpan);
        left.appendChild(nativeSpan);
        row.appendChild(left);

        if (isCurrent) {
            var check = document.createElement('span');
            check.style.cssText = 'color:#C49A7A;font-size:14px;';
            check.textContent = '\u2713';
            row.appendChild(check);
        }

        a.appendChild(row);

        // Border between items (not on last)
        if (i < locales.length - 1) {
            a.style.borderBottom = '1px solid rgba(255,255,255,0.05)';
        }

        if (isCurrent) {
            a.style.cursor = 'default';
            a.onclick = function(e) { e.preventDefault(); };
        } else {
            (function(code) {
                a.onmouseover = function() { this.style.background = 'rgba(196,154,122,0.12)'; };
                a.onmouseout = function() { this.style.background = 'transparent'; };
                a.addEventListener('click', function() {
                    var d = 365 * 24 * 60 * 60;
                    var s = location.protocol === 'https:' ? ';Secure' : '';
                    document.cookie = 'delos_locale=' + code + ';Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
                    document.cookie = 'delos_locale_seen=1;Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
                });
            })(loc.code);
        }

        panel.appendChild(a);
    }

    container.appendChild(panel);
    container.appendChild(btn);
    document.body.appendChild(container);

    // Toggle
    var isOpen = false;
    function setOpen(open) {
        isOpen = open;
        panel.style.opacity = open ? '1' : '0';
        panel.style.visibility = open ? 'visible' : 'hidden';
        panel.style.transform = open ? 'translateY(0) scale(1)' : 'translateY(8px) scale(0.95)';
        panel.style.pointerEvents = open ? 'auto' : 'none';
        var svg = btn.querySelector('svg');
        if (svg) { svg.style.transform = open ? 'rotate(20deg)' : 'rotate(0)'; svg.style.transition = 'transform 0.3s'; }
    }

    btn.addEventListener('click', function(e) { e.stopPropagation(); setOpen(!isOpen); });
    document.addEventListener('click', function(e) { if (isOpen && !container.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && isOpen) setOpen(false); });

    if (isFirstVisit) {
        setTimeout(function() { setOpen(true); }, 800);
    }
})();
