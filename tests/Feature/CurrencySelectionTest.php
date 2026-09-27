<?php

namespace Tests\Feature;

use App\Support\CurrencyFormatter;
use Tests\TestCase;

class CurrencySelectionTest extends TestCase
{
    public function test_product_prices_format_in_the_selected_product_currency(): void
    {
        $this->assertSame('€9.20', CurrencyFormatter::format(9.2, 'EUR'));
        $this->assertSame('£10.00', CurrencyFormatter::format(10, 'GBP'));
        $this->assertSame('¥150', CurrencyFormatter::format(150, 'JPY'));
    }

    public function test_listed_currency_is_converted_to_the_usd_order_base(): void
    {
        $this->assertSame('10.00', CurrencyFormatter::toBase(9.2, 'EUR'));
        $this->assertSame('$10.00', CurrencyFormatter::format(10, 'XYZ'));
    }
}