<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
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
            ->paginate(16);

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active' && $product->vendor?->status === 'active', 404);

        return new ProductResource($product->load(['vendor', 'category']));
    }

    public function store(StoreProductRequest $request)
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor && $vendor->status === 'active', 403);

        $data = $request->safe()->except('image');
        $data['slug'] = Str::slug($data['name']);
        $product = $vendor->products()->create($data);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return (new ProductResource($product->load(['vendor', 'category'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($request->user()->can('products.update') && $request->user()->vendor?->id === $product->vendor_id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('products', 'name')->ignore($product->id)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:4000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'currency_code' => ['required', 'string', 'size:3', Rule::in(array_keys(config('marketplace.currencies')))],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $image = $request->file('image');
        $product->fill(collect($data)->except('image')->all());
        $product->slug = Str::slug($data['name']);
        $product->save();

        if ($image) {
            $product->clearMediaCollection('images');
            $product->addMedia($image)->toMediaCollection('images');
        }

        return new ProductResource($product->load(['vendor', 'category']));
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($request->user()->can('products.delete') && $request->user()->vendor?->id === $product->vendor_id, 403);
        $product->delete();

        return response()->noContent();
    }
}
