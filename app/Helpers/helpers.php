<?php
if (!function_exists('settings')) {
    function settings($key, $default = null) {
        try {
            $setting = \App\Models\Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
?>