<?php

use App\Models\Currency;

if (!function_exists('getExchangeRate')) {
    function getExchangeRate($from, $to)
    {
        if ($from === $to)
            return 1.0;

        $usdToFrom = Currency::where('base_code', 'USD')->where('code', $from)->first();
        $usdToTo = Currency::where('base_code', 'USD')->where('code', $to)->first();

        if (!$usdToFrom || !$usdToTo) {
            throw new \Exception("Exchange rate not found for $from or $to");
        }

        return $usdToTo->exchange_rate / $usdToFrom->exchange_rate;
    }
}