<style>
    :root {
        --admin-sidebar-width: 16rem;
        --admin-header-height: 0px;
        --product-tabs-height: 64px;
        --product-action-bar-height: 96px;
    }

    .product-editor-shell {
        width: min(100%, 1400px);
        margin-inline: auto;
        padding-inline: 1rem;
        padding-bottom: calc(var(--product-action-bar-height) + 40px);
        --product-editor-gutter: 1rem;
    }

    .product-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
        overflow: visible;
    }

    .product-form-header {
        padding: 1rem var(--product-editor-gutter) !important;
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e5e7eb;
        border-radius: 1.25rem 1.25rem 0 0;
    }

    .product-form-body {
        padding: var(--product-editor-gutter);
        padding-bottom: calc(var(--product-action-bar-height) + 40px);
    }

    .product-form-nav {
        position: sticky;
        top: var(--admin-header-height);
        z-index: 20;
        display: flex;
        align-items: center;
        gap: .5rem;
        overflow-x: auto;
        padding: .85rem var(--product-editor-gutter);
        border-bottom: 1px solid #e5e7eb;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(12px);
        scrollbar-width: none;
    }

    .product-form-nav::-webkit-scrollbar {
        display: none;
    }

    .product-form-nav a {
        display: inline-flex;
        min-height: 2.5rem;
        flex: 0 0 auto;
        align-items: center;
        gap: .5rem;
        border: 1px solid #e5e7eb;
        border-radius: .75rem;
        padding: .55rem .8rem;
        color: #475569;
        background: #fff;
        font-size: .875rem;
        line-height: 1.2;
        font-weight: 600;
        transition: border-color .2s ease, color .2s ease, background-color .2s ease;
    }

    .product-form-nav a:hover,
    .product-form-nav a.is-active {
        border-color: var(--primary-color, #16a34a);
        color: var(--primary-color, #16a34a);
        background: color-mix(in srgb, var(--primary-color, #16a34a) 8%, white);
    }

    .product-form-section {
        scroll-margin-top: calc(var(--admin-header-height) + var(--product-tabs-height) + 20px);
        margin-bottom: 1.25rem !important;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        background: #fff;
    }

    .product-form-section > .product-form-section-heading {
        margin-bottom: 1.25rem !important;
        padding-bottom: .9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .product-form-actions {
        position: fixed;
        bottom: 0;
        left: var(--admin-sidebar-width);
        right: 0;
        z-index: 25;
        min-height: var(--product-action-bar-height);
        padding: .85rem 1.5rem max(.85rem, env(safe-area-inset-bottom));
        border-top: 1px solid #e5e7eb;
        background: rgba(255, 255, 255, .98);
        backdrop-filter: blur(14px);
        box-shadow: 0 -10px 30px rgba(15, 23, 42, .07);
    }

    [dir="rtl"] .product-form-actions {
        right: var(--admin-sidebar-width);
        left: 0;
    }

    .product-form-actions > div {
        width: min(100%, 1400px);
        margin-inline: auto;
    }

    .product-form-section input,
    .product-form-section select,
    .product-form-section textarea {
        font-size: 16px;
    }

    .product-form-section input:focus,
    .product-form-section select:focus,
    .product-form-section textarea:focus {
        border-color: var(--primary-color, #16a34a) !important;
        --tw-ring-color: color-mix(in srgb, var(--primary-color, #16a34a) 28%, transparent) !important;
    }

    @media (min-width: 640px) {
        .product-editor-shell {
            padding-inline: 1.5rem;
            --product-editor-gutter: 1.5rem;
        }

        .product-form-header {
            padding-block: 1.25rem !important;
        }

        .product-form-section {
            padding: 1.5rem;
            margin-bottom: 1.25rem !important;
        }
    }

    @media (max-width: 1023px) {
        .product-form-actions,
        [dir="rtl"] .product-form-actions {
            right: 0;
            left: 0;
        }
    }

    @media (max-width: 639px) {
        :root {
            --product-action-bar-height: 172px;
        }

        .product-editor-shell {
            padding-inline: 0;
        }

        .product-form-nav {
            top: var(--admin-header-height);
            gap: .45rem;
            padding-block: .7rem;
        }

        .product-form-card {
            border-right: 0;
            border-left: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .product-form-header {
            padding: 1rem !important;
            border-radius: 0;
        }

        .product-form-header .product-form-header-meta {
            display: none;
        }

        .product-form-section {
            padding: 1rem;
            border-radius: .875rem;
        }

        .product-form-section .grid {
            gap: 1rem;
        }

        #product-options > .grid > div {
            padding: 1rem;
        }

        #dropZone {
            padding: 1.25rem !important;
        }

        .product-form-actions a,
        .product-form-actions button {
            width: 100%;
            min-height: 3rem;
        }

        .product-form-actions {
            right: 0;
            left: 0;
            padding-inline: 1rem;
        }

        [dir="rtl"] .product-form-actions {
            right: 0;
            left: 0;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const links = [...document.querySelectorAll('.product-form-nav a')];
    const sections = links
        .map(link => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);
    const scrollContainer = document.querySelector('main.overflow-y-auto') || document.scrollingElement;
    const nav = document.querySelector('.product-form-nav');

    const scrollToSection = section => {
        if (!section || !scrollContainer) return;

        const containerRect = scrollContainer.getBoundingClientRect();
        const sectionRect = section.getBoundingClientRect();
        const styles = getComputedStyle(section);
        const scrollMarginTop = parseFloat(styles.scrollMarginTop) || 0;
        const navHeight = nav?.offsetHeight || 64;
        const offset = Math.max(scrollMarginTop, navHeight + 20);
        const nextTop = scrollContainer.scrollTop + sectionRect.top - containerRect.top - offset;

        scrollContainer.scrollTo({ top: Math.max(nextTop, 0), behavior: 'smooth' });
    };

    links.forEach(link => {
        link.addEventListener('click', event => {
            const section = document.querySelector(link.getAttribute('href'));
            if (!section) return;
            event.preventDefault();
            scrollToSection(section);
        });
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            const visible = entries
                .filter(entry => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
            if (!visible) return;
            links.forEach(link => link.classList.toggle('is-active', link.getAttribute('href') === `#${visible.target.id}`));
        }, { rootMargin: '-20% 0px -65% 0px', threshold: [0.05, 0.25, 0.5] });

        sections.forEach(section => observer.observe(section));
    }

    const firstInvalid = document.querySelector('.product-form-section .border-red-500');
    if (firstInvalid) {
        scrollToSection(firstInvalid.closest('.product-form-section'));
    }
});
</script>
