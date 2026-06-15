function t(key, replacements = {}) {
    const messages = window.appTranslations || {};
    let message = messages[key] || key;
    Object.entries(replacements).forEach(([name, value]) => {
        message = message.replace(`:${name}`, value);
    });
    return message;
}

    // Change main image when thumbnail is clicked
    function changeMainImage(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.style.opacity = '0.5';
        setTimeout(() => {
            mainImage.src = src;
            mainImage.style.opacity = '1';
        }, 200);
    }

    // Update quantity function
    function updateQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        if (!quantityInput) return true;

        const minimumMessage = document.getElementById('quantityMinimumMessage');
        let currentValue = parseInt(quantityInput.value, 10);
        const maxStock = parseInt(quantityInput.max);
        const minValue = parseInt(quantityInput.min);
        if (Number.isNaN(currentValue)) currentValue = minValue;

        // Calculate new value
        let newValue = currentValue + change;
        let isValid = true;
        
        // Validate bounds
        if (newValue < minValue) {
            isValid = false;
            if (minimumMessage) {
                minimumMessage.textContent = t('product.minimum_quantity_required', {
                    quantity: minValue,
                    unit: document.getElementById('quantityUnit')?.textContent?.trim() || ''
                });
                minimumMessage.classList.remove('hidden');
            }
            newValue = minValue;
        } else if (newValue > maxStock) {
            newValue = maxStock;
            alert(t('product.max_quantity_units', { stock: maxStock }));
        } else if (minimumMessage) {
            minimumMessage.textContent = '';
            minimumMessage.classList.add('hidden');
        }
        
        // Update input value
        quantityInput.value = newValue;
        
        // Update hidden inputs in forms
        document.getElementById('formQuantity').value = newValue;
        document.getElementById('buyNowQuantity').value = newValue;

        return isValid;
    }

    // Share product function
    function shareProduct() {
        const productName = "{{ $product->name }}";
        const productUrl = window.location.href;
        const shareText = `${t('product.share_discover', { product: productName })} ${productUrl}`;
        
        if (navigator.share) {
            // Use Web Share API if available
            navigator.share({
                title: productName,
                text: t('product.share_discover', { product: productName }),
                url: productUrl,
            })
            .then(() => console.log(t('product.share_success')))
            .catch((error) => console.log(t('product.share_error') + ':', error));
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(shareText)
                .then(() => {
                    alert(t('product.link_copied'));
                })
                .catch(err => {
                    console.error(t('product.copy_error') + ':', err);
                    prompt(t('product.copy_link'), shareText);
                });
        }
    }
    
    // Tab switching function
    function switchTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('[id$="-content"]');
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        const tabs = document.querySelectorAll('[id^="tab-"]');
        tabs.forEach(tab => {
            tab.classList.remove('border-emerald-500', 'text-emerald-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected content
        const selectedContent = document.getElementById(`${tabName}-content`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        
        // Activate selected tab
        const selectedTab = document.getElementById(`tab-${tabName}`);
        if (selectedTab) {
            selectedTab.classList.remove('border-transparent', 'text-gray-500');
            selectedTab.classList.add('border-emerald-500', 'text-emerald-600');
        }
    }

    // Initialize first tab on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tabs
        switchTab('description');
        
        // Add event listener for quantity input changes
        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                const max = parseInt(this.max) || 999;
                const min = parseInt(this.min) || 1;
                
                if (isNaN(value)) value = min;
                if (value < min) value = min;
                if (value > max) value = max;
                
                this.value = value;
                document.getElementById('formQuantity').value = value;
                document.getElementById('buyNowQuantity').value = value;
            });
        }
        
        // Add to cart form handling
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                const quantity = parseInt(document.getElementById('quantity').value);
                const maxStock = parseInt(document.getElementById('quantity').max);
                
                if (quantity > maxStock) {
                    e.preventDefault();
                    alert(t('product.only_stock_available', { stock: maxStock }));
                    return false;
                }
                
                // Optional: Add loading state
                const button = this.querySelector('button[type="submit"]');
                button.innerHTML = `<i class="fas fa-spinner fa-spin mr-3"></i>${t('cart.adding')}`;
                button.disabled = true;
            });
        }
        
        // Buy Now form handling
        const buyNowForm = document.getElementById('buyNowForm');
        if (buyNowForm) {
            buyNowForm.addEventListener('submit', function(e) {
                const quantity = parseInt(document.getElementById('quantity').value);
                const maxStock = parseInt(document.getElementById('quantity').max);
                
                if (quantity > maxStock) {
                    e.preventDefault();
                    alert(t('product.only_stock_available', { stock: maxStock }));
                    return false;
                }
                
                const minimumQuantity = parseInt(document.getElementById('quantity').min) || 1;
                if (quantity < minimumQuantity) {
                    e.preventDefault();
                    updateQuantity(0);
                    return false;
                }
                
                // Optional: Add loading state
                const button = this.querySelector('button[type="submit"]');
                button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${t('product.redirecting_checkout')}`;
                button.disabled = true;
            });
        }
    });
