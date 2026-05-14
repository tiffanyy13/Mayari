@extends('layouts.app')
@section('title', 'Order Placed!')

@push('styles')
<style>
    body { background: #f8f4fc; display: flex; flex-direction: column; min-height: 100vh; }

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
    .nav-link:hover { background: rgba(233,213,230,0.15); color: #fff; }
    .btn-logout {
        background: transparent; color: var(--porcelain);
        border: 1.5px solid rgba(233,213,230,0.45); border-radius: 6px;
        padding: 0.32rem 0.8rem; font-family: 'Inter', sans-serif;
        font-size: 0.78rem; font-weight: 500; cursor: pointer;
        transition: all 0.18s; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .btn-logout:hover { background: rgba(233,213,230,0.15); color: #fff; border-color: rgba(233,213,230,0.7); }

    .success-page { flex:1; display:flex; align-items:center; justify-content:center; padding:3rem 1.5rem; }
    .success-card { background:#fff; border-radius:14px; box-shadow:0 8px 40px rgba(45,28,66,0.12); padding:3.5rem 3rem; text-align:center; max-width:500px; width:100%; animation:fadeUp 0.4s ease; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .success-icon { width:90px; height:90px; border-radius:50%; background:linear-gradient(135deg,rgba(76,175,135,0.15),rgba(76,175,135,0.08)); border:3px solid rgba(76,175,135,0.3); display:flex; align-items:center; justify-content:center; margin:0 auto 1.75rem; font-size:2.5rem; }
    .success-card h1 { font-size:2.25rem; color:var(--violet-night); margin-bottom:0.75rem; }
    .success-card p { font-size:0.925rem; color:var(--text-light); line-height:1.7; margin-bottom:2rem; }
    .success-card p strong { color:var(--text-mid); }
    .divider-dots { display:flex; justify-content:center; gap:0.5rem; margin:1.5rem 0; }
    .divider-dots span { width:6px; height:6px; border-radius:50%; background:var(--porcelain); }
    .success-actions { display:flex; gap:0.85rem; justify-content:center; flex-wrap:wrap; }
    .btn-shop { display:inline-flex; align-items:center; gap:0.45rem; background:var(--violet-night); color:var(--snow); padding:0.75rem 1.75rem; border-radius:8px; font-family:'Inter',sans-serif; font-size:0.9rem; font-weight:600; text-decoration:none; transition:background 0.2s; }
    .btn-shop:hover { background:var(--violet-mid); }
    .btn-orders { display:inline-flex; align-items:center; gap:0.45rem; background:transparent; color:var(--violet-night); padding:0.75rem 1.75rem; border-radius:8px; border:2px solid var(--violet-night); font-family:'Inter',sans-serif; font-size:0.9rem; font-weight:600; text-decoration:none; transition:all 0.2s; }
    .btn-orders:hover { background:var(--violet-night); color:var(--snow); }

    /*order placed w mobile responsive*/
    @media (max-width: 768px) {
        .page { padding: 1.25rem 1rem !important; }
        .order-confirm-card { padding: 1.5rem 1rem !important; }
    }
    @media (max-width: 480px) {
        .page { padding: 1rem 0.75rem !important; }
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
            <a href="{{ route('customer.orders') }}" class="nav-link">My Orders</a>
            <a href="{{ route('logout') }}" class="btn-logout">Log Out</a>
        </div>
    </div>
</nav>

<div class="success-page">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1>Order Placed!</h1>
        <p>
            Your order has been <strong>successfully submitted</strong> and is now
            pending review. We'll process it shortly!<br><br>
            You can track the status of your order anytime under <strong>My Orders</strong>.
        </p>
        <div class="divider-dots"><span></span><span></span><span></span></div>
        <div class="success-actions">
            <a href="{{ route('customer.home') }}" class="btn-shop">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 001.4 1.4L9 15h8"/></svg>
                Back to Shop
            </a>
            <a href="{{ route('customer.orders') }}" class="btn-orders">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                My Orders
            </a>
        </div>
    </div>
</div>

@if(!empty($promptSaveAddress) && !empty($postOrderAddress))
    <div class="modal-backdrop" id="saveAddressModal" style="display:none;" onclick="if(event.target===this)closeSaveAddressModal()">
        <div class="modal" style="max-width: 520px;">
            <div class="modal-header">
                <h3>Save this address for next time?</h3>
                <button type="button" class="modal-close" onclick="closeSaveAddressModal()">✕</button>
            </div>
            <div class="modal-body" style="color: var(--text-mid); line-height: 1.6;">
                <p style="margin-bottom: 0.75rem;">
                    Would you like to save this shipping address to your account for faster checkout next time?
                </p>
                <div style="background: rgba(45,28,66,0.04); border: 1px solid rgba(216,204,224,0.6); border-radius: 12px; padding: 0.85rem 1rem;">
                    <div style="font-weight:800; color: var(--violet-night); margin-bottom: 0.25rem;">Shipping Address</div>
                    <div style="font-size: 0.88rem; color: var(--text-mid); line-height: 1.55;">
                        {{ $postOrderAddress['fullName'] ?? '' }} • {{ $postOrderAddress['phone'] ?? '' }}<br>
                        {{ $postOrderAddress['addressLine'] ?? '' }}, {{ $postOrderAddress['city'] ?? '' }}, {{ $postOrderAddress['province'] ?? '' }}
                        @if(!empty($postOrderAddress['postal'])) ({{ $postOrderAddress['postal'] }}) @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeSaveAddressModal()">No thanks</button>
                <form action="{{ route('customer.addresses.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="redirect" value="/shop">
                    <input type="hidden" name="makeDefault" value="1">
                    <input type="hidden" name="label" value="Home">

                    <input type="hidden" name="fullName" value="{{ $postOrderAddress['fullName'] ?? '' }}">
                    <input type="hidden" name="phone" value="{{ $postOrderAddress['phone'] ?? '' }}">
                    <input type="hidden" name="addressLine" value="{{ $postOrderAddress['addressLine'] ?? '' }}">
                    <input type="hidden" name="city" value="{{ $postOrderAddress['city'] ?? '' }}">
                    <input type="hidden" name="province" value="{{ $postOrderAddress['province'] ?? '' }}">
                    <input type="hidden" name="country" value="{{ $postOrderAddress['country'] ?? 'Philippines' }}">
                    <input type="hidden" name="postal" value="{{ $postOrderAddress['postal'] ?? '' }}">
                    <input type="hidden" name="landmark" value="">

                    <button type="submit" class="btn btn-primary">Yes, save it</button>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
function closeSaveAddressModal() {
    const el = document.getElementById('saveAddressModal');
    if (el) el.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('saveAddressModal');
    if (!el) return;
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
});
</script>
@endpush