@foreach($products as $product)
    <div class="product-grid-item flex h-full">
        @include('products._card', ['product' => $product])
    </div>
@endforeach
