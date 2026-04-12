/*
 * Language dropdown fix — loaded via index.php output buffer injection.
 * Bypasses Blade template cache entirely.
 *
 * Finds the existing toggle button, creates a properly-positioned dropdown
 * using position:fixed + getBoundingClientRect (same logic as _dropdown-test.html).
 */
(function() {
    var btn = document.querySelector('[data-lang-dropdown-toggle]');
    if (!btn) return;

    // Remove any existing broken dropdown
    var old = document.getElementById('langDropdown');
    if (old) old.remove();

    // Detect current locale from URL
    var path = window.location.pathname;
    var currentLocale = 'en';
    var match = path.match(/^\/(en|ar|it)(\/|$)/);
    if (match) currentLocale = match[1];

    // Build the other two locale options
    var locales = { en: 'English', ar: 'العربية', it: 'Italiano' };
    var pathWithoutLocale = path.replace(/^\/(en|ar|it)(\/|$)/, '/');
    if (pathWithoutLocale === '/') pathWithoutLocale = '';

    // Create dropdown menu
    var menu = document.createElement('div');
    menu.id = 'langDropdownFixed';
    menu.style.cssText = 'position:fixed;min-width:190px;border-radius:8px;overflow:hidden;opacity:0;visibility:hidden;transform:translateY(-4px);z-index:9999;background:rgba(44,34,32,0.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(196,154,122,0.25);box-shadow:0 20px 60px rgba(0,0,0,0.3);pointer-events:none;transition:opacity 0.2s,visibility 0.2s,transform 0.2s;';

    Object.keys(locales).forEach(function(code) {
        if (code === currentLocale) return;
        var a = document.createElement('a');
        a.href = '/' + code + pathWithoutLocale;
        a.setAttribute('data-page-transition', 'false');
        a.style.cssText = 'cursor:pointer;display:block;padding:14px 20px;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(248,244,239,0.8);text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.05);font-family:Inter,sans-serif;transition:all 0.2s;';
        a.innerHTML = '<span style="font-weight:600;">' + code.toUpperCase() + '</span><span style="color:rgba(248,244,239,0.45);text-transform:none;letter-spacing:normal;margin-left:8px;">' + locales[code] + '</span>';
        a.onmouseover = function() { this.style.background = 'rgba(196,154,122,0.15)'; this.style.color = '#C49A7A'; };
        a.onmouseout = function() { this.style.background = 'transparent'; this.style.color = 'rgba(248,244,239,0.8)'; };

        // Set cookie on click
        a.addEventListener('click', function() {
            var d = 365*24*60*60;
            var s = location.protocol === 'https:' ? ';Secure' : '';
            document.cookie = 'delos_locale=' + code + ';Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
            document.cookie = 'delos_locale_seen=1;Max-Age=' + d + ';Path=/;SameSite=Lax' + s;
        });

        menu.appendChild(a);
    });

    document.body.appendChild(menu);

    // Toggle logic
    var isOpen = false;
    function toggle(show) {
        isOpen = show;
        if (show) {
            var r = btn.getBoundingClientRect();
            menu.style.top = (r.bottom + 8) + 'px';
            menu.style.right = (window.innerWidth - r.right) + 'px';
            menu.style.left = 'auto';
        }
        menu.style.opacity = show ? '1' : '0';
        menu.style.visibility = show ? 'visible' : 'hidden';
        menu.style.transform = show ? 'translateY(0)' : 'translateY(-4px)';
        menu.style.pointerEvents = show ? 'auto' : 'none';
    }

    btn.addEventListener('click', function(e) { e.stopPropagation(); toggle(!isOpen); });
    document.addEventListener('click', function(e) { if (isOpen && !menu.contains(e.target) && e.target !== btn) toggle(false); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && isOpen) toggle(false); });
})();
