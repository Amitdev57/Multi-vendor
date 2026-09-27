@extends('layout')

@section('title', $vendor->business_name)

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div><div class="eyebrow mb-2">Seller workspace</div><h1 class="h2 mb-1">{{ $vendor->business_name }}</h1><p class="text-secondary mb-0">Manage your catalog and recent sales.</p></div>
        <a class="btn btn-outline-success" href="{{ route('home') }}">View market</a>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3"><div class="dashboard-stat h-100"><div class="small text-secondary">Products</div><div class="h3 mb-0">{{ $products->count() }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="dashboard-stat h-100"><div class="small text-secondary">In stock</div><div class="h3 mb-0">{{ $products->sum('stock') }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="dashboard-stat h-100"><div class="small text-secondary">Recent line items</div><div class="h3 mb-0">{{ $orderItems->count() }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="dashboard-stat h-100"><div class="small text-secondary">Shop status</div><div class="h3 mb-0 text-capitalize">{{ $vendor->status }}</div></div></div>
    </div>

    <div class="row g-4 align-items-start">
        <section class="col-lg-5">
            <div class="bg-white border rounded-3 p-3 p-lg-4">
                <div class="eyebrow mb-2">New listing</div><h2 class="h5 mb-3">Add a product</h2>
                <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3"><label class="form-label" for="name">Product name</label><input class="form-control search-field" id="name" name="name" value="{{ old('name') }}" required maxlength="160"></div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4"><label class="form-label" for="price">Price</label><input class="form-control search-field" id="price" name="price" type="number" step="0.01" min="0.01" value="{{ old('price') }}" required></div>
                        <div class="col-6 col-sm-4"><label class="form-label" for="currency_code">Currency</label><select class="form-select search-field" id="currency_code" name="currency_code" required>@foreach (config('marketplace.currencies') as $code => $currency)<option value="{{ $code }}" @selected(old('currency_code', 'USD') === $code)>{{ $code }} · {{ $currency['name'] }}</option>@endforeach</select></div>
                        <div class="col-6 col-sm-4"><label class="form-label" for="stock">Stock</label><input class="form-control search-field" id="stock" name="stock" type="number" min="0" value="{{ old('stock', 1) }}" required></div>
                    </div>
                    <div class="mb-3"><label class="form-label" for="category_id">Category</label><select class="form-select search-field" id="category_id" name="category_id"><option value="">Uncategorized</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label" for="description">Description</label><textarea class="form-control search-field" id="description" name="description" rows="3" maxlength="4000">{{ old('description') }}</textarea></div>
                    <div class="mb-3"><label class="form-label" for="image">Product image</label><input class="form-control search-field" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPEG, PNG, or WebP, up to 5 MB.</div></div>
                    <div class="mb-3"><label class="form-label" for="image_url">Or image URL</label><input class="form-control search-field" id="image_url" name="image_url" type="url" value="{{ old('image_url') }}"></div>
                    <button class="btn btn-market w-100" type="submit">Publish listing</button>
                </form>
            </div>
        </section>

        <section class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Your products</h2><span class="small text-secondary">{{ $products->count() }} listings</span></div>
            <div class="table-responsive bg-white border rounded-3">
                <table class="table table-market align-middle mb-0">
                    <thead><tr><th>Product</th><th>Price</th><th>Stock</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td><div class="d-flex align-items-center gap-2"><img src="{{ $product->display_image }}" alt="" width="48" height="48" class="rounded-2" style="object-fit:cover"><div><a class="text-dark fw-semibold text-decoration-none" href="{{ route('products.show', $product) }}">{{ $product->name }}</a><div class="small text-secondary">{{ $product->category?->name ?? 'Uncategorized' }}</div></div></div></td>
                                <td>{{ \App\Support\CurrencyFormatter::format($product->price, $product->currency_code) }}</td>
                                <td>{{ $product->stock }}</td>
                                <td class="text-end"><form action="{{ route('vendor.products.destroy', $product) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">Your first listing will appear here.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5"><h2 class="h5 mb-3">Recent orders</h2>
                @forelse ($orderItems as $item)
                    <div class="d-flex justify-content-between gap-3 py-3 border-top"><div><div class="fw-semibold">{{ $item->product->name }}</div><div class="small text-secondary">{{ $item->quantity }} ordered by {{ $item->order->user->name }} · {{ $item->order->order_number }}</div></div><span class="product-price">{{ \App\Support\CurrencyFormatter::format($item->unit_price * $item->quantity, $item->currency_code) }}</span></div>
                @empty
                    <p class="small text-secondary border-top pt-3">New orders will show here.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection