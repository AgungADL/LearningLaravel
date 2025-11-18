<?php

namespace App\Helpers;

class StringHelper
{
    public static function cleanUtf8($string)
    {
        // Pastikan string adalah UTF-8 valid
        $clean = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        // Hilangkan karakter kontrol
        $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $clean);
        return $clean;
    }
}
