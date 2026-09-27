@extends('layout')

@section('title', 'Your account')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div><div class="eyebrow mb-2">Account</div><h1 class="h2 mb-1">Hello, {{ auth()->user()->name }}</h1><p class="text-secondary mb-0">Your orders and shop, together in one place.</p></div>
        <div class="d-flex gap-2">
            @if (auth()->user()->vendor)
                <a class="btn btn-market" href="{{ route('vendor.dashboard') }}">Manage your shop</a>
            @else
                <a class="btn btn-outline-success" href="{{ route('vendor.apply') }}">Open a shop</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-outline-secondary" type="submit">Sign out</button></form>
        </div>
    </div>

    <section class="section-rule border-top pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Your orders</h2><span class="text-secondary small">{{ $orders->count() }} total</span></div>
        @forelse ($orders as $order)
            <article class="bg-white border rounded-3 p-3 p-md-4 mb-3">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <div><span class="eyebrow">{{ $order->order_number }}</span><div class="small text-secondary">{{ $order->created_at->format('M j, Y') }}</div></div>
                    <div class="text-end"><span class="badge text-bg-light border text-capitalize">{{ $order->status }}</span><div class="product-price mt-1">{{ \App\Support\CurrencyFormatter::format($order->total, 'USD') }}</div><div class="small text-secondary">Order total</div></div>
                </div>
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between gap-3 py-2 border-top small"><span>{{ $item->quantity }} × {{ $item->product->name }} <span class="text-secondary">from {{ $item->vendor->business_name }}</span></span><span>{{ \App\Support\CurrencyFormatter::format($item->unit_price * $item->quantity, $item->currency_code) }}</span></div>
                @endforeach
            </article>
        @empty
            <div class="py-5 text-center bg-white border rounded-3"><p class="text-secondary">Your first order is waiting to happen.</p><a class="btn btn-market" href="{{ route('home') }}">Explore the market</a></div>
        @endforelse
    </section>
</div>
@endsection