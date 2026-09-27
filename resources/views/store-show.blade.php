@extends('layout')

@section('title', $product->name)

@section('content')
<div class="container py-4 py-lg-5">
    <a class="small text-secondary text-decoration-none" href="{{ route('home') }}">Market <span aria-hidden="true">/</span> {{ $product->name }}</a>
    <div class="row g-4 g-lg-5 mt-2 align-items-start">
        <div class="col-lg-7">
            <img class="img-fluid rounded-3 w-100" style="max-height:620px;object-fit:cover;background:#e7eee8" src="{{ $product->display_image }}" alt="{{ $product->name }}">
        </div>
        <div class="col-lg-5 pt-lg-4">
            <div class="eyebrow mb-2">{{ $product->category?->name ?? 'Independent goods' }}</div>
            <h1 class="h2 mb-2">{{ $product->name }}</h1>
            <a class="vendor-label text-decoration-none" href="#seller">Made by {{ $product->vendor->business_name }}</a>
            <div class="product-price fs-3 mt-4">{{ \App\Support\CurrencyFormatter::format($product->price, $product->currency_code) }}</div>
            <p class="text-secondary mt-3">{{ $product->description }}</p>
            <div class="stock-label mb-4">{{ $product->stock > 0 ? $product->stock.' available' : 'Currently sold out' }}</div>
            @if ($product->stock > 0)
                <form class="d-flex gap-2" action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="visually-hidden" for="quantity">Quantity</label>
                    <input id="quantity" class="form-control search-field" style="max-width:100px" type="number" name="quantity" value="1" min="1" max="{{ min(99, $product->stock) }}">
                    <button class="btn btn-market flex-grow-1" type="submit">Add to bag</button>
                </form>
            @else
                <button class="btn btn-secondary w-100" disabled>Out of stock</button>
            @endif
            <div id="seller" class="mt-5 pt-4 section-rule border-top">
                <div class="eyebrow mb-2">The maker</div>
                <h2 class="h5">{{ $product->vendor->business_name }}</h2>
                <p class="text-secondary mb-0">{{ $product->vendor->description ?: 'An independent seller on Marketfield.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection