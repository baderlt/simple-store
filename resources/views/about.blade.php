@extends('layouts.app')

@section('title', 'À Propos')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-center">À Propos de Notre Boutique</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold mb-4">{{ settings('store_name', 'Simple Store') }}</h2>
                
                <p class="text-gray-700 mb-4">
                    Bienvenue chez Maison Dorée, une boutique dédiée aux miels d’exception, aux thés originaux, aux parfums raffinés et aux produits bio.
                </p>

                <p class="text-gray-700 mb-4">
                    Chaque collection met en avant l’origine, l’authenticité et le plaisir de découvrir : saveurs de terroir, senteurs élégantes, rituels naturels et coffrets à offrir.
                </p>

                <h3 class="text-xl font-bold mb-3 mt-6">Nos Services</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mb-6">
                    <li>Livraison rapide à domicile (24-48h)</li>
                    <li>Produits 100% authentiques et certifiés</li>
                    <li>Paiement sécurisé à la livraison</li>
                    <li>Service client disponible et à l'écoute</li>
                    <li>Conseils personnalisés</li>
                </ul>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-green-600 mr-3"></i> Notre Adresse
                </h3>
                <p class="text-gray-700">{{ settings('address', 'Adresse de la boutique') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4 flex items-center">
                    <i class="fas fa-clock text-green-600 mr-3"></i> Horaires
                </h3>
                <p class="text-gray-700">{{ settings('working_hours', 'Lun-Sam: 9h-20h') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4 flex items-center">
                    <i class="fas fa-phone text-green-600 mr-3"></i> Téléphone
                </h3>
                <p class="text-gray-700">{{ settings('phone', '+212 XXX-XXXXXX') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-xl mb-4 flex items-center">
                    <i class="fas fa-envelope text-green-600 mr-3"></i> Email
                </h3>
                <p class="text-gray-700">{{ settings('email', 'contact@example.com') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection