<?php

namespace App\Helpers;

class NumberHelper
{
    public static function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}