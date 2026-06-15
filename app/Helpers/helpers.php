<?php

use App\Services\StoreSettingsService;

if (!function_exists('settings')) {
    function settings($key, $default = null) {
        return app(StoreSettingsService::class)->get($key, $default);
    }
}

if (! function_exists('format_price')) {
    function format_price($amount): string
    {
        return rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.');
    }
}
