<style>
    .product-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
        overflow: visible;
    }

    .product-form-header {
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e5e7eb;
        border-radius: 1.25rem 1.25rem 0 0;
    }

    .product-form-nav {
        position: sticky;
        top: 0;
        z-index: 20;
        display: flex;
        gap: .5rem;
        overflow-x: auto;
        padding: .75rem 1rem;
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
        font-size: .78rem;
        font-weight: 700;
        transition: border-color .2s ease, color .2s ease, background-color .2s ease;
    }

    .product-form-nav a:hover,
    .product-form-nav a.is-active {
        border-color: var(--primary-color, #16a34a);
        color: var(--primary-color, #16a34a);
        background: color-mix(in srgb, var(--primary-color, #16a34a) 8%, white);
    }

    .product-form-section {
        scroll-margin-top: 5.5rem;
        margin-bottom: 1rem !important;
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
        position: sticky;
        bottom: 0;
        z-index: 25;
        margin: 0 -1rem -1rem;
        padding: .9rem 1rem;
        border-top: 1px solid #e5e7eb;
        background: rgba(255, 255, 255, .97);
        backdrop-filter: blur(14px);
        box-shadow: 0 -10px 30px rgba(15, 23, 42, .07);
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
        .product-form-nav {
            padding-inline: 1.5rem;
        }

        .product-form-section {
            padding: 1.5rem;
            margin-bottom: 1.25rem !important;
        }

        .product-form-actions {
            margin: 0 -2rem -2rem;
            padding: 1rem 2rem;
        }
    }

    @media (max-width: 639px) {
        .product-form-nav {
            top: 4rem;
        }

        .product-form-section {
            scroll-margin-top: 9rem;
        }

        .product-form-card {
            margin-inline: -.75rem;
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
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const links = [...document.querySelectorAll('.product-form-nav a')];
    const sections = links
        .map(link => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    links.forEach(link => {
        link.addEventListener('click', event => {
            const section = document.querySelector(link.getAttribute('href'));
            if (!section) return;
            event.preventDefault();
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
    firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>
