@extends('admin.layouts.app')

@section('title', 'Homepage Builder - Admin')
@section('header', 'Homepage Builder')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-5 py-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4">{{ $errors->first() }}</div>
    @endif

    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <p class="text-sm uppercase tracking-wide text-blue-600 font-semibold">JSON-driven modular page</p>
                <h2 class="text-2xl font-bold text-gray-900">Add, remove, reorder, and configure sections</h2>
                <p class="text-gray-500">Each section stores flexible JSON settings so the same storefront can power fashion, electronics, grocery, restaurants, digital products, or any future niche.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.homepage-sections.store') }}" class="grid lg:grid-cols-6 gap-4 rounded-xl bg-gray-50 p-4 mb-6">
            @csrf
            <input name="key" placeholder="unique_key" class="rounded-xl border-gray-300" required>
            <input name="name" placeholder="Section name" class="rounded-xl border-gray-300" required>
            <select name="section_type" class="rounded-xl border-gray-300" required>
                @foreach($availableTypes as $type => $description)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            <input name="layout" placeholder="layout" value="grid" class="rounded-xl border-gray-300" required>
            <input type="number" name="position" placeholder="position" class="rounded-xl border-gray-300">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_enabled" value="1" checked class="rounded border-gray-300 text-blue-600"> Enabled</label>
            <textarea name="settings_json" rows="3" placeholder='{"title":"New section","limit":8}' class="lg:col-span-5 rounded-xl border-gray-300"></textarea>
            <button class="rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Add section</button>
        </form>

        <div class="space-y-4">
            @foreach($sections as $section)
                <form method="POST" action="{{ route('admin.homepage-sections.update', $section) }}" class="rounded-2xl border border-gray-200 p-4">
                    @csrf
                    @method('PUT')
                    <div class="grid lg:grid-cols-12 gap-4 items-start">
                        <div class="lg:col-span-2">
                            <label class="text-xs uppercase text-gray-500">Key</label>
                            <div class="font-mono text-sm bg-gray-100 rounded-lg px-3 py-2">{{ $section->key }}</div>
                        </div>
                        <label class="lg:col-span-2 block"><span class="text-xs uppercase text-gray-500">Name</span><input name="name" value="{{ $section->name }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                        <label class="lg:col-span-2 block"><span class="text-xs uppercase text-gray-500">Type</span><select name="section_type" class="mt-1 w-full rounded-xl border-gray-300">@foreach($availableTypes as $type => $description)<option value="{{ $type }}" @selected(($section->settings['type'] ?? $section->key) === $type)>{{ $type }}</option>@endforeach</select></label>
                        <label class="lg:col-span-2 block"><span class="text-xs uppercase text-gray-500">Layout</span><input name="layout" value="{{ $section->layout }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                        <label class="block"><span class="text-xs uppercase text-gray-500">Position</span><input type="number" name="position" value="{{ $section->position }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                        <label class="inline-flex items-center gap-2 pt-7"><input type="checkbox" name="is_enabled" value="1" @checked($section->is_enabled) class="rounded border-gray-300 text-blue-600"> Enabled</label>
                        <div class="lg:col-span-2 flex gap-2 pt-6">
                            <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold">Save</button>
                        </div>
                        <label class="lg:col-span-12 block"><span class="text-xs uppercase text-gray-500">Settings JSON</span><textarea name="settings_json" rows="5" class="mt-1 w-full font-mono text-sm rounded-xl border-gray-300">{{ json_encode($section->settings ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.homepage-sections.destroy', $section) }}" onsubmit="return confirm('Remove this homepage section?')" class="-mt-3 ml-4">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:text-red-800">Remove section</button>
                </form>
            @endforeach
        </div>
    </section>
</div>
@endsection
