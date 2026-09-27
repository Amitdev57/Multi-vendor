@extends('layout')

@section('title', 'Order placed')

@section('content')
<div class="container py-5">
    <section class="form-panel text-center">
        <div class="eyebrow mb-2">Order placed</div>
        <h1 class="h3">Thank you, {{ auth()->user()->name }}</h1>
        <p class="text-secondary">Your order <strong>{{ $order->order_number }}</strong> has been sent to the shops.</p>
        <div class="text-start border-top border-bottom py-3 my-4">
            @foreach ($order->items as $item)
                <div class="d-flex justify-content-between gap-3 py-2"><span>{{ $item->quantity }} × {{ $item->product->name }}</span><span>{{ \App\Support\CurrencyFormatter::format($item->unit_price * $item->quantity, $item->currency_code) }}</span></div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between mb-4"><span>Total (USD)</span><strong class="product-price">{{ \App\Support\CurrencyFormatter::format($order->total, 'USD') }}</strong></div>
        <a class="btn btn-market" href="{{ route('dashboard') }}">View your orders</a>
    </section>
</div>
@endsection