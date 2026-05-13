{{--
    Login page — converted from the team's existing static HTML.
    Original design (login-box, login-form-image, login-form-box) is preserved;
    only the wrapping, asset paths, form action, and CSRF token were changed
    so it can talk to AuthController@login.
--}}

@extends('layouts.app')

@section('title', 'Login | TechEvents UTAS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_style.css') }}">
@endpush

@section('content')
<div class="page">
    <div class="container">
        <div class="login-box">

            <div class="login-form-image">
                {{-- asset() resolves to /images/login_bg1.jpg from public/ --}}
                <img src="{{ asset('images/login_bg1.jpg') }}" alt="">
            </div>

            <div class="login-form-box">
                <h2>Login</h2>

                {{-- Session timeout banner (Tutorial 5 requirement).
                     Shows only when redirected here with ?expired=1. --}}
                @if (request()->query('expired'))
                    <div class="flash flash--error">Session expired due to inactivity.</div>
                @endif

                {{--
                    Form changes from the original:
                      - method="POST" and action="{{ url('/login') }}"
                      - @csrf token added (required by Laravel)
                      - name="email" / name="password" (must match controller validation keys)
                      - Removed the 'role' field — login doesn't need it; role is
                        already stored against the account from registration.
                --}}
                <form id="login-form" method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="loginEmail">Email</label>
                        <input type="email" id="loginEmail" name="email"
                               placeholder="Email" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="password"
                               placeholder="Password" required>
                        @error('password')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Remember Me checkbox — supports Tutorial 5 requirement.
                         Auth::attempt(..., $request->boolean('remember')) reads this. --}}
                    <div class="form-group form-group--inline">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember">Remember Me</label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                    <p>Don't have an account? <a href="{{ url('/register') }}">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Keep the team's existing client-side validation JS.
         Make sure js/login_script.js uses name="email" / name="password"
         (not the old id-only selectors) when reading values. --}}
    <script src="{{ asset('js/login_script.js') }}"></script>
@endpush