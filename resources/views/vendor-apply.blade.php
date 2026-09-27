@extends('layout')

@section('title', 'Open a shop')

@section('content')
<div class="container py-5">
    <section class="form-panel">
        <div class="eyebrow mb-2">Sell on Marketfield</div>
        <h1 class="h3 mb-3">Open your shop</h1>
        <p class="text-secondary mb-4">Set up your seller profile. You can add products and manage orders from your shop dashboard.</p>
        @if (auth()->user()->vendor)
            <a class="btn btn-market" href="{{ route('vendor.dashboard') }}">Go to your shop</a>
        @else
            <form action="{{ route('vendor.apply.store') }}" method="POST">
                @csrf
                <div class="mb-3"><label class="form-label" for="business_name">Shop name</label><input class="form-control search-field" id="business_name" name="business_name" value="{{ old('business_name') }}" maxlength="120" required>@error('business_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="description">About your shop</label><textarea class="form-control search-field" id="description" name="description" rows="4" maxlength="2000">{{ old('description') }}</textarea></div>
                <button class="btn btn-market w-100" type="submit">Create shop</button>
            </form>
        @endif
    </section>
</div>
@endsection