@foreach($products as $product)
    <div class="product-grid-item flex h-full min-w-0">
        @include('products._card', ['product' => $product])
    </div>
@endforeach
