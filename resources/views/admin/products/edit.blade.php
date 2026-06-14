@extends('admin.layouts.app')

@section('title', 'Modifier le Produit - Admin')
@section('header', 'Modifier le Produit')
@section('subheader', 'Mettre à jour les informations du produit')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-3 rounded-xl shadow">
                            <i class="fas fa-edit text-white text-lg"></i>
                        </div>
                        @if($product->is_featured)
                            <div class="absolute -top-2 -right-2 bg-amber-500 text-white p-1 rounded-full">
                                <i class="fas fa-star text-xs"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
                        <p class="text-gray-600">SKU: {{ $product->sku ?: 'Non défini' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        {{ $product->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                        ID: {{ $product->id }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
            @csrf
            @method('PUT')
            
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
                                       value="{{ old('name', $product->name) }}" 
                                       required
                                       placeholder="ex: Miel de thym 250g"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
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
                                        class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white cursor-pointer transition-all duration-200 @error('category_id') border-red-500 ring-2 ring-red-200 @enderror">
                                    <option value="" disabled>Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                       value="{{ old('price', $product->price) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required
                                       placeholder="0.00"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('price') border-red-500 ring-2 ring-red-200 @enderror">
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
                                       value="{{ old('sku', $product->sku) }}"
                                       placeholder="ex: PROD-001"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('sku') border-red-500 ring-2 ring-red-200 @enderror">
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
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                       min="0" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('stock_quantity') border-red-500 ring-2 ring-red-200 @enderror">
                            </div>
                            @error('stock_quantity')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <!-- Indicateur de stock -->
                            @php
                                $stockClass = '';
                                $stockIcon = '';
                                if($product->stock_quantity == 0) {
                                    $stockClass = 'text-red-600 bg-red-50';
                                    $stockIcon = 'fas fa-times-circle';
                                } elseif($product->stock_quantity <= $product->low_stock_alert) {
                                    $stockClass = 'text-amber-600 bg-amber-50';
                                    $stockIcon = 'fas fa-exclamation-triangle';
                                } else {
                                    $stockClass = 'text-green-600 bg-green-50';
                                    $stockIcon = 'fas fa-check-circle';
                                }
                            @endphp
                            <div class="mt-2 {{ $stockClass }} text-sm px-3 py-2 rounded-lg flex items-center">
                                <i class="{{ $stockIcon }} mr-2"></i>
                                @if($product->stock_quantity == 0)
                                    Rupture de stock
                                @elseif($product->stock_quantity <= $product->low_stock_alert)
                                    Stock faible ({{ $product->stock_quantity }} unités)
                                @else
                                    Stock suffisant ({{ $product->stock_quantity }} unités)
                                @endif
                            </div>
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
                                       value="{{ old('low_stock_alert', $product->low_stock_alert) }}" 
                                       min="0" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('low_stock_alert') border-red-500 ring-2 ring-red-200 @enderror">
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
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description', $product->description) }}</textarea>
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
                                <span id="charCount">{{ strlen(old('description', $product->description)) }}</span> caractères
                            </div>
                        </div>
                    </div>
                </div>

    

@if($product->images->count() > 0)
    <div class="mb-10">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-1 h-8 bg-amber-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-bold text-gray-800">Images existantes</h3>
            </div>
            <span class="text-sm text-gray-500">
                {{ $product->images->count() }} image{{ $product->images->count() > 1 ? 's' : '' }}
            </span>
        </div>
        
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($product->images as $image)
                    <div class="relative group">
                        <div class="aspect-square rounded-xl overflow-hidden border-2 {{ $image->is_primary ? 'border-green-500' : 'border-gray-200' }} bg-gray-100">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            
                            @if($image->is_primary)
                                <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-lg flex items-center">
                                    <i class="fas fa-crown mr-1"></i>
                                    Principale
                                </div>
                            @endif
                            
                            <!-- Overlay avec actions -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
                                <a href="{{ asset('storage/' . $image->image_path) }}" 
                                   target="_blank"
                                   class="bg-white p-2 rounded-full hover:bg-gray-100"
                                   title="Voir en grand">
                                    <i class="fas fa-expand text-gray-700"></i>
                                </a>
                                
                                <!-- Action buttons WITHOUT forms -->
                                <button type="button" 
                                        onclick="setAsPrimary(event,'{{ $image->id }}')"
                                        class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 {{ $image->is_primary ? 'hidden' : '' }}"
                                        title="Définir comme principale">
                                    <i class="fas fa-star"></i>
                                </button>
                                
                                <button type="button" 
                                        onclick="deleteImage( event,'{{ $image->id }}')"
                                        class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-xs text-center">
                            <p class="text-gray-600 truncate">
                                {{ basename($image->image_path) }}
                            </p>
                            <p class="text-gray-400">
                                {{ $image->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($product->images->where('is_primary', true)->count() == 0)
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                        <p class="text-yellow-700">
                            Ce produit n'a pas d'image principale. Veuillez en définir une.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

                <!-- Ajouter de nouvelles images -->
                <div class="mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-indigo-500 rounded-full mr-3"></div>
                        <h3 class="text-lg font-bold text-gray-800">Ajouter de nouvelles images</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 cursor-pointer"
                             onclick="document.getElementById('imageUpload').click()"
                             id="dropZone">
                            <div class="max-w-sm mx-auto">
                                <div class="bg-gradient-to-r from-blue-100 to-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-blue-500 text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-700 mb-2">Glissez-déposez vos images ici</h4>
                                <p class="text-gray-500 text-sm mb-4">ou cliquez pour parcourir vos fichiers</p>
                                <div class="flex items-center justify-center space-x-2 text-sm">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">JPG</span>
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">PNG</span>
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">WEBP</span>
                                </div>
                                <p class="text-xs text-blue-600 mt-3">Les images sont automatiquement redimensionnées et compressées pour un chargement rapide.</p>
                            </div>
                            <input type="file" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   id="imageUpload"
                                   class="hidden"
                                   onchange="previewImages(event)">
                            <input type="hidden" name="new_primary_image_index" id="newPrimaryImageIndex" value="-1">
                        </div>
                        
                        <!-- Image preview container -->
                        <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
                            <!-- Preview will be inserted here -->
                        </div>
                        
                        <p class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Vous pouvez conserver jusqu'à 10 images au total. Vous pouvez aussi choisir une nouvelle image principale.
                        </p>
                        
                        @error('images')
                            <div class="flex items-center text-red-600 text-sm mt-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror

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
        <div class="w-1 h-8 bg-pink-500 rounded-full mr-3"></div>
        <h3 class="text-lg font-bold text-gray-800">Options</h3>
    </div>
    
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Produit Actif -->
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               id="is_active"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                    </label>
                    <div>
                        <label for="is_active" class="font-semibold text-gray-700 cursor-pointer">Produit Actif</label>
                        <p class="text-sm text-gray-500 mt-1">Le produit sera visible sur le site</p>
                    </div>
                </div>
                <div id="is_active_icon" class="{{ old('is_active', $product->is_active) ? 'text-green-500' : 'text-gray-400' }}">
                    <i class="fas fa-toggle-{{ old('is_active', $product->is_active) ? 'on' : 'off' }} text-2xl"></i>
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
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-all duration-200"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                    </label>
                    <div>
                        <label for="is_featured" class="font-semibold text-gray-700 cursor-pointer">Produit en Vedette</label>
                        <p class="text-sm text-gray-500 mt-1">Mettre en avant sur la page d'accueil</p>
                    </div>
                </div>
                <div id="is_featured_icon" class="{{ old('is_featured', $product->is_featured) ? 'text-amber-500' : 'text-gray-400' }}">
                    <i class="fas fa-star text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Variantes du produit -->
        <div id="productVariantsToggleCard" class="bg-amber-50 rounded-xl p-6 border border-amber-200 hover:border-amber-400 transition-all duration-200 {{ old('has_variants', $product->variants->isNotEmpty()) ? 'ring-2 ring-amber-300' : '' }}">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center space-x-4 min-w-0">
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox"
                               name="has_variants"
                               value="1"
                               id="has_variants"
                               {{ old('has_variants', $product->variants->isNotEmpty()) ? 'checked' : '' }}
                               class="sr-only peer">
                        <span class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-all duration-200"></span>
                        <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:translate-x-6 shadow"></span>
                    </label>
                    <div class="min-w-0">
                        <label for="has_variants" class="font-semibold text-gray-800 cursor-pointer">{{ __('admin.product_has_variants') }}</label>
                        <p class="text-sm text-gray-600 mt-1">{{ __('admin.variants_toggle_help') }}</p>
                    </div>
                </div>
                <div id="has_variants_icon" class="{{ old('has_variants', $product->variants->isNotEmpty()) ? 'text-amber-500' : 'text-gray-400' }} shrink-0">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
                @include('admin.products.partials.variants', ['showVariantToggle' => false])

                <!-- Actions -->
                <div class="pt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-history mr-2"></i>
                                <span class="text-sm">Dernière modification: {{ $product->updated_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="text-blue-600 hover:text-blue-800 flex items-center text-sm">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Voir le produit
                            </a>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.products.index') }}" 
                               class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Mettre à jour le produit
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
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }
    
    function unhighlight() {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('imageUpload').files = files;
        previewImages({ target: { files: files } });
    }
});

// Prévisualisation et validation des nouvelles images
function previewImages(event) {
    const input = document.getElementById('imageUpload');
    const preview = document.getElementById('imagePreview');
    const maximumNewImages = Math.max(0, 10 - {{ $product->images->count() }});
    const selectedFiles = Array.from(event.target.files || []);

    if (selectedFiles.length > maximumNewImages) {
        alert(`Vous pouvez encore ajouter ${maximumNewImages} image(s) maximum pour ce produit.`);
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    const validFiles = selectedFiles.filter(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`L'image "${file.name}" dépasse la taille maximale de 5MB.`);
            return false;
        }
        if (!validTypes.includes(file.type)) {
            alert(`Le format de "${file.name}" n'est pas supporté.`);
            return false;
        }
        return true;
    });

    const transfer = new DataTransfer();
    validFiles.forEach(file => transfer.items.add(file));
    input.files = transfer.files;
    preview.innerHTML = '';

    const primaryInput = document.getElementById('newPrimaryImageIndex');
    if (Number(primaryInput.value) >= validFiles.length) primaryInput.value = '-1';

    validFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(loadEvent) {
            const item = document.createElement('div');
            item.className = 'relative group';
            const isPrimary = Number(primaryInput.value) === index;
            item.innerHTML = `
                <div class="aspect-square rounded-xl overflow-hidden border ${isPrimary ? 'border-amber-500 ring-2 ring-amber-200' : 'border-gray-200'} bg-gray-100">
                    <img src="${loadEvent.target.result}" class="w-full h-full object-cover" alt="Preview">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                        <button type="button" onclick="setNewImagePrimary(${index})" class="bg-amber-500 text-white p-2 rounded-full hover:bg-amber-600" title="Définir comme principale"><i class="fas fa-star"></i></button>
                        <button type="button" onclick="removeNewImage(${index})" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="new-primary-badge absolute top-2 left-2 ${isPrimary ? '' : 'hidden'} bg-amber-500 text-white text-xs px-2 py-1 rounded-lg"><i class="fas fa-crown mr-1"></i>Principale</div>
                </div>
                <p class="text-xs text-gray-500 mt-2 truncate">${file.name}</p>`;
            preview.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

function setNewImagePrimary(index) {
    document.getElementById('newPrimaryImageIndex').value = index;
    const input = document.getElementById('imageUpload');
    previewImages({ target: { files: input.files } });
}

// Supprimer une nouvelle image de la prévisualisation
function removeNewImage(index) {
    const input = document.getElementById('imageUpload');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.splice(index, 1);
    
    files.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
    const primaryInput = document.getElementById('newPrimaryImageIndex');
    const selectedPrimary = Number(primaryInput.value);
    if (selectedPrimary === index) primaryInput.value = '-1';
    else if (selectedPrimary > index) primaryInput.value = String(selectedPrimary - 1);
    
    // Recharger la prévisualisation
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files.length > 0) {
        previewImages({ target: { files: input.files } });
    }
}

// Validation du formulaire
document.getElementById('editProductForm')?.addEventListener('submit', function(e) {
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

// Toggle checkbox icons
document.getElementById('is_active')?.addEventListener('change', function() {
    const icon = document.getElementById('is_active_icon');
    if (this.checked) {
        icon.innerHTML = '<i class="fas fa-toggle-on text-2xl text-green-500"></i>';
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-green-500');
    } else {
        icon.innerHTML = '<i class="fas fa-toggle-off text-2xl text-gray-400"></i>';
        icon.classList.remove('text-green-500');
        icon.classList.add('text-gray-400');
    }
});

document.getElementById('is_featured')?.addEventListener('change', function() {
    const icon = document.getElementById('is_featured_icon');
    if (this.checked) {
        icon.innerHTML = '<i class="fas fa-star text-2xl text-amber-500"></i>';
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-amber-500');
    } else {
        icon.innerHTML = '<i class="fas fa-star text-2xl text-gray-400"></i>';
        icon.classList.remove('text-amber-500');
        icon.classList.add('text-gray-400');
    }
});

function setAsPrimary(event, imageId) {
    event.preventDefault();
    
    if (confirm('Définir cette image comme principale ?')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // Make the request
        fetch(`{{ route('admin.products.images.set-primary', [$product, '__IMAGE__']) }}`.replace('__IMAGE__', imageId), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                // If not JSON, get the text to see what's wrong
                return response.text().then(text => {
                    throw new Error(`Expected JSON but got: ${text.substring(0, 100)}`);
                });
            }
        })
        .then(data => {
            if (data.success) {
                // Success - reload the page
                showNotification(data.message || 'Image principale mise à jour avec succès', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Erreur inconnue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erreur: ' + error.message, 'error');
            // Restore button
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}

function deleteImage(event, imageId) {
    event.preventDefault();
    
    if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        fetch(`{{ route('admin.products.images.delete', [$product, '__IMAGE__']) }}`.replace('__IMAGE__', imageId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`Expected JSON but got: ${text.substring(0, 100)}`);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Image supprimée avec succès', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Erreur inconnue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erreur: ' + error.message, 'error');
            // Restore button
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}

// Helper function to show notifications
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.getElementById('ajax-notification');
    if (existing) existing.remove();
    
    // Create notification
    const notification = document.createElement('div');
    notification.id = 'ajax-notification';
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
// Confirmation avant suppression d'image existante
document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault();
        if (confirm(this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir supprimer cette image ?')) {
            this.submit();
        }
    };
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

/* Style pour les images au hover */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
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

/* Animation pour les badges */
@keyframes pulse-ring {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1.2); opacity: 0; }
}

.pulse-ring {
    position: relative;
}

.pulse-ring::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 9999px;
    border: 2px solid currentColor;
    animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection
