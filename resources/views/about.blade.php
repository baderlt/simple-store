@extends('layouts.app')

@section('title', settings('about_title', 'About us'))
@section('description', settings('about_seo_description', settings('store_description', 'Learn more about our store.')))

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-black mb-8 text-center">{{ settings('about_title', 'About our store') }}</h1>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e(settings('about_body', 'Use the admin settings area to replace this content with your brand story, mission, services, and local business details.'))) !!}
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            @if(settings('address'))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-xl mb-4 flex items-center"><i class="fas fa-map-marker-alt text-green-600 mr-3"></i> Address</h3>
                    <p class="text-gray-700">{{ settings('address') }}</p>
                </div>
            @endif
            @if(settings('working_hours'))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-xl mb-4 flex items-center"><i class="fas fa-clock text-green-600 mr-3"></i> Hours</h3>
                    <p class="text-gray-700">{{ settings('working_hours') }}</p>
                </div>
            @endif
            @if(settings('phone'))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-xl mb-4 flex items-center"><i class="fas fa-phone text-green-600 mr-3"></i> Phone</h3>
                    <p class="text-gray-700">{{ settings('phone') }}</p>
                </div>
            @endif
            @if(settings('email'))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-xl mb-4 flex items-center"><i class="fas fa-envelope text-green-600 mr-3"></i> Email</h3>
                    <p class="text-gray-700">{{ settings('email') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
