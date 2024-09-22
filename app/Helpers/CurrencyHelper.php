<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function formatCurrency($amount, $currencySymbol = '€', $decimals = 0, $decimalSeparator = ',', $thousandSeparator = '.')
    {
        return $currencySymbol . ' ' . number_format($amount, $decimals, $decimalSeparator, $thousandSeparator);
    }
}
