<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    /**
     * Mengambil semua pengaturan sebagai array Kunci => Nilai.
     * Digunakan oleh SettingManager dan POS.
     */
    public static function getSettings()
    {
        return self::pluck('value', 'key')->all();
    }

    /**
     * Mengupdate atau membuat pengaturan baru.
     */
    public static function setSetting(string $key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }
}
