@extends('layout')

@section('title', 'Your bag')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="eyebrow mb-2">Your picks</div>
    <h1 class="h2 mb-4">Your bag</h1>
    @if ($lines->isEmpty())
        <div class="py-5 text-center">
            <h2 class="h4">Nothing in your bag yet</h2>
            <p class="text-secondary">There are plenty of small shops to discover.</p>
            <a class="btn btn-market" href="{{ route('home') }}">Browse the market</a>
        </div>
    @else
        <div class="row g-5">
            <div class="col-lg-8">
                @foreach ($lines as $line)
                    <article class="row align-items-center g-3 py-3 section-rule">
                        <div class="col-3 col-md-2"><img class="product-image rounded-2" style="aspect-ratio:1" src="{{ $line['product']->display_image }}" alt="{{ $line['product']->name }}"></div>
                        <div class="col-6 col-md-7">
                            <div class="vendor-label">{{ $line['product']->vendor->business_name }}</div>
                            <h2 class="h6 mb-1">{{ $line['product']->name }}</h2>
                            <div class="small text-secondary">Quantity: {{ $line['quantity'] }}</div>
                        </div>
                        <div class="col-3 text-end">
                            <div class="product-price">{{ \App\Support\CurrencyFormatter::format($line['product']->price * $line['quantity'], $line['product']->currency_code) }}</div>
                            <form action="{{ route('cart.destroy', $line['product']) }}" method="POST" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link btn-sm p-0 text-secondary" type="submit">Remove</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
            <aside class="col-lg-4">
                <div class="p-4 bg-white border rounded-3">
                    <h2 class="h5">Order summary</h2>
                    @foreach ($totalsByCurrency as $code => $subtotal)
                        <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary">Subtotal ({{ $code }})</span><strong>{{ \App\Support\CurrencyFormatter::format($subtotal, $code) }}</strong></div>
                    @endforeach
                    <p class="small text-secondary mt-3">At checkout, each item is converted to USD using the marketplace’s configured exchange estimate. Shipping and tax are not included.</p>
                    @auth
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button class="btn btn-market w-100" type="submit">Place order</button>
                        </form>
                    @else
                        <a class="btn btn-market w-100" href="{{ route('login') }}">Sign in to continue</a>
                    @endauth
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection