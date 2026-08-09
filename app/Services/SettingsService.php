<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingsService
{
    protected static $cacheKey = 'system_settings';

    public static function get(string $key, $default = null)
    {
        $item = self::getCached()[$key] ?? null;

        if ($item === null) {
            return $default;
        }

        return $item['value'];
    }

    public static function set(string $key, $value)
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->is_encrypted) {
            $value = Crypt::encryptString($value);
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::clearCache();
    }

    public static function getCached(): array
    {
        return Cache::remember(self::$cacheKey, 3600, function () {
            return Setting::all()->mapWithKeys(function (Setting $setting) {
                $value = $setting->value;

                if ($setting->is_encrypted && $value) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Exception $e) {
                        // Keep stored value as-is
                    }
                }

                return [
                    $setting->key => [
                        'value' => $value,
                        'is_encrypted' => (bool) $setting->is_encrypted,
                    ],
                ];
            })->toArray();
        });
    }

    public static function getGroup(string $group): array
    {
        $all = self::getCached();

        return Setting::where('group', $group)
            ->pluck('key')
            ->mapWithKeys(function (string $key) use ($all) {
                return [$key => $all[$key]['value'] ?? null];
            })
            ->toArray();
    }

    public static function clearCache()
    {
        Cache::forget(self::$cacheKey);
    }

    public static function getAll(): array
    {
        return self::getCached();
    }
}