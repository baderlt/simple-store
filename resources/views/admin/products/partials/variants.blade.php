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

    $attributeRows = $variantRows
        ->flatMap(fn ($variant) => collect($variant['values'])->keys())
        ->unique()
        ->values()
        ->map(function ($attributeName) use ($variantRows) {
            return [
                'name' => $attributeName,
                'values' => $variantRows
                    ->map(fn ($variant) => $variant['values'][$attributeName] ?? null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            ];
        })
        ->values();

    $oldVariantsPayload = old('variants_payload');
    $initialVariantState = $oldVariantsPayload ? json_decode($oldVariantsPayload, true) : [
        'attributes' => $attributeRows,
        'variants' => $variantRows,
    ];
    $initialVariantState = is_array($initialVariantState) ? $initialVariantState : ['attributes' => [], 'variants' => []];
@endphp

<div class="mb-8 sm:mb-10" id="variantsManager">
    <input type="hidden" name="variants_payload" id="variantsPayload" value="{{ old('variants_payload') }}">
    <script type="application/json" id="variantsInitialState">{!! json_encode($initialVariantState, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <div class="flex items-center mb-4 sm:mb-6">
        <div class="w-1 h-6 sm:h-8 bg-amber-500 rounded-full mr-3"></div>
        <div>
            <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ __('admin.variants') }}</h3>
            <p class="text-xs sm:text-sm text-gray-500">{{ __('admin.product_has_variants_help') }}</p>
        </div>
    </div>

    @if($showVariantToggle ?? true)
        <div class="bg-amber-50 rounded-xl p-4 sm:p-6 border border-amber-200 mb-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <label for="has_variants" class="font-semibold text-gray-800 cursor-pointer text-sm sm:text-base">{{ __('admin.product_has_variants') }}</label>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('admin.variants_toggle_help') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="has_variants" value="1" id="has_variants" class="sr-only peer" {{ old('has_variants', $variantRows->isNotEmpty()) ? 'checked' : '' }}>
                    <span class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-colors"></span>
                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow"></span>
                </label>
            </div>
        </div>
    @endif

    <div id="variantSection" class="space-y-6 {{ old('has_variants', $variantRows->isNotEmpty()) ? '' : 'hidden' }}">
        @error('variants_payload')
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
            </div>
        @enderror

        @error('variant_images')
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
            </div>
        @enderror

        @error('variant_images.*')
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
            </div>
        @enderror

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-6 border-b border-gray-100">
                <div>
                    <h4 class="font-bold text-gray-800">{{ __('admin.variant_groups') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('admin.variant_groups_help') }}</p>
                </div>
                <button type="button" id="addAttributeBtn" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 text-white rounded-lg font-semibold hover:bg-amber-600 transition shadow-sm">
                    <i class="fas fa-plus mr-2"></i>{{ __('admin.add_variant_group') }}
                </button>
            </div>
            <div id="attributesList" class="p-4 sm:p-6 space-y-4"></div>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="font-semibold text-blue-900"><i class="fas fa-wand-magic-sparkles mr-2"></i>{{ __('admin.generate_combinations_help') }}</p>
                <p id="variantCombinationSummary" class="text-sm text-blue-700 mt-1"></p>
            </div>
            <button type="button" id="generateVariantsBtn" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 shadow-sm transition">
                <i class="fas fa-layer-group mr-2"></i>{{ __('admin.generate_combinations') }}
            </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h4 class="font-bold text-gray-800">{{ __('admin.variant_combinations') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('admin.variant_combinations_help') }}</p>
                </div>
                <span id="variantCount" class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold"></span>
            </div>
            <div id="variantsList" class="p-4 sm:p-6 space-y-4"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('variantsManager');
    if (!root || root.dataset.ready === '1') return;
    root.dataset.ready = '1';

  const copy = {!! json_encode([
    'noGroups' => __('admin.no_variant_groups'),
    'groupName' => __('admin.variant_group_name'),
    'groupPlaceholder' => __('admin.variant_group_placeholder'),
    'options' => __('admin.variant_options'),
    'optionPlaceholder' => __('admin.variant_option_placeholder'),
    'addOption' => __('admin.add_variant_option'),
    'removeGroup' => __('admin.remove_variant_group'),
    'removeOption' => __('admin.remove_variant_option'),
    'noVariants' => __('admin.no_variants_yet'),
    'defaultVariant' => __('admin.default_variant'),
    'active' => __('admin.active'),
    'unit' => __('admin.unit'),
    'priceType' => __('admin.price_type'),
    'fixedPrice' => __('admin.fixed_price'),
    'extraPrice' => __('admin.extra_price'),
    'price' => __('admin.price'),
    'adjustment' => __('admin.price_adjustment'),
    'stock' => __('admin.stock'),
    'image' => __('admin.variant_image'),
    'removeVariant' => __('admin.remove_variant'),
    'addGroupsFirst' => __('admin.add_attributes_first'),
    'completeGroups' => __('admin.complete_variant_groups'),
    'combinationSummary' => __('admin.combination_summary'),
    'variantCount' => __('admin.variant_count'),
    'confirmRemoveGroup' => __('admin.confirm_remove_variant_group'),
    'confirmRemoveVariant' => __('admin.confirm_remove_variant'),
]) !!};

    const hasVariants = document.getElementById('has_variants');
    const section = document.getElementById('variantSection');
    const attributesList = document.getElementById('attributesList');
    const variantsList = document.getElementById('variantsList');
    const payloadInput = document.getElementById('variantsPayload');
    const summary = document.getElementById('variantCombinationSummary');
    const variantCount = document.getElementById('variantCount');
    const productForm = root.closest('form');
    const initialStateElement = document.getElementById('variantsInitialState');
    let state = JSON.parse(initialStateElement?.textContent || '{"attributes":[],"variants":[]}');
    state.attributes = Array.isArray(state.attributes) ? state.attributes : [];
    state.variants = Array.isArray(state.variants) ? state.variants : [];

    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    })[character]);
    const normalize = value => String(value ?? '').trim();
    const uniqueValues = values => [...new Set((values || []).map(normalize).filter(Boolean))];
    const keyForValues = values => Object.entries(values || {})
        .sort(([left], [right]) => left.localeCompare(right))
        .map(([attribute, value]) => `${attribute}:${value}`)
        .join('|');

    function syncPayload() {
        const payload = {
            attributes: state.attributes.map(attribute => ({
                name: normalize(attribute.name),
                values: uniqueValues(attribute.values),
            })),
            variants: state.variants,
        };
        payloadInput.value = JSON.stringify(payload);
    }

    function ensureActiveDefault() {
        if (!state.variants.length || state.variants.some(variant => variant.is_default && variant.is_active !== false)) return;
        state.variants.forEach(variant => variant.is_default = false);
        const nextDefault = state.variants.find(variant => variant.is_active !== false);
        if (nextDefault) nextDefault.is_default = true;
    }

    function removeOptionVariants(groupName, optionValue) {
        if (!normalize(groupName) || !normalize(optionValue)) return;
        state.variants = state.variants.filter(variant => normalize(variant.values?.[groupName]) !== normalize(optionValue));
        ensureActiveDefault();
    }

    function removeGroupFromVariants(groupName) {
        if (!normalize(groupName)) return;
        const variantsByCombination = new Map();
        state.variants.forEach(variant => {
            const values = {...(variant.values || {})};
            delete values[groupName];
            const key = keyForValues(values);
            if (!variantsByCombination.has(key)) variantsByCombination.set(key, {...variant, values});
        });
        state.variants = [...variantsByCombination.values()];
        ensureActiveDefault();
    }

    function expectedCombinationCount() {
        const completeGroups = state.attributes.filter(attribute => normalize(attribute.name) && uniqueValues(attribute.values).length);
        if (!completeGroups.length || completeGroups.length !== state.attributes.length) return 0;
        return completeGroups.reduce((count, attribute) => count * uniqueValues(attribute.values).length, 1);
    }

    function updateSummary() {
        const count = expectedCombinationCount();
        summary.textContent = copy.combinationSummary.replace(':count', count);
        variantCount.textContent = copy.variantCount.replace(':count', state.variants.length);
    }

    function renderAttributes() {
        attributesList.innerHTML = '';

        if (!state.attributes.length) {
            attributesList.innerHTML = `
                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-5 py-8 text-center">
                    <i class="fas fa-layer-group text-3xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-600">${escapeHtml(copy.noGroups)}</p>
                </div>`;
        }

        state.attributes.forEach((attribute, groupIndex) => {
            attribute.values = Array.isArray(attribute.values) && attribute.values.length ? attribute.values : [''];
            const card = document.createElement('div');
            card.className = 'rounded-xl border border-gray-200 bg-gray-50 p-4';
            card.dataset.groupIndex = groupIndex;
            card.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-end gap-3 mb-4">
                    <label class="flex-1 text-sm font-semibold text-gray-700">
                        ${escapeHtml(copy.groupName)}
                        <input type="text" value="${escapeHtml(attribute.name)}" placeholder="${escapeHtml(copy.groupPlaceholder)}"
                               data-group-name="${groupIndex}" class="mt-1 w-full px-3 py-2.5 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </label>
                    <button type="button" data-remove-group="${groupIndex}" class="inline-flex items-center justify-center px-3 py-2.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100" title="${escapeHtml(copy.removeGroup)}">
                        <i class="fas fa-trash mr-2"></i>${escapeHtml(copy.removeGroup)}
                    </button>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <p class="text-sm font-semibold text-gray-700">${escapeHtml(copy.options)}</p>
                        <button type="button" data-add-option="${groupIndex}" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-amber-700 bg-amber-100 rounded-lg hover:bg-amber-200">
                            <i class="fas fa-plus mr-1.5"></i>${escapeHtml(copy.addOption)}
                        </button>
                    </div>
                    <div class="space-y-2" data-options-list="${groupIndex}">
                        ${attribute.values.map((value, optionIndex) => `
                            <div class="flex items-center gap-2">
                                <input type="text" value="${escapeHtml(value)}" placeholder="${escapeHtml(copy.optionPlaceholder)}"
                                       data-option-value="${groupIndex}" data-option-index="${optionIndex}"
                                       class="flex-1 px-3 py-2 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <button type="button" data-remove-option="${groupIndex}" data-option-index="${optionIndex}"
                                        class="w-10 h-10 inline-flex items-center justify-center text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50" title="${escapeHtml(copy.removeOption)}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>`).join('')}
                    </div>
                </div>`;
            attributesList.appendChild(card);
        });

        syncPayload();
        updateSummary();
    }

    function renderVariants() {
        variantsList.innerHTML = '';

        if (!state.variants.length) {
            variantsList.innerHTML = `
                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-5 py-8 text-center">
                    <i class="fas fa-boxes-stacked text-3xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-600">${escapeHtml(copy.noVariants)}</p>
                </div>`;
            syncPayload();
            updateSummary();
            return;
        }

        state.variants.forEach((variant, index) => {
            variant.key = variant.key || `variant_${Date.now()}_${index}`;
            variant.price_type = variant.price_type === 'adjustment' ? 'adjustment' : 'fixed';
            variant.is_active = variant.is_active !== false;
            const label = Object.entries(variant.values || {}).map(([name, value]) => `${name}: ${value}`).join(' / ');
            const image = variant.image_path
                ? `<img src="/storage/${escapeHtml(variant.image_path)}" class="h-12 w-12 object-cover rounded-lg border" alt="">`
                : '';
            const row = document.createElement('div');
            row.className = `rounded-xl border p-4 transition ${variant.is_active ? 'border-gray-200 bg-white' : 'border-gray-200 bg-gray-50 opacity-75'}`;
            row.innerHTML = `
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="font-bold text-gray-900">${escapeHtml(label)}</p>
                            <label class="inline-flex items-center mt-2 text-sm text-gray-600">
                                <input type="radio" name="default_variant_choice" ${variant.is_default ? 'checked' : ''} data-default-variant="${index}" class="mr-2 text-amber-600 focus:ring-amber-500">
                                ${escapeHtml(copy.defaultVariant)}
                            </label>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-sm font-semibold text-green-700">
                                <input type="checkbox" ${variant.is_active ? 'checked' : ''} data-variant-field="is_active" data-variant-index="${index}" class="rounded text-green-600">
                                ${escapeHtml(copy.active)}
                            </label>
                            <button type="button" data-remove-variant="${index}" class="inline-flex items-center px-3 py-2 bg-red-50 border border-red-200 text-red-600 rounded-lg hover:bg-red-100" title="${escapeHtml(copy.removeVariant)}">
                                <i class="fas fa-trash mr-2"></i>${escapeHtml(copy.removeVariant)}
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                        <label class="text-xs font-semibold text-gray-600">SKU
                            <input type="text" value="${escapeHtml(variant.sku)}" placeholder="SKU" data-variant-field="sku" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white">
                        </label>
                        <label class="text-xs font-semibold text-gray-600">${escapeHtml(copy.unit)}
                            <input type="text" value="${escapeHtml(variant.unit)}" placeholder="kg, g, ml, litre..." data-variant-field="unit" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white">
                        </label>
                        <label class="text-xs font-semibold text-gray-600">${escapeHtml(copy.priceType)}
                            <select data-variant-field="price_type" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white">
                                <option value="fixed" ${variant.price_type === 'fixed' ? 'selected' : ''}>${escapeHtml(copy.fixedPrice)}</option>
                                <option value="adjustment" ${variant.price_type === 'adjustment' ? 'selected' : ''}>${escapeHtml(copy.extraPrice)}</option>
                            </select>
                        </label>
                        <label class="text-xs font-semibold text-gray-600">${escapeHtml(copy.price)}
                            <input type="number" min="0" step="0.01" value="${escapeHtml(variant.price)}" data-variant-field="price" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white" required>
                        </label>
                        <label class="text-xs font-semibold text-gray-600">${escapeHtml(copy.adjustment)}
                            <input type="number" step="0.01" value="${escapeHtml(variant.price_adjustment || 0)}" data-variant-field="price_adjustment" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white">
                        </label>
                        <label class="text-xs font-semibold text-gray-600">${escapeHtml(copy.stock)}
                            <input type="number" min="0" step="1" value="${escapeHtml(variant.stock_quantity)}" data-variant-field="stock_quantity" data-variant-index="${index}" class="mt-1 w-full px-3 py-2 border rounded-lg bg-white" required>
                        </label>
                        <label class="sm:col-span-2 text-xs font-semibold text-gray-600">${escapeHtml(copy.image)}
                            <div class="mt-1 flex items-center gap-2">${image}<input type="file" name="variant_images[${escapeHtml(variant.key)}]" accept="image/jpeg,image/png,image/gif,image/webp" class="text-sm w-full bg-white border rounded-lg px-3 py-2"></div>
                        </label>
                    </div>
                </div>`;
            variantsList.appendChild(row);
        });

        syncPayload();
        updateSummary();
    }

    function generateCombinations() {
        const attributes = state.attributes.map(attribute => ({
            name: normalize(attribute.name),
            values: uniqueValues(attribute.values),
        }));

        if (!attributes.length) {
            alert(copy.addGroupsFirst);
            return;
        }
        if (attributes.some(attribute => !attribute.name || !attribute.values.length)) {
            alert(copy.completeGroups);
            return;
        }

        const combinations = attributes.reduce((carry, attribute) => carry.flatMap(existing =>
            attribute.values.map(value => ({...existing, [attribute.name]: value}))
        ), [{}]);
        const existingByKey = new Map(state.variants.map(variant => [keyForValues(variant.values), variant]));
        const hadDefault = state.variants.some(variant => variant.is_default);

        state.attributes = attributes;
        state.variants = combinations.map((values, index) => {
            const existing = existingByKey.get(keyForValues(values));
            if (existing) return {...existing, values};
            return {
                id: null,
                key: `new_${Date.now()}_${index}`,
                sku: '',
                unit: '',
                price_type: 'fixed',
                price_adjustment: 0,
                price: document.querySelector('input[name="price"]')?.value || 0,
                stock_quantity: 0,
                image_path: null,
                is_default: index === 0 && !hadDefault,
                is_active: true,
                values,
            };
        });

        if (state.variants.length && !state.variants.some(variant => variant.is_default && variant.is_active)) {
            state.variants.forEach((variant, index) => variant.is_default = index === 0);
        }
        renderAttributes();
        renderVariants();
    }

    hasVariants?.addEventListener('change', () => {
        section.classList.toggle('hidden', !hasVariants.checked);
        const toggleIcon = document.getElementById('has_variants_icon');
        toggleIcon?.classList.toggle('text-amber-500', hasVariants.checked);
        toggleIcon?.classList.toggle('text-gray-400', !hasVariants.checked);
        document.getElementById('productVariantsToggleCard')?.classList.toggle('ring-2', hasVariants.checked);
        document.getElementById('productVariantsToggleCard')?.classList.toggle('ring-amber-300', hasVariants.checked);
        if (hasVariants.checked && !state.attributes.length) {
            state.attributes.push({name: '', values: ['']});
            renderAttributes();
            attributesList.querySelector('[data-group-name]')?.focus();
        }
        syncPayload();
    });

    document.getElementById('addAttributeBtn')?.addEventListener('click', () => {
        state.attributes.push({name: '', values: ['']});
        renderAttributes();
        attributesList.querySelector(`[data-group-name="${state.attributes.length - 1}"]`)?.focus();
    });
    document.getElementById('generateVariantsBtn')?.addEventListener('click', generateCombinations);

    attributesList.addEventListener('input', event => {
        if (event.target.dataset.groupName !== undefined) {
            state.attributes[Number(event.target.dataset.groupName)].name = event.target.value;
        }
        if (event.target.dataset.optionValue !== undefined) {
            const groupIndex = Number(event.target.dataset.optionValue);
            const optionIndex = Number(event.target.dataset.optionIndex);
            state.attributes[groupIndex].values[optionIndex] = event.target.value;
        }
        syncPayload();
        updateSummary();
    });

    attributesList.addEventListener('click', event => {
        const addOptionButton = event.target.closest('[data-add-option]');
        if (addOptionButton) {
            const groupIndex = Number(addOptionButton.dataset.addOption);
            state.attributes[groupIndex].values.push('');
            renderAttributes();
            const options = attributesList.querySelectorAll(`[data-option-value="${groupIndex}"]`);
            options[options.length - 1]?.focus();
            return;
        }

        const removeOptionButton = event.target.closest('[data-remove-option]');
        if (removeOptionButton) {
            const groupIndex = Number(removeOptionButton.dataset.removeOption);
            const optionIndex = Number(removeOptionButton.dataset.optionIndex);
            const group = state.attributes[groupIndex];
            const removedValue = group.values[optionIndex];
            group.values.splice(optionIndex, 1);
            removeOptionVariants(group.name, removedValue);
            renderAttributes();
            renderVariants();
            return;
        }

        const removeGroupButton = event.target.closest('[data-remove-group]');
        if (removeGroupButton && confirm(copy.confirmRemoveGroup)) {
            const groupIndex = Number(removeGroupButton.dataset.removeGroup);
            const removedGroup = state.attributes[groupIndex];
            state.attributes.splice(groupIndex, 1);
            removeGroupFromVariants(removedGroup.name);
            renderAttributes();
            renderVariants();
        }
    });

    variantsList.addEventListener('input', event => {
        const index = event.target.dataset.variantIndex;
        const field = event.target.dataset.variantField;
        if (index === undefined || !field) return;
        state.variants[Number(index)][field] = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
        syncPayload();
    });

    variantsList.addEventListener('change', event => {
        const fieldIndex = event.target.dataset.variantIndex;
        const field = event.target.dataset.variantField;
        if (fieldIndex !== undefined && field) {
            state.variants[Number(fieldIndex)][field] = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
            if (field === 'is_active' && !state.variants[Number(fieldIndex)].is_active && state.variants[Number(fieldIndex)].is_default) {
                state.variants[Number(fieldIndex)].is_default = false;
                const nextDefault = state.variants.find(variant => variant.is_active);
                if (nextDefault) nextDefault.is_default = true;
                renderVariants();
                return;
            }
            syncPayload();
        }

        const defaultIndex = event.target.dataset.defaultVariant;
        if (defaultIndex !== undefined) {
            state.variants.forEach((variant, index) => variant.is_default = index === Number(defaultIndex));
            syncPayload();
        }
    });

    variantsList.addEventListener('click', event => {
        const removeButton = event.target.closest('[data-remove-variant]');
        if (!removeButton || !confirm(copy.confirmRemoveVariant)) return;
        state.variants.splice(Number(removeButton.dataset.removeVariant), 1);
        if (state.variants.length && !state.variants.some(variant => variant.is_default && variant.is_active)) {
            const nextDefault = state.variants.find(variant => variant.is_active);
            if (nextDefault) nextDefault.is_default = true;
        }
        renderVariants();
    });

    productForm?.addEventListener('submit', () => syncPayload());
    renderAttributes();
    renderVariants();
});
</script>
