<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Support\HomepageSectionRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $this->ensureDefaultsExist();

        $sections = HomepageSection::query()->orderBy('position')->get();
        $availableTypes = HomepageSectionRegistry::types();

        return view('admin.homepage-sections.index', compact('sections', 'availableTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:80|alpha_dash|unique:homepage_sections,key',
            'name' => 'required|string|max:255',
            'layout' => 'required|string|max:80',
            'section_type' => 'required|string|max:80',
            'position' => 'nullable|integer|min:0',
            'is_enabled' => 'nullable|boolean',
            'settings_json' => 'nullable|string',
        ]);

        HomepageSection::create([
            'key' => $validated['key'],
            'name' => $validated['name'],
            'layout' => $validated['layout'],
            'position' => $validated['position'] ?? ((HomepageSection::max('position') ?? 0) + 10),
            'is_enabled' => $request->boolean('is_enabled', true),
            'settings' => $this->decodeSettings($validated['settings_json'] ?? null, $validated['section_type']),
        ]);

        return back()->with('success', 'Homepage section created.');
    }

    public function update(Request $request, HomepageSection $homepageSection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string|max:80',
            'section_type' => 'required|string|max:80',
            'position' => 'required|integer|min:0',
            'is_enabled' => 'nullable|boolean',
            'settings_json' => 'nullable|string',
        ]);

        $homepageSection->update([
            'name' => $validated['name'],
            'layout' => $validated['layout'],
            'position' => $validated['position'],
            'is_enabled' => $request->boolean('is_enabled'),
            'settings' => $this->decodeSettings($validated['settings_json'] ?? null, $validated['section_type']),
        ]);

        return back()->with('success', 'Homepage section updated.');
    }

    public function destroy(HomepageSection $homepageSection)
    {
        $homepageSection->delete();

        return back()->with('success', 'Homepage section removed.');
    }

    private function ensureDefaultsExist(): void
    {
        foreach (HomepageSectionRegistry::defaults() as $section) {
            HomepageSection::firstOrCreate(['key' => $section['key']], $section + ['is_enabled' => true]);
        }
    }

    private function decodeSettings(?string $json, string $sectionType): array
    {
        $settings = ['type' => $sectionType];

        if ($json) {
            $decoded = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                throw ValidationException::withMessages(['settings_json' => 'Settings must be valid JSON.']);
            }
            $settings = array_replace($settings, $decoded);
        }

        $settings['type'] = $sectionType;

        return $settings;
    }
}
