<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['vendor', 'category'])
            ->where('status', 'active')
            ->whereHas('vendor', fn ($query) => $query->where('status', 'active'))
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('store-index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'search' => $request->string('q')->toString(),
            'selectedCategory' => $request->string('category')->toString(),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active' && $product->vendor?->status === 'active', 404);

        return view('store-show', ['product' => $product->load(['vendor', 'category'])]);
    }
}
