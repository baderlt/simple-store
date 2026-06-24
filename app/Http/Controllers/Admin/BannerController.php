<?php
// app/Http/Controllers/Admin/BannerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Support\OptimizesImages;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->paginate(10);
        
        $activeBanners = Banner::where('is_active', true)->count();
        $scheduledBanners = Banner::where('is_active', true)
            ->where('start_at', '>', now())
            ->count();
        $expiredBanners = Banner::where('is_active', true)
            ->where('end_at', '<', now())
            ->count();
            
        return view('admin.banners.index', compact(
            'banners',
            'activeBanners',
            'scheduledBanners',
            'expiredBanners'
        ));
    }
    
    public function create()
    {
        return view('admin.banners.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'position' => 'required|in:hero,middle,bottom,sidebar',
            'order' => 'nullable|integer|min:0|max:100',
            'cta_text' => 'nullable|string|max:50',
            'cta_text_ar' => 'nullable|string|max:50',
            'cta_link' => 'nullable|url|max:500',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'is_active' => 'boolean',
        ]);
        
        if ($request->hasFile('image')) {
            $path = OptimizesImages::store($request->file('image'), 'banners', 1920, 1000);
            $validated['image_path'] = $path;
        }

        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = OptimizesImages::store(
                $request->file('mobile_image'),
                'banners/mobile',
                900,
                1000
            );
        }
        
        Banner::create($validated);
        
        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banner_created'));
    }
    
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }
    
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }
    
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'delete_mobile_image' => 'nullable|boolean',
            'position' => 'required|in:hero,middle,bottom,sidebar',
            'order' => 'nullable|integer|min:0|max:100',
            'cta_text' => 'nullable|string|max:50',
            'cta_text_ar' => 'nullable|string|max:50',
            'cta_link' => 'nullable|url|max:500',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'is_active' => 'boolean',
        ]);
        
        if ($request->boolean('delete_image') && ! $request->hasFile('image')) {
            $validated['image_path'] = null;
            $validated['is_active'] = false;
        }

        if ($request->hasFile('image')) {
            // The Banner model removes the old file when image_path changes.
            $path = OptimizesImages::store($request->file('image'), 'banners', 1920, 1000);
            $validated['image_path'] = $path;
        }

        if ($request->boolean('delete_mobile_image') && ! $request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = null;
        }

        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = OptimizesImages::store(
                $request->file('mobile_image'),
                'banners/mobile',
                900,
                1000
            );
        }

        unset($validated['delete_mobile_image']);
        
        $banner->update($validated);
        
        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banner_updated'));
    }
    
    public function destroy(Banner $banner)
    {
        $banner->delete();
        
        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banner_deleted'));
    }
    
    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        
        $status = $banner->is_active ? 'activée' : 'désactivée';
        
        return back()->with('success', __('admin.banner_status_updated', ['status' => $status]));
    }
    
    public function duplicate(Banner $banner)
    {
        $newBanner = $banner->replicate();
        $newBanner->title = $newBanner->title . ' (Copie)';
        $newBanner->save();
        
        return redirect()->route('admin.banners.edit', $newBanner)
            ->with('success', __('admin.banner_duplicated'));
    }
}
