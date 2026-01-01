@extends('admin.layouts.app')

@section('title', 'Ajouter une Catégorie - Admin')
@section('header', 'Nouvelle Catégorie')
@section('subheader', 'Créez une nouvelle catégorie pour organiser vos produits')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl shadow">
                    <i class="fas fa-folder-plus text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Nouvelle Catégorie</h2>
                    <p class="text-gray-600">Remplissez les informations ci-dessous pour créer une catégorie</p>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf
            
            <div class="p-8">
                <!-- Informations principales -->
                <div class="space-y-8">
                    <!-- Nom de la catégorie -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-1 h-6 bg-green-500 rounded-full mr-3"></div>
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
                                           value="{{ old('name') }}" 
                                           required
                                           placeholder="ex: Médicaments, Produits de beauté..."
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror">
                                </div>
                                @error('name')
                                    <div class="flex items-center text-red-600 text-sm mt-1">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Le nom doit être unique et descriptif. Il sera visible par les clients.
                                </p>
                            </div>

                            <!-- Slug (généré automatiquement) -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Slug (URL)
                                    <span class="text-xs font-normal text-gray-500 block mt-1">Généré automatiquement à partir du nom</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-link text-gray-400"></i>
                                    </div>
                                    <input type="text" 
                                           id="slugPreview"
                                           readonly
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Identifiant unique dans l'URL. Modifiable ultérieurement.
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
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <div class="flex items-center text-red-600 text-sm mt-1">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="flex justify-between items-center mt-2">
                                    <div class="text-xs text-gray-500">
                                        <span id="charCount">0</span> caractères
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
                        <div class="flex items-center">
                            <div class="w-1 h-6 bg-blue-500 rounded-full mr-3"></div>
                            <label class="block text-lg font-bold text-gray-800">
                                Image de la catégorie
                            </label>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Upload zone -->
                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-200 cursor-pointer"
                                 onclick="document.getElementById('imageUpload').click()"
                                 id="dropZone">
                                <div class="max-w-sm mx-auto">
                                    <div class="bg-gradient-to-r from-green-100 to-emerald-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-cloud-upload-alt text-green-500 text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-700 mb-2">Ajouter une image</h4>
                                    <p class="text-gray-500 text-sm mb-4">Glissez-déposez ou cliquez pour parcourir</p>
                                    <div class="flex items-center justify-center space-x-2 text-sm">
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">JPG</span>
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">PNG</span>
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">WEBP</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-4">Taille recommandée : 800×800px</p>
                                </div>
                            </div>
                            <input type="file" 
                                   name="image" 
                                   accept="image/*"
                                   id="imageUpload"
                                   class="hidden">
                            
                            <!-- Prévisualisation -->
                            <div id="imagePreviewContainer" class="hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="font-medium text-gray-700">Aperçu de l'image</h5>
                                    <button type="button" 
                                            onclick="removeImage()"
                                            class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                        <i class="fas fa-trash mr-1"></i>
                                        Supprimer
                                    </button>
                                </div>
                                <div class="relative">
                                    <img id="previewImage" 
                                         class="w-48 h-48 rounded-xl object-cover border border-gray-200 shadow-sm">
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
                                                   {{ old('is_active', true) ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                            <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-7"></div>
                                        </div>
                                        <div>
                                            <label for="is_active" class="font-semibold text-gray-700 cursor-pointer block">Catégorie Active</label>
                                            <p class="text-sm text-gray-500 mt-1">La catégorie sera visible sur le site</p>
                                        </div>
                                    </div>
                                    <div class="text-green-500">
                                        <i class="fas fa-toggle-on text-2xl"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Catégorie vedette (option future) -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200 hover:border-amber-300 transition-all duration-200 opacity-50 cursor-not-allowed">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="w-14 h-7 bg-gray-300 rounded-full"></div>
                                            <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full"></div>
                                        </div>
                                        <div>
                                            <label class="font-semibold text-gray-500 cursor-not-allowed block">Catégorie en Vedette</label>
                                            <p class="text-sm text-gray-400 mt-1">À venir - Mettre en avant sur la page d'accueil</p>
                                        </div>
                                    </div>
                                    <div class="text-gray-400">
                                        <i class="fas fa-star text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-8 mt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span class="text-sm">Toutes les données sont sécurisées et chiffrées</span>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.categories.index') }}" 
                               class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Créer la catégorie
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript corrigé pour la prévisualisation d'image -->
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

// Supprimer l'image
function removeImage() {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('imageUpload');
    
    if (previewContainer) previewContainer.classList.add('hidden');
    if (dropZone) dropZone.classList.remove('hidden');
    if (fileInput) fileInput.value = '';
}

// Réinitialiser les filtres (fonction globale)
window.resetFilters = function() {
    // Cette fonction est définie pour éviter les erreurs
    console.log('Reset filters function called');
};

document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du slug
    const nameInput = document.querySelector('input[name="name"]');
    const slugPreview = document.getElementById('slugPreview');
    
    if (nameInput && slugPreview) {
        nameInput.addEventListener('input', function() {
            const name = this.value.trim();
            if (name) {
                // Générer un slug simple
                const slug = name
                    .toLowerCase()
                    .replace(/[^\w\s]/gi, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                
                slugPreview.value = slug;
            } else {
                slugPreview.value = '';
            }
        });
        
        // Initialiser le slug si il y a déjà une valeur
        if (nameInput.value) {
            nameInput.dispatchEvent(new Event('input'));
        }
    }
    
    // Compteur de caractères pour la description
    const description = document.querySelector('textarea[name="description"]');
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
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-green-500', 'bg-green-50');
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
    const imageInput = document.getElementById('imageUpload');
    if (imageInput && imageInput.files.length > 0) {
        // Déclencher la prévisualisation si une image est déjà sélectionnée
        const event = new Event('change');
        imageInput.dispatchEvent(event);
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
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
</style>
@endsection