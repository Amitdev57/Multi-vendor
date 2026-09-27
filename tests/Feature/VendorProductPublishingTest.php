<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorProductPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_vendor_gets_permissions_and_can_publish_a_product(): void
    {
        $seller = User::factory()->create();
        Vendor::create([
            'user_id' => $seller->id,
            'business_name' => 'Test Shop',
            'slug' => 'test-shop',
            'status' => 'active',
        ]);

        $this->actingAs($seller)
            ->get(route('vendor.products.index'))
            ->assertOk()
            ->assertSee('name="currency_code"', false)
            ->assertSee('EUR · Euro');

        $this->assertTrue($seller->fresh()->can('products.create'));

        $response = $this->actingAs($seller)->post(route('vendor.products.store'), [
            'name' => 'New Handmade Vase',
            'description' => 'A test product.',
            'price' => '32.50',
            'currency_code' => 'EUR',
            'stock' => 4,
        ]);

        $response->assertRedirect(route('products.show', 'new-handmade-vase'));
        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'New Handmade Vase')->firstOrFail();
        $this->assertSame('new-handmade-vase', $product->slug);
        $this->assertSame('EUR', $product->currency_code);

        $this->actingAs($seller)
            ->get(route('vendor.dashboard'))
            ->assertOk()
            ->assertSee('New Handmade Vase');

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('New Handmade Vase')
            ->assertSee('€32.50');
    }
}