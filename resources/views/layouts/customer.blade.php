<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mayari – @yield('title', 'Shop')</title>
    <link rel="stylesheet" href="{{ asset('css/mayari-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer-responsive.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .flash-stack {
            position: fixed;
            top: 74px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1200;
            width: min(640px, calc(100% - 2rem));
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .flash-toast {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            border-radius: 10px;
            padding: 0.85rem 1.1rem;
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 1;
            border: 1px solid transparent;
        }
        .flash-success { background: #e8f7ef; color: #1f6e4d; border-color: #9fd9bc; }
        .flash-error   { background: #fdecec; color: #9f2f2f; border-color: #efb0b0; }
        .flash-warning { background: #fff4df; color: #8a5510; border-color: #f0cf95; }
        .flash-info    { background: #eaf1ff; color: #2b4f99; border-color: #b7c9ef; }
        .flash-close {
            border: none; background: transparent; color: inherit;
            font-size: 1rem; cursor: pointer; opacity: 0.8; line-height: 1;
        }
        .flash-close:hover { opacity: 1; }
        .flash-toast.hide { opacity: 0; transform: translateY(-8px); transition: opacity 0.25s ease, transform 0.25s ease; }
        .nav-mobile-toggle { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="customer-body">
    @php
        $flashMessages = [
            ['key' => 'success', 'class' => 'flash-success'],
            ['key' => 'error',   'class' => 'flash-error'],
            ['key' => 'warning', 'class' => 'flash-warning'],
            ['key' => 'info',    'class' => 'flash-info'],
            ['key' => 'status',  'class' => 'flash-info'],
        ];
    @endphp
    <div class="flash-stack" id="flashStack">
        @foreach($flashMessages as $flash)
            @if(session($flash['key']))
                <div class="flash-toast {{ $flash['class'] }}">
                    <span>{{ session($flash['key']) }}</span>
                    <button type="button" class="flash-close" aria-label="Dismiss message">×</button>
                </div>
            @endif
        @endforeach
    </div>

    <nav class="navbar">
        <div class="navbar-logo-row">
            <a href="{{ route('customer.home') }}" class="nav-logo">
                <img src="{{ asset('images/mayari-logo.png') }}" alt="Mayari" class="nav-logo-img"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span class="nav-logo-text" style="display:none;">MAYARI</span>
            </a>
        </div>

        <div class="navbar-links-row" id="navLinksRow">
            <div class="nav-left">
                <a href="{{ route('customer.profile') }}" class="nav-profile" style="text-decoration:none;">
                    <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    <span>{{ Auth::user()->firstName }}</span>
                </a>
            </div>
            <div class="nav-right">
                <a href="{{ route('customer.orders') }}" class="nav-link {{ request()->routeIs('customer.orders') ? 'active' : '' }}">MY ORDERS</a>
                <a href="{{ route('customer.cart') }}" class="nav-link cart-link {{ request()->routeIs('customer.cart') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    CART
                    @php $cartCount = array_sum(array_column(session()->get('cart',[]),'quantity')); @endphp
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-logout">Log Out</button>
                </form>
            </div>
        </div>
    </nav>

    <main>@yield('content')</main>
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function dismissToast(el) {
            el.classList.add('hide');
            setTimeout(function () { el.remove(); }, 280);
        }
        document.querySelectorAll('#flashStack .flash-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const toast = btn.closest('.flash-toast');
                if (toast) dismissToast(toast);
            });
        });
        document.querySelectorAll('#flashStack .flash-toast').forEach(function (el) {
            setTimeout(function () { dismissToast(el); }, 10000);
        });
    });
    </script>
</body>
</html>
