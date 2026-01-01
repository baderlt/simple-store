@extends('admin.layouts.app')

@section('title', 'Mon Profil')
@section('header', 'Mon Profil')
@section('subheader', 'Gestion du compte')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Informations personnelles --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Informations personnelles</h3>
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                <span class="font-bold text-white text-lg">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
        </div>
        
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>Mettre à jour le profil
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Changer le mot de passe --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Changer le mot de passe</h3>
        
        <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel</label>
                    <div class="relative">
                        <input type="password" 
                               name="current_password" 
                               id="current_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                               required>
                        <button type="button" 
                                onclick="togglePassword('current_password', this)"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password', this)"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-500">Le mot de passe doit contenir :</p>
                        <ul class="text-xs text-gray-500 list-disc list-inside">
                            <li>Au moins 8 caractères</li>
                            <li>Au moins une lettre majuscule</li>
                            <li>Au moins une lettre minuscule</li>
                            <li>Au moins un chiffre</li>
                            <li>Au moins un caractère spécial</li>
                        </ul>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-500 to-cyan-600 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-key mr-2"></i>Changer le mot de passe
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistiques du compte --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Activité du compte</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Date d'inscription</p>
                        <p class="text-xl font-bold text-gray-800">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-800 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600">Dernière connexion</p>
                        <p class="text-xl font-bold text-gray-800">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Jamais
                            @endif
                        </p>
                    </div>
                    <div class="bg-blue-500 p-3 rounded-lg">
                        <i class="fas fa-sign-in-alt text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600">Statut du compte</p>
                        <p class="text-xl font-bold text-gray-800 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            Actif
                        </p>
                    </div>
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="fas fa-user-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript pour toggle password --}}
<script>
function togglePassword(inputId, button) {
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

// Validation en temps réel du mot de passe
document.getElementById('password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Vous pouvez ajouter une validation visuelle ici
});
</script>

<style>
.password-strength {
    height: 4px;
    border-radius: 2px;
    transition: all 0.3s;
}

.strength-weak { background-color: #ef4444; width: 25%; }
.strength-fair { background-color: #f59e0b; width: 50%; }
.strength-good { background-color: #10b981; width: 75%; }
.strength-strong { background-color: #3b82f6; width: 100%; }
</style>
@endsection