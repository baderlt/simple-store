<style>
    html,
    body {
        font-family: "Jost", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        font-size: 16px;
        line-height: 1.6;
        font-weight: 400;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body,
    button,
    input,
    textarea,
    select {
        font-family: inherit !important;
    }

    html[lang="ar"],
    html[dir="rtl"],
    html[lang="ar"] body,
    html[dir="rtl"] body,
    body[dir="rtl"],
    [dir="rtl"] {
        font-family: "Tajawal", "Noto Sans Arabic", system-ui, sans-serif !important;
        line-height: 1.7;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    h1 {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 800;
    }

    h2 {
        font-size: clamp(1.375rem, 2.4vw, 1.75rem);
    }

    h3 {
        font-size: clamp(1.125rem, 2vw, 1.375rem);
    }

    p,
    li {
        line-height: 1.6;
    }

    label {
        font-size: 0.9375rem;
        line-height: 1.45;
        font-weight: 500;
    }

    input,
    textarea,
    select {
        font-size: 1rem;
        line-height: 1.45;
    }

    button,
    [type="button"],
    [type="submit"],
    .btn,
    .button {
        font-size: 0.9375rem;
        line-height: 1.2;
        font-weight: 600;
    }

    .card-product .product-content {
        line-height: 1.45;
    }

    .card-product h3 {
        min-height: 2.8rem;
        font-size: clamp(0.98rem, 1.5vw, 1.125rem);
        line-height: 1.38;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .card-product h3 a {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }

    .card-product .product-content > div:first-child a {
        font-size: 0.75rem;
        line-height: 1.25;
        font-weight: 600;
        letter-spacing: 0.045em;
    }

    .card-product [data-card-final-price] {
        font-size: clamp(1.05rem, 2vw, 1.25rem);
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .card-product [data-card-base-price] {
        font-size: 0.875rem;
        line-height: 1.2;
        font-weight: 500;
    }

    .card-product .add-to-pack-btn {
        min-height: 2.75rem;
        font-size: clamp(0.92rem, 1.45vw, 1rem);
        line-height: 1.18;
        font-weight: 600;
        letter-spacing: 0.005em;
    }

    .product-detail-title {
        font-size: clamp(1.875rem, 3.5vw, 2.35rem);
        line-height: 1.18;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .variant-option {
        font-size: 0.95rem;
        line-height: 1.25;
        font-weight: 600;
    }

    .purchase-action-button {
        font-size: 1rem;
        line-height: 1.2;
        font-weight: 700;
    }

    .product-form-section {
        line-height: 1.55;
    }

    .product-form-section input,
    .product-form-section select,
    .product-form-section textarea {
        font-size: 1rem;
        line-height: 1.45;
    }

    .product-form-section label {
        font-size: 0.95rem;
        line-height: 1.45;
        font-weight: 600;
    }

    @media (max-width: 639px) {
        html,
        body {
            font-size: 15px;
        }

        h1 {
            font-size: clamp(1.5rem, 7vw, 1.875rem);
        }

        h2 {
            font-size: clamp(1.25rem, 5.5vw, 1.625rem);
        }

        h3 {
            font-size: clamp(1rem, 4.8vw, 1.25rem);
        }

        .card-product h3 {
            min-height: 2.65rem;
            font-size: 1rem;
            line-height: 1.35;
        }

        .card-product .product-content > div:first-child a {
            font-size: 0.7rem;
        }

        .card-product [data-card-final-price] {
            font-size: 1.08rem;
        }

        .card-product .add-to-pack-btn {
            min-height: 2.65rem;
            font-size: 0.92rem;
        }

        .product-detail-title {
            font-size: clamp(1.55rem, 7.5vw, 1.95rem);
            line-height: 1.2;
        }

        .purchase-action-button {
            font-size: 0.95rem;
        }
    }
</style>
