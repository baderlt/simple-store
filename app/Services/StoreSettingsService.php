<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class StoreSettingsService
{
    private const CACHE_KEY = 'store.settings';

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $settings = Setting::query()->get(['key', 'value', 'type']);

            return $settings->mapWithKeys(function (Setting $setting): array {
                $value = $setting->value;
                if ($setting->type === 'json' && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $value = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
                }

                return [$setting->key => $value];
            })->toArray();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, mixed $value, string $type = 'text'): void
    {
        Setting::set($key, $type === 'json' ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value, $type);
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
