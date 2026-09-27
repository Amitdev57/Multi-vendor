<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marketfield') | Marketfield</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-market sticky-top">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark">M</span><span>marketfield</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#marketNav" aria-controls="marketNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="marketNav">
                <form class="d-flex mx-lg-auto my-3 my-lg-0 w-100" style="max-width: 420px" action="{{ route('home') }}" method="GET">
                    <input class="form-control search-field" type="search" name="q" value="{{ request('q') }}" placeholder="Search independent shops" aria-label="Search products">
                    <button class="btn btn-market ms-2 px-3" type="submit">Search</button>
                </form>
                <div class="d-flex align-items-center gap-3 ms-lg-4">
                    <a class="text-decoration-none text-dark" href="{{ route('cart.index') }}">Bag <span class="badge rounded-pill text-bg-success">{{ array_sum(session('cart', [])) }}</span></a>
                    @auth
                        <a class="text-decoration-none text-dark" href="{{ route('dashboard') }}">Account</a>
                        @if (auth()->user()->vendor)
                            <a class="btn btn-sm btn-outline-success" href="{{ route('vendor.dashboard') }}">Your shop</a>
                        @endif
                    @else
                        <a class="text-decoration-none text-dark" href="{{ route('login') }}">Sign in</a>
                        <a class="btn btn-sm btn-market" href="{{ route('register') }}">Join</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if (session('status') || $errors->any())
        <div class="container pt-3">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="status">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-0" role="alert">{{ $errors->first() }}</div>
            @endif
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="container py-5 mt-5 section-rule border-top">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}"><span class="brand-mark">M</span><span>marketfield</span></a>
            <div class="d-flex align-items-center gap-3 small text-secondary">
                <a class="text-reset" href="{{ route('vendor.apply') }}">Sell with us</a>
                <span>Seller currencies accepted; checkout totals are in USD.</span>
            </div>
        </div>
    </footer>
</body>
</html>