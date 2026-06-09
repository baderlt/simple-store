@php
    $variantRows = isset($product) ? $product->variants->map(function ($variant) {
        return [
            'id' => $variant->id,
            'key' => 'existing_' . $variant->id,
            'sku' => $variant->sku,
            'unit' => $variant->unit,
            'price_type' => $variant->price_type,
            'price_adjustment' => (float) $variant->price_adjustment,
            'price' => (float) $variant->price,
            'stock_quantity' => $variant->stock_quantity,
            'image_path' => $variant->image_path,
            'is_default' => $variant->is_default,
            'is_active' => $variant->is_active,
            'values' => $variant->items->mapWithKeys(fn ($item) => [$item->attribute->name => $item->value->value])->all(),
        ];
    })->values() : collect();

    $attributeRows = $variantRows->flatMap(fn ($variant) => collect($variant['values'])->keys())->unique()->values()->map(function ($attributeName) use ($variantRows) {
        return [
            'name' => $attributeName,
            'values' => $variantRows->map(fn ($variant) => $variant['values'][$attributeName] ?? null)->filter()->unique()->values()->all(),
        ];
    })->values();

    $oldVariantsPayload = old('variants_payload');
    $initialVariantState = $oldVariantsPayload ? json_decode($oldVariantsPayload, true) : [
        'attributes' => $attributeRows,
        'variants' => $variantRows,
    ];
@endphp

<div class="mb-8 sm:mb-10" id="variantsManager" data-initial="{{ e(json_encode($initialVariantState)) }}">
    <input type="hidden" name="variants_payload" id="variantsPayload" value="{{ old('variants_payload') }}">

    <div class="flex items-center mb-4 sm:mb-6">
        <div class="w-1 h-6 sm:h-8 bg-purple-500 rounded-full mr-3"></div>
        <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ __('admin.variants') }}</h3>
    </div>

    <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200 mb-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <label for="has_variants" class="font-semibold text-gray-700 cursor-pointer text-sm sm:text-base">{{ __('admin.product_has_variants') }}</label>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ __('admin.product_has_variants_help') }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                <input type="checkbox" name="has_variants" value="1" id="has_variants" class="sr-only peer" {{ old('has_variants', $variantRows->isNotEmpty()) ? 'checked' : '' }}>
                <div class="w-10 h-5 sm:w-12 sm:h-6 bg-gray-300 rounded-full peer peer-checked:bg-purple-500 transition-all duration-200"></div>
                <div class="absolute left-0.5 top-0.5 sm:left-1 sm:top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:translate-x-5 sm:peer-checked:translate-x-6"></div>
            </label>
        </div>
    </div>

    <div id="variantSection" class="space-y-6 {{ old('has_variants', $variantRows->isNotEmpty()) ? '' : 'hidden' }}">
        @error('variants_payload')
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
            </div>
        @enderror

        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h4 class="font-bold text-gray-800">{{ __('admin.attributes') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('admin.attributes_help') }}</p>
                </div>
                <button type="button" id="addAttributeBtn" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-semibold hover:bg-purple-200 transition">
                    <i class="fas fa-plus mr-2"></i>{{ __('admin.add_attribute') }}
                </button>
            </div>
            <div id="attributesList" class="space-y-3"></div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i>{{ __('admin.generate_combinations_help') }}</p>
            <button type="button" id="generateVariantsBtn" class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-random mr-2"></i>{{ __('admin.generate_combinations') }}
            </button>
        </div>

        <div id="variantsList" class="space-y-4"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('variantsManager');
    if (!root || root.dataset.ready) return;
    root.dataset.ready = '1';

    const hasVariants = document.getElementById('has_variants');
    const section = document.getElementById('variantSection');
    const attributesList = document.getElementById('attributesList');
    const variantsList = document.getElementById('variantsList');
    const payloadInput = document.getElementById('variantsPayload');
    let state = root.dataset.initial ? JSON.parse(root.dataset.initial) : {attributes: [], variants: []};
    state.attributes = state.attributes || [];
    state.variants = state.variants || [];

    const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[ch]));
    const splitValues = (value) => String(value || '').split(',').map(item => item.trim()).filter(Boolean);
    const keyForValues = (values) => Object.entries(values).sort(([a], [b]) => a.localeCompare(b)).map(([a, v]) => `${a}:${v}`).join('|');

    function syncPayload() {
        payloadInput.value = JSON.stringify(state);
    }

    function renderAttributes() {
        attributesList.innerHTML = '';
        if (!state.attributes.length) {
            attributesList.innerHTML = `<div class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">{{ __('admin.no_attributes_yet') }}</div>`;
        }
        state.attributes.forEach((attribute, index) => {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 lg:grid-cols-12 gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3';
            row.innerHTML = `
                <input type="text" value="${esc(attribute.name)}" placeholder="{{ __('admin.attribute_name_placeholder') }}" class="lg:col-span-3 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" data-attribute-name="${index}">
                <input type="text" value="${esc((attribute.values || []).join(', '))}" placeholder="{{ __('admin.attribute_values_placeholder') }}" class="lg:col-span-8 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" data-attribute-values="${index}">
                <button type="button" class="lg:col-span-1 px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200" data-remove-attribute="${index}"><i class="fas fa-trash"></i></button>
            `;
            attributesList.appendChild(row);
        });
        syncPayload();
    }

    function renderVariants() {
        variantsList.innerHTML = '';
        if (!state.variants.length) {
            variantsList.innerHTML = `<div class="text-sm text-gray-500 bg-white border border-dashed border-gray-300 rounded-xl p-5 text-center">{{ __('admin.no_variants_yet') }}</div>`;
            syncPayload();
            return;
        }

        state.variants.forEach((variant, index) => {
            variant.key = variant.key || `variant_${Date.now()}_${index}`;
            const label = Object.values(variant.values || {}).join(' / ');
            const image = variant.image_path ? `<img src="/storage/${esc(variant.image_path)}" class="h-12 w-12 object-cover rounded-lg border" alt="">` : '';
            const row = document.createElement('div');
            row.className = 'bg-white rounded-xl border border-gray-200 p-4';
            row.innerHTML = `
                <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                    <div class="lg:w-56">
                        <p class="font-bold text-gray-800">${esc(label)}</p>
                        <label class="inline-flex items-center mt-2 text-sm text-gray-600">
                            <input type="radio" name="default_variant_choice" ${variant.is_default ? 'checked' : ''} data-default-variant="${index}" class="mr-2 text-purple-600">
                            {{ __('admin.default_variant') }}
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 flex-1">
                        <label class="text-xs font-semibold text-gray-600">SKU<input type="text" value="${esc(variant.sku)}" placeholder="SKU" data-variant-field="sku" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg"></label>
                        <label class="text-xs font-semibold text-gray-600">{{ __('admin.unit') }}<input type="text" value="${esc(variant.unit)}" placeholder="kg, g, ml, litre..." data-variant-field="unit" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg"></label>
                        <label class="text-xs font-semibold text-gray-600">{{ __('admin.price_type') }}<select data-variant-field="price_type" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg"><option value="fixed" ${variant.price_type !== 'adjustment' ? 'selected' : ''}>{{ __('admin.fixed_price') }}</option><option value="adjustment" ${variant.price_type === 'adjustment' ? 'selected' : ''}>{{ __('admin.extra_price') }}</option></select></label>
                        <label class="text-xs font-semibold text-gray-600">{{ __('admin.price') }}<input type="number" min="0" step="0.01" value="${esc(variant.price)}" data-variant-field="price" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg" required></label>
                        <label class="text-xs font-semibold text-gray-600">{{ __('admin.price_adjustment') }}<input type="number" step="0.01" value="${esc(variant.price_adjustment || 0)}" data-variant-field="price_adjustment" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg"></label>
                        <label class="text-xs font-semibold text-gray-600">{{ __('admin.stock') }}<input type="number" min="0" step="1" value="${esc(variant.stock_quantity)}" data-variant-field="stock_quantity" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg" required></label>
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" ${variant.is_active !== false ? 'checked' : ''} data-variant-field="is_active" data-variant-index="${index}" class="rounded text-purple-600">{{ __('admin.active') }}</label>
                        <div class="flex items-center gap-2">${image}<input type="file" name="variant_images[${esc(variant.key)}]" accept="image/*" class="text-sm w-full"></div>
                    </div>
                    <button type="button" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200" data-remove-variant="${index}"><i class="fas fa-trash"></i></button>
                </div>
            `;
            variantsList.appendChild(row);
        });
        syncPayload();
    }

    function generateCombinations() {
        const validAttributes = state.attributes.filter(attr => attr.name && (attr.values || []).length);
        if (!validAttributes.length) {
            alert('{{ __('admin.add_attributes_first') }}');
            return;
        }

        const combinations = validAttributes.reduce((carry, attribute) => {
            return carry.flatMap(existing => attribute.values.map(value => ({...existing, [attribute.name]: value})));
        }, [{}]);

        const existingByKey = new Map(state.variants.map(variant => [keyForValues(variant.values || {}), variant]));
        state.variants = combinations.map((values, index) => {
            const existing = existingByKey.get(keyForValues(values));
            return existing || {
                id: null,
                key: `new_${Date.now()}_${index}`,
                sku: '',
                unit: '',
                price_type: 'fixed',
                price_adjustment: 0,
                price: document.querySelector('input[name="price"]')?.value || 0,
                stock_quantity: 0,
                image_path: null,
                is_default: index === 0 && !state.variants.some(variant => variant.is_default),
                is_active: true,
                values,
            };
        });
        if (!state.variants.some(variant => variant.is_default) && state.variants[0]) state.variants[0].is_default = true;
        renderVariants();
    }

    hasVariants?.addEventListener('change', () => section.classList.toggle('hidden', !hasVariants.checked));
    document.getElementById('addAttributeBtn')?.addEventListener('click', () => { state.attributes.push({name: '', values: []}); renderAttributes(); });
    document.getElementById('generateVariantsBtn')?.addEventListener('click', generateCombinations);

    attributesList.addEventListener('input', event => {
        const nameIndex = event.target.dataset.attributeName;
        const valuesIndex = event.target.dataset.attributeValues;
        if (nameIndex !== undefined) state.attributes[nameIndex].name = event.target.value.trim();
        if (valuesIndex !== undefined) state.attributes[valuesIndex].values = splitValues(event.target.value);
        syncPayload();
    });
    attributesList.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-attribute]');
        if (!button) return;
        state.attributes.splice(Number(button.dataset.removeAttribute), 1);
        renderAttributes();
    });
    variantsList.addEventListener('input', event => {
        const index = event.target.dataset.variantIndex;
        const field = event.target.dataset.variantField;
        if (index === undefined || !field) return;
        state.variants[index][field] = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
        syncPayload();
    });
    variantsList.addEventListener('change', event => {
        const fieldIndex = event.target.dataset.variantIndex;
        const field = event.target.dataset.variantField;
        if (fieldIndex !== undefined && field) {
            state.variants[fieldIndex][field] = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
            syncPayload();
        }
        const index = event.target.dataset.defaultVariant;
        if (index === undefined) return;
        state.variants.forEach((variant, variantIndex) => variant.is_default = variantIndex === Number(index));
        syncPayload();
    });
    variantsList.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-variant]');
        if (!button) return;
        state.variants.splice(Number(button.dataset.removeVariant), 1);
        if (state.variants[0] && !state.variants.some(variant => variant.is_default)) state.variants[0].is_default = true;
        renderVariants();
    });

    renderAttributes();
    renderVariants();
});
</script>
