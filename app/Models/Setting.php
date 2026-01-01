<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type'
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'text')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * Get multiple settings as array
     */
    public static function getMultiple(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->get();
        $result = [];
        
        foreach ($keys as $key) {
            $setting = $settings->firstWhere('key', $key);
            $result[$key] = $setting ? $setting->value : null;
        }
        
        return $result;
    }

    /**
     * Delete a setting
     */
    public static function remove($key): bool
    {
        return static::where('key', $key)->delete() > 0;
    }

    /**
     * Check if setting exists
     */
    public static function has($key): bool
    {
        return static::where('key', $key)->exists();
    }
}