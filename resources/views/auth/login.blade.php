@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 100px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="card animate-in" style="max-width: 450px; width: 100%; padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">EduVerse</div>
            <p style="color: var(--text-muted); margin-top: 10px;">Welcome back!! Please login to your account.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <!-- Remember Me -->
            <div class="checkbox-wrap mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>
                
                @if (Route::has('password.request'))
                    <a style="color: var(--primary); text-decoration: none; font-size: 0.9rem;" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                    Log in
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 20px; font-size: 0.95rem; color: var(--text-muted);">
                Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Register here</a>
            </div>
        </form>
    </div>
</div>
@endsection
