{{-- resources/views/admin/banners/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Modifier la Bannière - Admin')
@section('header', 'Modifier la Bannière')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-green-600">
                            <i class="fas fa-home mr-2"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <a href="{{ route('admin.banners.index') }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-green-600 md:ml-2">
                                Bannières
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                Modifier
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Modifier la bannière</h1>
                    <p class="text-gray-600">Mettez à jour les informations de la bannière</p>
                </div>
                <a href="{{ route('admin.banners.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow p-6">
            <form action="{{ route('admin.banners.update', $banner) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="bannerForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Image Upload -->
                    <div class="lg:col-span-1">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image ordinateur / tablette
                            </label>
                            <div id="imagePreview" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-500 transition-colors cursor-pointer"
                                 onclick="document.getElementById('imageInput').click()">
                                @if($banner->image_path)
                                    <img src="{{ asset('storage/' . $banner->image_path) }}"
                                         alt="Prévisualisation"
                                         class="w-full h-48 object-cover rounded-lg mb-2">
                                    <p class="text-sm text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Image actuelle
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Cliquer pour changer
                                    </p>
                                @else
                                    <div class="py-12">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-sm text-gray-600">Cliquer pour sélectionner une image</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF jusqu'à 5MB</p>
                                    </div>
                                @endif
                            </div>

                            <input type="file"
                                   name="image"
                                   id="imageInput"
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this)">

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if($banner->image_path)
                                <label class="mt-4 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    <input type="checkbox" name="delete_image" value="1" class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">
                                    <span>
                                        <span class="font-semibold">Supprimer la photo actuelle</span><br>
                                        La bannière sera désactivée jusqu'à l'ajout d'une nouvelle image.
                                    </span>
                                </label>
                            @endif

                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle"></i>
                                Laisser vide pour conserver l'image actuelle
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-ruler"></i>
                                Taille recommandée: 1920×900 px
                            </p>
                        </div>

                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image mobile portrait <span class="text-xs font-normal text-gray-500">(optionnelle)</span>
                            </label>
                            <div id="mobileImagePreview"
                                 class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-500 transition-colors cursor-pointer"
                                 onclick="document.getElementById('mobileImageInput').click()">
                                @if($banner->mobile_image_path)
                                    <img src="{{ asset('storage/' . $banner->mobile_image_path) }}"
                                         alt="Prévisualisation mobile"
                                         class="mx-auto h-64 w-full rounded-lg object-cover">
                                    <p class="mt-2 text-sm text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Image mobile actuelle
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Cliquer pour changer</p>
                                @else
                                    <div class="py-10">
                                        <i class="fas fa-mobile-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-sm text-gray-600">Aucune image mobile</p>
                                        <p class="text-xs text-gray-500 mt-1">L’image ordinateur est utilisée sur mobile</p>
                                    </div>
                                @endif
                            </div>

                            <input type="file"
                                   name="mobile_image"
                                   id="mobileImageInput"
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewMobileImage(this)">

                            @error('mobile_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if($banner->mobile_image_path)
                                <label class="mt-4 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    <input type="checkbox" name="delete_mobile_image" value="1" class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">
                                    <span>
                                        <span class="font-semibold">Supprimer l’image mobile</span><br>
                                        L’image ordinateur sera utilisée à la place.
                                    </span>
                                </label>
                            @endif

                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-ruler"></i>
                                Taille recommandée: 900×1000 px
                            </p>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-gray-50 rounded-lg p-4 mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Informations</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Créée le:</span>
                                    <span class="font-medium">{{ $banner->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dernière modification:</span>
                                    <span class="font-medium">{{ $banner->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Statut:</span>
                                    <span class="font-medium {{ $banner->is_active ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $banner->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Form Fields -->
                    <div class="lg:col-span-2">
                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title', $banner->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Titre de la bannière">
                            @error('title')
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
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Description de la bannière">{{ old('description', $banner->description) }}</textarea>
                            @error('description')
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

                                    $selectedPositions = old('positions',
                                        isset($banner->positions) ? json_decode($banner->positions, true) : [$banner->position]
                                    );

                                    if (is_string($selectedPositions)) {
                                        $selectedPositions = json_decode($selectedPositions, true);
                                    }
                                @endphp

                                @foreach($positions as $key => $position)
                                    <label class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:border-green-400 hover:bg-green-50 transition-all duration-200 cursor-pointer group has-[:checked]:border-green-500 has-[:checked]:bg-green-50 has-[:checked]:ring-1 has-[:checked]:ring-green-500">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox"
                                                   name="positions[]"
                                                   value="{{ $key }}"
                                                   {{ in_array($key, (array)$selectedPositions) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900 flex items-center">
                                                    <i class="{{ $position['icon'] }} text-gray-500 mr-2 text-sm"></i>
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
                            <input type="hidden" name="position" id="selectedPosition" value="{{ old('position', $banner->position) }}">

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
                                <div class="relative">
                                    <input type="number"
                                           name="order"
                                           id="order"
                                           value="{{ old('order', $banner->order ?? 0) }}"
                                           min="0"
                                           max="100"
                                           class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-sort-numeric-down mr-2"></i>
                                        <span>Plus petit = premier dans l'ordre d'affichage</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        S'applique à toutes les positions sélectionnées
                                    </div>
                                </div>
                            </div>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Call to Action -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Appel à l'action (Optionnel)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- CTA Text -->
                                <div>
                                    <label for="cta_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Texte du bouton
                                    </label>
                                    <input type="text"
                                           name="cta_text"
                                           id="cta_text"
                                           value="{{ old('cta_text', $banner->cta_text) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           placeholder="Ex: Découvrir maintenant">
                                    @error('cta_text')
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
                                           value="{{ old('cta_link', $banner->cta_link) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           placeholder="Ex: /produits ou https://...">
                                    @error('cta_link')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                                    <span>Remplissez les deux champs pour afficher un bouton</span>
                                </div>
                                @if($banner->cta_text && $banner->cta_link)
                                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Bouton actif: "{{ $banner->cta_text }}"
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Programmation (Optionnel)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div>
                                    <label for="start_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de début
                                    </label>
                                    <input type="datetime-local"
                                           name="start_at"
                                           id="start_at"
                                           value="{{ old('start_at', $banner->start_at ? $banner->start_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Laisser vide = immédiat
                                    </p>
                                    @error('start_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label for="end_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de fin
                                    </label>
                                    <input type="datetime-local"
                                           name="end_at"
                                           id="end_at"
                                           value="{{ old('end_at', $banner->end_at ? $banner->end_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Laisser vide = pas d'expiration
                                    </p>
                                    @error('end_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-3 text-sm">
                                @if($banner->start_at || $banner->end_at)
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    @if($banner->start_at && $banner->end_at)
                                        Programmé du {{ $banner->start_at->format('d/m/Y H:i') }} au {{ $banner->end_at->format('d/m/Y H:i') }}
                                    @elseif($banner->start_at)
                                        Commence le {{ $banner->start_at->format('d/m/Y H:i') }}
                                    @elseif($banner->end_at)
                                        Expire le {{ $banner->end_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Bannière active
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Désactivez pour masquer temporairement la bannière
                            </p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-history mr-1"></i>
                        Dernière modification: {{ $banner->updated_at->diffForHumans() }}
                    </div>
                    <div class="flex gap-4">
                        <button type="button"
                                onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md hover:shadow-lg flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview & Danger Zone -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Preview -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aperçu de la bannière</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="w-full h-64 bg-gray-100 relative">
                            @if($banner->image_path)
                                <img src="{{ asset('storage/' . $banner->image_path) }}"
                                     alt="{{ $banner->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center text-gray-400">
                                    <div class="text-center">
                                        <i class="fas fa-image text-4xl mb-2"></i>
                                        <p>Aucune image</p>
                                    </div>
                                </div>
                            @endif
                            @if($banner->title || $banner->description)
                                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-8">
                                    <div class="text-white max-w-md">
                                        @if($banner->title)
                                            <h4 class="text-2xl font-bold mb-2">{{ $banner->title }}</h4>
                                        @endif
                                        @if($banner->description)
                                            <p class="mb-4 text-gray-200">{{ $banner->description }}</p>
                                        @endif
                                        @if($banner->cta_text && $banner->cta_link)
                                            <a href="#" class="inline-block bg-white text-green-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                                {{ $banner->cta_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="text-gray-600">
                                    <div class="font-medium">Positions actives:</div>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @php
                                            $displayPositions = isset($banner->positions) ? json_decode($banner->positions, true) : [$banner->position];
                                        @endphp
                                        @foreach($displayPositions as $pos)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 capitalize">
                                                <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                                                {{ $pos }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-gray-600">
                                    <div class="font-medium">Statut:</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas fa-{{ $banner->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                            {{ $banner->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow p-6 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-700 mb-4">Zone de danger</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                Ces actions sont irréversibles
                            </p>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.banners.destroy', $banner) }}"
                                  method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette bannière ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                    Supprimer cette bannière
                                </button>
                            </form>

                            <!-- Duplicate Button -->
                            <form action="{{ route('admin.banners.duplicate', $banner) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-copy"></i>
                                    Dupliquer cette bannière
                                </button>
                            </form>
                                   <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="mt-3">
                        @csrf
                        @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <i class="fas fa-toggle-{{ $banner->is_active ? 'off' : 'on' }}"></i>
                                    {{ $banner->is_active ? 'Désactiver' : 'Activer' }} immédiatement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}"
                         alt="Nouvelle image"
                         class="w-full h-48 object-cover rounded-lg mb-2">
                    <p class="text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        Nouvelle image sélectionnée
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Cliquer pour changer à nouveau
                    </p>
                `;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMobileImage(input) {
        const preview = document.getElementById('mobileImagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}"
                         alt="Nouvelle image mobile"
                         class="mx-auto h-64 w-full rounded-lg object-cover">
                    <p class="mt-2 text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        Nouvelle image mobile sélectionnée
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Cliquer pour changer à nouveau
                    </p>
                `;
            }

            reader.readAsDataURL(input.files[0]);
        }
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
</script>
@endpush
