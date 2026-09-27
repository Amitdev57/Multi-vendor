<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\CurrencyFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function placeOrder(User $user, array $items): Order
    {
        $quantities = collect($items)
            ->groupBy('product_id')
            ->map(fn ($rows) => $rows->sum('quantity'));

        return DB::transaction(function () use ($user, $quantities) {
            $products = Product::query()
                ->whereKey($quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->with('vendor')
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages(['items' => 'One or more products are no longer available.']);
            }

            $totalCents = 0;
            $lines = [];

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if ($product->status !== 'active' || $product->vendor?->status !== 'active') {
                    throw ValidationException::withMessages(['items' => "{$product->name} is no longer available."]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages(['items' => "There is not enough stock for {$product->name}."]);
                }

                $unitPrice = number_format((float) $product->price, 2, '.', '');
                $baseUnitPrice = CurrencyFormatter::toBase($unitPrice, $product->currency_code);
                $totalCents += (int) round((float) $baseUnitPrice * 100) * $quantity;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'currency_code' => $product->currency_code,
                ];
            }

            $total = number_format($totalCents / 100, 2, '.', '');
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'MP-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'status' => 'placed',
                'subtotal' => $total,
                'total' => $total,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'vendor_id' => $line['product']->vendor_id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'currency_code' => $line['currency_code'],
                ]);
                $line['product']->decrement('stock', $line['quantity']);
            }

            return $order->load('items.product', 'items.vendor');
        }, 3);
    }
}
