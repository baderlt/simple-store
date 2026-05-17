@extends('admin.layouts.app')

@section('title', 'Store Settings - Admin')
@section('header', 'Store Settings')

@section('content')
@php
    $supportedLocales = old('supported_locales', $settings['supported_locales'] ?? ['en', 'fr', 'ar']);
    if (is_string($supportedLocales)) {
        $supportedLocales = json_decode($supportedLocales, true) ?: ['en', 'fr', 'ar'];
    }
    $assetUrl = fn ($key) => !empty($settings[$key]) ? asset('storage/' . $settings[$key]) : null;
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-5 py-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <p class="text-sm uppercase tracking-wide text-blue-600 font-semibold">Brand customization</p>
                <h2 class="text-2xl font-bold text-gray-900">Identity, SEO, and localization</h2>
                <p class="text-gray-500">Use these fields to adapt the storefront to any niche or business type.</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-5">
                <label class="block">
                    <span class="font-semibold text-gray-700">Store name *</span>
                    <input name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'Simple Store') }}" required class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">Tagline</span>
                    <input name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">Currency</span>
                    <input name="currency" value="{{ old('currency', $settings['currency'] ?? 'USD') }}" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block lg:col-span-3">
                    <span class="font-semibold text-gray-700">Store description</span>
                    <textarea name="store_description" rows="3" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('store_description', $settings['store_description'] ?? '') }}</textarea>
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">SEO title</span>
                    <input name="seo_title" value="{{ old('seo_title', $settings['seo_title'] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">SEO description</span>
                    <input name="seo_description" value="{{ old('seo_description', $settings['seo_description'] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">SEO keywords</span>
                    <input name="seo_keywords" value="{{ old('seo_keywords', $settings['seo_keywords'] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">Timezone</span>
                    <input name="timezone" value="{{ old('timezone', $settings['timezone'] ?? config('app.timezone')) }}" placeholder="America/New_York" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="font-semibold text-gray-700">Default language</span>
                    <select name="default_locale" class="mt-2 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach(['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'] as $code => $label)
                            <option value="{{ $code }}" @selected(old('default_locale', $settings['default_locale'] ?? 'en') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div>
                    <span class="font-semibold text-gray-700">Supported languages</span>
                    <div class="mt-3 flex flex-wrap gap-4">
                        @foreach(['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'] as $code => $label)
                            <label class="inline-flex items-center gap-2"><input type="checkbox" name="supported_locales[]" value="{{ $code }}" @checked(in_array($code, $supportedLocales, true)) class="rounded border-gray-300 text-blue-600"> {{ $label }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <p class="text-sm uppercase tracking-wide text-purple-600 font-semibold">Appearance</p>
                <h2 class="text-2xl font-bold text-gray-900">Theme customization</h2>
            </div>
            <div class="grid lg:grid-cols-4 gap-5">
                @foreach(['logo' => 'Logo', 'favicon' => 'Favicon', 'footer_logo' => 'Footer logo'] as $key => $label)
                    <div class="rounded-xl border border-gray-200 p-4">
                        <span class="font-semibold text-gray-700">{{ $label }}</span>
                        @if($assetUrl($key))
                            <img src="{{ $assetUrl($key) }}" alt="{{ $label }}" class="mt-3 h-14 w-auto object-contain">
                        @endif
                        <input type="file" name="{{ $key }}" class="mt-3 block w-full text-sm">
                    </div>
                @endforeach
                <label class="block"><span class="font-semibold text-gray-700">Primary color</span><input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#2563EB') }}" class="mt-2 h-12 w-full rounded-xl border-gray-300"></label>
                <label class="block"><span class="font-semibold text-gray-700">Secondary color</span><input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#0F172A') }}" class="mt-2 h-12 w-full rounded-xl border-gray-300"></label>
                <label class="block"><span class="font-semibold text-gray-700">Accent color</span><input type="color" name="accent_color" value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}" class="mt-2 h-12 w-full rounded-xl border-gray-300"></label>
                <label class="block"><span class="font-semibold text-gray-700">Font family</span><input name="font_family" value="{{ old('font_family', $settings['font_family'] ?? 'Inter') }}" class="mt-2 w-full rounded-xl border-gray-300"></label>
                <label class="block"><span class="font-semibold text-gray-700">Button style</span><select name="button_style" class="mt-2 w-full rounded-xl border-gray-300">@foreach(['rounded','pill','square'] as $v)<option value="{{ $v }}" @selected(old('button_style', $settings['button_style'] ?? 'rounded') === $v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
                <label class="block"><span class="font-semibold text-gray-700">Border radius</span><input name="border_radius" value="{{ old('border_radius', $settings['border_radius'] ?? '1rem') }}" class="mt-2 w-full rounded-xl border-gray-300"></label>
                <label class="block"><span class="font-semibold text-gray-700">Theme mode</span><select name="theme_mode" class="mt-2 w-full rounded-xl border-gray-300">@foreach(['light','dark','system'] as $v)<option value="{{ $v }}" @selected(old('theme_mode', $settings['theme_mode'] ?? 'light') === $v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
                <label class="block"><span class="font-semibold text-gray-700">Header layout</span><select name="header_layout" class="mt-2 w-full rounded-xl border-gray-300">@foreach(['classic','centered','minimal'] as $v)<option value="{{ $v }}" @selected(old('header_layout', $settings['header_layout'] ?? 'classic') === $v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
                <label class="block"><span class="font-semibold text-gray-700">Footer layout</span><select name="footer_layout" class="mt-2 w-full rounded-xl border-gray-300">@foreach(['classic','compact','columns'] as $v)<option value="{{ $v }}" @selected(old('footer_layout', $settings['footer_layout'] ?? 'classic') === $v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
                <label class="block"><span class="font-semibold text-gray-700">Product cards</span><select name="product_card_style" class="mt-2 w-full rounded-xl border-gray-300">@foreach(['standard','compact','overlay'] as $v)<option value="{{ $v }}" @selected(old('product_card_style', $settings['product_card_style'] ?? 'standard') === $v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <p class="text-sm uppercase tracking-wide text-emerald-600 font-semibold">Homepage content</p>
                <h2 class="text-2xl font-bold text-gray-900">Hero and marketing copy</h2>
            </div>
            <div class="grid lg:grid-cols-2 gap-5">
                @foreach([
                    'hero_badge' => 'Hero badge',
                    'hero_title' => 'Hero title',
                    'hero_subtitle' => 'Hero subtitle',
                    'hero_primary_button_text' => 'Primary button text',
                    'hero_primary_button_url' => 'Primary button URL',
                    'hero_secondary_button_text' => 'Secondary button text',
                    'hero_secondary_button_url' => 'Secondary button URL',
                    'newsletter_title' => 'Newsletter title',
                    'newsletter_description' => 'Newsletter description',
                    'footer_description' => 'Footer description',
                    'about_title' => 'About page title',
                    'about_body' => 'About page body',
                    'about_seo_description' => 'About SEO description',
                ] as $key => $label)
                    <label class="block {{ str_contains($key, 'description') || in_array($key, ['hero_subtitle', 'about_body'], true) ? 'lg:col-span-2' : '' }}">
                        <span class="font-semibold text-gray-700">{{ $label }}</span>
                        @if(str_contains($key, 'description') || in_array($key, ['hero_subtitle', 'about_body'], true))
                            <textarea name="{{ $key }}" rows="2" class="mt-2 w-full rounded-xl border-gray-300">{{ old($key, $settings[$key] ?? '') }}</textarea>
                        @else
                            <input name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300">
                        @endif
                    </label>
                @endforeach
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <p class="text-sm uppercase tracking-wide text-orange-600 font-semibold">Operations</p>
                <h2 class="text-2xl font-bold text-gray-900">Contact, shipping, taxes, and social links</h2>
            </div>
            <div class="grid lg:grid-cols-3 gap-5">
                @foreach(['phone' => 'Phone', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'address' => 'Address', 'working_hours' => 'Working hours', 'delivery_fee' => 'Delivery fee', 'free_delivery_threshold' => 'Free delivery threshold', 'delivery_zone' => 'Delivery zone', 'delivery_time' => 'Delivery time', 'tax_rate' => 'Tax rate (%)', 'latitude' => 'Latitude', 'longitude' => 'Longitude', 'maps_link' => 'Maps link', 'facebook_url' => 'Facebook URL', 'instagram_url' => 'Instagram URL', 'twitter_url' => 'Twitter/X URL', 'linkedin_url' => 'LinkedIn URL', 'youtube_url' => 'YouTube URL', 'tiktok_url' => 'TikTok URL'] as $key => $label)
                    <label class="block {{ in_array($key, ['address', 'maps_link'], true) ? 'lg:col-span-2' : '' }}">
                        <span class="font-semibold text-gray-700">{{ $label }}</span>
                        <input name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" class="mt-2 w-full rounded-xl border-gray-300">
                    </label>
                @endforeach
            </div>
        </section>

        <div class="sticky bottom-4 flex justify-end">
            <button class="px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow-lg hover:bg-blue-700">Save store settings</button>
        </div>
    </form>
</div>
@endsection
