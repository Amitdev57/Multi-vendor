<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_checkout_can_create_lines_for_multiple_vendors_and_decrement_stock(): void
    {
        $buyer = User::factory()->create();
        $firstProduct = $this->makeProduct('Fieldwork Studio', 'tote', 38, 5);
        $secondProduct = $this->makeProduct('North House', 'cup', 24, 3);

        $order = app(CheckoutService::class)->placeOrder($buyer, [
            ['product_id' => $firstProduct->id, 'quantity' => 2],
            ['product_id' => $secondProduct->id, 'quantity' => 1],
        ]);

        $this->assertSame('100.00', $order->total);
        $this->assertCount(2, $order->items);
        $this->assertDatabaseHas('products', ['id' => $firstProduct->id, 'stock' => 3]);
        $this->assertDatabaseHas('products', ['id' => $secondProduct->id, 'stock' => 2]);
    }

    public function test_insufficient_stock_does_not_create_an_order_or_change_inventory(): void
    {
        $buyer = User::factory()->create();
        $product = $this->makeProduct('Fieldwork Studio', 'limited-run', 45, 1);

        try {
            app(CheckoutService::class)->placeOrder($buyer, [
                ['product_id' => $product->id, 'quantity' => 2],
            ]);
            $this->fail('Checkout should reject an order larger than available stock.');
        } catch (ValidationException) {
            $this->assertSame(1, $product->fresh()->stock);
            $this->assertSame(0, Order::count());
        }
    }

    public function test_foreign_currency_listing_is_converted_to_usd_and_keeps_its_currency_snapshot(): void
    {
        $buyer = User::factory()->create();
        $product = $this->makeProduct('Euro Shop', 'ceramic-cup', 9, 2, 'EUR');
        $product->update(['price' => '9.20']);

        $order = app(CheckoutService::class)->placeOrder($buyer, [
            ['product_id' => $product->id, 'quantity' => 1],
        ]);

        $this->assertSame('10.00', $order->total);
        $this->assertSame('9.20', $order->items->first()->unit_price);
        $this->assertSame('EUR', $order->items->first()->currency_code);
    }

    private function makeProduct(string $shop, string $slug, int|float $price, int $stock, string $currency = 'USD'): Product
    {
        $seller = User::factory()->create();
        $vendor = Vendor::create([
            'user_id' => $seller->id,
            'business_name' => $shop,
            'slug' => strtolower(str_replace(' ', '-', $shop)),
            'status' => 'active',
        ]);

        return Product::create([
            'vendor_id' => $vendor->id,
            'name' => $slug,
            'slug' => $slug,
            'price' => $price,
            'currency_code' => $currency,
            'stock' => $stock,
            'status' => 'active',
        ]);
    }
}