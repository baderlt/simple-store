{{-- resources/views/admin/banners/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Bannières - Admin')
@section('header', 'Gestion des Bannières')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Bannières</h1>
            <p class="text-gray-600">Gérez les bannières affichées sur votre site</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" 
           class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i> Nouvelle Bannière
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-image text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Bannières</p>
                    <p class="text-2xl font-bold">{{ $banners->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-play-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Bannières Actives</p>
                    <p class="text-2xl font-bold">{{ $activeBanners }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Programmées</p>
                    <p class="text-2xl font-bold">{{ $scheduledBanners }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i class="fas fa-calendar-times text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Expirées</p>
                    <p class="text-2xl font-bold">{{ $expiredBanners }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par position</label>
                <select id="positionFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Toutes positions</option>
                    <option value="hero">Hero (Carrousel)</option>
                    <option value="middle">Milieu de page</option>
                    <option value="bottom">Bas de page</option>
                    <option value="sidebar">Barre latérale</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par statut</label>
                <select id="statusFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Tous statuts</option>
                    <option value="active">Actives</option>
                    <option value="inactive">Inactives</option>
                    <option value="scheduled">Programmées</option>
                    <option value="expired">Expirées</option>
                </select>
            </div>
            
            <button id="resetFilters" class="mt-6 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Réinitialiser
            </button>
        </div>
    </div>

    <!-- Banners Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 banner-row" 
                            data-position="{{ $banner->position }}"
                            data-status="{{ $banner->status }}">
                            <!-- Image -->
                            <td class="px-6 py-4">
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                         alt="{{ $banner->title }}"
                                         class="w-24 h-16 object-cover rounded-lg shadow-sm border border-gray-200 group-hover:scale-105 transition-transform duration-200">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 rounded-lg transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <a href="{{ asset('storage/' . $banner->image_path) }}" 
                                           target="_blank"
                                           class="text-white text-sm bg-black bg-opacity-60 px-2 py-1 rounded">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Title & Description -->
                            <td class="px-6 py-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $banner->title ?? 'Sans titre' }}</h4>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $banner->description ?? 'Sans description' }}
                                    </p>
                                    @if($banner->cta_text)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2 py-1 rounded text-xs">
                                                <i class="fas fa-mouse-pointer"></i>
                                                {{ $banner->cta_text }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Position -->
                            <td class="px-6 py-4">
                                @php
                                    $positionColors = [
                                        'hero' => 'bg-purple-100 text-purple-800',
                                        'middle' => 'bg-blue-100 text-blue-800',
                                        'bottom' => 'bg-green-100 text-green-800',
                                        'sidebar' => 'bg-orange-100 text-orange-800',
                                    ];
                                    
                                    $positionIcons = [
                                        'hero' => 'images',
                                        'middle' => 'image',
                                        'bottom' => 'layer-group',
                                        'sidebar' => 'columns',
                                    ];
                                @endphp
                                
                                <span class="inline-flex items-center gap-2 {{ $positionColors[$banner->position] ?? 'bg-gray-100 text-gray-800' }} px-3 py-1.5 rounded-full text-sm font-medium">
                                    <i class="fas fa-{{ $positionIcons[$banner->position] ?? 'image' }}"></i>
                                    {{ ucfirst($banner->position) }}
                                </span>
                                
                                <div class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-sort-amount-down"></i>
                                    Ordre: {{ $banner->order }}
                                </div>
                            </td>
                            
                            <!-- Dates -->
                            <td class="px-6 py-4">
                                @if($banner->start_at)
                                    <div class="text-sm">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            <i class="fas fa-play-circle text-xs"></i>
                                            <span>Début: {{ $banner->start_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($banner->end_at)
                                    <div class="text-sm mt-1">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            <i class="fas fa-stop-circle text-xs"></i>
                                            <span>Fin: {{ $banner->end_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!$banner->start_at && !$banner->end_at)
                                    <span class="text-sm text-gray-500">Pas de dates définies</span>
                                @endif
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @php
                                    $now = now();
                                    $isActive = $banner->is_active;
                                    $hasStarted = !$banner->start_at || $banner->start_at <= $now;
                                    $hasEnded = $banner->end_at && $banner->end_at < $now;
                                @endphp
                                
                                @if(!$isActive)
                                    <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium">
                                        <i class="fas fa-pause-circle"></i>
                                        Inactif
                                    </span>
                                @elseif($hasEnded)
                                    <span class="inline-flex items-center gap-2 bg-red-100 text-red-800 px-3 py-1.5 rounded-full text-sm font-medium">
                                        <i class="fas fa-calendar-times"></i>
                                        Expirée
                                    </span>
                                @elseif(!$hasStarted)
                                    <span class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full text-sm font-medium">
                                        <i class="fas fa-clock"></i>
                                        Programmée
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-3 py-1.5 rounded-full text-sm font-medium">
                                        <i class="fas fa-play-circle"></i>
                                        Active
                                    </span>
                                @endif
                                
                                <!-- Quick toggle -->
                                <div class="mt-2">
                                    <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-xs {{ $banner->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                            {{ $banner->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.banners.show', $banner) }}" 
                                       class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                       title="Aperçu">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette bannière ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Preview button -->
                                    <button onclick="previewBanner({{ $banner->id }})"
                                            class="text-purple-600 hover:text-purple-800 p-2 hover:bg-purple-50 rounded-lg transition-colors duration-200"
                                            title="Prévisualiser">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-image text-4xl mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">Aucune bannière trouvée</p>
                                    <p class="text-gray-600 mt-2">Commencez par créer votre première bannière</p>
                                    <a href="{{ route('admin.banners.create') }}" 
                                       class="inline-block mt-4 bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                                        <i class="fas fa-plus mr-2"></i> Créer une bannière
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($banners->hasPages())
        <div class="mt-6">
            {{ $banners->links() }}
        </div>
    @endif
@endsection

@push('scripts')
<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const positionFilter = document.getElementById('positionFilter');
        const statusFilter = document.getElementById('statusFilter');
        const resetButton = document.getElementById('resetFilters');
        const bannerRows = document.querySelectorAll('.banner-row');
        
        function filterBanners() {
            const position = positionFilter.value;
            const status = statusFilter.value;
            
            bannerRows.forEach(row => {
                let show = true;
                
                if (position && row.dataset.position !== position) {
                    show = false;
                }
                
                if (status && row.dataset.status !== status) {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        positionFilter.addEventListener('change', filterBanners);
        statusFilter.addEventListener('change', filterBanners);
        
        resetButton.addEventListener('click', function() {
            positionFilter.value = '';
            statusFilter.value = '';
            bannerRows.forEach(row => {
                row.style.display = '';
            });
        });
    });
    
    function previewBanner(bannerId) {
        // Open in new tab for preview
        window.open(`/admin/banners/${bannerId}/preview`, '_blank');
    }
</script>
@endpush

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    table tr:hover {
        background-color: #f9fafb;
    }
</style>
@endpush