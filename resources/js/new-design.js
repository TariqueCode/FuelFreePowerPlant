const navToggle = document.querySelector('[data-new-nav-toggle]');
const navLinks = document.querySelector('[data-new-nav-links]');

if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
        const open = navLinks.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', String(open));
        if (open) navLinks.querySelector('a,button')?.focus();
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navLinks.classList.contains('is-open')) {
            navLinks.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.focus();
        }
    });
}

document.querySelectorAll('[data-new-nav-dropdown]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const group = toggle.closest('.new-nav-group');
        const menu = group?.querySelector('.new-nav-dropdown');
        if (!menu) return;
        const open = toggle.getAttribute('aria-expanded') === 'true';
        document.querySelectorAll('.new-nav-group [data-new-nav-dropdown]').forEach((other) => {
            if (other !== toggle) {
                other.setAttribute('aria-expanded', 'false');
                other.closest('.new-nav-group')?.querySelector('.new-nav-dropdown')?.setAttribute('hidden', '');
            }
        });
        toggle.setAttribute('aria-expanded', String(!open));
        menu.toggleAttribute('hidden', open);
    });
});

document.addEventListener('click', (event) => {
    if (event.target.closest('.new-nav-group')) return;
    document.querySelectorAll('.new-nav-group [data-new-nav-dropdown]').forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.closest('.new-nav-group')?.querySelector('.new-nav-dropdown')?.setAttribute('hidden', '');
    });
});

const revealItems = document.querySelectorAll('.new-reveal');
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
} else if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, instance) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                instance.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -30px' });
    revealItems.forEach((item) => observer.observe(item));
} else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
}
