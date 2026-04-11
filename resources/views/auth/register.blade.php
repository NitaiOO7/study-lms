@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 100px); display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <div class="card animate-in" style="max-width: 500px; width: 100%; padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">EduVerse</div>
            <p style="color: var(--text-muted); margin-top: 10px;">Create your account and unlock your true potential.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger" />
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <!-- Password -->
            <div class="grid-2">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
                </div>
            </div>

            <!-- Role Selection -->
            <div class="form-group">
                <label for="role" class="form-label">Account Type</label>
                <div style="position: relative;">
                    <select id="role" name="role" class="form-select" required style="width: 100%; border: 1px solid var(--dark-border); background: var(--dark-surface); color: var(--text-primary);">
                        <option value="student">Student Account (Learn)</option>
                        <option value="teacher">Teacher Account (Instruct)</option>
                    </select>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;"><i class="fas fa-info-circle"></i> Teachers can create channels and upload courses.</p>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                    Register Now
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 20px; font-size: 0.95rem; color: var(--text-muted);">
                Already registered? <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Log in here</a>
            </div>
        </form>
    </div>
</div>
@endsection
