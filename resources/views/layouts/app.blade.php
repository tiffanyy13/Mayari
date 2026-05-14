<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mayari – @yield('title', 'Online Makeup Shop')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --violet-night: #2D1C42;
            --violet-dark:  #1e1230;
            --violet-mid:   #3d2758;
            --porcelain:    #E9D5E6;
            --porcelain-light: #f5edf4;
            --snow:         #FFF9FF;
            --text-dark:    #1a0f28;
            --text-mid:     #4a3560;
            --text-light:   #8b7aa0;
            --success:      #4caf87;
            --warning:      #e8a040;
            --danger:       #d95f5f;
            --info:         #5f8fd9;
            --control-height: 2.75rem;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--snow);
            color: var(--text-dark);
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: clip;
        }
        h1, h2, h3, h4, h5 {
            font-family: 'Inter', sans-serif;
            color: var(--violet-night);
        }

        .navbar {
            background: var(--violet-night);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(45,28,66,0.4);
        }

        .navbar-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 2rem 0.0rem;
        }
        .navbar-logo-row img {
            height: 63px;
            width: auto;
            object-fit: contain;
        }
        .navbar-logo-text {
            font-family: 'Inter', sans-serif;
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--porcelain);
            text-decoration: none;
            letter-spacing: 0.14em;
        }

        .navbar-links-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 48px;
        }
        .navbar-left  { display: flex; align-items: center; gap: 0.5rem; }
        .navbar-right { display: flex; align-items: center; gap: 0.25rem; }

        .user-chip {
            display: flex; align-items: center; gap: 0.5rem;
            color: var(--porcelain); font-size: 0.85rem;
            padding: 0.35rem 0.65rem; border-radius: 8px;
            cursor: pointer; transition: background 0.18s; text-decoration: none;
        }
        .user-chip:hover { background: rgba(233,213,230,0.15); }
        .user-chip .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--violet-mid); border: 2px solid var(--porcelain);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.68rem; color: var(--porcelain);
            text-transform: uppercase;
        }

        .nav-link {
            color: var(--porcelain); text-decoration: none;
            font-size: 0.78rem; font-weight: 500;
            padding: 0.4rem 0.8rem; border-radius: 4px;
            transition: background 0.18s;
            display: flex; align-items: center; gap: 0.4rem;
            white-space: nowrap; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .nav-link:hover { background: rgba(233,213,230,0.15); color: #fff; }

        .cart-badge {
            background: var(--porcelain); color: var(--violet-night);
            font-size: 0.65rem; font-weight: 700; border-radius: 50%;
            width: 17px; height: 17px;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-logout {
            background: transparent; color: var(--porcelain);
            border: 1.5px solid rgba(233,213,230,0.5); border-radius: 6px;
            padding: 0.35rem 0.85rem; font-family: 'Inter', sans-serif;
            font-size: 0.78rem; font-weight: 500; cursor: pointer;
            transition: all 0.18s; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .btn-logout:hover { background: rgba(233,213,230,0.15); color: #fff; }
        a.btn-logout {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            line-height: 1.2;
            box-sizing: border-box;
        }

        /* inline alerts (forms) */
        .alert {
            padding: 0.85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem;
            font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .alert-success { background: #e8f7ef; color: #1f6e4d; border: 1px solid #9fd9bc; }
        .alert-danger  { background: #fdecec; color: #9f2f2f; border: 1px solid #efb0b0; }
        .alert-warning { background: #fff4df; color: #8a5510; border: 1px solid #f0cf95; }
        .alert-info    { background: #eaf1ff; color: #2b4f99; border: 1px solid #b7c9ef; }

        /* flash toasts — frosted glass, bottom-right */
        .flash-stack {
            position: fixed;
            bottom: 1.25rem;
            right: 1.25rem;
            left: auto;
            top: auto;
            transform: none;
            z-index: 3000;
            width: min(380px, calc(100vw - 2rem));
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            pointer-events: none;
        }
        .flash-stack .flash-toast { pointer-events: auto; }
        .flash-toast {
            margin: 0;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.65rem;
            font-weight: 600;
            color: var(--text-dark);
            background: rgba(255, 249, 255, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(45, 28, 66, 0.12);
            box-shadow:
                0 12px 36px rgba(45, 28, 66, 0.14),
                inset 0 1px 0 rgba(255, 255, 255, 0.65);
            animation: toastIn 0.28s ease;
        }
        .flash-toast.alert-success {
            border-left: 4px solid var(--success);
            color: var(--text-dark);
        }
        .flash-toast.alert-danger {
            border-left: 4px solid var(--danger);
            color: var(--text-dark);
        }
        .flash-toast.alert-warning {
            border-left: 4px solid var(--warning);
            color: var(--text-dark);
        }
        .flash-toast.alert-info {
            border-left: 4px solid var(--info);
            color: var(--text-dark);
        }
        .flash-toast .flash-text { flex: 1; line-height: 1.45; }
        .flash-close {
            flex-shrink: 0;
            background: rgba(45, 28, 66, 0.06);
            border: none;
            border-radius: 8px;
            color: var(--text-mid);
            cursor: pointer;
            font-size: 1.05rem;
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
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(14px) translateY(8px); }
            to { opacity: 1; transform: translateX(0) translateY(0); }
        }

        /*buttons*/
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
            padding: 0.6rem 1.4rem; border-radius: 8px;
            font-family: 'Inter', sans-serif; font-size: 0.875rem; font-weight: 500;
            cursor: pointer; border: none; transition: all 0.2s; text-decoration: none;
        }
        .btn-primary   { background: var(--violet-night); color: var(--snow); }
        .btn-primary:hover { background: var(--violet-mid); color: #fff; }
        .btn-secondary { background: var(--porcelain); color: var(--violet-night); }
        .btn-secondary:hover { background: #dfc8db; }
        .btn-outline   { background: transparent; color: var(--violet-night); border: 1.5px solid var(--violet-night); }
        .btn-outline:hover { background: var(--violet-night); color: #fff; }
        .btn-danger    { background: rgba(217,95,95,0.1); color: var(--danger); border: 1px solid rgba(217,95,95,0.3); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm  { padding: 0.4rem 0.9rem; font-size: 0.8rem; }
        .modal-footer .btn.btn-sm {
            min-height: var(--control-height);
            padding-top: 0;
            padding-bottom: 0;
            font-size: 0.875rem;
        }
        .btn-lg  { padding: 0.85rem 2rem; font-size: 1rem; }
        .btn-full { width: 100%; }

        /*form*/
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block; font-size: 0.8rem; font-weight: 600;
            letter-spacing: 0.04em; text-transform: uppercase;
            color: var(--text-mid); margin-bottom: 0.4rem;
        }
        .form-control {
            width: 100%; padding: 0.65rem 0.9rem;
            border: 1.5px solid #d8cce0; border-radius: 8px;
            font-family: 'Inter', sans-serif; font-size: 0.9rem;
            color: var(--text-dark); background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none;
        }
        input.form-control:not([type="hidden"]):not([type="file"]),
        select.form-control {
            min-height: var(--control-height);
        }
        textarea.form-control { min-height: auto; }
        .form-control:focus { border-color: var(--violet-mid); box-shadow: 0 0 0 3px rgba(45,28,66,0.08); }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 0.78rem; margin-top: 0.25rem; display: block; }

        /* STATUS BADGES */
        .badge {
            display: inline-flex; align-items: center;
            padding: 0.25rem 0.7rem; border-radius: 20px;
            font-size: 0.75rem; font-weight: 600; letter-spacing: 0.03em;
        }
        .badge-pending   { background: rgba(232,160,64,0.15);  color: #8a5510; }
        .badge-accepted  { background: rgba(95,143,217,0.15);  color: #2850a0; }
        .badge-shipped   { background: rgba(95,143,217,0.2);   color: #1e3d80; }
        .badge-delivered { background: rgba(76,175,135,0.15);  color: #1e6048; }
        .badge-canceled  { background: rgba(217,95,95,0.15);   color: #8a2020; }

        /*card*/
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(45,28,66,0.08); overflow: hidden; }
        .card-body { padding: 1.5rem; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        .page-content { padding: 2rem 0; }

        /*modal*/
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(45,28,66,0.55); backdrop-filter: blur(3px);
            z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem;
        }
        .modal {
            background: #fff; border-radius: 14px;
            box-shadow: 0 20px 60px rgba(45,28,66,0.25);
            width: 100%; max-width: 460px; position: relative;
            animation: modalIn 0.25s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--porcelain-light);
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-header h3 { font-size: 1.25rem; }
        .modal-close {
            background: none; border: none; cursor: pointer;
            color: var(--text-light); font-size: 1.25rem; line-height: 1;
            padding: 0.25rem; border-radius: 4px; transition: color 0.2s, background 0.2s;
        }
        .modal-close:hover { color: var(--text-dark); background: var(--porcelain-light); }
        .modal-body   { padding: 1.5rem; }
        .modal-footer {
            padding: 1rem 1.5rem; border-top: 1px solid var(--porcelain-light);
            display: flex; gap: 0.75rem; justify-content: flex-end;
            align-items: stretch; flex-wrap: wrap;
        }
        .modal-footer .btn {
            min-height: var(--control-height);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .modal-footer form { display: inline-flex; align-items: stretch; }
        .modal-footer form .btn { align-self: stretch; }

        .modal-inline-controls {
            display: flex;
            gap: 0.75rem;
            align-items: stretch;
            flex-wrap: wrap;
            width: 100%;
        }
        .modal-inline-controls > .form-control {
            flex: 1;
            min-width: 0;
            min-height: var(--control-height);
        }
        .modal-inline-controls > .btn {
            flex-shrink: 0;
            min-height: var(--control-height);
            align-self: stretch;
        }

        .site-footer {
            background: var(--violet-night); color: var(--porcelain);
            text-align: center; padding: 1.25rem; font-size: 0.8rem; margin-top: auto;
        }
        .site-footer a { color: var(--porcelain); }

        .divider { border: none; border-top: 1px solid var(--porcelain-light); margin: 1.5rem 0; }
        .section-heading { font-size: 1.8rem; font-weight: 600; color: var(--violet-night); margin-bottom: 0.3rem; }
        .section-subheading { font-size: 0.9rem; color: var(--text-light); margin-bottom: 1.5rem; }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mid); font-size: 0.875rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.5rem; transition: color 0.2s;
        }
        .back-link:hover { color: var(--violet-night); }
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); }
        .empty-state svg { margin-bottom: 1rem; opacity: 0.4; }
        .empty-state h3 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-mid); }
        .empty-state p  { font-size: 0.9rem; margin-bottom: 1.5rem; }
    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/customer-responsive.css') }}">
</head>
<body>
@php
    $flashMessages = [
        ['key' => 'success', 'class' => 'alert-success'],
        ['key' => 'error',   'class' => 'alert-danger'],
        ['key' => 'warning', 'class' => 'alert-warning'],
        ['key' => 'info',    'class' => 'alert-info'],
        ['key' => 'status',  'class' => 'alert-info'],
    ];
@endphp
<div class="flash-stack" id="flashStack">
    @foreach($flashMessages as $flash)
        @if(session($flash['key']))
            <div class="alert flash-toast {{ $flash['class'] }}">
                <span class="flash-text">{{ session($flash['key']) }}</span>
                <button type="button" class="flash-close" aria-label="Dismiss message">×</button>
            </div>
        @endif
    @endforeach
</div>
@yield('content')
<footer class="site-footer">
    <p>&copy; {{ date('Y') }} Mayari &mdash; <em>Gorgeous eve.</em></p>
</footer>
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function dismissToast(el) {
        el.classList.add('hide');
        setTimeout(function () {
            el.remove();
        }, 280);
    }

    document.querySelectorAll('#flashStack .flash-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const toast = btn.closest('.flash-toast');
            if (toast) dismissToast(toast);
        });
    });

    document.querySelectorAll('#flashStack .flash-toast').forEach(function (el) {
        setTimeout(function () {
            dismissToast(el);
        }, 10000);
    });
});
</script>
</body>
</html>