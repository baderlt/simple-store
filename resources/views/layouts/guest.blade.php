<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ settings('store_name', config('app.name', 'Parapharmacy')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                font-family: 'Poppins', sans-serif;
            }
            
            :root {
                --primary-color: {{ settings('primary_color', '#22c55e') }};
                --secondary-color: {{ settings('secondary_color', '#16a34a') }};
            }

            .gradient-bg {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            }

            .text-green-500,
            .text-emerald-500,
            .text-teal-500,
            .text-green-600,
            .text-emerald-600,
            .text-teal-600,
            .text-green-700,
            .hover\:text-green-600:hover,
            .hover\:text-green-700:hover,
            .hover\:text-emerald-600:hover,
            .hover\:text-teal-600:hover {
                color: var(--primary-color) !important;
            }

            .bg-green-500,
            .bg-emerald-500,
            .bg-teal-500,
            .bg-green-600,
            .bg-green-700,
            .bg-emerald-600,
            .bg-emerald-700,
            .bg-teal-600,
            .bg-teal-700,
            .hover\:bg-green-500:hover,
            .hover\:bg-green-600:hover,
            .hover\:bg-green-700:hover,
            .hover\:bg-emerald-600:hover,
            .hover\:bg-emerald-700:hover,
            .hover\:bg-teal-600:hover,
            .hover\:bg-teal-700:hover {
                background-color: var(--primary-color) !important;
            }

            .border-green-500,
            .border-green-600,
            .focus\:ring-green-500:focus,
            .focus\:border-green-500:focus {
                border-color: var(--primary-color) !important;
                --tw-ring-color: var(--primary-color) !important;
            }
            
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            /* Custom focus styles */
            input:focus, button:focus {
                outline: none;
                ring: 2px;
                ring-color: #22c55e;
            }
            
            /* Smooth transitions */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }
            
            /* Page animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
            
            .animate-slide-up {
                animation: slideUp 0.5s ease-out 0.2s both;
            }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">
    

        <!-- Main Content -->
        <div class="min-h-[calc(100vh-4rem)] flex flex-col justify-center items-center pt-6 sm:pt-0 animate-fade-in">
            <!-- Logo/Header -->
            <div class="w-full max-w-md px-6">
                <div class="text-center mt-2">
                    @php
                        $logoPath = settings('logo');
                        $storeName = settings('store_name', 'Parapharmacy');
                    @endphp
                    
                    <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 hover-lift">
                        @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                                 class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105">
                        @else 
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-600 text-white p-3 rounded-lg">
                                    <i class="fas fa-prescription-bottle-alt text-3xl"></i>
                                </div>
                                <div class="text-left">
                                    <h1 class="text-3xl font-bold text-gray-800">{{ $storeName }}</h1>
                                    <p class="text-gray-600 text-sm mt-1">Votre santé, notre priorité</p>
                                </div>
                            </div>
                        @endif 
                    </a>
                </div>
            </div>

            <!-- Form Container -->
            <div class="w-full sm:max-w-md mt-4 px-6 animate-slide-up">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <!-- Decorative top bar -->
                    <div class="h-2 gradient-bg"></div>
                    
                    <!-- Form content -->
                    <div class="px-8 py-8">
                        {{ $slot }}
                    </div>
                    
                    <!-- Bottom decorative bar -->
                    <div class="h-1 bg-gray-100"></div>
                </div>
                
                <!-- Additional links -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600 text-sm">
                        © {{ date('Y') }} {{ $storeName }}. Tous droits réservés.
                        <a href="{{ route('home') }}" class="text-green-600 hover:text-green-700 font-medium ml-2">
                            <i class="fas fa-home mr-1"></i> Retour à l'accueil
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Background Pattern -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/2 -right-1/2 w-full h-full opacity-5">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%2322c55e" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
        </div>

        <!-- Back to Top Button (for longer pages) -->
        <button id="backToTop" class="fixed bottom-8 right-8 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-all opacity-0 transform translate-y-10 z-40">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Back to Top Button
                const backToTopButton = document.getElementById('backToTop');
                
                if (backToTopButton) {
                    window.addEventListener('scroll', () => {
                        if (window.pageYOffset > 300) {
                            backToTopButton.classList.remove('opacity-0', 'translate-y-10');
                            backToTopButton.classList.add('opacity-100', 'translate-y-0');
                        } else {
                            backToTopButton.classList.remove('opacity-100', 'translate-y-0');
                            backToTopButton.classList.add('opacity-0', 'translate-y-10');
                        }
                    });

                    backToTopButton.addEventListener('click', () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }

                // Auto-hide messages after 5 seconds
                setTimeout(() => {
                    const messages = document.querySelectorAll('[class*="messages"]');
                    messages.forEach(message => {
                        message.style.transition = 'opacity 0.5s';
                        message.style.opacity = '0';
                        setTimeout(() => message.remove(), 500);
                    });
                }, 5000);
                
                // Add focus styles to form elements
                const formInputs = document.querySelectorAll('input, textarea, select');
                formInputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                    });
                });
            });
        </script>
    </body>
</html>