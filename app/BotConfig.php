<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class BotConfig
{
    const CACHE_PREFIX='SETTINGS::';

    public static function getAll(string ...$keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = self::get($key);
        }

        return $result;
    }

    public static function get(string $key)
    {
        if (Cache::has(self::CACHE_PREFIX.$key)) {
            return Cache::get(self::CACHE_PREFIX.$key);
        }

        if ($setting = Setting::find($key)) {
            Cache::put(self::CACHE_PREFIX.$key, $setting->value, 60);
            return $setting->value;
        }

        throw new UnknownSettingKeyException($key);
    }

    public static function set($key, $value): void
    {
        $setting = Setting::find($key);
        if (!$setting) {
            $setting = new Setting();
            $setting->key = $key;
        }
        $setting->value = $value;
        $setting->save();

        Cache::put(self::CACHE_PREFIX.$key, $value, 60);
    }
}
