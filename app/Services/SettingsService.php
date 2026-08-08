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
        $settings = self::getCached();
        $value = $settings[$key] ?? $default;

        // Decrypt if encrypted
        $setting = Setting::where('key', $key)->first();
        if ($setting && $setting->is_encrypted && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
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

    public static function getCached()
    {
        return Cache::remember(self::$cacheKey, 3600, function () {
            return Setting::all()->mapWithKeys(function ($setting) {
                $value = $setting->value;
                if ($setting->is_encrypted && $value) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Exception $e) {
                        // Keep as is
                    }
                }
                return [$setting->key => $value];
            })->toArray();
        });
    }

    public static function getGroup(string $group)
    {
        $settings = self::getCached();
        return Setting::where('group', $group)->get()->mapWithKeys(function ($setting) use ($settings) {
            return [$setting->key => $settings[$setting->key] ?? null];
        })->toArray();
    }

    public static function clearCache()
    {
        Cache::forget(self::$cacheKey);
    }

    public static function getAll()
    {
        return self::getCached();
    }
}
