// Placed in head to prevent flash of wrong theme
(function() {
    const savedTheme = localStorage.getItem('lumina_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
})();