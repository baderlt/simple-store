<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResponsiveHeroBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_desktop_and_mobile_hero_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'title' => 'Responsive hero',
                'image' => UploadedFile::fake()->image('desktop.jpg', 1920, 900),
                'mobile_image' => UploadedFile::fake()->image('mobile.jpg', 900, 1000),
                'position' => 'hero',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.banners.index'));

        $banner = Banner::firstOrFail();

        $this->assertNotNull($banner->image_path);
        $this->assertNotNull($banner->mobile_image_path);
        Storage::disk('public')->assertExists($banner->image_path);
        Storage::disk('public')->assertExists($banner->mobile_image_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('media="(max-width: 767px)"', false)
            ->assertDontSee('orientation: portrait', false)
            ->assertSee(asset('storage/' . $banner->mobile_image_path), false)
            ->assertSee(asset('storage/' . $banner->image_path), false);
    }

    public function test_desktop_image_is_the_mobile_fallback(): void
    {
        $banner = Banner::create([
            'title' => 'Desktop only hero',
            'image_path' => 'banners/desktop-only.jpg',
            'position' => 'hero',
            'is_active' => true,
        ]);

        $this->assertSame($banner->full_image_url, $banner->mobile_image_url);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('media="(max-width: 767px)"', false)
            ->assertSee(asset('storage/banners/desktop-only.jpg'), false);
    }

    public function test_admin_can_remove_only_the_mobile_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::disk('public')->put('banners/desktop.jpg', 'desktop');
        Storage::disk('public')->put('banners/mobile/mobile.jpg', 'mobile');

        $banner = Banner::create([
            'title' => 'Editable hero',
            'image_path' => 'banners/desktop.jpg',
            'mobile_image_path' => 'banners/mobile/mobile.jpg',
            'position' => 'hero',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.banners.update', $banner), [
                'title' => $banner->title,
                'position' => 'hero',
                'delete_mobile_image' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.banners.index'));

        $banner->refresh();

        $this->assertNull($banner->mobile_image_path);
        $this->assertSame('banners/desktop.jpg', $banner->image_path);
        Storage::disk('public')->assertMissing('banners/mobile/mobile.jpg');
        Storage::disk('public')->assertExists('banners/desktop.jpg');
    }
}
