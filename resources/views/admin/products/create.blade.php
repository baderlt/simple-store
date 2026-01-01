@extends('admin.layouts.app')

@section('title', 'Ajouter un Produit - Admin')
@section('header', 'Ajouter un Produit')
@section('subheader', 'Créer un nouveau produit dans le catalogue')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl shadow">
                        <i class="fas fa-box text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Nouveau Produit</h2>
                        <p class="text-gray-600">Remplissez les informations ci-dessous pour ajouter un produit</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-500">Tous les champs marqués d'un</span>
                    <span class="text-red-500 font-bold">*</span>
                    <span class="text-sm font-medium text-gray-500">sont obligatoires</span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            
            <div class="p-8">
                <!-- Informations de base -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-green-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Informations de base</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Nom du produit -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Nom du Produit
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="ex: Paracétamol 500mg"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('name')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Catégorie
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tags text-gray-400"></i>
                                </div>
                                <select name="category_id" 
                                        required
                                        class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white cursor-pointer transition-all duration-200 @error('category_id') border-red-500 ring-2 ring-red-200 @enderror">
                                    <option value="" disabled selected>Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            @error('category_id')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Prix -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Prix (DH)
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-money-bill-wave text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="price" 
                                       value="{{ old('price') }}" 
                                       step="0.01" 
                                       min="0" 
                                       required
                                       placeholder="0.00"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('price') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">DH</span>
                                </div>
                            </div>
                            @error('price')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations d'inventaire -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-blue-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Inventaire</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- SKU -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Référence (SKU)
                                <span class="text-xs font-normal text-gray-500 block mt-1">Identifiant unique du produit</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-barcode text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="sku" 
                                       value="{{ old('sku') }}"
                                       placeholder="ex: PROD-001"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('sku') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('sku')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Quantité en stock -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Quantité en Stock
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-boxes text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="stock_quantity" 
                                       value="{{ old('stock_quantity', 0) }}" 
                                       min="0" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('stock_quantity') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('stock_quantity')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Alerte stock faible -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Alerte Stock Faible
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-exclamation-triangle text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="low_stock_alert" 
                                       value="{{ old('low_stock_alert', 10) }}" 
                                       min="0" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('low_stock_alert') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">unités</span>
                                </div>
                            </div>
                            @error('low_stock_alert')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Une alerte sera envoyée lorsque le stock atteindra ce niveau
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-purple-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Description</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Description du produit
                            <span class="text-xs font-normal text-gray-500 block mt-1">
                                Décrivez le produit, ses caractéristiques, indications, etc.
                            </span>
                        </label>
                        <div class="relative">
                            <textarea name="description" 
                                      rows="6"
                                      placeholder="Saisissez une description détaillée du produit..."
                                      id="description"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <div>
                                @error('description')
                                    <div class="flex items-center text-red-600 text-sm">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="text-xs text-gray-500">
                                <span id="charCount">{{ strlen(old('description', '')) }}</span> caractères
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-amber-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Images</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-200 cursor-pointer"
                             onclick="document.getElementById('imageUpload').click()"
                             id="dropZone">
                            <div class="max-w-sm mx-auto">
                                <div class="bg-gradient-to-r from-green-100 to-emerald-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-green-500 text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-700 mb-2">Glissez-déposez vos images ici</h4>
                                <p class="text-gray-500 text-sm mb-4">ou cliquez pour parcourir vos fichiers</p>
                                <div class="flex items-center justify-center space-x-2 text-sm">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">JPG</span>
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">PNG</span>
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">WEBP</span>
                                </div>
                            </div>
                            <input type="file" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   id="imageUpload"
                                   class="hidden"
                                   onchange="previewImages(event)">
                        </div>
                        
                        <!-- Image preview container -->
                        <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
                            <!-- Preview will be inserted here -->
                        </div>
                        
                        <p class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Vous pouvez sélectionner jusqu'à 10 images. La première sera l'image principale. Taille maximale : 5MB par image.
                        </p>
                        
                        @error('images.*')
                            <div class="flex items-center text-red-600 text-sm mt-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Options -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-indigo-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Options</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Produit Actif -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1" 
                                               id="is_active"
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                                    </label>
                                    <div>
                                        <label for="is_active" class="font-semibold text-gray-700 cursor-pointer">Produit Actif</label>
                                        <p class="text-sm text-gray-500 mt-1">Le produit sera visible sur le site</p>
                                    </div>
                                </div>
                                <div id="is_active_icon" class="{{ old('is_active', true) ? 'text-green-500' : 'text-gray-400' }}">
                                    <i class="fas fa-toggle-{{ old('is_active', true) ? 'on' : 'off' }} text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Produit en Vedette -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-amber-300 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="is_featured" 
                                               value="1" 
                                               id="is_featured"
                                               {{ old('is_featured') ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-all duration-200"></div>
                                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                                    </label>
                                    <div>
                                        <label for="is_featured" class="font-semibold text-gray-700 cursor-pointer">Produit en Vedette</label>
                                        <p class="text-sm text-gray-500 mt-1">Mettre en avant sur la page d'accueil</p>
                                    </div>
                                </div>
                                <div id="is_featured_icon" class="{{ old('is_featured') ? 'text-amber-500' : 'text-gray-400' }}">
                                    <i class="fas fa-star text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-history mr-2"></i>
                            <span class="text-sm">Tous les changements seront enregistrés</span>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.products.index') }}" 
                               class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer le produit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript pour les fonctionnalités améliorées -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur de caractères pour la description
    const description = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    if (description && charCount) {
        charCount.textContent = description.value.length;
        
        description.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Gestion du drag and drop pour les images
    const dropZone = document.getElementById('dropZone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropZone.classList.add('border-green-500', 'bg-green-50');
    }
    
    function unhighlight() {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('imageUpload').files = files;
        previewImages({ target: { files: files } });
    }
    
    // Toggle checkbox icons initialization
    updateCheckboxIcon('is_active', '{{ old('is_active', true) ? "true" : "false" }}' === 'true');
    updateCheckboxIcon('is_featured', '{{ old('is_featured') ? "true" : "false" }}' === 'true');
    
    // Add event listeners for checkboxes
    document.getElementById('is_active')?.addEventListener('change', function() {
        updateCheckboxIcon('is_active', this.checked);
    });
    
    document.getElementById('is_featured')?.addEventListener('change', function() {
        updateCheckboxIcon('is_featured', this.checked);
    });
});

function updateCheckboxIcon(checkboxId, isChecked) {
    const icon = document.getElementById(`${checkboxId}_icon`);
    if (!icon) return;
    
    if (checkboxId === 'is_active') {
        if (isChecked) {
            icon.innerHTML = '<i class="fas fa-toggle-on text-2xl text-green-500"></i>';
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-green-500');
        } else {
            icon.innerHTML = '<i class="fas fa-toggle-off text-2xl text-gray-400"></i>';
            icon.classList.remove('text-green-500');
            icon.classList.add('text-gray-400');
        }
    } else if (checkboxId === 'is_featured') {
        if (isChecked) {
            icon.innerHTML = '<i class="fas fa-star text-2xl text-amber-500"></i>';
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-amber-500');
        } else {
            icon.innerHTML = '<i class="fas fa-star text-2xl text-gray-400"></i>';
            icon.classList.remove('text-amber-500');
            icon.classList.add('text-gray-400');
        }
    }
}

// Prévisualisation des images
function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = event.target.files;
    const maxFiles = 10;
    
    if (files.length > maxFiles) {
        alert(`Vous ne pouvez sélectionner que ${maxFiles} images maximum.`);
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Vérifier la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert(`L'image "${file.name}" dépasse la taille maximale de 5MB`);
            continue;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('div');
            img.className = 'relative group';
            img.innerHTML = `
                <div class="aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                    <img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">
                    ${i === 0 ? '<div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">Principal</div>' : ''}
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button type="button" onclick="removeImage(${i})" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 truncate">${file.name}</p>
            `;
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(file);
    }
}

// Supprimer une image de la prévisualisation
function removeImage(index) {
    const input = document.getElementById('imageUpload');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.splice(index, 1);
    
    files.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
    
    // Recharger la prévisualisation
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files.length > 0) {
        previewImages({ target: { files: input.files } });
    }
}

// Validation du formulaire
document.getElementById('productForm')?.addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            
            // Ajouter un message d'erreur si pas déjà présent
            if (!field.nextElementSibling?.classList.contains('text-red-600')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center text-red-600 text-sm mt-1';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Ce champ est obligatoire';
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        
        // Scroll vers le premier champ en erreur
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
        
        // Supprimer le message d'erreur
        const errorMsg = this.nextElementSibling;
        if (errorMsg?.classList.contains('text-red-600')) {
            errorMsg.remove();
        }
    });
});
</script>

<style>
/* Animation pour les boutons */
button, a {
    transition: all 0.2s ease;
}

/* Style pour le placeholder */
::-webkit-input-placeholder {
    color: #9CA3AF;
    font-style: italic;
}

/* Style pour le scrollbar dans textarea */
textarea::-webkit-scrollbar {
    width: 8px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection