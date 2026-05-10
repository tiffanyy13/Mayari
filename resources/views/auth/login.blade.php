@extends('layouts.app')
@section('title', 'Log In')

@push('styles')
<style>
    body { background: var(--violet-night); display: flex; align-items: stretch; min-height: 100vh; }
    .site-footer { display: none; }

    .auth-wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
    }

    .auth-left {
        flex: 1;
        background: url('/images/auth-bg.jpg') center center / cover no-repeat;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 2.5rem 2.5rem 6rem;
        position: relative;
        overflow: hidden;
    }
    .auth-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(20, 10, 35, 0.45);
        pointer-events: none;
    }
    .auth-quote {
        position: absolute;      
        top: 45%;        
        left: 50%;        
        transform: translate(-50%, -50%);
        color: rgba(233, 213, 230, 0.75);
        font-style: italic;
        font-size: 1rem;
        text-align: center;
        letter-spacing: 0.4em;
        margin-bottom: 2rem;
        width: 650px;
        max-width: 65%;
        line-height: 1.7;
        filter: blur(1px);
        text-shadow: 0 0 8px rgba(233, 213, 230, 0.6);
    }
    .auth-brand-bottom {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 1.25rem;
        text-align: center;
        background: var(--violet-night);
        width: auto;
        padding: 0.3rem 2.5rem;
        margin: 0;
    }
    .auth-brand-bottom img {
        display: block;
        height: 70px;
        width: auto;
        margin: 0 auto;
        object-fit: contain;
    }

    .auth-right {
        width: 460px;
        background: #e8daea;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 2.75rem;
        overflow-y: auto;
    }
    .auth-right h2 {
        font-size: 1.6rem;
        font-weight: 700;
        text-align: center;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-dark);
        margin-bottom: 0;
    }

    .auth-divider {
        border: none;
        border-top: 1.5px solid #c4b0d0;
        margin: 1.25rem 0 1.75rem 0;
    }

    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display:block; font-size:0.78rem; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:var(--text-dark); margin-bottom:0.4rem; }
    .form-control { width:100%; padding:0.7rem 1rem; border:1.5px solid #d8cce0; border-radius:6px; font-family:'Inter',sans-serif; font-size:0.9rem; color:var(--text-dark); background:#fff; outline:none; transition:border-color 0.2s, box-shadow 0.2s; box-sizing:border-box; }
    .form-control:focus { border-color:var(--violet-mid); box-shadow:0 0 0 3px rgba(45,28,66,0.08); }
    .form-control.is-invalid { border-color:#d95f5f; }
    .invalid-feedback { color:#d95f5f; font-size:0.78rem; margin-top:0.25rem; display:block; }

    .password-wrapper { position: relative; }
    .password-wrapper .form-control { padding-right: 2.75rem; }
    .btn-eye {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: #9b8aaa;
        display: flex;
        align-items: center;
    }
    .btn-eye:hover { color: var(--violet-night); }

    .btn-login {
        width: 100%;
        padding: 0.8rem;
        background: var(--violet-night);
        color: var(--snow);
        border: none;
        border-radius: 6px;
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
        margin-top: 0.5rem;
        letter-spacing: 0.02em;
    }
    .btn-login:hover { background: var(--violet-mid); transform: translateY(-1px); }

    .auth-alt-link {
        text-align: center;
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 1.25rem;
    }
    .auth-alt-link a {
        color: var(--violet-night);
        font-weight: 700;
        text-decoration: none;
    }
    .auth-alt-link a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .auth-left { display: none; }
        .auth-right { width: 100%; padding: 2.5rem 1.75rem; }
    }

    /*auth w mobile responsive*/
    @media (max-width: 768px) {
        .auth-left  { display: none !important; }
        .auth-right { width: 100% !important; padding: 2.5rem 1.75rem !important; }
    }
    @media (max-width: 480px) {
        .auth-right { padding: 2rem 1.25rem !important; }
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-left">
        <p class="auth-quote">"Beauty blooms in the quiet spaces between light and shadow."</p>
        <div class="auth-brand-bottom">
            <img src="{{ asset('images/mayari-logo.png') }}" alt="Mayari Logo">
        </div>
    </div>

    <div class="auth-right">
        <h2>Log In</h2>
        <hr class="auth-divider">

        <form action="{{ route('login') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••" required>
                    <button type="button" class="btn-eye" data-field="password" data-icon="eyeIcon-password">
                        <svg id="eyeIcon-password" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-login">Log In</button>
        </form>

        <p class="auth-alt-link">
            Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-eye').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const fieldId = this.dataset.field;
                const iconId = this.dataset.icon;
                const input = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);
                const isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';

                icon.innerHTML = isHidden
                    ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                       <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                       <line x1="1" y1="1" x2="23" y2="23"/>`
                    : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                       <circle cx="12" cy="12" r="3"/>`;
            });
        });
    });
</script>
@endsection