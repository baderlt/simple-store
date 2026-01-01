<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth transitions */
        .sidebar-link, .user-avatar {
            transition: all 0.2s ease;
        }
        
        /* Card hover effects */
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col shadow-xl">
            {{-- Logo Section --}}
                    @php
                    $logoPath = settings('logo');
                    $storeName = settings('store_name', 'Parapharmacy');
                @endphp
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                  @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                             class="h-12 w-auto object-contain transition-transform duration-300 hover:scale-105">
                    @else 
                             <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-prescription-bottle-medical text-white text-lg"></i>
                    </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">Admin Panel</h1>
                        <p class="text-gray-400 text-sm">Gestion de pharmacie</p>
                    </div>
                </div>
            </div>
            
            {{-- User Profile Mini --}}
            <div class="p-4 border-b border-gray-700 flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                    <span class="font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-sm truncate">Administrateur</p>
                </div>
            </div>
            
            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-6">
                <div class="px-4 mb-6">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Principal</p>
                </div>
                
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'fas fa-chart-line', 'label' => 'Dashboard'],
                        ['route' => 'admin.products.index', 'icon' => 'fas fa-box', 'label' => 'Produits'],
                        ['route' => 'admin.categories.index', 'icon' => 'fas fa-tags', 'label' => 'Catégories'],
                        ['route' => 'admin.orders.index', 'icon' => 'fas fa-shopping-cart', 'label' => 'Commandes'],
                        ['route' => 'admin.discounts.index', 'icon' => 'fas fa-percent', 'label' => 'Réductions'],
                        ['route' => 'admin.banners.index', 'icon' => 'fas fa-layer-group', 'label' => 'Bannières'],
                    ];
                    
                    $settingsItems = [
                        ['route' => 'admin.settings.index', 'icon' => 'fas fa-cog', 'label' => 'Paramètres'],
                    ];
                @endphp
                
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-green-500/20 to-emerald-500/20 border-l-4 border-green-500 text-white' : 'text-gray-300' }}">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                        @if(request()->routeIs(str_replace('.index', '.*', $item['route'])))
                            <div class="ml-auto w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        @endif
                    </a>
                @endforeach
                
                <div class="px-4 my-6">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Système</p>
                </div>
                
                @foreach($settingsItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-blue-500/20 to-cyan-500/20 border-l-4 border-blue-500 text-white' : 'text-gray-300' }}">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
                {{-- Dans la section navigation de votre sidebar --}}
<a href="{{ route('admin.profile.edit') }}" 
   class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
          {{ request()->routeIs('admin.profile.*') ? 'bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-l-4 border-purple-500 text-white' : 'text-gray-300' }}">
    <i class="fas fa-user-circle mr-3 w-5 text-center"></i>
    <span class="font-medium">Mon Profil</span>
</a>
            </nav>
            
            {{-- Footer Actions --}}
            <div class="border-t border-gray-700 p-4 space-y-2">
                <a href="{{ route('home') }}" target="_blank" 
                   class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-700/50 text-gray-300 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        <span>Voir le site</span>
                    </div>
                    <i class="fas fa-external-link text-xs text-gray-400"></i>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span>Déconnexion</span>
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-8 py-4">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h2>
                        @hasSection('subheader')
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-600">@yield('subheader')</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        {{-- Notifications --}}
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        {{-- User Menu --}}
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">Administrateur</p>
                            </div>
                            <div class="relative">
                                <a href="{{ route('admin.profile.edit') }}" >

                                    <div class="user-avatar w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md cursor-pointer hover:shadow-lg">
                                        <span class="font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Messages --}}
            <div class="px-8 pt-6">
                @if(session('success'))
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm flex items-center animate-fade-in">
                        <div class="bg-green-500 p-2 rounded-lg mr-4">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Succès !</p>
                            <p>{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm flex items-center animate-fade-in">
                        <div class="bg-red-500 p-2 rounded-lg mr-4">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Erreur !</p>
                            <p>{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm animate-fade-in">
                        <div class="flex items-center mb-2">
                            <div class="bg-red-500 p-2 rounded-lg mr-4">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                            <p class="font-semibold">Veuillez corriger les erreurs suivantes :</p>
                        </div>
                        <ul class="list-disc list-inside ml-10 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
                @yield('content')
            </main>
            
            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 py-4 px-8">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div>
                        <span>© {{ date('Y') }} Admin Panel. Tous droits réservés.</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span>Dernière connexion: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'N/A' }}</span>
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            En ligne
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script>
        // Add fade-in animation
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fade-in {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-fade-in {
                    animation: fade-in 0.3s ease-out;
                }
                
                .sidebar-link.active {
                    background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
                    border-left: 4px solid #10b981;
                    color: white;
                }
            `;
            document.head.appendChild(style);
            
            // Auto-hide success messages after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('[class*="bg-gradient-to-r"]').forEach(alert => {
                    if (alert.textContent.includes('Succès') || alert.textContent.includes('Erreur')) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s';
                        setTimeout(() => alert.remove(), 500);
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html>