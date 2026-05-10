<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mayari Admin – @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/mayari-style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .flash-stack {
            position: fixed;
            bottom: 1.25rem;
            right: 1.25rem;
            left: auto;
            top: auto;
            transform: none;
            z-index: 1200;
            width: min(380px, calc(100vw - 2rem));
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            pointer-events: none;
        }
        .flash-stack .flash-toast { pointer-events: auto; }
        .flash-toast {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.65rem;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
            background: rgba(255, 249, 255, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(45, 28, 66, 0.12);
            box-shadow:
                0 12px 36px rgba(45, 28, 66, 0.14),
                inset 0 1px 0 rgba(255, 255, 255, 0.65);
            animation: adminToastIn 0.28s ease;
        }
        .flash-toast span:first-child { flex: 1; line-height: 1.45; }
        .flash-success { border-left: 4px solid var(--success); }
        .flash-error { border-left: 4px solid var(--danger); }
        .flash-warning { border-left: 4px solid var(--warning); }
        .flash-info { border-left: 4px solid var(--info); }
        .flash-close {
            flex-shrink: 0;
            border: none;
            border-radius: 8px;
            background: rgba(45, 28, 66, 0.06);
            color: var(--text-mid);
            font-size: 1.05rem;
            cursor: pointer;
            line-height: 1;
            padding: 0.2rem 0.45rem;
            margin-top: -0.1rem;
            opacity: 0.85;
            transition: background 0.18s, color 0.18s;
        }
        .flash-close:hover {
            opacity: 1;
            background: rgba(45, 28, 66, 0.12);
            color: var(--violet-night);
        }
        .flash-toast.hide {
            opacity: 0;
            transform: translateX(12px) translateY(6px);
            transition: opacity 0.28s ease, transform 0.28s ease;
        }
        @keyframes adminToastIn {
            from { opacity: 0; transform: translateX(14px) translateY(8px); }
            to { opacity: 1; transform: translateX(0) translateY(0); }
        }
        .admin-mobile-topbar { display: none; }

        @media (max-width: 768px) {
            .admin-mobile-topbar { display: flex; }
            .flash-stack { bottom: 1rem; right: 0.75rem; width: min(360px, calc(100vw - 1.25rem)); }
        }
    </style>
    @stack('styles')
</head>
<body class="admin-body">
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

    {{--mobile responsive--}}
    <div class="admin-mobile-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Open menu">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <img src="{{ asset('images/mayari-logo.png') }}" alt="Mayari" style="height:36px;width:auto;object-fit:contain;"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
            <span style="display:none;font-size:1.1rem;font-weight:700;color:var(--porcelain);letter-spacing:0.08em;">MAYARI</span>
        </div>
        <div style="width:38px;"></div>{{-- spacer to center logo --}}
    </div>

    {{--sidebar--}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-wrapper">
        <aside class="sidebar" id="adminSidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('images/mayari-logo.png') }}" alt="Mayari Logo" class="sidebar-logo-img" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span class="sidebar-logo-text" style="display:none;">MAYARI</span>
            </div>
            <div class="sidebar-role">Admin / Shop Owner</div>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Orders
                </a>
                <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products','admin.archived') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Products
                </a>
                <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Customers
                </a>
                <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports','admin.reports.pdf') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Reports
                </a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-logout">
                @csrf
                <button type="submit" class="btn-sidebar-logout">Log Out</button>
            </form>
        </aside>
        <div class="admin-main">
            @yield('content')
        </div>
    </div>
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Flash toasts
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

        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (toggle) toggle.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });
    });
    </script>
</body>
</html>
