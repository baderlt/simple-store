@if($errors->any())
    <div class="mx-4 mt-4 rounded-xl border border-red-200 bg-red-50 p-4 sm:mx-6" role="alert">
        <div class="flex items-start gap-3">
            <i class="fas fa-circle-exclamation mt-0.5 text-red-500"></i>
            <div>
                <p class="font-bold text-red-800">Veuillez corriger les informations signalées.</p>
                <p class="mt-1 text-sm text-red-700">{{ $errors->count() }} champ{{ $errors->count() > 1 ? 's' : '' }} nécessite{{ $errors->count() > 1 ? 'nt' : '' }} votre attention.</p>
            </div>
        </div>
    </div>
@endif

<nav class="product-form-nav" aria-label="Navigation du formulaire">
    <a href="#product-basics"><i class="fas fa-box"></i><span>Essentiel</span></a>
    <a href="#product-inventory"><i class="fas fa-cubes"></i><span>Stock</span></a>
    <a href="#product-description"><i class="fas fa-align-left"></i><span>Description</span></a>
    <a href="#product-seo"><i class="fas fa-magnifying-glass-chart"></i><span>SEO</span></a>
    <a href="#product-images"><i class="fas fa-images"></i><span>Images</span></a>
    <a href="#product-options"><i class="fas fa-sliders"></i><span>Options</span></a>
    <a href="#variantsManager"><i class="fas fa-layer-group"></i><span>Variantes</span></a>
</nav>
