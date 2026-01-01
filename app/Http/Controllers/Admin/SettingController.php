<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            // Informations Générales
            'store_name' => 'required|string|max:255',
            'working_hours' => 'required|string|max:255',
            
            // Contact & Réseaux
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            "instagram_url"=>'nullable|string',
            "facebook_url"=>"nullable|string",
            
            // Localisation GPS
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'maps_link' => 'nullable|string',
            
            
            // Paramètres de Livraison
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_zone' => 'nullable|string|max:255',
            'delivery_time' => 'nullable|string|max:100',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            
            // Logo
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ], [
            'latitude.required' => 'La latitude est requise pour la localisation.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.required' => 'La longitude est requise pour la localisation.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            'delivery_fee.min' => 'Les frais de livraison ne peuvent pas être négatifs.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format JPEG, PNG, JPG ou SVG.',
            'logo.max' => 'Le logo ne doit pas dépasser 2MB.',
        ]);

        // Add custom validation for logo dimensions
        $validator->after(function ($validator) use ($request) {
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $dimensions = getimagesize($image->getPathname());
                
                if ($dimensions[0] < 100 || $dimensions[1] < 100) {
                    $validator->errors()->add(
                        'logo', 'Le logo doit avoir au moins 100x100 pixels.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get validated data
        $validated = $validator->validated();
        
        // Define field types for your model
        $fieldTypes = [
            'store_name' => 'text',
            'phone' => 'text',
            'email' => 'text',
            'whatsapp' => 'text',
            'address' => 'textarea',
            'working_hours' => 'text',
            'delivery_fee' => 'number',
            'latitude' => 'number',
            'longitude' => 'number',
            'maps_link' => 'text',
            "instagram_url"=>"text",
            "facebook_url"=>"text",
            'delivery_zone' => 'text',
            'delivery_time' => 'text',
            'free_delivery_threshold' => 'number',
            'logo' => 'image',
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $logoPath = $request->file('logo')->store('settings/logos', 'public');
            Setting::set('logo', $logoPath, 'image');
        }

        // Handle other fields
        foreach ($validated as $key => $value) {
            // Skip logo as it's already handled
            if ($key === 'logo') {
                continue;
            }

            // Handle empty values for optional fields
            if (empty($value) && in_array($key, ['whatsapp', 'delivery_zone', 'delivery_time', 'free_delivery_threshold'])) {
                $value = null;
            }

            // Get field type or default to 'text'
            $type = $fieldTypes[$key] ?? 'text';
            
            // Save setting
            Setting::set($key, $value, $type);
        }

return back()->with('success', 'Tous les paramètres ont été enregistrés avec succès.');
    }

    /**
     * Delete the logo
     */
    public function deleteLogo(Request $request)
    {
        try {
            $logoPath = Setting::get('logo');
            
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            Setting::remove('logo');
            
return back()->with('success', 'Le logo a été supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error','Impossible de supprimer le logo : ' . $e->getMessage());
        }
    }



    /**
     * Show settings as JSON (for API or AJAX requests)
     */
    public function getJson()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Add some computed fields
        $settings['has_logo'] = !empty($settings['logo']);
        $settings['has_whatsapp'] = !empty($settings['whatsapp']);
        
        // Format numeric values
        $numericFields = ['delivery_fee', 'latitude', 'longitude', 'free_delivery_threshold'];
        foreach ($numericFields as $field) {
            if (isset($settings[$field]) && is_numeric($settings[$field])) {
                $settings[$field] = (float) $settings[$field];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting($key)
    {
        $value = Setting::get($key);
        
        if (is_null($value)) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }

    /**
     * Update a single setting (for AJAX requests)
     */
    public function updateSingle(Request $request, $key)
    {
        $validKeys = [
            'store_name', 'phone', 'email', 'whatsapp', 'address', 'working_hours',
            'delivery_fee', 'latitude', 'longitude', 'delivery_zone', 'delivery_time',
            'free_delivery_threshold'
        ];

        if (!in_array($key, $validKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid setting key'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $value = $request->input('value');
        
        // Validate specific fields
        $validationRules = [
            'email' => 'email',
            'latitude' => 'numeric|between:-90,90',
            'longitude' => 'numeric|between:-180,180',
            'delivery_fee' => 'numeric|min:0',
            'free_delivery_threshold' => 'numeric|min:0',
        ];

        if (isset($validationRules[$key])) {
            $fieldValidator = Validator::make(['value' => $value], [
                'value' => $validationRules[$key]
            ]);

            if ($fieldValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $fieldValidator->errors()
                ], 422);
            }
        }

        // Determine field type
        $numberFields = ['delivery_fee', 'latitude', 'longitude', 'free_delivery_threshold'];
        $type = in_array($key, $numberFields) ? 'number' : 'text';
        
        Setting::set($key, $value, $type);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }
}