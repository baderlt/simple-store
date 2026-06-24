@php
    $product = $product ?? null;
@endphp

<div class="product-form-section" id="product-seo">
    <div class="product-form-section-heading flex items-center mb-4 sm:mb-6">
        <div class="w-1 h-6 sm:h-8 bg-cyan-500 rounded-full mr-3"></div>
        <div>
            <h3 class="text-base sm:text-lg font-bold text-gray-800">SEO du produit</h3>
            <p class="text-xs sm:text-sm text-gray-500">Optionnel. Si vide, le site génère automatiquement un SEO propre depuis le produit.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <div class="space-y-2">
            <label for="meta_title" class="block text-sm font-semibold text-gray-700">Meta title</label>
            <input type="text"
                   name="meta_title"
                   id="meta_title"
                   maxlength="70"
                   value="{{ old('meta_title', $product?->meta_title) }}"
                   placeholder="Titre SEO affiché dans Google"
                   class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('meta_title') border-red-500 ring-2 ring-red-200 @enderror">
            @error('meta_title')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta keywords</label>
            <input type="text"
                   name="meta_keywords"
                   id="meta_keywords"
                   maxlength="255"
                   value="{{ old('meta_keywords', $product?->meta_keywords) }}"
                   placeholder="miel, bio, naturel..."
                   class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('meta_keywords') border-red-500 ring-2 ring-red-200 @enderror">
            @error('meta_keywords')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="lg:col-span-2 space-y-2">
            <label for="meta_description" class="flex items-center justify-between text-sm font-semibold text-gray-700">
                <span>Meta description</span>
                <span class="text-xs font-normal text-gray-500">max 170 caractères</span>
            </label>
            <textarea name="meta_description"
                      id="meta_description"
                      rows="3"
                      maxlength="170"
                      placeholder="Description SEO courte et attractive..."
                      class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none @error('meta_description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('meta_description', $product?->meta_description) }}</textarea>
            @error('meta_description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="canonical_url" class="block text-sm font-semibold text-gray-700">Canonical URL</label>
            <input type="url"
                   name="canonical_url"
                   id="canonical_url"
                   value="{{ old('canonical_url', $product?->canonical_url) }}"
                   placeholder="https://wanybio.com/products/..."
                   class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('canonical_url') border-red-500 ring-2 ring-red-200 @enderror">
            @error('canonical_url')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="og_image" class="block text-sm font-semibold text-gray-700">Open Graph image URL/path</label>
            <input type="text"
                   name="og_image"
                   id="og_image"
                   value="{{ old('og_image', $product?->og_image) }}"
                   placeholder="URL complète ou chemin storage"
                   class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('og_image') border-red-500 ring-2 ring-red-200 @enderror">
            @error('og_image')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="og_title" class="block text-sm font-semibold text-gray-700">Open Graph title</label>
            <input type="text"
                   name="og_title"
                   id="og_title"
                   maxlength="95"
                   value="{{ old('og_title', $product?->og_title) }}"
                   placeholder="Titre pour Facebook/WhatsApp"
                   class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('og_title') border-red-500 ring-2 ring-red-200 @enderror">
            @error('og_title')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="og_description" class="block text-sm font-semibold text-gray-700">Open Graph description</label>
            <textarea name="og_description"
                      id="og_description"
                      rows="3"
                      maxlength="170"
                      placeholder="Description pour réseaux sociaux"
                      class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none @error('og_description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('og_description', $product?->og_description) }}</textarea>
            @error('og_description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
