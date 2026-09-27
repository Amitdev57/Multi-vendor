<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $products = Product::query()
            ->with(['vendor', 'category'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');
        $lines = collect($cart)
            ->filter(fn ($quantity, $id) => $products->has($id))
            ->map(fn ($quantity, $id) => ['product' => $products->get($id), 'quantity' => $quantity]);
        $totalsByCurrency = $lines
            ->groupBy(fn ($line) => $line['product']->currency_code)
            ->map(fn ($currencyLines) => $currencyLines->sum(fn ($line) => (float) $line['product']->price * $line['quantity']));

        return view('cart-index', compact('lines', 'totalsByCurrency'));
    }

    public function store(AddCartItemRequest $request)
    {
        $product = Product::query()
            ->where('status', 'active')
            ->whereHas('vendor', fn ($query) => $query->where('status', 'active'))
            ->findOrFail($request->validated('product_id'));

        abort_if($product->stock < 1, 422, 'This product is out of stock.');

        $cart = session('cart', []);
        $cart[$product->id] = min(99, $product->stock, ($cart[$product->id] ?? 0) + $request->validated('quantity'));
        session(['cart' => $cart]);

        return back()->with('status', 'Added to your bag.');
    }

    public function destroy(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('status', 'Item removed.');
    }

    public function checkout(Request $request, CheckoutService $checkout)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your bag is empty.']);
        }

        $items = collect($cart)->map(fn ($quantity, $productId) => [
            'product_id' => (int) $productId,
            'quantity' => (int) $quantity,
        ])->values()->all();

        $order = $checkout->placeOrder($request->user(), $items);
        $request->session()->forget('cart');

        return view('orders-complete', compact('order'));
    }
}
