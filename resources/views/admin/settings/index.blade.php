@extends('admin.layouts.app')

@section('title', 'Paramètres - Admin')
@section('header', 'Paramètres de la Boutique')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" 
              class="bg-white rounded-xl shadow-lg overflow-hidden">
            @csrf
            @method('PUT')
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Configuration Générale</h2>
                        <p class="text-blue-100 mt-1">Gérez les informations de votre boutique</p>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" 
                                class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors duration-200 flex items-center shadow-lg">
                            <i class="fas fa-save mr-2"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <!-- Success/Error Messages -->
            @if(session('success'))
    @php
        $success = session('success');
        // Handle both array and string success messages
        $title = is_array($success) ? ($success['title'] ?? 'Succès') : 'Succès';
        $message = is_array($success) ? ($success['message'] ?? $success) : $success;
        $icon = is_array($success) ? ($success['icon'] ?? 'success') : 'success';
    @endphp
    
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($icon === 'success')
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                @elseif($icon === 'info')
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                @elseif($icon === 'warning')
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                @else
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    {{ $title }}
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>{{ $message }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

            @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Il y a {{ $errors->count() }} erreur(s) dans votre formulaire
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

                <!-- Section 1: Informations Générales -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Informations Générales</h3>
                            <p class="text-gray-600">Informations de base de votre boutique</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-signature text-blue-500 mr-2"></i>
                                Nom de la Boutique *
                            </label>
                            <input type="text" name="store_name" required
                                   value="{{ old('store_name', $settings['store_name'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="Entrez le nom de votre établissement">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                Horaires d'Ouverture *
                            </label>
                            <input type="text" name="working_hours" required
                                   value="{{ old('working_hours', $settings['working_hours'] ?? '') }}"
                                   placeholder="Lun-Sam: 9h-20h, Dim: 10h-18h"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact & Réseaux -->
                <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                    <div class="flex items-center mb-6">
                        <div class="bg-green-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-phone-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Contact & Réseaux</h3>
                            <p class="text-gray-600">Coordonnées et réseaux sociaux</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-phone text-green-500 mr-2"></i>
                                Téléphone *
                            </label>
                            <input type="text" name="phone" required
                                   value="{{ old('phone', $settings['phone'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                   placeholder="+212 6XX-XXXXXX">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-envelope text-green-500 mr-2"></i>
                                Email *
                            </label>
                            <input type="email" name="email" required
                                   value="{{ old('email', $settings['email'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                   placeholder="contact@votre-boutique.com">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fab fa-whatsapp text-green-600 mr-2"></i>
                                WhatsApp
                            </label>
                            <input type="text" name="whatsapp"
                                   value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                   placeholder="+212 6XX-XXXXXX">
                            <p class="text-sm text-gray-500 mt-1 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Format international requis
                            </p>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                                Adresse Complète *
                            </label>
                            <textarea name="address" rows="2" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                      placeholder="Entrez votre adresse complète">{{ old('address', $settings['address'] ?? '') }}</textarea>
                        </div>

                        <!-- New: Facebook Field -->
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fab fa-facebook text-blue-600 mr-2"></i>
                                Page Facebook
                            </label>
                            <div class="relative">
                                <input type="url" name="facebook_url"
                                       value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                       placeholder="https://facebook.com/votre-page">
                                <div class="absolute right-3 top-3 text-blue-600">
                                    <i class="fab fa-facebook"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Optionnel - URL complète de votre page
                            </p>
                        </div>

                        <!-- New: Instagram Field -->
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fab fa-instagram text-pink-600 mr-2"></i>
                                Compte Instagram
                            </label>
                            <div class="relative">
                                <input type="url" name="instagram_url"
                                       value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                       placeholder="https://instagram.com/votre-compte">
                                <div class="absolute right-3 top-3 text-pink-600">
                                    <i class="fab fa-instagram"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Optionnel - URL complète de votre compte
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Localisation GPS -->
       <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
    <div class="flex items-center mb-6">
        <div class="bg-purple-500 p-3 rounded-lg mr-4">
            <i class="fas fa-map-marked-alt text-white text-xl"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">Localisation GPS</h3>
            <p class="text-gray-600">Coordonnées pour la carte et calcul des distances</p>
        </div>
    </div>

    <!-- New Maps Link Input -->
    <div class="mb-6">
        <label class="block font-semibold text-gray-700 mb-2 flex items-center">
            <i class="fas fa-link text-purple-500 mr-2"></i>
            Lien Google Maps
        </label>
        <div class="relative">
            <input type="url" name="maps_link" 
                   value="{{ old('maps_link', $settings['maps_link'] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                   placeholder="https://maps.google.com/?q=33.5731,-7.5898">
            <div class="absolute right-3 top-3 text-purple-500">
                <i class="fas fa-external-link-alt"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-1">
            Optionnel : Collez ici le lien de partage Google Maps
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-globe-americas text-purple-500 mr-2"></i>
                Latitude *
            </label>
            <div class="relative">
                <input type="number" name="latitude" step="any" required
                       value="{{ old('latitude', $settings['latitude'] ?? '33.5731') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                       placeholder="33.5731">
                <div class="absolute right-3 top-3 text-purple-500">
                    <i class="fas fa-crosshairs"></i>
                </div>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-globe-americas text-purple-500 mr-2"></i>
                Longitude *
            </label>
            <div class="relative">
                <input type="number" name="longitude" step="any" required
                       value="{{ old('longitude', $settings['longitude'] ?? '-7.5898') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                       placeholder="-7.5898">
                <div class="absolute right-3 top-3 text-purple-500">
                    <i class="fas fa-crosshairs"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4 p-4 bg-purple-100 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-purple-600 mt-1 mr-3"></i>
            <div>
                <p class="text-sm text-purple-800 font-medium">Conseil :</p>
                <p class="text-sm text-purple-700">
                    Utilisez <a href="https://www.latlong.net/" target="_blank" class="underline font-semibold">latlong.net</a> 
                    pour obtenir vos coordonnées précises ou copiez directement le lien de partage depuis Google Maps
                </p>
            </div>
        </div>
    </div>
</div>

                <!-- Section 4: Paramètres de Livraison -->
                <div class="bg-orange-50 rounded-xl p-6 border border-orange-100">
                    <div class="flex items-center mb-6">
                        <div class="bg-orange-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-shipping-fast text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Paramètres de Livraison</h3>
                            <p class="text-gray-600">Configuration des services de livraison</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-money-bill-wave text-orange-500 mr-2"></i>
                                Frais de Livraison (DH) *
                            </label>
                            <div class="relative">
                                <input type="number" name="delivery_fee" min="0" step="0.01" required
                                       value="{{ old('delivery_fee', $settings['delivery_fee'] ?? '30') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                <div class="absolute right-3 top-3 text-gray-500">DH</div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-truck text-orange-500 mr-2"></i>
                                Zone de Livraison
                            </label>
                            <input type="text" name="delivery_zone"
                                   value="{{ old('delivery_zone', $settings['delivery_zone'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                   placeholder="Ex: Casablanca et périphérie (10km)">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-clock text-orange-500 mr-2"></i>
                                Délai de Livraison
                            </label>
                            <input type="text" name="delivery_time"
                                   value="{{ old('delivery_time', $settings['delivery_time'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                   placeholder="Ex: 24-48 heures">
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-percentage text-orange-500 mr-2"></i>
                                Livraison Gratuite Seuil
                            </label>
                            <div class="relative">
                                <input type="number" name="free_delivery_threshold" min="0" step="0.01"
                                       value="{{ old('free_delivery_threshold', $settings['free_delivery_threshold'] ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                       placeholder="Montant pour livraison gratuite">
                                <div class="absolute right-3 top-3 text-gray-500">DH</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Logo & Identité Visuelle -->
                <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                    <div class="flex items-center mb-6">
                        <div class="bg-indigo-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-image text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Logo & Identité Visuelle</h3>
                            <p class="text-gray-600">Image de votre établissement</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <!-- Current Logo Preview -->
                        <div class="md:w-1/3">
                            <label class="block font-semibold text-gray-700 mb-3">Logo Actuel</label>
                            @if(!empty($settings['logo']))
                                <div class="bg-white p-4 rounded-xl shadow-sm border">
                                    <img src="{{ asset('storage/' . $settings['logo']) }}" 
                                         alt="Logo actuel" 
                                         class="w-full h-48 object-contain rounded-lg">
                                    <div class="mt-3 flex space-x-2">
                                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full">
                                            Logo actif
                                        </span>
                                        <button type="button" id="deleteLogoBtn"
                                                class="inline-block bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full hover:bg-red-200">
                                            <i class="fas fa-trash mr-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-100 p-8 rounded-xl text-center border-2 border-dashed border-gray-300">
                                    <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Aucun logo uploadé</p>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Section -->
                        <div class="md:w-2/3">
                            <label class="block font-semibold text-gray-700 mb-3">
                                <i class="fas fa-upload text-indigo-500 mr-2"></i>
                                Télécharger un Nouveau Logo
                            </label>
                            
                            <div class="border-2 border-dashed border-indigo-300 rounded-xl p-8 bg-white hover:bg-indigo-50 transition-colors duration-200">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt text-indigo-400 text-4xl mb-4"></i>
                                    <p class="text-gray-700 font-medium mb-2">Glissez-déposez votre fichier</p>
                                    <p class="text-gray-500 text-sm mb-4">ou cliquez pour parcourir</p>
                                    
                                    <input type="file" name="logo" accept="image/*" id="logoUpload"
                                           class="hidden">
                                    <label for="logoUpload" 
                                           class="inline-block bg-indigo-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-600 cursor-pointer transition-colors duration-200">
                                        <i class="fas fa-folder-open mr-2"></i> Choisir un fichier
                                    </label>
                                    
                                    <p id="fileName" class="text-sm text-gray-600 mt-3"></p>
                                </div>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-indigo-100 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-indigo-600 mr-2"></i>
                                        <span class="font-medium text-indigo-800">Recommandations :</span>
                                    </div>
                                    <ul class="text-sm text-indigo-700 mt-2 space-y-1">
                                        <li>• Format: PNG, JPG ou SVG</li>
                                        <li>• Taille: 100×100px min</li>
                                        <li>• Fond transparent (recommandé)</li>
                                        <li>• Taille max: 2MB</li>
                                    </ul>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex items-center">
                                        <i class="fas fa-eye text-gray-600 mr-2"></i>
                                        <span class="font-medium text-gray-800">Aperçu :</span>
                                    </div>
                                    <div id="imagePreview" class="mt-2 hidden">
                                        <img id="previewImage" class="w-16 h-16 object-cover rounded-lg border">
                                    </div>
                                    <p id="noPreview" class="text-sm text-gray-500 mt-2">
                                        Aperçu disponible après sélection
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                <div class="flex items-center mb-6">
                    <div class="bg-amber-500 p-3 rounded-lg mr-4"><i class="fas fa-palette text-white text-xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Personnalisation du site</h3>
                        <p class="text-gray-600">Couleurs principales et textes de la page d'accueil</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div><label class="block font-semibold text-gray-700 mb-2">Couleur principale</label><input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#B7791F') }}" class="w-full h-12 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Couleur secondaire</label><input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#3D2B1F') }}" class="w-full h-12 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Couleur d’accent</label><input type="color" name="accent_color" value="{{ old('accent_color', $settings['accent_color'] ?? '#F4B400') }}" class="w-full h-12 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Arrière-plan</label><input type="color" name="background_color" value="{{ old('background_color', $settings['background_color'] ?? '#FFFCF5') }}" class="w-full h-12 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Couleur des boutons</label><input type="color" name="button_color" value="{{ old('button_color', $settings['button_color'] ?? '#B7791F') }}" class="w-full h-12 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Favicon</label><input type="file" name="favicon" accept="image/png,image/jpeg,image/svg+xml,image/webp,image/x-icon" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white"></div>
                    <div class="md:col-span-2"><label class="block font-semibold text-gray-700 mb-2">Titre SEO</label><input type="text" name="seo_title" maxlength="70" value="{{ old('seo_title', $settings['seo_title'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div class="md:col-span-2"><label class="block font-semibold text-gray-700 mb-2">Description SEO</label><textarea name="seo_description" maxlength="170" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('seo_description', $settings['seo_description'] ?? '') }}</textarea></div>
                    <div class="md:col-span-2"><label class="block font-semibold text-gray-700 mb-2">Texte du pied de page</label><textarea name="footer_text" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">X / Twitter</label><input type="url" name="twitter_url" value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">TikTok</label><input type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">YouTube</label><input type="url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Titre hero (début)</label><input type="text" name="hero_title_prefix" value="{{ old('hero_title_prefix', $settings['hero_title_prefix'] ?? 'Your') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block font-semibold text-gray-700 mb-2">Titre hero (accent)</label><input type="text" name="hero_title_emphasis" value="{{ old('hero_title_emphasis', $settings['hero_title_emphasis'] ?? 'Store') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div class="md:col-span-2"><label class="block font-semibold text-gray-700 mb-2">Titre hero (fin)</label><input type="text" name="hero_title_suffix" value="{{ old('hero_title_suffix', $settings['hero_title_suffix'] ?? 'Your Priority') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg"></div>
                    <div class="md:col-span-2"><label class="block font-semibold text-gray-700 mb-2">Sous-titre hero</label><textarea name="hero_subtitle" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea></div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-8 py-6 border-t">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tous les champs marqués d'un * sont obligatoires
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg flex items-center">
                            <i class="fas fa-save mr-2"></i> Enregistrer les Paramètres
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo upload preview
    const logoUpload = document.getElementById('logoUpload');
    const fileName = document.getElementById('fileName');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const noPreview = document.getElementById('noPreview');

    if (logoUpload) {
        logoUpload.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = `Fichier sélectionné : ${file.name}`;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    noPreview.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Delete logo button
    const deleteLogoBtn = document.getElementById('deleteLogoBtn');
    if (deleteLogoBtn) {
        deleteLogoBtn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer le logo ?')) {
                fetch('{{ route("admin.settings.delete-logo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue.');
                });
            }
        });
    }


    // Auto-save for single fields (optional feature)
    const autoSaveFields = document.querySelectorAll('input[data-auto-save]');
    autoSaveFields.forEach(field => {
        field.addEventListener('blur', function() {
            const key = this.name;
            const value = this.value;
            
            fetch(`/admin/settings/${key}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ value: value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Setting saved:', key);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Set default coordinates if empty on page load
    const latitudeInput = document.querySelector('input[name="latitude"]');
    const longitudeInput = document.querySelector('input[name="longitude"]');
    
    if (latitudeInput && !latitudeInput.value) {
        latitudeInput.value = '33.5731';
    }
    if (longitudeInput && !longitudeInput.value) {
        longitudeInput.value = '-7.5898';
    }
});
</script>