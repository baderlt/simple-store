@extends('admin.layouts.app')

@section('title', 'Ajouter un Produit - Admin')
@section('header', 'Ajouter un Produit')
@section('subheader', 'Créer un nouveau produit dans le catalogue')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-4 sm:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl shadow">
                        <i class="fas fa-box text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Nouveau Produit</h2>
                        <p class="text-gray-600 text-sm sm:text-base">Remplissez les informations ci-dessous pour ajouter un produit</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-gray-500">Tous les champs marqués d'un</span>
                    <span class="text-red-500 font-bold">*</span>
                    <span class="text-gray-500">sont obligatoires</span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            
            <div class="p-4 sm:p-8">
                <!-- Informations de base -->
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center mb-4 sm:mb-6">
                        <div class="w-1 h-6 sm:h-8 bg-green-500 rounded-full mr-3"></div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">Informations de base</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
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
                                       placeholder="ex: Miel de thym 250g"
                                       class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('name')
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
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
                                        class="w-full pl-10 pr-10 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white cursor-pointer transition-all duration-200 @error('category_id') border-red-500 ring-2 ring-red-200 @enderror">
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
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
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
                                       class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('price') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">DH</span>
                                </div>
                            </div>
                            @error('price')
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations d'inventaire -->
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center mb-4 sm:mb-6">
                        <div class="w-1 h-6 sm:h-8 bg-blue-500 rounded-full mr-3"></div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">Inventaire</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
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
                                       class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('sku') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('sku')
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
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
                                       class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('stock_quantity') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('stock_quantity')
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
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
                                       class="w-full pl-10 pr-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('low_stock_alert') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">unités</span>
                                </div>
                            </div>
                            @error('low_stock_alert')
                                <div class="flex items-center text-red-600 text-xs sm:text-sm mt-1">
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
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center mb-4 sm:mb-6">
                        <div class="w-1 h-6 sm:h-8 bg-purple-500 rounded-full mr-3"></div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">Description</h3>
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
                                      rows="4"
                                      placeholder="Saisissez une description détaillée du produit..."
                                      id="description"
                                      class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <div>
                                @error('description')
                                    <div class="flex items-center text-red-600 text-xs sm:text-sm">
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
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center mb-4 sm:mb-6">
                        <div class="w-1 h-6 sm:h-8 bg-amber-500 rounded-full mr-3"></div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">Images</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Zone de téléchargement mobile optimisée -->
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-4 sm:p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-200 cursor-pointer"
                             onclick="document.getElementById('multiImageUpload').click()"
                             id="dropZone">
                            <div class="max-w-sm mx-auto">
                                <div class="bg-gradient-to-r from-green-100 to-emerald-100 w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                    <i class="fas fa-cloud-upload-alt text-green-500 text-xl sm:text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-700 text-sm sm:text-base mb-2">Glissez-déposez vos images ici</h4>
                                <p class="text-gray-500 text-xs sm:text-sm mb-3 sm:mb-4">ou cliquez pour parcourir vos fichiers</p>
                                <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm">
                                    <span class="px-2 sm:px-3 py-1 bg-gray-100 rounded-full text-gray-600">JPG</span>
                                    <span class="px-2 sm:px-3 py-1 bg-gray-100 rounded-full text-gray-600">PNG</span>
                                    <span class="px-2 sm:px-3 py-1 bg-gray-100 rounded-full text-gray-600">WEBP</span>
                                </div>
                                <p class="text-xs sm:text-sm text-red-500 mt-2 sm:mt-3 font-medium" id="fileCountMessage">
                                    Maximum 10 images
                                </p>
                                <p class="text-xs text-emerald-600 mt-1">Les images sont automatiquement redimensionnées et compressées pour un chargement rapide.</p>
                            </div>
                            <input type="file" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   id="multiImageUpload"
                                   class="hidden"
                                   onchange="handleImageUpload(event)">
                        </div>
                        
                        <!-- Bouton d'ajout d'image unique pour mobile -->
                        <div class="block sm:hidden">
                            <button type="button" 
                                    onclick="document.getElementById('multiImageUpload').click()"
                                    class="w-full bg-blue-50 text-blue-600 border border-blue-200 rounded-xl py-3 px-4 font-medium hover:bg-blue-100 transition-colors flex items-center justify-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Ajouter une image
                            </button>

                        </div>
                        
                        <!-- Instructions et compteur -->
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-xl">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm text-gray-500 flex items-start sm:items-center">
                                        <i class="fas fa-info-circle mr-2 mt-0.5 sm:mt-0"></i>
                                        Sélectionnez jusqu'à 4 images. Cliquez sur ★ pour définir l'image principale.
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <span id="selectedCount">0</span> / 10 images
                                </div>
                            </div>
                            
                            <!-- Instructions détaillées pour mobile -->
                            <div class="mt-2 space-y-1 text-xs text-gray-600 sm:hidden">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-amber-500 mr-2 w-4"></i>
                                    <span>Cliquez sur ★ pour définir comme image principale</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-trash text-red-500 mr-2 w-4"></i>
                                    <span>Cliquez sur 🗑️ pour supprimer</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conteneur de prévisualisation -->
                        <div id="imagePreviewContainer" class="relative">
                            <div id="imagePreview" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                                <!-- Preview will be inserted here -->
                            </div>
                            
                            <!-- État vide -->
                            <div id="emptyState" class="flex items-center justify-center py-12 text-center">
                                <div>
                                    <i class="fas fa-images text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500 text-sm">Aucune image sélectionnée</p>
                                    <p class="text-gray-400 text-xs mt-1">Ajoutez des images pour les prévisualiser</p>
                                </div>
                            </div>
                            
                            <!-- Champs cachés pour gérer les images -->
                            <input type="hidden" name="image_order" id="imageOrder" value="">
                            <input type="hidden" name="primary_image_index" id="primaryImageIndex" value="0">
                        </div>
                        
                        @error('images.*')
                            <div class="flex items-center text-red-600 text-xs sm:text-sm mt-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        
                        @error('images')
                            <div class="flex items-center text-red-600 text-xs sm:text-sm mt-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Options -->
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center mb-4 sm:mb-6">
                        <div class="w-1 h-6 sm:h-8 bg-indigo-500 rounded-full mr-3"></div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800">Options</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Produit Actif -->
                        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200 hover:border-green-300 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1" 
                                               id="is_active"
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-5 sm:w-12 sm:h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                        <div class="absolute left-0.5 top-0.5 sm:left-1 sm:top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-5 sm:peer-checked:translate-x-6"></div>
                                    </label>
                                    <div>
                                        <label for="is_active" class="font-semibold text-gray-700 cursor-pointer text-sm sm:text-base">Produit Actif</label>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Le produit sera visible sur le site</p>
                                    </div>
                                </div>
                                <div id="is_active_icon" class="{{ old('is_active', true) ? 'text-green-500' : 'text-gray-400' }}">
                                    <i class="fas fa-toggle-{{ old('is_active', true) ? 'on' : 'off' }} text-xl sm:text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Produit en Vedette -->
                        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200 hover:border-amber-300 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="is_featured" 
                                               value="1" 
                                               id="is_featured"
                                               {{ old('is_featured') ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-5 sm:w-12 sm:h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-all duration-200"></div>
                                        <div class="absolute left-0.5 top-0.5 sm:left-1 sm:top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-5 sm:peer-checked:translate-x-6"></div>
                                    </label>
                                    <div>
                                        <label for="is_featured" class="font-semibold text-gray-700 cursor-pointer text-sm sm:text-base">Produit en Vedette</label>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Mettre en avant sur la page d'accueil</p>
                                    </div>
                                </div>
                                <div id="is_featured_icon" class="{{ old('is_featured') ? 'text-amber-500' : 'text-gray-400' }}">
                                    <i class="fas fa-star text-xl sm:text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Variantes du produit -->
                        <div id="productVariantsToggleCard" class="bg-amber-50 rounded-xl p-4 sm:p-6 border border-amber-200 hover:border-amber-400 transition-all duration-200 {{ old('has_variants') ? 'ring-2 ring-amber-300' : '' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox"
                                               name="has_variants"
                                               value="1"
                                               id="has_variants"
                                               {{ old('has_variants') ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="w-10 h-5 sm:w-12 sm:h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-all duration-200"></span>
                                        <span class="absolute left-0.5 top-0.5 sm:left-1 sm:top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:translate-x-5 sm:peer-checked:translate-x-6 shadow"></span>
                                    </label>
                                    <div class="min-w-0">
                                        <label for="has_variants" class="font-semibold text-gray-800 cursor-pointer text-sm sm:text-base">{{ __('admin.product_has_variants') }}</label>
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('admin.variants_toggle_help') }}</p>
                                    </div>
                                </div>
                                <div id="has_variants_icon" class="{{ old('has_variants') ? 'text-amber-500' : 'text-gray-400' }} shrink-0">
                                    <i class="fas fa-layer-group text-xl sm:text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('admin.products.partials.variants', ['showVariantToggle' => false])

                <!-- Actions -->
                <div class="pt-6 sm:pt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center text-gray-600 text-sm">
                            <i class="fas fa-history mr-2"></i>
                            <span>Tous les changements seront enregistrés</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                            <a href="{{ route('admin.products.index') }}" 
                               class="px-6 sm:px-8 py-2.5 sm:py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center justify-center text-sm sm:text-base">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" 
                                    class="px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center text-sm sm:text-base">
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

<!-- JavaScript amélioré pour mobile -->
<script>
// Variables globales
let uploadedImages = [];
let primaryImageIndex = 0;

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
        const files = Array.from(dt.files);
        handleNewFiles(files);
    }
});

function updateCheckboxIcon(checkboxId, isChecked) {
    const icon = document.getElementById(`${checkboxId}_icon`);
    if (!icon) return;
    
    if (checkboxId === 'is_active') {
        if (isChecked) {
            icon.innerHTML = '<i class="fas fa-toggle-on text-xl sm:text-2xl text-green-500"></i>';
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-green-500');
        } else {
            icon.innerHTML = '<i class="fas fa-toggle-off text-xl sm:text-2xl text-gray-400"></i>';
            icon.classList.remove('text-green-500');
            icon.classList.add('text-gray-400');
        }
    } else if (checkboxId === 'is_featured') {
        if (isChecked) {
            icon.innerHTML = '<i class="fas fa-star text-xl sm:text-2xl text-amber-500"></i>';
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-amber-500');
        } else {
            icon.innerHTML = '<i class="fas fa-star text-xl sm:text-2xl text-gray-400"></i>';
            icon.classList.remove('text-amber-500');
            icon.classList.add('text-gray-400');
        }
    }
}

// Gérer l'upload d'images
function handleImageUpload(event) {
    const files = Array.from(event.target.files);
    handleNewFiles(files);
}

// Traiter les nouveaux fichiers
function handleNewFiles(newFiles) {
    const maxFiles = 10;
    
    if (uploadedImages.length + newFiles.length > maxFiles) {
        showAlert(`Vous ne pouvez ajouter que ${maxFiles} images maximum. Vous avez déjà ${uploadedImages.length} image(s).`, 'error');
        return;
    }
    
    // Filtrer les fichiers valides
    const validFiles = newFiles.filter(file => {
        // Vérifier la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showAlert(`L'image "${file.name}" dépasse la taille maximale de 5MB`, 'error');
            return false;
        }
        
        // Vérifier le type MIME
        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showAlert(`Le format de "${file.name}" n'est pas supporté. Utilisez JPG, PNG, GIF ou WEBP.`, 'error');
            return false;
        }
        
        return true;
    });
    
    if (validFiles.length === 0) return;
    
    // Ajouter les nouveaux fichiers
    uploadedImages.push(...validFiles);
    
    // Mettre à jour l'affichage
    updateImagePreview();
    updateImageCount();
    
    // Si c'est la première image, la définir comme principale
    if (uploadedImages.length === 1) {
        setAsPrimary(0);
    }
}

// Mettre à jour la prévisualisation
function updateImagePreview() {
    const preview = document.getElementById('imagePreview');
    const emptyState = document.getElementById('emptyState');
    const container = document.getElementById('imagePreviewContainer');
    
    if (uploadedImages.length === 0) {
        preview.innerHTML = '';
        emptyState.style.display = 'flex';
        container.classList.remove('min-h-[200px]');
        return;
    }
    
    emptyState.style.display = 'none';
    container.classList.add('min-h-[200px]');
    preview.innerHTML = '';
    
    uploadedImages.forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'relative group';
            imgDiv.innerHTML = `
                <div class="aspect-square rounded-xl overflow-hidden border-2 ${index === primaryImageIndex ? 'border-green-500 bg-green-50' : 'border-gray-200'} bg-gray-100">
                    <img src="${e.target.result}" 
                         class="w-full h-full object-cover" 
                         alt="Preview"
                         loading="lazy">
                    
                    <!-- Bouton pour définir comme primaire -->
                    <button type="button" 
                            onclick="setAsPrimary(${index})"
                            class="absolute top-2 left-2 ${index === primaryImageIndex ? 'bg-green-500' : 'bg-gray-200 hover:bg-amber-500'} text-white text-xs p-1.5 rounded-full transition-colors"
                            title="${index === primaryImageIndex ? 'Image principale' : 'Définir comme principale'}">
                        <i class="fas ${index === primaryImageIndex ? 'fa-crown' : 'fa-star'}"></i>
                    </button>
                    
                    <!-- Numéro de l'image -->
                    <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-1.5 py-1 rounded-full">
                        ${index + 1}
                    </div>
                    
                    <!-- Bouton supprimer -->
                    <button type="button" 
                            onclick="removeImage(${index})"
                            class="absolute bottom-2 right-2 bg-red-500 text-white p-1.5 rounded-full hover:bg-red-600 transition-colors"
                            title="Supprimer">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                    
                    <!-- Boutons de déplacement pour mobile -->
                    <div class="absolute bottom-2 left-2 flex space-x-1">
                        <button type="button" 
                                onclick="moveImage(${index}, -1)"
                                class="bg-gray-800 bg-opacity-70 text-white p-1 rounded ${index === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-900'}"
                                ${index === 0 ? 'disabled' : ''}>
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button type="button" 
                                onclick="moveImage(${index}, 1)"
                                class="bg-gray-800 bg-opacity-70 text-white p-1 rounded ${index === uploadedImages.length - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-900'}"
                                ${index === uploadedImages.length - 1 ? 'disabled' : ''}>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Info fichier -->
                <div class="mt-2 px-1">
                    <p class="text-xs text-gray-600 truncate" title="${file.name}">
                        ${truncateFileName(file.name, window.innerWidth < 640 ? 15 : 20)}
                    </p>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-400">
                            ${formatFileSize(file.size)}
                        </span>
                        <span class="text-xs ${index === primaryImageIndex ? 'text-green-600 font-medium' : 'text-gray-500'}">
                            ${index === primaryImageIndex ? 'Principale' : ''}
                        </span>
                    </div>
                </div>
            `;
            
            preview.appendChild(imgDiv);
        };
        
        reader.readAsDataURL(file);
    });
    
    // Mettre à jour les champs cachés
    updateHiddenFields();
}

// Tronquer le nom de fichier
function truncateFileName(name, maxLength) {
    return name.length > maxLength ? name.substring(0, maxLength) + '...' : name;
}

// Définir une image comme principale
function setAsPrimary(index) {
    if (index === primaryImageIndex) return;
    
    primaryImageIndex = index;
    updateImagePreview();
    showAlert(`Image ${index + 1} définie comme principale`, 'success');
}

// Supprimer une image
function removeImage(index) {
    if (!confirm('Supprimer cette image ?')) return;
    
    const wasPrimary = index === primaryImageIndex;
    uploadedImages.splice(index, 1);
    
    // Ajuster l'index de l'image principale si nécessaire
    if (wasPrimary) {
        if (uploadedImages.length > 0) {
            primaryImageIndex = 0;
            showAlert('Image principale supprimée. La première image est maintenant principale.', 'info');
        } else {
            primaryImageIndex = 0;
        }
    } else if (index < primaryImageIndex) {
        primaryImageIndex--;
    }
    
    updateImagePreview();
    updateImageCount();
}

// Déplacer une image
function moveImage(index, direction) {
    if ((index === 0 && direction === -1) || (index === uploadedImages.length - 1 && direction === 1)) {
        return;
    }
    
    const newIndex = index + direction;
    
    // Échanger les images
    [uploadedImages[index], uploadedImages[newIndex]] = [uploadedImages[newIndex], uploadedImages[index]];
    
    // Ajuster l'index de l'image principale
    if (primaryImageIndex === index) {
        primaryImageIndex = newIndex;
    } else if (primaryImageIndex === newIndex) {
        primaryImageIndex = index;
    }
    
    updateImagePreview();
    updateHiddenFields();
}

// Mettre à jour le compteur
function updateImageCount() {
    const countElement = document.getElementById('selectedCount');
    const messageElement = document.getElementById('fileCountMessage');
    
    if (countElement) {
        countElement.textContent = uploadedImages.length;
    }
    
    if (messageElement) {
        if (uploadedImages.length >= 10) {
            messageElement.textContent = 'Limite de 10 images atteinte';
            messageElement.className = 'text-xs sm:text-sm text-red-500 mt-2 sm:mt-3 font-medium';
        } else {
            messageElement.textContent = `Maximum 10 images (${uploadedImages.length}/10)`;
            messageElement.className = 'text-xs sm:text-sm text-gray-500 mt-2 sm:mt-3 font-medium';
        }
    }
}

// Mettre à jour les champs cachés
function updateHiddenFields() {
    document.getElementById('primaryImageIndex').value = primaryImageIndex;
    
    // Créer un tableau d'ordre
    const order = uploadedImages.map((_, index) => index);
    document.getElementById('imageOrder').value = JSON.stringify(order);
    
    // Mettre à jour l'input file multiple
    updateFileInput();
}

// Mettre à jour l'input file
function updateFileInput() {
    const dataTransfer = new DataTransfer();
    
    uploadedImages.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    document.getElementById('multiImageUpload').files = dataTransfer.files;
}

// Formater la taille du fichier
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Afficher une alerte/notification
function showAlert(message, type = 'info') {
    // Supprimer les alertes existantes
    const existingAlert = document.getElementById('image-upload-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Créer l'alerte
    const alert = document.createElement('div');
    alert.id = 'image-upload-alert';
    alert.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center animate-fade-in max-w-xs sm:max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-3"></i>
        <span class="text-sm">${message}</span>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-suppression après 3 secondes
    setTimeout(() => {
        alert.classList.add('animate-fade-out');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Validation du formulaire
document.getElementById('productForm')?.addEventListener('submit', function(e) {
    updateHiddenFields();

    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    // Validation des champs requis
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            
            if (!field.nextElementSibling?.classList.contains('text-red-600')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center text-red-600 text-xs sm:text-sm mt-1';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Ce champ est obligatoire';
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        }
    });
    
    // Validation des images
    if (uploadedImages.length === 0) {
        isValid = false;
        showAlert('Veuillez ajouter au moins une image pour le produit', 'error');
        
        // Scroll vers la section images
        document.getElementById('imagePreviewContainer').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }
    
    if (!isValid) {
        e.preventDefault();
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

// Initialiser
updateImageCount();
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
    width: 6px;
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

/* Animations pour les notifications */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px) translateX(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0) translateX(0);
    }
}

@keyframes fade-out {
    from {
        opacity: 1;
        transform: translateY(0) translateX(0);
    }
    to {
        opacity: 0;
        transform: translateY(-10px) translateX(10px);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-fade-out {
    animation: fade-out 0.3s ease-in;
}

/* Améliorations pour mobile */
@media (max-width: 640px) {
    #imagePreview img {
        transition: transform 0.2s ease;
    }
    
    #imagePreview img:active {
        transform: scale(1.02);
    }
    
    .group:active .group-active\:scale-105 {
        transform: scale(1.05);
    }
}

/* Style pour les zones de drop sur mobile */
@media (max-width: 640px) {
    #dropZone {
        padding: 1.5rem;
    }
    
    #dropZone:active {
        background-color: rgba(16, 185, 129, 0.1);
        transform: scale(0.99);
    }
}

/* Amélioration de l'accessibilité pour mobile */
@media (max-width: 640px) {
    button, 
    input[type="button"], 
    input[type="submit"] {
        min-height: 44px;
        min-width: 44px;
    }
    
    select {
        font-size: 16px; /* Empêche le zoom automatique sur iOS */
    }
}

/* Optimisation des images */
#imagePreview img {
    max-width: 100%;
    height: auto;
}

/* État de chargement */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Support tactile amélioré */
@media (hover: none) and (pointer: coarse) {
    .hover\:border-green-400:hover {
        border-color: #10B981;
    }
    
    .hover\:bg-green-50:hover {
        background-color: #f0fdf4;
    }
}
</style>
@endsection
