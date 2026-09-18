// 1. Remove Loader (Top Level)
window.addEventListener('load', () => {
    setTimeout(() => {
        document.body.classList.add('page-loaded');
        
        // Initialize AOS after loader disappears
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 50
            });
        }
    }, 500);
});

// 2. Navigation & Theme Logic
document.addEventListener('DOMContentLoaded', () => {
    // Sticky Navbar & Transparent to Solid
    const header = document.getElementById('navbar');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Theme Toggle Logic
    const themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
        const iconMoon = themeBtn.querySelector('.icon-moon');
        const iconSun = themeBtn.querySelector('.icon-sun');

        function updateIcons(theme) {
            if (theme === 'dark') {
                iconMoon.style.display = 'none';
                iconSun.style.display = 'inline';
            } else {
                iconMoon.style.display = 'inline';
                iconSun.style.display = 'none';
            }
        }

        updateIcons(document.documentElement.getAttribute('data-theme'));

        themeBtn.addEventListener('click', () => {
            let currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('lumina_theme', newTheme);
            updateIcons(newTheme);
        });
    }
});