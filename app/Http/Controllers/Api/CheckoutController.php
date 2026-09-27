<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request, CheckoutService $checkout)
    {
        $order = $checkout->placeOrder($request->user(), $request->validated('items'));

        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'total' => $order->total,
            'currency_code' => 'USD',
            'items' => $order->items->map(fn ($item) => [
                'product' => $item->product->name,
                'vendor' => $item->vendor->business_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'currency_code' => $item->currency_code,
            ]),
        ], 201);
    }
}
