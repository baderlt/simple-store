@extends('admin.layouts.app')

@section('title', 'Modifier la Catégorie - Admin')
@section('header', 'Modifier la Catégorie')
@section('subheader', 'Mettez à jour les informations de la catégorie')

@section('content')
<div class="max-w-3xl mx-auto">
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
                        @if($category->is_active)
                            <div class="absolute -top-2 -right-2 bg-green-500 text-white p-1 rounded-full">
                                <i class="fas fa-circle text-xs"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
                        <p class="text-gray-600">ID: {{ $category->id }} | Produits: {{ $category->products_count }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <!-- Informations principales -->
                <div class="space-y-8">
                    <!-- Nom de la catégorie -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-1 h-6 bg-blue-500 rounded-full mr-3"></div>
                            <label class="block text-lg font-bold text-gray-800 flex items-center">
                                Informations principales
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Nom -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                    Nom de la catégorie
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', $category->name) }}" 
                                           required
                                           placeholder="ex: Produits, Produits de beauté..."
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                                </div>
                                @error('name')
                                    <div class="flex items-center text-red-600 text-sm mt-1">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Slug (URL)
                                    <span class="text-xs font-normal text-gray-500 block mt-1">Identifiant unique dans l'URL</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-link text-gray-400"></i>
                                    </div>
                                    <input type="text" 
                                           name="slug" 
                                           value="{{ old('slug', $category->slug) }}"
                                           placeholder="ex: medicaments"
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('slug') border-red-500 ring-2 ring-red-200 @enderror">
                                </div>
                                @error('slug')
                                    <div class="flex items-center text-red-600 text-sm mt-1">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Utilisez des lettres minuscules, chiffres et tirets uniquement
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Description
                                    <span class="text-xs font-normal text-gray-500 block mt-1">Décrivez la catégorie (optionnel)</span>
                                </label>
                                <div class="relative">
                                    <textarea name="description" 
                                              rows="4"
                                              placeholder="Décrivez cette catégorie, ses caractéristiques, son utilité..."
                                              id="description"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description', $category->description) }}</textarea>
                                </div>
                                @error('description')
                                    <div class="flex items-center text-red-600 text-sm mt-1">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="flex justify-between items-center mt-2">
                                    <div class="text-xs text-gray-500">
                                        <span id="charCount">{{ strlen(old('description', $category->description)) }}</span> caractères
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Idéalement entre 50 et 200 caractères
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image de la catégorie -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-1 h-6 bg-purple-500 rounded-full mr-3"></div>
                                <label class="block text-lg font-bold text-gray-800">
                                    Image de la catégorie
                                </label>
                            </div>
                            @if($category->image)
                                <a href="{{ asset('storage/' . $category->image) }}" 
                                   target="_blank"
                                   class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Voir l'originale
                                </a>
                            @endif
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Image actuelle -->
                            @if($category->image)
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-4">Image actuelle</label>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $category->image) }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="w-32 h-32 rounded-xl object-cover border-2 border-blue-300 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="mb-3">
                                                <p class="text-sm font-medium text-gray-900">Image actuelle</p>
                                                <p class="text-xs text-gray-500">Cliquez sur l'image pour agrandir</p>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ asset('storage/' . $category->image) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-expand mr-2"></i>
                                                    Agrandir
                                                </a>
                                                <button type="button" 
                                                        onclick="showDeleteConfirmation()"
                                                        class="inline-flex items-center text-sm text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash-alt mr-2"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Option pour supprimer l'image -->
                                    <div id="deleteOption" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                                <div>
                                                    <p class="font-medium text-red-700">Supprimer l'image actuelle</p>
                                                    <p class="text-sm text-red-600">Cette action est irréversible</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <button type="button" 
                                                        onclick="hideDeleteConfirmation()"
                                                        class="text-sm text-gray-600 hover:text-gray-800">
                                                    Annuler
                                                </button>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" 
                                                           name="delete_image" 
                                                           value="1"
                                                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="ml-2 text-sm font-medium text-red-700">Confirmer la suppression</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-300"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 bg-white text-gray-500">OU</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Upload nouvelle image -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-4">
                                    {{ $category->image ? 'Remplacer par une nouvelle image' : 'Ajouter une image' }}
                                </label>
                                
                                <!-- Upload zone -->
                                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 cursor-pointer"
                                     onclick="document.getElementById('imageUpload').click()"
                                     id="dropZone">
                                    <div class="max-w-sm mx-auto">
                                        <div class="bg-gradient-to-r from-blue-100 to-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-cloud-upload-alt text-blue-500 text-2xl"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-700 mb-2">Télécharger une nouvelle image</h4>
                                        <p class="text-gray-500 text-sm mb-4">Glissez-déposez ou cliquez pour parcourir</p>
                                        <div class="flex items-center justify-center space-x-2 text-sm">
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">JPG</span>
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">PNG</span>
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">WEBP</span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-4">Taille recommandée : 800×800px • Max 5MB</p>
                                    </div>
                                </div>
                                <input type="file" 
                                       name="image" 
                                       accept="image/*"
                                       id="imageUpload"
                                       class="hidden">
                                
                                <!-- Prévisualisation -->
                                <div id="imagePreviewContainer" class="hidden mt-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="font-medium text-gray-700">Aperçu de la nouvelle image</h5>
                                        <button type="button" 
                                                onclick="removeNewImage()"
                                                class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                            <i class="fas fa-trash mr-1"></i>
                                            Supprimer
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <img id="previewImage" 
                                             class="w-48 h-48 rounded-xl object-cover border-2 border-blue-300 shadow-sm">
                                        <div class="absolute top-2 right-2 bg-white p-2 rounded-lg shadow">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('image')
                                    <div class="flex items-center text-red-600 text-sm mt-2">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-1 h-6 bg-amber-500 rounded-full mr-3"></div>
                            <label class="block text-lg font-bold text-gray-800">
                                Options
                            </label>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Statut actif -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <input type="checkbox" 
                                                   name="is_active" 
                                                   value="1" 
                                                   id="is_active"
                                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                            <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-7"></div>
                                        </div>
                                        <div>
                                            <label for="is_active" class="font-semibold text-gray-700 cursor-pointer block">Catégorie Active</label>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $category->is_active ? 'Actuellement visible sur le site' : 'Actuellement masquée sur le site' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="{{ $category->is_active ? 'text-green-500' : 'text-gray-400' }}">
                                        <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }} text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métadonnées -->
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <h4 class="text-lg font-bold text-gray-800 mb-6">
                        <i class="fas fa-info-circle mr-2 text-gray-500"></i>
                        Informations système
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">Créée le</p>
                            <p class="font-medium text-gray-900">{{ $category->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">Dernière modification</p>
                            <p class="font-medium text-gray-900">{{ $category->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">Nombre de produits</p>
                            <p class="font-medium text-gray-900">{{ $category->products_count }} produit(s)</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-8 mt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-history mr-2"></i>
                                <span class="text-sm">Dernière modification: {{ $category->updated_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" 
                               class="text-blue-600 hover:text-blue-800 flex items-center text-sm">
                                <i class="fas fa-box mr-2"></i>
                                Voir les produits
                            </a>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.categories.index') }}" 
                               class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Mettre à jour la catégorie
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
// Déclarer les fonctions globales d'abord
function previewImage(event) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImageElement = document.getElementById('previewImage');
    const dropZone = document.getElementById('dropZone');
    
    const file = event.target.files[0];
    
    if (!file) return;
    
    // Vérifier la taille (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('L\'image ne doit pas dépasser 5MB');
        event.target.value = '';
        return;
    }
    
    // Vérifier le type
    if (!file.type.match('image.*')) {
        alert('Veuillez sélectionner une image valide (JPG, PNG, GIF, WEBP)');
        event.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    
    reader.onload = function(e) {
        previewImageElement.src = e.target.result;
        previewContainer.classList.remove('hidden');
        if (dropZone) {
            dropZone.classList.add('hidden');
        }
    };
    
    reader.readAsDataURL(file);
}

// Supprimer la nouvelle image
function removeNewImage() {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('imageUpload');
    
    if (previewContainer) previewContainer.classList.add('hidden');
    if (dropZone) dropZone.classList.remove('hidden');
    if (fileInput) fileInput.value = '';
}

// Afficher la confirmation de suppression
function showDeleteConfirmation() {
    const deleteOption = document.getElementById('deleteOption');
    if (deleteOption) {
        deleteOption.classList.remove('hidden');
    }
}

// Cacher la confirmation de suppression
function hideDeleteConfirmation() {
    const deleteOption = document.getElementById('deleteOption');
    if (deleteOption) {
        deleteOption.classList.add('hidden');
        // Décocher la checkbox de suppression
        const deleteCheckbox = deleteOption.querySelector('input[name="delete_image"]');
        if (deleteCheckbox) {
            deleteCheckbox.checked = false;
        }
    }
}

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
    
    // Gestion du drag and drop pour l'image
    const dropZone = document.getElementById('dropZone');
    const imageUpload = document.getElementById('imageUpload');
    
    if (dropZone && imageUpload) {
        // Ajouter l'événement onchange via JavaScript
        imageUpload.addEventListener('change', function(event) {
            previewImage(event);
        });
        
        // Drag and drop
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
        
        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                imageUpload.files = files;
                // Déclencher l'événement change manuellement
                const event = new Event('change');
                imageUpload.dispatchEvent(event);
            }
        });
    }
    
    // Validation du formulaire
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            const nameInput = this.querySelector('input[name="name"]');
            const deleteCheckbox = this.querySelector('input[name="delete_image"]');
            const newImageInput = this.querySelector('input[name="image"]');
            
            // Validation du nom
            if (!nameInput.value.trim()) {
                e.preventDefault();
                
                nameInput.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                nameInput.focus();
                
                // Ajouter un message d'erreur
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center text-red-600 text-sm mt-1';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Le nom de la catégorie est obligatoire';
                
                // Trouver l'élément parent pour insérer l'erreur
                const nameFieldContainer = nameInput.closest('.space-y-2');
                if (nameFieldContainer) {
                    // Supprimer les anciennes erreurs
                    const existingErrors = nameFieldContainer.querySelectorAll('.text-red-600');
                    existingErrors.forEach(error => error.remove());
                    
                    // Insérer la nouvelle erreur
                    nameFieldContainer.appendChild(errorDiv);
                }
                
                // Scroll vers l'erreur
                nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            // Confirmation si suppression d'image sans remplacement
            const hasExistingImage = @json($category->image ? true : false);
            if (hasExistingImage && deleteCheckbox && deleteCheckbox.checked && 
                (!newImageInput || !newImageInput.files || newImageInput.files.length === 0)) {
                const confirmDelete = confirm('⚠️ Vous allez supprimer l\'image actuelle sans la remplacer.\n\nLa catégorie n\'aura plus d\'image. Continuer ?');
                if (!confirmDelete) {
                    e.preventDefault();
                    hideDeleteConfirmation();
                    return;
                }
            }
            
            // Confirmation générale
            const hasChanges = this.querySelectorAll('input:not([type="hidden"]), textarea, select').length > 0;
            if (hasChanges) {
                const confirmUpdate = confirm('Confirmez-vous la mise à jour de cette catégorie ?');
                if (!confirmUpdate) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Supprimer les classes d'erreur lors de la saisie
    document.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
            
            // Trouver et supprimer les messages d'erreur dans le même container
            const container = this.closest('.space-y-2');
            if (container) {
                const errorMsg = container.querySelector('.text-red-600');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
    
    // Vérifier s'il y a déjà une image à prévisualiser (en cas d'erreur de validation)
    if (imageUpload && imageUpload.files.length > 0) {
        // Déclencher la prévisualisation si une image est déjà sélectionnée
        const event = new Event('change');
        imageUpload.dispatchEvent(event);
    }
    
    // Gestion de la suppression d'image
    const deleteCheckbox = document.querySelector('input[name="delete_image"]');
    if (deleteCheckbox) {
        deleteCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Si on coche la suppression, on peut masquer la zone de drop
                const dropZone = document.getElementById('dropZone');
                if (dropZone) {
                    dropZone.classList.add('opacity-50', 'cursor-not-allowed');
                }
                
                // Cacher aussi la prévisualisation si elle est visible
                const previewContainer = document.getElementById('imagePreviewContainer');
                if (previewContainer && !previewContainer.classList.contains('hidden')) {
                    removeNewImage();
                }
            } else {
                // Si on décoche, réactiver la zone de drop
                const dropZone = document.getElementById('dropZone');
                if (dropZone) {
                    dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        });
    }
});
</script>

<style>
/* Styles personnalisés */
/* Animation pour les boutons */
button, a {
    transition: all 0.2s ease;
}

/* Style pour le placeholder */
::-webkit-input-placeholder {
    color: #9CA3AF;
    font-style: italic;
}

/* Style pour les toggles */
.sr-only + div {
    cursor: pointer;
}

/* Style pour le textarea */
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

/* Animation pour les sections */
.space-y-8 > div {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Style pour les champs en focus */
input:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Style pour les images */
#previewImage {
    transition: transform 0.3s ease;
}

#previewImage:hover {
    transform: scale(1.02);
}

/* Style pour la zone de drop */
#dropZone {
    transition: all 0.3s ease;
}

/* Style pour le container de prévisualisation */
#imagePreviewContainer {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .p-8 {
        padding: 1.5rem;
    }
    
    .px-8 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    .text-2xl {
        font-size: 1.5rem;
    }
}

/* Style pour les messages d'erreur */
.text-red-600 {
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Style pour les champs avec erreur */
.border-red-500 {
    border-color: #ef4444 !important;
}

.ring-red-200 {
    --tw-ring-color: rgba(254, 202, 202, 0.5);
    box-shadow: 0 0 0 3px var(--tw-ring-color);
}

/* Animation pour les images */
.group-hover\:scale-105 {
    transition: transform 0.3s ease;
}

/* Style pour la section de suppression */
#deleteOption {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 200px;
    }
}
</style>
@endsection