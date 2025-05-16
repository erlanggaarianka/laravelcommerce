@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="text-center">
    <main class="form-signin">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- <img class="mb-4" src="{{ asset('path/to/your/logo.svg') }}" alt="" width="72" height="57"> --}}
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="floatingInput" name="email" value="{{ old('email') }}"
                       placeholder="name@example.com" required autocomplete="email" autofocus>
                <label for="floatingInput">Email address</label>
                @error('email')
                    <div class="invalid-feedback text-start">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="floatingPassword" name="password"
                       placeholder="Password" required autocomplete="current-password">
                <label for="floatingPassword">Password</label>
                @error('password')
                    <div class="invalid-feedback text-start">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Sign in</button>

            {{-- @if (Route::has('password.request'))
                <a class="text-decoration-none" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif --}}

            <p class="mt-5 mb-3 text-muted">© {{ date('Y') }} {{ config('app.name', 'GOOD CREDIT') }}</p>
        </form>
    </main>
</div>

<style>
    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: auto;
    }

    .form-signin .form-floating:focus-within {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
</style>
@endsection
