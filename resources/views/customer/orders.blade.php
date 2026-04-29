@extends('layouts.app')
@section('title', 'My Orders')

@push('styles')
<style>
    body { background: #f8f4fc; display: flex; flex-direction: column; min-height: 100vh; }

    /* ─── NAVBAR ─────────────────────────────────────────────────────── */
    .navbar { background: var(--violet-night); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 20px rgba(45,28,66,0.4); }

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
        font-size: 1.65rem; font-weight: 700;
        color: var(--porcelain); text-decoration: none; letter-spacing: 0.14em;
    }

    .navbar-links-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 2rem; height: 46px;
    }
    .navbar-left  { display: flex; align-items: center; gap: 0.5rem; }
    .navbar-right { display: flex; align-items: center; gap: 0.25rem; }

    .user-chip {
        display: flex; align-items: center; gap: 0.5rem;
        color: var(--porcelain); font-size: 0.82rem;
        padding: 0.3rem 0.6rem; border-radius: 8px;
        cursor: pointer; transition: background 0.18s; text-decoration: none;
    }
    .user-chip:hover { background: rgba(233,213,230,0.15); }
    .user-chip .avatar {
        width: 27px; height: 27px; border-radius: 50%;
        background: var(--violet-mid); border: 2px solid var(--porcelain);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.66rem; color: var(--porcelain); text-transform: uppercase;
    }
    .nav-link {
        color: var(--porcelain); text-decoration: none;
        font-size: 0.78rem; font-weight: 500;
        padding: 0.38rem 0.75rem; border-radius: 4px;
        transition: background 0.18s; display: flex; align-items: center; gap: 0.4rem;
        white-space: nowrap; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .nav-link:hover, .nav-link.active-nav { background: rgba(233,213,230,0.15); color: #fff; }
    .cart-badge {
        background: var(--porcelain); color: var(--violet-night);
        font-size: 0.63rem; font-weight: 700; border-radius: 50%;
        width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-logout {
        background: transparent; color: var(--porcelain);
        border: 1.5px solid rgba(233,213,230,0.45); border-radius: 6px;
        padding: 0.32rem 0.8rem; font-family: 'Inter', sans-serif;
        font-size: 0.78rem; font-weight: 500; cursor: pointer;
        transition: all 0.18s; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .btn-logout:hover { background: rgba(233,213,230,0.15); color: #fff; border-color: rgba(233,213,230,0.7); }

    /* ─── PAGE ───────────────────────────────────────────────────────── */
    .page { max-width:1400px; margin:0 auto; padding:2.25rem 2.25rem 2.25rem 2.75rem; flex:1; width:100%; }
    .back-link { display:inline-flex; align-items:center; gap:0.4rem; color:var(--text-mid); font-size:0.875rem; font-weight:500; text-decoration:none; margin-bottom:1.5rem; transition:color 0.2s; }
    .back-link:hover { color:var(--violet-night); }
    .page-heading { font-size: 2rem; color: var(--violet-night); margin-bottom: 0.3rem; }
    .page-sub { font-size: 0.875rem; color: var(--text-light); margin-bottom: 2rem; }

    .orders-list { display: flex; flex-direction: column; gap: 1rem; }
    .order-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(45,28,66,0.07); overflow: hidden; transition: box-shadow 0.2s; }
    .order-card:hover { box-shadow: 0 4px 18px rgba(45,28,66,0.12); }
    .order-card-header { display:flex; align-items:flex-start; justify-content:space-between; padding:0.9rem 1.25rem 0.65rem; gap:0.75rem; }
    .order-num { font-size:1rem; font-weight:700; color:var(--violet-night); text-transform:uppercase; }
    .order-date { font-size:0.78rem; color:var(--text-light); margin-top:0.1rem; }

    .badge { display:inline-flex; align-items:center; font-size:0.95rem; font-weight:700; letter-spacing:0.01em; padding-top:0.15rem; }
    .badge-pending   { color:#d18d13; }
    .badge-accepted  { color:#2f72ff; }
    .badge-shipped   { color:#4a56d6; }
    .badge-delivered { color:#22c55e; }
    .badge-canceled  { color:#d95f5f; }

    .order-card-body { padding:0.55rem 1.25rem 0.75rem; border-top:1px solid var(--porcelain-light); }
    .order-items-preview { font-size:0.8rem; color:var(--text-mid); }
    .order-card-footer { display:flex; align-items:center; justify-content:space-between; padding:0 1.25rem 0.95rem; gap:0.5rem; }
    .order-meta { font-size:0.8rem; color:var(--text-light); }
    .order-total { font-weight:700; color:var(--violet-night); font-size:0.95rem; line-height:1.1; }

    .empty-state { text-align:center; padding:5rem 2rem; color:var(--text-light); }
    .empty-state h3 { font-size:1.5rem; margin-bottom:0.5rem; color:var(--text-mid); }
    .empty-state p { margin-bottom:1.5rem; }
    .btn-shop { display:inline-flex; align-items:center; gap:.5rem; background:var(--violet-night); color:var(--snow); padding:.75rem 1.75rem; border-radius:6px; text-decoration:none; font-weight:600; font-size:.875rem; }
    .btn-shop:hover { background: var(--violet-mid); }

    /* ── MOBILE RESPONSIVE – orders ── */
    @media (max-width: 768px) {
        .page { padding: 1.25rem 1rem !important; }
        .page-heading { font-size: 1.5rem !important; }
        .order-card-header { flex-wrap: wrap; gap: 0.5rem !important; }
        .order-id-block  { flex: 1 1 auto; }
        .order-badge-block { flex: 0 0 auto; }
    }
    @media (max-width: 480px) {
        .page { padding: 1rem 0.75rem !important; }
        .order-items-mini { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<nav class="navbar">
    {{-- Row 1: Logo --}}
    <div class="navbar-logo-row">
        <img src="{{ asset('images/mayari-logo.png') }}"
             alt="Mayari"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <a href="{{ route('customer.home') }}" class="navbar-logo-text" style="display:none;">MAYARI</a>
    </div>

    {{-- Row 2: Profile left, nav links right --}}
    <div class="navbar-links-row">
        <div class="navbar-left">
            <a href="{{ route('customer.profile') }}" class="user-chip">
                <div class="avatar">{{ substr(auth()->user()->firstName, 0, 1) }}{{ substr(auth()->user()->lastName, 0, 1) }}</div>
                <span>{{ auth()->user()->firstName }}</span>
            </a>
        </div>
        <div class="navbar-right">
            <a href="{{ route('customer.orders') }}" class="nav-link active-nav">My Orders</a>
            <a href="{{ route('customer.cart') }}" class="nav-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 001.4 1.4L9 15h8m-5 4a1 1 0 11-2 0 1 1 0 012 0zm5 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                Cart
                @php $cartCount = array_sum(array_column(session()->get('cart',[]),'quantity')); @endphp
                @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Log Out</button>
            </form>
        </div>
    </div>
</nav>

<div class="page">
    <a href="{{ route('customer.home') }}" class="back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Go back to Shop
    </a>
    <h1 class="page-heading">My Orders</h1>
    <p class="page-sub">Track all your past and current orders.</p>

    @if($orders->isEmpty())
        <div class="empty-state">
            <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">📦</div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders. Start shopping!</p>
            <a href="{{ route('customer.home') }}" class="btn-shop">Shop Now</a>
        </div>
    @else
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <div class="order-num">ORDER NO. {{ $order->orderID }}</div>
                        <div class="order-date">{{ \Carbon\Carbon::parse($order->createdAt)->format('F j, Y') }}</div>
                    </div>
                    @php $statusClass = strtolower($order->status); @endphp
                    <span class="badge badge-{{ $statusClass }}">{{ $order->status }}</span>
                </div>
                <div class="order-card-body">
                    <div class="order-items-preview">
                        @foreach($order->items as $i => $item)
                            {{ $item->product->pName ?? 'Product' }} x{{ $item->quantity }}@if(!$loop->last) · @endif
                        @endforeach
                    </div>
                </div>
                <div class="order-card-footer">
                    <div class="order-total">₱{{ number_format($order->total, 2) }}</div>
                    <div class="order-meta">{{ $order->paymentMethod === 'cod' ? 'Cash on Delivery' : 'GCash' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection