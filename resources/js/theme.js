(function () {
    const html = document.documentElement;
    const stored = localStorage.getItem('theme');

    function applyTheme(dark) {
        html.classList.toggle('dark', dark);
        html.classList.toggle('light', !dark);
        html.style.colorScheme = dark ? 'dark' : 'light';
    }

    if (stored === 'dark') {
        applyTheme(true);
    } else {
        if (stored !== 'light') {
            localStorage.setItem('theme', 'light');
        }
        applyTheme(false);
    }
})();
