<?php

use App\Services\StoreSettingsService;

if (!function_exists('settings')) {
    function settings($key, $default = null) {
        return app(StoreSettingsService::class)->get($key, $default);
    }
}
