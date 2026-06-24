{{-- resources/views/admin/banners/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', isset($banner) ? 'Modifier la Bannière' : 'Nouvelle Bannière - Admin')
@section('header', isset($banner) ? 'Modifier la Bannière' : 'Créer une Nouvelle Bannière')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <form action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  id="bannerForm">
                @csrf
                @if(isset($banner))
                    @method('PUT')
                @endif
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Image Upload -->
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Images responsives</h3>
                        
                        <!-- Image Preview -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image ordinateur / tablette <span class="text-red-500">*</span>
                            </label>
                            <div id="imagePreview" class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-500 transition-colors duration-200">
                                @if(isset($banner) && $banner->image_path)
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                         alt="Prévisualisation"
                                         class="w-full h-48 object-cover rounded-lg mb-4">
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                        Image chargée
                                    </div>
                                @else
                                    <div class="py-12">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-sm text-gray-600">Aucune image sélectionnée</p>
                                        <p class="text-xs text-gray-500 mt-1">Cliquez pour télécharger</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Hidden file input -->
                            <input type="file" 
                                   name="image" 
                                   id="imageInput" 
                                   accept="image/*"
                                   class="hidden"
                                   {{ !isset($banner) ? 'required' : '' }}>
                            
                            <!-- Upload button -->
                            <button type="button" 
                                    onclick="document.getElementById('imageInput').click()"
                                    class="mt-4 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i>
                                {{ isset($banner) ? 'Changer l\'image' : 'Télécharger une image' }}
                            </button>
                            
                            <!-- Image requirements -->
                            <div class="mt-3 text-xs text-gray-500">
                                <p><i class="fas fa-info-circle mr-1"></i> Formats acceptés: JPG, PNG, GIF, WEBP</p>
                                <p><i class="fas fa-info-circle mr-1"></i> Taille max: 5MB</p>
                                <p><i class="fas fa-ruler mr-1"></i> Recommandé: 1920×900 px</p>
                                @if(isset($banner))
                                    <p class="mt-2 text-green-600">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Laisser vide pour conserver l'image actuelle
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image mobile <span class="text-xs font-normal text-gray-500">(optionnelle)</span>
                            </label>
                            <div id="mobileImagePreview" class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-500 transition-colors duration-200">
                                <div class="py-10">
                                    <i class="fas fa-mobile-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-sm text-gray-600">Aucune image mobile sélectionnée</p>
                                    <p class="text-xs text-gray-500 mt-1">L’image ordinateur sera utilisée automatiquement</p>
                                </div>
                            </div>

                            <input type="file"
                                   name="mobile_image"
                                   id="mobileImageInput"
                                   accept="image/*"
                                   class="hidden">

                            <button type="button"
                                    onclick="document.getElementById('mobileImageInput').click()"
                                    class="mt-4 w-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-mobile-alt"></i>
                                Télécharger l’image mobile
                            </button>

                            <div class="mt-3 text-xs text-gray-500">
                                <p><i class="fas fa-ruler mr-1"></i> Recommandé: 900×1000 px</p>
                                <p><i class="fas fa-lightbulb mr-1"></i> Gardez le sujet principal près du centre.</p>
                            </div>
                            @error('mobile_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Right Column: Form Fields -->
                    <div class="lg:col-span-2">
                        <!-- Banner Details -->
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails de la Bannière</h3>
                        
                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title', $banner->title ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Titre de la bannière"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-6">
                            <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre arabe <span class="text-xs font-normal text-gray-500">(optionnel)</span>
                            </label>
                            <input type="text"
                                   name="title_ar"
                                   id="title_ar"
                                   value="{{ old('title_ar', $banner->title_ar ?? '') }}"
                                   dir="rtl"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                   placeholder="عنوان البانر">
                            @error('title_ar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                      placeholder="Description de la bannière (optionnel)">{{ old('description', $banner->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">
                                Description arabe <span class="text-xs font-normal text-gray-500">(optionnelle)</span>
                            </label>
                            <textarea name="description_ar"
                                      id="description_ar"
                                      rows="3"
                                      dir="rtl"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                      placeholder="وصف البانر">{{ old('description_ar', $banner->description_ar ?? '') }}</textarea>
                            @error('description_ar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Positions (Checkboxes) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Positions <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal">(Sélectionnez une ou plusieurs positions)</span>
                            </label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @php
                                    $positions = [
                                        'hero' => [
                                            'label' => 'Section Hero',
                                            'description' => 'Carrousel principal en haut de page',
                                            'icon' => 'fas fa-home'
                                        ],
                                        'middle' => [
                                            'label' => 'Milieu de Page',
                                            'description' => 'Section intermédiaire',
                                            'icon' => 'fas fa-square'
                                        ],
                                        'bottom' => [
                                            'label' => 'Bas de Page',
                                            'description' => 'Appel à l\'action final',
                                            'icon' => 'fas fa-arrow-down'
                                        ],
                                        'sidebar' => [
                                            'label' => 'Barre Latérale',
                                            'description' => 'Colonne latérale',
                                            'icon' => 'fas fa-columns'
                                        ],
                                    ];
                                    
                                    $selectedPositions = old('positions', isset($banner) ? json_decode($banner->positions, true) ?? [$banner->position] : []);
                                    if (is_string($selectedPositions)) {
                                        $selectedPositions = json_decode($selectedPositions, true);
                                    }
                                    if (empty($selectedPositions) && isset($banner) && $banner->position) {
                                        $selectedPositions = [$banner->position];
                                    }
                                @endphp
                                
                                @foreach($positions as $key => $position)
                                    <label class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:border-green-400 hover:bg-green-50 transition-all duration-200 cursor-pointer group has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" 
                                                   name="positions[]" 
                                                   value="{{ $key }}"
                                                   {{ in_array($key, (array)$selectedPositions) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900">
                                                    <i class="{{ $position['icon'] }} text-gray-500 mr-2"></i>
                                                    {{ $position['label'] }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 group-has-[:checked]:bg-green-100 group-has-[:checked]:text-green-800">
                                                    {{ $key }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $position['description'] }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            <!-- Hidden input for single position (backward compatibility) -->
                            <input type="hidden" name="position" id="selectedPosition" value="{{ old('position', $banner->position ?? '') }}">
                            
                            @error('positions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('positions.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Order -->
                        <div class="mb-6">
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                Ordre d'affichage
                            </label>
                            <div class="flex items-center space-x-4">
                                <input type="number" 
                                       name="order" 
                                       id="order"
                                       value="{{ old('order', $banner->order ?? 0) }}"
                                       min="0"
                                       max="100"
                                       class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                <div class="flex-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <span>Définit l'ordre d'affichage (plus petit = premier)</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        Pour les positions multiples, l'ordre s'applique à toutes
                                    </div>
                                </div>
                            </div>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Call to Action -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Appel à l'action (Bouton)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- CTA Text -->
                                <div>
                                    <label for="cta_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Texte du bouton
                                    </label>
                                    <input type="text" 
                                           name="cta_text" 
                                           id="cta_text"
                                           value="{{ old('cta_text', $banner->cta_text ?? '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Ex: Découvrir maintenant">
                                </div>
                                
                                <div>
                                    <label for="cta_text_ar" class="block text-sm font-medium text-gray-700 mb-2">
                                        Texte du bouton arabe
                                    </label>
                                    <input type="text"
                                           name="cta_text_ar"
                                           id="cta_text_ar"
                                           value="{{ old('cta_text_ar', $banner->cta_text_ar ?? '') }}"
                                           dir="rtl"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="اكتشف الآن">
                                    @error('cta_text_ar')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- CTA Link -->
                                <div>
                                    <label for="cta_link" class="block text-sm font-medium text-gray-700 mb-2">
                                        Lien du bouton
                                    </label>
                                    <input type="url" 
                                           name="cta_link" 
                                           id="cta_link"
                                           value="{{ old('cta_link', $banner->cta_link ?? '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Ex: https://votresite.com/produits">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle"></i>
                                Remplissez les deux champs pour afficher un bouton d'action
                            </p>
                        </div>
                        
                        <!-- Schedule -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Programmation</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div>
                                    <label for="start_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de début
                                    </label>
                                    <input type="datetime-local" 
                                           name="start_at" 
                                           id="start_at"
                                           value="{{ old('start_at', isset($banner) && $banner->start_at ? $banner->start_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Laisser vide pour commencer immédiatement
                                    </p>
                                </div>
                                
                                <!-- End Date -->
                                <div>
                                    <label for="end_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de fin
                                    </label>
                                    <input type="datetime-local" 
                                           name="end_at" 
                                           id="end_at"
                                           value="{{ old('end_at', isset($banner) && $banner->end_at ? $banner->end_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Laisser vide pour pas de date d'expiration
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Activer cette bannière
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle"></i>
                                Désactivez pour cacher temporairement la bannière
                            </p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.banners.index') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg hover:from-green-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                {{ isset($banner) ? 'Mettre à jour' : 'Créer la bannière' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Preview Section -->
        @if(isset($banner))
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aperçu</h3>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="w-full h-64 bg-gray-100 rounded-lg overflow-hidden relative">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" 
                             alt="{{ $banner->title }}"
                             class="w-full h-full object-cover">
                        @if($banner->title || $banner->description)
                            <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent flex items-center p-8">
                                <div class="text-white max-w-md">
                                    @if($banner->title)
                                        <h4 class="text-2xl font-bold mb-2">{{ $banner->title }}</h4>
                                    @endif
                                    @if($banner->description)
                                        <p class="mb-4">{{ $banner->description }}</p>
                                    @endif
                                    @if($banner->cta_text && $banner->cta_link)
                                        <a href="#" class="inline-block bg-white text-green-700 px-4 py-2 rounded-lg font-semibold">
                                            {{ $banner->cta_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div class="text-gray-600">
                            <div class="font-medium">Positions:</div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @php
                                    $displayPositions = isset($banner->positions) ? json_decode($banner->positions, true) : [$banner->position];
                                @endphp
                                @foreach($displayPositions as $pos)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 capitalize">
                                        {{ $pos }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-gray-600">
                            <div class="font-medium">Statut:</div>
                            <div class="capitalize">{{ $banner->is_active ? 'Actif' : 'Inactif' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const mobileImageInput = document.getElementById('mobileImageInput');
        const mobileImagePreview = document.getElementById('mobileImagePreview');
        
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `
                            <img src="${e.target.result}" 
                                 alt="Prévisualisation"
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                            <div class="text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Image sélectionnée
                            </div>
                        `;
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        }

        if (mobileImageInput) {
            mobileImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        mobileImagePreview.innerHTML = `
                            <img src="${e.target.result}"
                                 alt="Prévisualisation mobile"
                                 class="mx-auto h-64 w-full rounded-lg object-cover">
                            <div class="mt-3 text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Image mobile sélectionnée
                            </div>
                        `;
                    }

                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Update hidden position field when checkboxes change
        const positionCheckboxes = document.querySelectorAll('input[name="positions[]"]');
        const selectedPositionInput = document.getElementById('selectedPosition');
        
        function updateSelectedPosition() {
            const selected = Array.from(positionCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            // Set the first selected position for backward compatibility
            selectedPositionInput.value = selected.length > 0 ? selected[0] : '';
            
            // Update form validation
            const form = document.getElementById('bannerForm');
            const positionError = document.querySelector('.position-error');
            
            if (selected.length === 0) {
                if (!positionError) {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-2 text-sm text-red-600 position-error';
                    errorDiv.textContent = 'Veuillez sélectionner au moins une position.';
                    positionCheckboxes[0].closest('.mb-6').appendChild(errorDiv);
                }
            } else if (positionError) {
                positionError.remove();
            }
        }
        
        positionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedPosition);
        });
        
        // Initial update
        updateSelectedPosition();
        
        // Form validation
        const form = document.getElementById('bannerForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedPositions = Array.from(positionCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                
                if (selectedPositions.length === 0) {
                    e.preventDefault();
                    updateSelectedPosition();
                    document.querySelector('.position-error')?.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    return false;
                }
            });
        }
    });
</script>
