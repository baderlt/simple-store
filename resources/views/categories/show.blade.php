@extends('layouts.app')

@section('title', $category->name . ' - ' . settings('store_name', 'Maison Dorée'))

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Category Header --}}
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-700 hover:text-green-600">
                        <i class="fas fa-home mr-2"></i>
                        Accueil
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('categories.index') }}" class="ml-1 text-sm text-gray-700 hover:text-green-600">
                            Catégories
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6">
            <div class="flex items-center space-x-4">
                @if($category->icon)
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="{{ $category->icon }} text-2xl text-green-600"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-gray-600">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Products --}}
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                Produits ({{ $products->total() }})
            </h2>
            <div class="flex space-x-2">
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="newest">Plus récents</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                </select>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover-lift">
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            @if($product->image)
                                <div class="h-48 bg-gray-100">
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center">
                                    <i class="fas fa-basket-shopping text-4xl text-green-400"></i>
                                </div>
                            @endif
                        </a>
                        <div class="p-4">
                            <div class="mb-2">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-800">
                                    {{ $product->category->name }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2 truncate">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="bidi-auto bidi-auto-block hover:text-green-600"
                                   dir="auto">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->discount_price)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg font-bold text-gray-800">
                                                {{ number_format($product->discount_price, 2) }} DH
                                            </span>
                                            <span class="text-sm text-gray-400 line-through">
                                                {{ number_format($product->price, 2) }} DH
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-lg font-bold text-gray-800">
                                            {{ number_format($product->price, 2) }} DH
                                        </span>
                                    @endif
                                </div>
                                <button class="add-to-cart-btn bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-colors"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-price="{{ $product->discount_price ?? $product->price }}">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun produit dans cette catégorie</h3>
                <p class="text-gray-500 mb-6">Les produits seront bientôt ajoutés.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gradient-bg text-white font-semibold px-6 py-3 rounded-lg hover:shadow-lg transition-shadow">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour aux produits
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
