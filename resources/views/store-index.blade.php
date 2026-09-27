@extends('layout')

@section('title', 'Independent finds')

@section('content')
<div class="container py-4 py-lg-5">
    <section class="hero-market mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 hero-copy">
                <div class="eyebrow mb-3">A marketplace for good things</div>
                <h1>Small shops.<br>Good things.</h1>
                <p class="mt-3 mb-4">Find everyday objects made by independent sellers, and know who made what you bring home.</p>
                <a class="btn btn-market btn-lg px-4" href="#shop">Explore the market</a>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <img class="img-fluid rounded-3 shadow-sm" style="width:100%;height:290px;object-fit:cover" src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1000&q=85" alt="A thoughtfully arranged independent clothing shop">
            </div>
        </div>
    </section>

    <section id="shop" class="pt-2">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <div class="eyebrow mb-2">The market</div>
                <h2 class="h3 mb-1">Find your next favorite</h2>
                <p class="text-secondary mb-0">{{ $products->total() }} independent finds, selected for everyday life.</p>
            </div>
            <form class="d-flex gap-2" method="GET" action="{{ route('home') }}">
                <input class="form-control search-field" type="search" name="q" value="{{ $search }}" placeholder="Product name" aria-label="Filter by product name">
                <select class="form-select search-field" name="category" aria-label="Filter by category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-success" type="submit">Filter</button>
            </form>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse ($products as $product)
                <div class="col-6 col-lg-4 col-xl-3">
                    <article class="product-card h-100">
                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                            <img class="product-image" src="{{ $product->display_image }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                        <div class="p-3">
                            <div class="vendor-label mb-1">{{ $product->vendor->business_name }} @if($product->category)<span aria-hidden="true">·</span> {{ $product->category->name }}@endif</div>
                            <a class="text-decoration-none text-dark" href="{{ route('products.show', $product) }}"><h3 class="h6 mb-2">{{ $product->name }}</h3></a>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="product-price">{{ \App\Support\CurrencyFormatter::format($product->price, $product->currency_code) }}</span>
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-sm btn-outline-success" type="submit" @disabled($product->stock < 1)>Add</button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <h3 class="h5">No matches just yet</h3>
                    <p class="text-secondary">Try another search or browse all categories.</p>
                    <a class="btn btn-outline-success" href="{{ route('home') }}">Clear filters</a>
                </div>
            @endforelse
        </div>
        <div class="d-flex justify-content-between mt-4">
            @if ($products->previousPageUrl()) <a class="btn btn-outline-success" href="{{ $products->previousPageUrl() }}">Previous</a> @else <span></span> @endif
            @if ($products->nextPageUrl()) <a class="btn btn-outline-success" href="{{ $products->nextPageUrl() }}">More finds</a> @endif
        </div>
    </section>
</div>
@endsection