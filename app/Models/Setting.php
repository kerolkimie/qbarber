<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting:{$key}", 300, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    /**
     * Tukar no. telefon (cth: "013-4558430") ke pautan wa.me (WhatsApp Web/App).
     * Buang semua aksara bukan nombor, tukar "0" depan ke "60" (kod negara Malaysia).
     */
    public static function whatsappLink(): ?string
    {
        $number = static::get('whatsapp_number');

        if (! $number) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $number);

        if (str_starts_with($digits, '0')) {
            $digits = '60' . substr($digits, 1);
        }

        return "https://wa.me/{$digits}";
    }
}
