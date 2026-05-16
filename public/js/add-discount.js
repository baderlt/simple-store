function t(key, replacements = {}) {
    const messages = window.appTranslations || {};
    let message = messages[key] || key;
    Object.entries(replacements).forEach(([name, value]) => {
        message = message.replace(`:${name}`, value);
    });
    return message;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les événements
    initEventListeners();
    
    // Initialiser les icônes de checkbox
    updateCheckboxIcon('is_active', document.getElementById('is_active').checked);
    
    // Initialiser les produits si une sélection existe déjà
    const productSelect = document.getElementById('productSelect');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (selectedOption && selectedOption.value) {
        selectProduct(selectedOption.value);
    }
});

// Initialiser les événements
function initEventListeners() {
    // Recherche de produit
    const searchInput = document.getElementById('productSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const productList = document.getElementById('productList');
            
            if (searchTerm === '') {
                productList.classList.add('hidden');
                return;
            }
            
            // Filtrer les produits
            filterProducts(searchTerm);
            productList.classList.remove('hidden');
        });
        
        // Cacher la liste quand on clique en dehors
        document.addEventListener('click', function(e) {
            const productList = document.getElementById('productList');
            const searchInput = document.getElementById('productSearch');
            
            if (!productList.contains(e.target) && !searchInput.contains(e.target)) {
                productList.classList.add('hidden');
            }
        });
        
        // Focus sur le champ de recherche
        searchInput.addEventListener('focus', function() {
            if (this.value.trim() !== '') {
                const productList = document.getElementById('productList');
                productList.classList.remove('hidden');
            }
        });
    }
    
    // Pourcentage de réduction
    document.getElementById('discountPercentage').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('fixedAmount').value = '';
        }
        calculateDiscount();
        updateSummary();
    });
    
    // Montant fixe
    document.getElementById('fixedAmount').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discountPercentage').value = '';
        }
        calculateDiscount();
        updateSummary();
    });
    
    // Dates
    document.querySelectorAll('input[type="datetime-local"]').forEach(input => {
        input.addEventListener('change', function() {
            calculateDuration();
            updateSummary();
        });
    });
    
    // Checkbox active
    document.getElementById('is_active').addEventListener('change', function() {
        updateCheckboxIcon('is_active', this.checked);
        updateSummary();
    });
}

// Filtrer et afficher les produits
function filterProducts(searchTerm) {
    const productSelect = document.getElementById('productSelect');
    const productList = document.getElementById('productList');
    const options = productSelect.querySelectorAll('option');
    
    // Vider la liste actuelle
    productList.innerHTML = '';
    
    let hasResults = false;
    
    options.forEach(option => {
        if (option.value === '') return; // Ignorer l'option placeholder
        
        const name = option.dataset.name || '';
        const sku = option.dataset.sku || '';
        const category = option.dataset.category || '';
        const text = option.textContent.toLowerCase();
        
        // Recherche dans le nom, SKU, catégorie et texte complet
        const matches = name.includes(searchTerm) || 
                       sku.includes(searchTerm) || 
                       category.includes(searchTerm) || 
                       text.includes(searchTerm);
        
        if (matches) {
            hasResults = true;
            
            // Créer un élément de liste
            const listItem = document.createElement('div');
            listItem.className = 'p-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors';
            listItem.innerHTML = `
                <div class="font-medium text-gray-800">${option.textContent.split(' - ')[0]}</div>
                <div class="text-sm text-gray-600 mt-1">
                    ${option.dataset.sku ? `<span class="mr-3">${option.dataset.sku}</span>` : ''}
                    <span class="mr-3">${parseFloat(option.dataset.price || 0).toFixed(2)} DH</span>
                    ${option.dataset.category ? `<span>${option.dataset.category}</span>` : ''}
                </div>
            `;
            
            // Ajouter l'événement de clic
            listItem.addEventListener('click', function() {
                selectProduct(option.value);
                document.getElementById('productList').classList.add('hidden');
                document.getElementById('productSearch').value = '';
            });
            
            productList.appendChild(listItem);
        }
    });
    
    // Si aucun résultat
    if (!hasResults) {
        const noResults = document.createElement('div');
        noResults.className = 'p-4 text-center text-gray-500';
        noResults.textContent = t('products.none_found');
        productList.appendChild(noResults);
    }
}

// Sélectionner un produit
function selectProduct(productId) {
    const productSelect = document.getElementById('productSelect');
    const selectedOption = productSelect.querySelector(`option[value="${productId}"]`);
    
    if (!selectedOption) return;
    
    // Mettre à jour le select caché
    productSelect.value = productId;
    
    // Afficher le produit sélectionné
    const selectedProductDiv = document.getElementById('selectedProduct');
    const selectedProductName = document.getElementById('selectedProductName');
    const selectedProductPrice = document.getElementById('selectedProductPrice');
    const selectedProductSKU = document.getElementById('selectedProductSKU');
    const selectedProductCategory = document.getElementById('selectedProductCategory');
    
    selectedProductName.textContent = selectedOption.textContent.split(' - ')[0];
    selectedProductPrice.textContent = parseFloat(selectedOption.dataset.price || 0).toFixed(2) + ' DH';
    selectedProductSKU.textContent = selectedOption.dataset.sku ? `${t('products.sku')}: ${selectedOption.dataset.sku}` : '';
    selectedProductCategory.textContent = selectedOption.dataset.category ? `${t('products.category')}: ${selectedOption.dataset.category}` : '';
    
    selectedProductDiv.classList.remove('hidden');
    
    // Calculer le discount
    calculateDiscount();
    updateProductInfo();
    updateSummary();
}

// Effacer la sélection du produit
function clearProductSelection() {
    const productSelect = document.getElementById('productSelect');
    const selectedProductDiv = document.getElementById('selectedProduct');
    const productInfo = document.getElementById('productInfo');
    
    productSelect.value = '';
    selectedProductDiv.classList.add('hidden');
    productInfo.classList.add('hidden');
    
    // Réinitialiser le résumé
    document.getElementById('summaryProduct').textContent = '-';
    document.getElementById('summaryOriginalPrice').textContent = '- DH';
    document.getElementById('summaryNewPrice').textContent = '- DH';
    document.getElementById('summarySavings').textContent = '- DH';
}

// Calculer le prix après réduction
function calculateDiscount() {
    const productSelect = document.getElementById('productSelect');
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    const productInfo = document.getElementById('productInfo');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value && (discountPercentage.value || fixedAmount.value)) {
        const originalPrice = parseFloat(selectedOption.dataset.price) || 0;
        let discountedPrice = originalPrice;
        let savings = 0;
        
        if (discountPercentage.value) {
            const discount = parseFloat(discountPercentage.value) / 100;
            savings = originalPrice * discount;
            discountedPrice = originalPrice - savings;
        } else if (fixedAmount.value) {
            savings = parseFloat(fixedAmount.value);
            discountedPrice = originalPrice - savings;
        }
        
        // S'assurer que le prix n'est pas négatif
        if (discountedPrice < 0) discountedPrice = 0;
        if (savings < 0) savings = 0;
        
        // Afficher les informations
        document.getElementById('originalPrice').textContent = originalPrice.toFixed(2) + ' DH';
        document.getElementById('discountedPrice').textContent = discountedPrice.toFixed(2) + ' DH';
        document.getElementById('savingsAmount').textContent = savings.toFixed(2) + ' DH d\'économie';
        productInfo.classList.remove('hidden');
    } else {
        productInfo.classList.add('hidden');
    }
}

// Calculer la durée de validité
function calculateDuration() {
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');
    const durationIndicator = document.getElementById('durationIndicator');
    
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const now = new Date();
        
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let statusText = '';
        let statusClass = '';
        
        if (start > now) {
            statusText = 'Programmée';
            statusClass = 'text-blue-600';
        } else if (end > now) {
            statusText = 'Active';
            statusClass = 'text-green-600';
        } else {
            statusText = 'Expirée';
            statusClass = 'text-red-600';
        }
        
        document.getElementById('durationText').textContent = `Durée : ${diffDays} jour(s)`;
        document.getElementById('dateRange').textContent = 
            `${start.toLocaleDateString('fr-FR')} → ${end.toLocaleDateString('fr-FR')}`;
        document.getElementById('discountStatus').textContent = statusText;
        document.getElementById('discountStatus').className = `font-bold ${statusClass}`;
        
        durationIndicator.classList.remove('hidden');
    } else {
        durationIndicator.classList.add('hidden');
    }
}

// Mettre à jour les informations du produit
function updateProductInfo() {
    const productSelect = document.getElementById('productSelect');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        // Afficher les informations dans le résumé
        const productName = selectedOption.textContent.split(' - ')[0];
        document.getElementById('summaryProduct').textContent = productName;
        document.getElementById('summaryOriginalPrice').textContent = 
            parseFloat(selectedOption.dataset.price || 0).toFixed(2) + ' DH';
    }
}

// Mettre à jour le résumé
function updateSummary() {
    const productSelect = document.getElementById('productSelect');
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');
    const isActive = document.getElementById('is_active');
    
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const originalPrice = parseFloat(selectedOption.dataset.price) || 0;
        let discountValue = 0;
        let newPrice = originalPrice;
        let savings = 0;
        
        if (discountPercentage.value) {
            discountValue = parseFloat(discountPercentage.value);
            savings = originalPrice * (discountValue / 100);
            newPrice = originalPrice - savings;
        } else if (fixedAmount.value) {
            savings = parseFloat(fixedAmount.value);
            discountValue = (savings / originalPrice) * 100;
            newPrice = originalPrice - savings;
        }
        
        // S'assurer que les valeurs ne sont pas négatives
        if (newPrice < 0) newPrice = 0;
        if (savings < 0) savings = 0;
        if (discountValue < 0) discountValue = 0;
        
        // Mettre à jour les valeurs du résumé
        document.getElementById('summaryDiscount').textContent = 
            discountPercentage.value ? `${discountValue.toFixed(2)}%` : 'Montant fixe';
        document.getElementById('summaryNewPrice').textContent = newPrice.toFixed(2) + ' DH';
        document.getElementById('summarySavings').textContent = savings.toFixed(2) + ' DH';
        
        // Validité
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            document.getElementById('summaryValidity').textContent = 
                `${start.toLocaleDateString('fr-FR')} → ${end.toLocaleDateString('fr-FR')}`;
        }
        
        // Statut
        document.getElementById('summaryStatus').textContent = isActive.checked ? 'Active' : 'Inactive';
        document.getElementById('summaryStatus').className = 
            isActive.checked ? 'font-bold text-green-600' : 'font-bold text-gray-600';
    }
}

// Mettre à jour l'icône de checkbox
function updateCheckboxIcon(checkboxId, isChecked) {
    const icon = document.getElementById(`${checkboxId}_icon`);
    if (!icon) return;
    
    if (isChecked) {
        icon.innerHTML = '<i class="fas fa-toggle-on text-2xl text-green-500"></i>';
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-green-500');
    } else {
        icon.innerHTML = '<i class="fas fa-toggle-off text-2xl text-gray-400"></i>';
        icon.classList.remove('text-green-500');
        icon.classList.add('text-gray-400');
    }
}

// Validation du formulaire
document.getElementById('discountForm')?.addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            
            if (!field.nextElementSibling?.classList.contains('text-red-600')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center text-red-600 text-sm mt-1';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> ${t('validation.required_field')}`;
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        }
    });
    
    // Vérifier que la date de fin est après la date de début
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');
    
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        
        if (end <= start) {
            isValid = false;
            alert(t('discounts.end_date_after_start'));
        }
    }
    
    // Vérifier qu'un produit est sélectionné
    const productSelect = document.getElementById('productSelect');
    if (!productSelect.value || productSelect.value === '') {
        isValid = false;
        alert(t('discounts.select_product'));
    }
    
    // Vérifier qu'un type de réduction est défini
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    if (!discountPercentage.value && !fixedAmount.value) {
        isValid = false;
        discountPercentage.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        alert(t('discounts.enter_discount_value'));
    }
    
    if (!isValid) {
        e.preventDefault();
        const firstError = this.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
});

// Supprimer les classes d'erreur lors de la saisie
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('input', function() {
        this.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        
        const errorMsg = this.nextElementSibling;
        if (errorMsg?.classList.contains('text-red-600')) {
            errorMsg.remove();
        }
    });
});