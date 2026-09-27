@extends('layout')

@section('title', 'Sign in')

@section('content')
<div class="container py-5">
    <section class="form-panel">
        <div class="eyebrow mb-2">Welcome back</div>
        <h1 class="h3 mb-4">Sign in to Marketfield</h1>
        <form action="{{ route('login.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control search-field" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control search-field" id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <button class="btn btn-market w-100 py-2" type="submit">Sign in</button>
        </form>
        <p class="small text-secondary text-center mt-4 mb-0">New to the market? <a href="{{ route('register') }}">Create an account</a></p>
    </section>
</div>
@endsection