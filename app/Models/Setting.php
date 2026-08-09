<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'is_public',
        'is_encrypted',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
    ];

    // Cache the settings
    protected static $cacheKey = 'system_settings';

    public static function get($key, $default = null)
    {
        $settings = self::getCached();
        return $settings[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache
        \Cache::forget(self::$cacheKey);

        return $setting;
    }

    public static function getCached()
    {
        return \Cache::remember(self::$cacheKey, 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    public static function getGrouped($group = null)
    {
        $query = self::query();
        if ($group) {
            $query->where('group', $group);
        }
        return $query->get()->groupBy('group')->toArray();
    }
}
