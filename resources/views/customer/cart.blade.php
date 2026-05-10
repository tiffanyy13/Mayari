@extends('layouts.app')
@section('title', 'Cart')

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

    .page { max-width:1400px; margin:0 auto; padding:2.25rem 2.25rem 2.25rem 2.75rem; flex:1; width:100%; }
    .back-link { display:inline-flex; align-items:center; gap:0.4rem; color:var(--text-mid); font-size:0.875rem; font-weight:500; text-decoration:none; margin-bottom:1.5rem; transition:color 0.2s; }
    .back-link:hover { color:var(--violet-night); }
    .page-heading { font-size:2rem; color:var(--violet-night); margin-bottom:1.75rem; }

    .cart-layout { display:grid; grid-template-columns:minmax(0, 1fr) 370px; gap:1.5rem; align-items:start; }

    .cart-panel { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(45,28,66,0.07); margin-bottom:1.25rem; overflow:hidden; }
    .cart-panel-header { padding:1rem 1.5rem; border-bottom:1px solid var(--porcelain-light); }
    .cart-panel-header h3 { font-size:1rem; font-weight:600; color:var(--violet-night); }

    /*cart item*/
    .cart-item { display:grid; grid-template-columns:72px minmax(0, 1fr) auto; gap:1rem; padding:0.95rem 1.25rem; border-bottom:1px solid #faf5fb; align-items:center; }
    .cart-item:last-child { border-bottom:none; }
    .cart-item-img { width:72px; height:72px; border-radius:8px; background:var(--porcelain-light); overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .cart-item-img img { width:100%; height:100%; object-fit:cover; }
    .cart-item-img-ph { font-size:1.6rem; opacity:0.4; }
    .cart-item-info { display:flex; flex-direction:column; gap:0.2rem; min-width:0; }
    .cart-item-name { font-weight:600; font-size:0.95rem; color:var(--violet-night); }
    .cart-item-desc { font-size:0.77rem; color:var(--text-light); }
    .item-controls-row { display:flex; align-items:center; gap:0.55rem; margin-top:0.45rem; }
    .qty-controls { display:flex; align-items:center; gap:0.3rem; margin-top:0; }
    .qty-btn { width:26px; height:26px; border-radius:5px; border:1.5px solid #d8cce0; background:#fff; color:var(--text-mid); font-size:1rem; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.18s; }
    .qty-btn:hover { border-color:var(--violet-mid); color:var(--violet-night); }
    .qty-num { min-width:24px; text-align:center; font-weight:600; font-size:0.875rem; color:var(--text-dark); }
    .variant-wrap { margin-top: 0; display: flex; align-items: center; }
    .variant-select {
        width: auto;
        min-width: 130px;
        max-width: 170px;
        padding: 0.3rem 1.55rem 0.3rem 0.55rem;
        border: 1.5px solid #dac8e6;
        border-radius: 999px;
        font-family: 'Inter', sans-serif;
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--violet-night);
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%23513f67' d='M1.2.8 5 4.5 8.8.8a.75.75 0 1 1 1 1.1L5.5 5.9a.75.75 0 0 1-1 0L.2 1.9a.75.75 0 1 1 1-1.1Z'/%3E%3C/svg%3E") no-repeat right 0.55rem center;
        appearance: none;
        outline: none;
    }
    .variant-select:focus { border-color: var(--violet-mid); box-shadow: 0 0 0 3px rgba(81,63,103,0.1); }
    .cart-item-actions { display:flex; flex-direction:row; align-items:center; justify-self:end; gap:1rem; min-width:170px; text-align:right; justify-content:flex-end; }
    .cart-item-price { font-weight:700; color:var(--text-dark); font-size:0.95rem; white-space:nowrap; }
    .btn-remove { background:none; border:none; cursor:pointer; color:var(--text-light); font-size:0.78rem; font-weight:500; display:flex; align-items:center; gap:0.25rem; padding:0.2rem 0.3rem; border-radius:4px; transition:color 0.18s, background 0.18s; white-space:nowrap; }
    .btn-remove:hover { color:var(--danger); background:rgba(217,95,95,0.08); }

    /*address*/
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:0.9rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.78rem; font-weight:600; letter-spacing:0.05em; text-transform:uppercase; color:var(--text-mid); margin-bottom:0.35rem; }
    .form-control { width:100%; padding:0.65rem 0.9rem; border:1.5px solid #d8cce0; border-radius:6px; font-family:'Inter',sans-serif; font-size:0.875rem; color:var(--text-dark); background:#fff; outline:none; transition:border-color 0.2s; }
    .form-control:focus { border-color:var(--violet-mid); box-shadow:0 0 0 3px rgba(45,28,66,0.07); }
    .form-control.is-invalid { border-color:var(--danger); }
    .invalid-feedback { color:var(--danger); font-size:0.75rem; margin-top:0.2rem; display:block; }

    /*payment*/
    .payment-options { display:flex; gap:0.75rem; margin-top:0.25rem; }
    .payment-option { flex:1; }
    .payment-option input[type="radio"] { display:none; }
    .payment-label { display:block; padding:0.85rem 1rem; border:2px solid #d8cce0; border-radius:8px; cursor:pointer; transition:all 0.18s; text-align:center; }
    .payment-option input:checked + .payment-label { border-color:var(--violet-night); background:rgba(45,28,66,0.04); }
    .payment-label .pay-name { font-weight:600; font-size:0.85rem; color:var(--violet-night); display:block; }
    .payment-label .pay-desc { font-size:0.73rem; color:var(--text-light); display:block; margin-top:0.1rem; }
    .payment-details {
        margin-top: 0.9rem;
        border: 1px solid #eadff1;
        border-radius: 10px;
        padding: 1rem;
        background: #fcf9ff;
    }
    .payment-details[hidden] { display: none; }
    .gcash-qr-wrap { text-align: center; margin-bottom: 0.8rem; }
    .gcash-qr-img {
        width: 210px;
        max-width: 100%;
        border-radius: 8px;
        border: 1px solid #e0d5e8;
        background: #fff;
        padding: 0.4rem;
    }
    .gcash-meta { font-size: 0.8rem; color: var(--text-mid); margin-top: 0.55rem; }
    .gcash-meta strong { color: var(--violet-night); }

    /*order summary*/
    .summary-panel { background:var(--violet-night); border-radius:10px; padding:1.5rem; position:sticky; top:120px; color:var(--porcelain); }
    .summary-panel h3 { font-size:1.15rem; margin-bottom:1rem; color:#fff; }
    .summary-customer-name { font-size:1.1rem; font-weight:600; color:#fff; margin-bottom:1rem; }
    .summary-items { list-style:none; margin-bottom:1rem; }
    .summary-item { display:flex; justify-content:space-between; font-size:0.82rem; color:rgba(233,213,230,0.8); padding:0.3rem 0; border-bottom:1px solid rgba(233,213,230,0.1); }
    .summary-item:last-child { border-bottom:none; }
    .summary-item span:last-child { font-weight:500; color:var(--porcelain); }
    .summary-line { display:flex; justify-content:space-between; padding:0.4rem 0; font-size:0.85rem; color:rgba(233,213,230,0.7); }
    .summary-total { display:flex; justify-content:space-between; padding:0.75rem 0 0; margin-top:0.25rem; border-top:1px solid rgba(233,213,230,0.25); font-weight:700; font-size:1rem; color:#fff; }
    .btn-submit { width:100%; padding:0.85rem; background:var(--porcelain); color:var(--violet-night); border:none; border-radius:8px; font-family:'Inter',sans-serif; font-size:0.95rem; font-weight:700; cursor:pointer; margin-top:1.25rem; transition:background 0.2s,transform 0.15s; letter-spacing:0.02em; }
    .btn-submit:hover { background:#fff; transform:translateY(-1px); }

    /*empty cart*/
    .empty-cart { text-align:center; padding:4rem 2rem; color:var(--text-light); }
    .empty-cart h3 { font-size:1.5rem; margin-bottom:0.5rem; color:var(--text-mid); }
    .empty-cart p { margin-bottom:1.5rem; }

    /*modal*/
    .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(45,28,66,0.55); backdrop-filter:blur(3px); z-index:200; align-items:center; justify-content:center; }
    .modal-backdrop.open { display:flex; }
    .modal { background:#fff; border-radius:12px; box-shadow:0 20px 60px rgba(45,28,66,0.25); width:100%; max-width:420px; animation:modalIn 0.22s ease; }
    @keyframes modalIn { from{opacity:0;transform:scale(0.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .modal-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--porcelain-light); display:flex; align-items:center; justify-content:space-between; }
    .modal-header h3 { font-size:1.1rem; }
    .modal-close { background:none; border:none; cursor:pointer; color:var(--text-light); font-size:1.3rem; transition:color 0.2s; }
    .modal-close:hover { color:var(--text-dark); }
    .modal-body { padding:1.5rem; color:var(--text-mid); font-size:0.9rem; line-height:1.6; }
    .modal-footer { padding:1rem 1.5rem; border-top:1px solid var(--porcelain-light); display:flex; gap:0.75rem; justify-content:flex-end; }
    .btn-cancel-modal { background:var(--porcelain-light); color:var(--text-mid); border:none; border-radius:6px; padding:0.6rem 1.4rem; font-family:'Inter',sans-serif; font-size:0.875rem; font-weight:500; cursor:pointer; transition:background 0.18s; }
    .btn-cancel-modal:hover { background:var(--porcelain); }
    .btn-confirm { background:var(--violet-night); color:var(--snow); border:none; border-radius:6px; padding:0.6rem 1.4rem; font-family:'Inter',sans-serif; font-size:0.875rem; font-weight:600; cursor:pointer; transition:background 0.2s; }
    .btn-confirm:hover { background:var(--violet-mid); }

    @media (max-width:980px) {
        .page { padding:2rem 1.5rem; }
        .cart-item-actions { min-width:140px; gap:0.65rem; }
    }
    @media (max-width:860px) { .cart-layout { grid-template-columns:1fr; } .summary-panel { position:static; } }
    @media (max-width:540px) { .form-row { grid-template-columns:1fr; } .payment-options { flex-direction:column; } }

    /*cart w mobile responsive*/
    @media (max-width: 900px) {
        .cart-layout { grid-template-columns: 1fr !important; }
        .page { padding: 1.25rem 1rem !important; }
    }
    @media (max-width: 768px) {
        .page-heading { font-size: 1.5rem !important; }
        .cart-item-row { flex-direction: column; gap: 0.75rem; }
        .cart-item-img { width: 70px !important; height: 70px !important; flex-shrink: 0; }
        .cart-item-details { flex: 1; }
        .cart-item-qty { align-self: flex-start; }
        .cart-item-remove { align-self: flex-end; }
        .navbar-logo-row { position: relative; }
    }
    @media (max-width: 480px) {
        .page { padding: 1rem 0.75rem !important; }
        .page-heading { font-size: 1.3rem !important; }
    }
</style>
@endpush

@section('content')
<nav class="navbar">
    <div class="navbar-logo-row">
        <img src="{{ asset('images/mayari-logo.png') }}"
             alt="Mayari"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <a href="{{ route('customer.home') }}" class="navbar-logo-text" style="display:none;">MAYARI</a>
    </div>

    <div class="navbar-links-row">
        <div class="navbar-left">
            <a href="{{ route('customer.profile') }}" class="user-chip">
                <div class="avatar">{{ substr(auth()->user()->firstName, 0, 1) }}{{ substr(auth()->user()->lastName, 0, 1) }}</div>
                <span>{{ auth()->user()->firstName }}</span>
            </a>
        </div>
        <div class="navbar-right">
            <a href="{{ route('customer.orders') }}" class="nav-link">My Orders</a>
            <a href="{{ route('customer.cart') }}" class="nav-link active-nav">
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
    <h1 class="page-heading">My Cart</h1>

    @if(empty($cartItems))
        <div class="empty-cart">
            <div style="font-size:3.5rem;margin-bottom:1rem;opacity:.3;">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Add some products to get started.</p>
            <a href="{{ route('customer.home') }}" style="display:inline-flex;align-items:center;gap:.5rem;background:var(--violet-night);color:var(--snow);padding:.75rem 1.75rem;border-radius:8px;text-decoration:none;font-weight:600;font-size:.875rem;">Shop Now</a>
        </div>
    @else


    <form action="{{ route('customer.order.place') }}" method="POST" id="orderForm">
        @csrf
        <div class="cart-layout">
            <div class="cart-left">
                {{-- CART ITEMS --}}
                <div class="cart-panel">
                    <div class="cart-panel-header"><h3>Items ({{ count($cartItems) }})</h3></div>
                    @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="cart-item-img">
                            @if($item['image'] && $item['image'] !== 'example.image')
                                <img src="{{ $item['image'] }}" alt="{{ $item['pName'] }}">
                            @else
                                <span class="cart-item-img-ph">💄</span>
                            @endif
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">{{ $item['pName'] }}</div>
                            <div class="cart-item-desc">
                                {{ $item['descript'] }}
                            </div>
                            <div class="item-controls-row">
                                @if(!empty($item['variants']))
                                    <div class="variant-wrap">
                                        <select
                                            id="variant-{{ $item['productID'] }}"
                                            class="variant-select"
                                            data-update-url="{{ route('customer.cart.update', $item['productID']) }}"
                                            data-qty="{{ $item['quantity'] }}"
                                            onchange="changeVariant(this)"
                                            aria-label="Shade or color"
                                        >
                                            @foreach($item['variants'] as $variant)
                                                <option value="{{ $variant }}" {{ ($item['variant'] ?? '') === $variant ? 'selected' : '' }}>
                                                    {{ $variant }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="qty-controls"
                                     data-update-url="{{ route('customer.cart.update', $item['productID']) }}"
                                     data-qty="{{ $item['quantity'] }}"
                                     data-variant="{{ $item['variant'] ?? '' }}">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>
                                    <span class="qty-num">{{ $item['quantity'] }}</span>
                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-actions">
                            <span class="cart-item-price">₱{{ number_format($item['unitPrice'] * $item['quantity'], 2) }}</span>
                            <button type="button"
                                    class="btn-remove"
                                    data-remove-url="{{ route('customer.cart.remove', $item['productID']) }}"
                                    onclick="removeFromCart(this)">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Remove
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{--address--}}
                <div class="cart-panel">
                    <div class="cart-panel-header"><h3>ADDRESS</h3></div>
                    <div style="padding:1.25rem 1.5rem;">
                        @if(empty($hasAddresses))
                            <div style="background:#fff4df;border:1px solid #f0cf95;border-radius:12px;padding:0.9rem 1rem;margin-bottom:1rem;">
                                <div style="font-weight:800;color:#8a5510;margin-bottom:0.2rem;">No shipping address yet</div>
                                <div style="color:#8a5510;font-size:0.86rem;line-height:1.5;margin-bottom:0.7rem;">
                                    Please add your shipping address to calculate delivery fees and complete your order quickly.
                                </div>
                                <a class="btn btn-primary btn-sm" href="{{ route('customer.profile', ['openAddress' => 1, 'redirect' => '/shop/cart']) }}">Add Shipping Address</a>
                            </div>
                        @else
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 1rem; margin-bottom: 0.8rem;">
                                <p style="font-size:0.78rem;color:var(--text-light);margin:0;">
                                    {{ !empty($defaultAddress) ? 'Default address is selected for checkout. You can change it anytime.' : 'Please review and update your address.' }}
                                </p>
                                <a class="btn btn-outline btn-sm" href="{{ route('customer.profile', ['openAddress' => 1, 'redirect' => '/shop/cart']) }}">Change Address</a>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Street</label>
                            <input type="text" name="street" class="form-control @error('street') is-invalid @enderror"
                                   value="{{ old('street', $defaultAddress->addressLine ?? '') }}" placeholder="House no., Street, Barangay, etc." required>
                            @error('street')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $defaultAddress->city ?? '') }}" placeholder="Davao City" required>
                                @error('city')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Province</label>
                                <input type="text" name="province" class="form-control @error('province') is-invalid @enderror"
                                       value="{{ old('province', $defaultAddress->province ?? '') }}" placeholder="Davao del Sur" required>
                                @error('province')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                       value="{{ old('country', $defaultAddress->country ?? 'Philippines') }}" placeholder="Philippines" required>
                                @error('country')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" name="postal" class="form-control @error('postal') is-invalid @enderror"
                                       value="{{ old('postal', $defaultAddress->postal ?? '') }}" placeholder="8000">
                                @error('postal')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{--payment--}}
                <div class="cart-panel">
                    <div class="cart-panel-header"><h3>PAYMENT METHOD</h3></div>
                    <div style="padding:1rem 1.5rem;">
                        <p style="font-size:0.78rem;color:var(--text-light);margin-bottom:0.75rem;">Select payment method.</p>
                        @error('paymentMethod')<span class="invalid-feedback" style="margin-bottom:.75rem;">{{ $message }}</span>@enderror
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" name="paymentMethod" id="cod" value="cod" {{ old('paymentMethod','cod') === 'cod' ? 'checked' : '' }}>
                                <label class="payment-label" for="cod">
                                    <span class="pay-name">Cash on Delivery</span>
                                    <span class="pay-desc">Pay when you receive.</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="paymentMethod" id="gcash" value="ecard" {{ old('paymentMethod') === 'ecard' ? 'checked' : '' }}>
                                <label class="payment-label" for="gcash">
                                    <span class="pay-name">GCash</span>
                                    <span class="pay-desc">E-wallet</span>
                                </label>
                            </div>
                        </div>
                        <div id="gcashDetails" class="payment-details" hidden>
                            <h4 style="font-size:0.95rem;color:var(--violet-night);margin-bottom:0.7rem;">Scan QR</h4>
                            <div class="gcash-qr-wrap">
                                <img
                                    class="gcash-qr-img"
                                    src="https://api.qrserver.com/v1/create-qr-code/?size=230x230&data=GCash%20Payment%20to%20Mayari%20Shop%20-%200911130611"
                                    alt="Mayari Shop GCash QR"
                                >
                                <div class="gcash-meta">
                                    Name: <strong>Mayari Shop</strong><br>
                                    GCash Number: <strong>0911130611</strong>
                                </div>
                            </div>
                            <div class="form-row" style="margin-bottom:0.8rem;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="gcashName">Sender Name</label>
                                    <input
                                        type="text"
                                        name="gcashName"
                                        id="gcashName"
                                        value="{{ old('gcashName') }}"
                                        class="form-control @error('gcashName') is-invalid @enderror"
                                        placeholder="Enter your sender name"
                                    >
                                    @error('gcashName')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="gcashNumber">GCash Number</label>
                                    <input
                                        type="text"
                                        name="gcashNumber"
                                        id="gcashNumber"
                                        value="{{ old('gcashNumber') }}"
                                        class="form-control @error('gcashNumber') is-invalid @enderror"
                                        inputmode="numeric"
                                        maxlength="11"
                                        placeholder="Enter your GCash number"
                                    >
                                    @error('gcashNumber')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="referenceNumber">Reference Number</label>
                                <input
                                    type="text"
                                    name="referenceNumber"
                                    id="referenceNumber"
                                    value="{{ old('referenceNumber') }}"
                                    class="form-control @error('referenceNumber') is-invalid @enderror"
                                    inputmode="numeric"
                                    maxlength="13"
                                    pattern="[0-9]{13}"
                                    placeholder="Enter your GCash reference number"
                                >
                                <small style="display:block;font-size:0.75rem;color:var(--text-light);margin-top:0.3rem;">
                                    Please copy your reference number and paste it here to complete the transaction.
                                </small>
                                @error('referenceNumber')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--order summary--}}
            <div class="summary-panel">
                <h3>Order Summary</h3>
                <div class="summary-customer-name">{{ $user->firstName }} {{ $user->lastName }}</div>
                <ul class="summary-items">
                    @foreach($cartItems as $item)
                    <li class="summary-item">
                        <span>{{ $item['pName'] }}@if(!empty($item['variant'])) ({{ $item['variant'] }})@endif x{{ $item['quantity'] }}</span>
                        <span>₱{{ number_format($item['unitPrice'] * $item['quantity'], 2) }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="summary-line">
                    <span>Subtotal</span><span>₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-line">
                    <span>Delivery Fee</span><span>₱{{ number_format($delivery, 2) }}</span>
                </div>
                <div class="summary-total">
                    <span>Total</span><span>₱{{ number_format($total, 2) }}</span>
                </div>
                <button type="button" class="btn-submit" onclick="validateAndSubmit()">Submit Order</button>
            </div>
        </div>
    </form>
    @endif
</div>

{{--confirm modal--}}
<div class="modal-backdrop" id="confirmModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Confirm Order</h3>
            <button class="modal-close" onclick="closeConfirmModal()">✕</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to place this order?<br>Please review your items and delivery details before confirming.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-modal" onclick="closeConfirmModal()">Cancel</button>
            <button class="btn-confirm" onclick="document.getElementById('orderForm').submit()">Place Order</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function validateAndSubmit() {
    const form = document.getElementById('orderForm');
    const required = form.querySelectorAll('input[required]');
    let valid = true;

    form.querySelectorAll('.client-invalid').forEach(function(el) {
        el.remove();
    });

    function showFieldError(input, message) {
        input.classList.add('is-invalid');
        const error = document.createElement('span');
        error.className = 'invalid-feedback client-invalid';
        error.textContent = message;
        input.insertAdjacentElement('afterend', error);
    }

    required.forEach(function(input) {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required.');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    const selectedPayment = form.querySelector('input[name="paymentMethod"]:checked');
    if (!selectedPayment) {
        const paymentBlock = form.querySelector('.payment-options');
        if (paymentBlock) {
            const error = document.createElement('span');
            error.className = 'invalid-feedback client-invalid';
            error.style.marginTop = '0.5rem';
            error.textContent = 'Please select a payment method.';
            paymentBlock.insertAdjacentElement('afterend', error);
        }
        valid = false;
    }

    if (!valid) {
        const first = form.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    document.getElementById('confirmModal').classList.add('open');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('open');
}

function changeQty(btn, delta) {
    const controls = btn.closest('.qty-controls');
    const numEl    = controls.querySelector('.qty-num');
    let val        = parseInt(controls.dataset.qty) + delta;
    if (val < 1) val = 1;
    controls.dataset.qty = val;
    numEl.textContent    = val;

    setTimeout(function() {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = controls.dataset.updateUrl;

        const csrf = document.createElement('input');
        csrf.type  = 'hidden';
        csrf.name  = '_token';
        csrf.value = '{{ csrf_token() }}';

        const method  = document.createElement('input');
        method.type   = 'hidden';
        method.name   = '_method';
        method.value  = 'PATCH';

        const qty  = document.createElement('input');
        qty.type   = 'hidden';
        qty.name   = 'quantity';
        qty.value  = val;

        const variantSelect = controls.parentElement.querySelector('.variant-select');
        const variantValue = variantSelect ? variantSelect.value : (controls.dataset.variant || '');

        if (variantValue) {
            const variant = document.createElement('input');
            variant.type  = 'hidden';
            variant.name  = 'variant';
            variant.value = variantValue;
            f.appendChild(variant);
        }

        f.appendChild(csrf);
        f.appendChild(method);
        f.appendChild(qty);
        document.body.appendChild(f);
        f.submit();
    }, 300);
}

function removeFromCart(btn) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = btn.dataset.removeUrl;

    const csrf = document.createElement('input');
    csrf.type  = 'hidden';
    csrf.name  = '_token';
    csrf.value = '{{ csrf_token() }}';

    const method = document.createElement('input');
    method.type  = 'hidden';
    method.name  = '_method';
    method.value = 'DELETE';

    f.appendChild(csrf);
    f.appendChild(method);
    document.body.appendChild(f);
    f.submit();
}

function changeVariant(selectEl) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = selectEl.dataset.updateUrl;

    const csrf = document.createElement('input');
    csrf.type  = 'hidden';
    csrf.name  = '_token';
    csrf.value = '{{ csrf_token() }}';

    const method = document.createElement('input');
    method.type  = 'hidden';
    method.name  = '_method';
    method.value = 'PATCH';

    const qty = document.createElement('input');
    qty.type  = 'hidden';
    qty.name  = 'quantity';
    qty.value = selectEl.dataset.qty || '1';

    const variant = document.createElement('input');
    variant.type  = 'hidden';
    variant.name  = 'variant';
    variant.value = selectEl.value;

    const updateType = document.createElement('input');
    updateType.type  = 'hidden';
    updateType.name  = 'updateType';
    updateType.value = 'variant';

    f.appendChild(csrf);
    f.appendChild(method);
    f.appendChild(qty);
    f.appendChild(variant);
    f.appendChild(updateType);
    document.body.appendChild(f);
    f.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('orderForm');
    if (!form) return;
    const gcashRadio = form.querySelector('#gcash');
    const gcashDetails = form.querySelector('#gcashDetails');
    const gcashNameInput = form.querySelector('#gcashName');
    const gcashNumberInput = form.querySelector('#gcashNumber');
    const referenceNumberInput = form.querySelector('#referenceNumber');

    function togglePaymentDetails() {
        const selectedPayment = form.querySelector('input[name="paymentMethod"]:checked');
        const isGcash = selectedPayment && selectedPayment.id === 'gcash';
        if (gcashDetails) gcashDetails.hidden = !isGcash;
        if (referenceNumberInput) {
            referenceNumberInput.required = !!isGcash;
            if (!isGcash) {
                referenceNumberInput.classList.remove('is-invalid');
                const next = referenceNumberInput.nextElementSibling;
                if (next && next.classList.contains('client-invalid')) {
                    next.remove();
                }
            }
        }
        if (gcashNameInput) gcashNameInput.required = !!isGcash;
        if (gcashNumberInput) gcashNumberInput.required = !!isGcash;
    }

    form.querySelectorAll('input[required]').forEach(function(input) {
        input.addEventListener('input', function() {
            if (input.value.trim()) {
                input.classList.remove('is-invalid');
                const next = input.nextElementSibling;
                if (next && next.classList.contains('client-invalid')) {
                    next.remove();
                }
            }
        });
    });

    form.querySelectorAll('input[name="paymentMethod"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            form.querySelectorAll('.client-invalid').forEach(function(el) {
                if (el.textContent.includes('payment method')) {
                    el.remove();
                }
            });
            togglePaymentDetails();
        });
    });

    if (referenceNumberInput) {
        referenceNumberInput.addEventListener('input', function() {
            referenceNumberInput.value = referenceNumberInput.value.replace(/\D/g, '').slice(0, 13);
            if (referenceNumberInput.value.trim()) {
                referenceNumberInput.classList.remove('is-invalid');
                const next = referenceNumberInput.nextElementSibling;
                if (next && next.classList.contains('client-invalid')) {
                    next.remove();
                }
            }
        });
    }

    if (gcashNumberInput) {
        gcashNumberInput.addEventListener('input', function() {
            gcashNumberInput.value = gcashNumberInput.value.replace(/\D/g, '').slice(0, 11);
            if (gcashNumberInput.value.trim()) {
                gcashNumberInput.classList.remove('is-invalid');
            }
        });
    }

    togglePaymentDetails();
});
</script>
@endpush