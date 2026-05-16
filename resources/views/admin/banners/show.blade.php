{{-- resources/views/admin/banners/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Détails Bannière - Admin')
@section('header', 'Détails de la Bannière')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ route('admin.banners.index') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
        </div>
        
        <!-- Banner Details Card -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-teal-50 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $banner->title ?? 'Sans titre' }}</h2>
                        <div class="flex items-center gap-4 mt-2">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800',
                                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                                    'expired' => 'bg-red-100 text-red-800',
                                ];
                                $status = $banner->status; // Vous devez définir cette méthode dans le modèle
                            @endphp
                            <span class="inline-flex items-center gap-2 {{ $statusColors[$status] }} px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-circle text-xs"></i>
                                {{ $status === 'active' ? 'Actif' : ($status === 'inactive' ? 'Inactif' : ($status === 'scheduled' ? 'Programmée' : 'Expirée')) }}
                            </span>
                            <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-{{ $banner->position == 'hero' ? 'images' : 'image' }}"></i>
                                {{ $banner->position == 'hero' ? 'Section Hero' : ($banner->position == 'middle' ? 'Milieu de Page' : ($banner->position == 'bottom' ? 'Bas de Page' : 'Barre Latérale')) }}
                            </span>
                            <span class="text-sm text-gray-600">
                                <i class="far fa-calendar-alt mr-1"></i>
                                Créée le {{ $banner->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.banners.edit', $banner) }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit mr-2"></i> Modifier
                        </a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" 
                              method="POST"
                              onsubmit="return confirm('Supprimer cette bannière ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-trash mr-2"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Image Preview -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Image</h3>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                 alt="{{ $banner->title }}"
                                 class="w-full h-64 object-cover">
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <p><i class="fas fa-info-circle mr-2"></i> Chemin: storage/{{ $banner->image_path }}</p>
                            <p class="mt-1"><i class="fas fa-clock mr-2"></i> Dernière modification: {{ $banner->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations</h3>
                        
                        <!-- Description -->
                        @if($banner->description)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Description</h4>
                                <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $banner->description }}</p>
                            </div>
                        @endif
                        
                        <!-- Call to Action -->
                        @if($banner->cta_text)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Appel à l'action</h4>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-medium">Texte:</span>
                                        <span class="text-gray-800">{{ $banner->cta_text }}</span>
                                    </div>
                                    @if($banner->cta_link)
                                        <div class="flex items-center gap-3">
                                            <span class="font-medium">Lien:</span>
                                            <a href="{{ $banner->cta_link }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 break-all">
                                                {{ $banner->cta_link }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Schedule -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Programmation</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Date de début</div>
                                    <div class="font-medium">
                                        @if($banner->start_at)
                                            {{ $banner->start_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-500">Immédiatement</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Date de fin</div>
                                    <div class="font-medium">
                                        @if($banner->end_at)
                                            {{ $banner->end_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-500">Pas d'expiration</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Technical Info -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Informations techniques</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-600">Ordre d'affichage</div>
                                        <div class="font-medium">{{ $banner->order }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-600">Statut</div>
                                        <div class="font-medium">
                                            <span class="{{ $banner->is_active ? 'text-green-600' : 'text-gray-600' }}">
                                                {{ $banner->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats & Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Preview -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aperçu live</h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="relative h-48 bg-gradient-to-r from-gray-50 to-gray-100">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" 
                             alt="{{ $banner->title }}"
                             class="w-full h-full object-cover">
                        @if($banner->title || $banner->description)
                            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-6">
                                <div class="text-white">
                                    @if($banner->title)
                                        <h4 class="text-xl font-bold mb-2">{{ $banner->title }}</h4>
                                    @endif
                                    @if($banner->description)
                                        <p class="mb-3 text-sm">{{ Str::limit($banner->description, 80) }}</p>
                                    @endif
                                    @if($banner->cta_text && $banner->cta_link)
                                        <a href="#" class="inline-block bg-white text-green-700 px-4 py-2 rounded text-sm font-semibold">
                                            {{ $banner->cta_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <button onclick="previewFullBanner()" 
                            class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i> Voir en plein écran
                    </button>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="inline w-full">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full {{ $banner->is_active ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center mr-3">
                                    <i class="fas {{ $banner->is_active ? 'fa-pause text-red-600' : 'fa-play text-green-600' }}"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-medium">{{ $banner->is_active ? 'Désactiver' : 'Activer' }} la bannière</div>
                                    <div class="text-sm text-gray-600">{{ $banner->is_active ? 'Masquer temporairement' : 'Rendre visible' }}</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </form>
                    
                    <!-- Duplicate -->
                    <a href="{{ route('admin.banners.duplicate', $banner) }}" 
                       class="block">
                        <div class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-copy text-blue-600"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-medium">Dupliquer la bannière</div>
                                    <div class="text-sm text-gray-600">Créer une copie avec les mêmes paramètres</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </a>
                    
                    <!-- View on Site -->
                    <a href="{{ url('/') }}" 
                       target="_blank"
                       class="block">
                        <div class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-external-link-alt text-green-600"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-medium">Voir sur le site</div>
                                    <div class="text-sm text-gray-600">Ouvrir la page d'accueil</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewFullBanner() {
        // Open a modal or new window with full banner preview
        const url = "{{ route('admin.banners.preview', $banner) }}";
        window.open(url, '_blank', 'width=1200,height=800');
    }
</script>
@endpush