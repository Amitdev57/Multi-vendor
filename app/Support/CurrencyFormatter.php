<?php

namespace App\Support;

class CurrencyFormatter
{
    public static function format(float|string|null $amount, ?string $code = null): string
    {
        $currencies = config('marketplace.currencies');
        $code ??= config('marketplace.default_currency', 'USD');
        $currency = $currencies[$code] ?? $currencies[config('marketplace.default_currency', 'USD')];

        return $currency['symbol'].number_format((float) $amount, $currency['decimals']);
    }

    public static function toBase(float|string|null $amount, ?string $code): string
    {
        $currencies = config('marketplace.currencies');
        $rate = (float) ($currencies[$code]['rate'] ?? 1);

        return number_format((float) $amount / ($rate ?: 1), 2, '.', '');
    }
}