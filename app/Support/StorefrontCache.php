<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class StorefrontCache
{
    public const HOME_KEY = 'storefront.home.v1';

    public static function clearHome(): void
    {
        Cache::forget(self::HOME_KEY);
    }
}
