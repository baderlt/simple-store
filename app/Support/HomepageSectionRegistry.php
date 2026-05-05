<?php

namespace App\Support;

class HomepageSectionRegistry
{
    public static function defaults(): array
    {
        return config('storefront.homepage_sections', []);
    }
}
