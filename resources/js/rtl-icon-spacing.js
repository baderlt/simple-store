const RTL_ICON_CLASS = 'rtl-leading-text-icon';
const FONT_AWESOME_ICON_SELECTOR = [
    'i.fas',
    'i.far',
    'i.fab',
    'i.fa-solid',
    'i.fa-regular',
    'i.fa-brands',
].join(',');

function visibleLabelAfter(icon) {
    let sibling = icon.nextSibling;

    while (sibling) {
        if (sibling.nodeType === Node.TEXT_NODE && sibling.textContent.trim() !== '') {
            return sibling;
        }

        if (sibling.nodeType === Node.ELEMENT_NODE) {
            const element = sibling;

            if (
                element.matches('script, style, template, [hidden], [aria-hidden="true"], .sr-only')
                || element.matches(FONT_AWESOME_ICON_SELECTOR)
            ) {
                sibling = sibling.nextSibling;
                continue;
            }

            const style = window.getComputedStyle(element);
            const isOutOfFlow = style.position === 'absolute' || style.position === 'fixed';
            const isHidden = style.display === 'none' || style.visibility === 'hidden';

            if (!isOutOfFlow && !isHidden && element.textContent.trim() !== '') {
                return element;
            }
        }

        sibling = sibling.nextSibling;
    }

    return null;
}

function nodeRect(node) {
    if (node.nodeType === Node.ELEMENT_NODE) {
        return node.getBoundingClientRect();
    }

    const range = document.createRange();
    range.selectNodeContents(node);

    return range.getBoundingClientRect();
}

function hasVisibleHorizontalGap(icon, label) {
    const iconRect = icon.getBoundingClientRect();
    const labelRect = nodeRect(label);

    if (iconRect.width === 0 || labelRect.width === 0) {
        return true;
    }

    const gap = Math.max(
        labelRect.left - iconRect.right,
        iconRect.left - labelRect.right,
        0
    );

    return gap >= 6;
}

function syncIcon(icon) {
    icon.classList.remove(RTL_ICON_CLASS);

    const label = visibleLabelAfter(icon);

    if (label && !hasVisibleHorizontalGap(icon, label)) {
        icon.classList.add(RTL_ICON_CLASS);
    }
}

function syncIconsWithin(root) {
    if (!root || (root.nodeType !== Node.ELEMENT_NODE && root.nodeType !== Node.DOCUMENT_NODE)) {
        return;
    }

    if (root.nodeType === Node.ELEMENT_NODE && root.matches(FONT_AWESOME_ICON_SELECTOR)) {
        syncIcon(root);
    }

    root.querySelectorAll(FONT_AWESOME_ICON_SELECTOR).forEach(syncIcon);
}

export function initializeRtlIconSpacing() {
    if (document.documentElement.dir !== 'rtl' || !document.body) {
        return;
    }

    syncIconsWithin(document);
    document.fonts?.ready.then(() => syncIconsWithin(document));

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'characterData') {
                syncIconsWithin(mutation.target.parentElement);
                return;
            }

            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    syncIconsWithin(node.parentElement);
                    return;
                }

                syncIconsWithin(node);
            });

            syncIconsWithin(mutation.target);
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        characterData: true,
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => syncIconsWithin(document), 100);
    }, { passive: true });
}
