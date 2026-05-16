<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class=" p-4 bg-green-50 text-green-700 rounded-lg border border-green-200" :status="session('status')" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-sign-in-alt text-green-600 mr-2"></i>
                {{ __('auth.login.title') }}
            </h2>
            <p class="mt-2 text-gray-600">
                {{ __('auth.login.subtitle') }}
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-envelope mr-2 text-green-600"></i>
                    {{ __('auth.login.email') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           value="{{ old('email') }}"
                           required 
                           autofocus 
                           autocomplete="email"
                           class="pl-10 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200
                                  @error('email') border-red-300 ring-2 ring-red-200 @enderror"
                           placeholder="votre@email.com">
                </div>
                @error('email')
                    <div class="flex items-center text-sm text-red-600 mt-1">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-lock mr-2 text-green-600"></i>
                    {{ __('auth.login.password') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-key text-gray-400"></i>
                    </div>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="current-password"
                           class="pl-10 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200
                                  @error('password') border-red-300 ring-2 ring-red-200 @enderror"
                           placeholder="••••••••">
                    <button type="button" 
                            onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="flex items-center text-sm text-red-600 mt-1">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" 
                           name="remember" 
                           type="checkbox"
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition-colors duration-200">
                        {{ __('auth.login.remember_me') }}
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm font-medium text-green-600 hover:text-green-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-key mr-1"></i>
                        {{ __('auth.login.forgot_password') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-green-200 group-hover:text-green-100 transition-colors duration-200"></i>
                    </span>
                    {{ __('auth.login.submit') }}
                    <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-arrow-right text-green-200 group-hover:text-green-100 transition-colors duration-200"></i>
                    </span>
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-white text-gray-500">
                    Nouveau client ?
                </span>
            </div>
        </div>

        <!-- Register Link -->
        <div>
            <a href="{{ route('register') }}"
               class="group w-full flex justify-center items-center py-3 px-4 border-2 border-green-600 rounded-lg shadow-sm text-sm font-medium text-green-600 hover:bg-green-50 hover:border-green-700 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                Créer un nouveau compte
            </a>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-alt text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Connexion sécurisée</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Vos informations sont protégées par un cryptage SSL. Nous ne partageons jamais vos données personnelles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Add animation to form elements
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('input, button, a');
            formElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.05}s`;
                element.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>

    <style>
        /* Custom animations */
        .animate__animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
        
        /* Custom focus styles */
        input:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }
        
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
    </style>
</x-guest-layout>