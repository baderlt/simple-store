@extends('layouts.app')

@section('title', 'Catégories - ' . settings('store_name', 'Maison Dorée'))
@section('canonical', route('categories.index'))

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-green-600 via-teal-500 to-emerald-600">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGM2LjYyNyAwIDEyIDUuMzczIDEyIDEycy01LjM3MyAxMi0xMiAxMm0wLTI0Yy02LjYyNyAwLTEyIDUuMzczLTEyIDEyczUuMzczIDEyIDEyIDEyIiBzdHJva2U9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==')] opacity-20"></div>
        
        <div class="container mx-auto px-4 py-16 md:py-20 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold mb-6 animate-pulse">
                    <i class="fas fa-tags mr-2"></i> {{ $categories->count() }} Catégories
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    Explorez Nos <span class="text-yellow-300">Catégories</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Découvrez nos produits organisés par catégories pour faciliter votre recherche
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="#categories-grid" 
                       class="group bg-white text-green-700 hover:bg-green-50 px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="fas fa-arrow-down group-hover:animate-bounce"></i>
                        Explorer maintenant
                    </a>
                    <a href="{{ route('products.index') }}" 
                       class="group bg-transparent border-2 border-white/50 text-white hover:bg-white/10 px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 backdrop-blur-sm flex items-center gap-2">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        Rechercher un produit
                    </a>
                </div>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-gray-50 to-transparent"></div>
    </div>



    {{-- Categories Grid --}}
    <section id="categories-grid" class="py-12 md:py-16">
        <div class="container mx-auto px-4">
            {{-- Filter Tabs --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                @forelse($categories as $category)
                    @php
                        // Définir des icônes basées sur le nom de la catégorie
                        $icons = [
                            'miel' => 'jar', 'thé' => 'mug-hot', 'parfum' => 'spray-can-sparkles',
                            'beauté' => 'spa', 'bio' => 'leaf', 'naturel' => 'seedling',
                            'cadeau' => 'gift', 'coffret' => 'gift', 'gourmand' => 'cookie-bite',
                            'épicerie' => 'bottle-droplet', 'huile' => 'bottle-droplet', 'savon' => 'soap',
                        ];
                        
                        $icon = 'basket-shopping';
                        foreach($icons as $key => $value) {
                            if(stripos($category->name, $key) !== false) {
                                $icon = $value;
                                break;
                            }
                        }
                        
                        // Couleurs thématiques
                        $colorSchemes = [
                            ['bg' => 'from-green-50 to-emerald-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => 'from-green-500 to-emerald-500'],
                            ['bg' => 'from-blue-50 to-cyan-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'from-blue-500 to-cyan-500'],
                            ['bg' => 'from-purple-50 to-pink-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'from-purple-500 to-pink-500'],
                            ['bg' => 'from-orange-50 to-amber-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => 'from-orange-500 to-amber-500'],
                            ['bg' => 'from-red-50 to-pink-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'from-red-500 to-pink-500'],
                            ['bg' => 'from-teal-50 to-emerald-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'icon' => 'from-teal-500 to-emerald-500'],
                        ];
                        $colors = $colorSchemes[$loop->index % count($colorSchemes)];
                        
                        // Image par défaut si non définie
                        $categoryImage = $category->image ? asset('storage/' . $category->image) : null;
                    @endphp
                    
                    <div class="category-card group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $colors['bg'] }} border {{ $colors['border'] }} transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 hover:border-transparent"
                         data-category="{{ $loop->index < 3 ? 'popular' : ($loop->index < 6 ? 'new' : 'all') }}">
                        
                        {{-- Image Container --}}
                        <div class="relative h-48 overflow-hidden rounded-t-2xl">
                            @if($categoryImage)
                                <img src="{{ $categoryImage }}" 
                                     alt="{{ $category->localized_name }}"
                                     loading="lazy"
                                     width="600"
                                     height="400"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br {{ $colors['icon'] }}">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl"></div>
                                        <i class="fas fa-{{ $icon }} relative z-10 text-5xl text-white"></i>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Product Count Badge --}}
                            @if($category->products_count > 0)
                                <div class="absolute top-4 right-4">
                                    <span class="bg-white/90 backdrop-blur-sm {{ $colors['text'] }} px-3 py-1.5 rounded-full text-sm font-bold shadow-lg">
                                        {{ $category->products_count }} produit(s)
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Hover Effect --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            {{-- Quick View Button --}}
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                                   class="bg-white {{ $colors['text'] }} px-6 py-2 rounded-full font-semibold shadow-lg flex items-center gap-2">
                                    <i class="fas fa-eye"></i>
                                    <span>Explorer</span>
                                </a>
                            </div>
                        </div>
                        
                        {{-- Category Info --}}
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold {{ $colors['text'] }} mb-2 group-hover:text-gray-900 transition-colors duration-300">
                                        {{ $category->localized_name }}
                                    </h3>
                                    @if($category->description)
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {{ Str::limit($category->description, 80) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br {{ $colors['icon'] }} flex items-center justify-center ml-4 flex-shrink-0">
                                    <i class="fas fa-{{ $icon }} text-white text-lg"></i>
                                </div>
                            </div>
                            
                            {{-- Progress Bar (simulation) --}}
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Popularité</span>
                                    <span>{{ min(100, ($category->products_count * 10) % 100) }}%</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $colors['text'] }} bg-current rounded-full" 
                                         style="width: {{ min(100, ($category->products_count * 10) % 100) }}%"></div>
                                </div>
                            </div>
                            
                            {{-- Action Button --}}
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                               class="block w-full text-center bg-white border {{ $colors['border'] }} {{ $colors['text'] }} hover:{{ $colors['text'] . '/20' }} py-3 rounded-lg font-semibold transition-all duration-300 group-hover:scale-105">
                                <div class="flex items-center justify-center gap-2">
                                    <span>Voir les produits</span>
                                    <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform duration-300"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full">
                        <div class="max-w-md mx-auto text-center py-16">
                            <div class="relative inline-block mb-6">
                                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-tags text-5xl text-gray-400"></i>
                                </div>
                                <div class="absolute -top-2 -right-2 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center animate-ping opacity-75">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-3">Catégories en préparation</h3>
                            <p class="text-gray-600 mb-8">
                                Nous sommes en train d'organiser nos produits par catégories pour faciliter votre navigation.
                            </p>
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-teal-600 text-white px-8 py-3 rounded-full font-semibold text-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-box-open"></i>
                                <span>Voir tous les produits</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Featured Categories Banner --}}
    @if($categories->isNotEmpty())
        <section class="py-12 md:py-16 bg-gradient-to-r from-green-600 via-teal-500 to-emerald-600 text-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <span class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        <i class="fas fa-crown mr-2"></i> Catégories en Vedette
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Découvrez Nos Spécialités</h2>
                    <p class="text-xl text-white/90 max-w-2xl mx-auto">Les collections préférées de Maison Dorée</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($categories->take(3) as $category)
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 rounded-xl bg-white/20 flex items-center justify-center">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-2xl text-white"></i>
                                    @else
                                        <i class="fas fa-star text-2xl text-white"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl">{{ $category->localized_name }}</h3>
                                    <p class="text-white/70 text-sm">{{ $category->products_count }} produits</p>
                                </div>
                            </div>
                            <a href="{{ route('categories.show', $category->slug) }}" 
                               class="inline-flex items-center gap-2 text-white font-semibold hover:underline">
                                <span>Explorer cette catégorie</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="relative rounded-3xl overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGM2LjYyNyAwIDEyIDUuMzczIDEyIDEycy01LjM3MyAxMi0xMiAxMm0wLTI0Yy02LjYyNyAwLTEyIDUuMzczLTEyIDEyczUuMzczIDEyIDEyIDEyIiBzdHJva2U9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==')] opacity-10"></div>
                
                <div class="relative z-10 py-12 md:py-16 px-4 md:px-8">
                    <div class="max-w-4xl mx-auto text-center">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                            Vous ne trouvez pas ce que vous cherchez ?
                        </h2>
                        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                            Utilisez notre moteur de recherche avancé pour trouver exactement le produit dont vous avez besoin.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('products.index') }}" 
                               class="group bg-white text-green-600 hover:bg-gray-50 px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                                Rechercher un produit
                            </a>
                            <a href="{{ route('home') }}" 
                               class="group bg-transparent border-2 border-white text-white hover:bg-white/10 px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 flex items-center justify-center gap-2 backdrop-blur-sm">
                                <i class="fas fa-home group-hover:scale-110 transition-transform"></i>
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .category-card {
        animation: fadeInUp 0.5s ease-out forwards;
        animation-delay: calc(var(--index, 0) * 0.1s);
        opacity: 0;
    }
    
    /* Line Clamp */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Hover Effects */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }
    
    /* Filter Animation */
    .filter-btn.active {
        box-shadow: 0 4px 12px rgba(6, 78, 59, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 640px) {
        .category-card {
            animation-delay: calc(var(--index, 0) * 0.05s);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize category cards with staggered animation
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach((card, index) => {
            card.style.setProperty('--index', index);
            card.style.animationPlayState = 'running';
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                this.classList.add('bg-green-600', 'text-white');
                
                // Filter categories
                const filter = this.dataset.filter;
                categoryCards.forEach(card => {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
        
        // Load more functionality
        const loadMoreBtn = document.getElementById('loadMoreCategories');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                // Simulate loading
                this.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span>Chargement...</span>
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                `;
                this.disabled = true;
                
                // In a real app, you would fetch more categories via AJAX
                setTimeout(() => {
                    this.innerHTML = `
                        <div class="flex items-center gap-2">
                            <span>Toutes les catégories chargées</span>
                            <i class="fas fa-check"></i>
                        </div>
                    `;
                }, 1500);
            });
        }
        
        // Add scroll animation for category cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);
        
        categoryCards.forEach(card => {
            observer.observe(card);
        });
    });
</script>
@endpush
