@extends('layout')

@section('title', 'Create an account')

@section('content')
<div class="container py-5">
    <section class="form-panel">
        <div class="eyebrow mb-2">A good place to start</div>
        <h1 class="h3 mb-4">Create your account</h1>
        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control search-field" id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control search-field" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="row g-3 mb-4">
                <div class="col-sm-6"><label class="form-label" for="password">Password</label><input class="form-control search-field" id="password" type="password" name="password" autocomplete="new-password" required></div>
                <div class="col-sm-6"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control search-field" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
            </div>
            <button class="btn btn-market w-100 py-2" type="submit">Create account</button>
        </form>
        <p class="small text-secondary text-center mt-4 mb-0">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
</div>
@endsection