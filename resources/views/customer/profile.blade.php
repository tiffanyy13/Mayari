@extends('layouts.app')
@section('title', 'My Profile')

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
    .user-chip.active-nav { background: rgba(233,213,230,0.18); }
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

    .profile-card { background:#fff; border-radius:12px; box-shadow:0 2px 14px rgba(45,28,66,0.09); overflow:hidden; }
    .profile-card-header {
        background: linear-gradient(135deg, var(--violet-night) 0%, var(--violet-mid) 100%);
        padding:2rem 2rem 1.75rem;
        display:flex; align-items:center; gap:1.25rem;
    }
    .profile-avatar-large {
        width:72px; height:72px; border-radius:50%;
        background:var(--porcelain); color:var(--violet-night);
        display:flex; align-items:center; justify-content:center;
        font-size:1.6rem; font-weight:700; text-transform:uppercase;
        border:3px solid rgba(255,255,255,0.3); flex-shrink:0;
    }
    .profile-card-header-info h2 { color:#fff; font-size:1.3rem; margin-bottom:0.2rem; }
    .profile-card-header-info p  { color:rgba(233,213,230,0.8); font-size:0.85rem; }
    .profile-card-body { padding:2rem; }
    .profile-section-title { font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--text-light); margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--porcelain-light); }
    .profile-field-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.78rem; font-weight:600; letter-spacing:0.05em; text-transform:uppercase; color:var(--text-mid); margin-bottom:0.35rem; }
    .password-wrap { position: relative; }
    .form-control { width:100%; padding:0.65rem 0.9rem; border:1.5px solid #d8cce0; border-radius:6px; font-family:'Inter',sans-serif; font-size:0.875rem; color:var(--text-dark); background:#fff; outline:none; transition:border-color 0.2s; }
    .form-control:focus { border-color:var(--violet-mid); box-shadow:0 0 0 3px rgba(45,28,66,0.07); }
    .password-wrap .form-control { padding-right: 2.75rem; }
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
    .btn-profile-save { background:var(--violet-night); color:#fff; border:none; border-radius:6px; padding:0.7rem 1.75rem; font-family:'Inter',sans-serif; font-size:0.9rem; font-weight:600; cursor:pointer; transition:background 0.2s; margin-top:0.5rem; }
    .btn-profile-save:hover { background:var(--violet-mid); }
    .invalid-feedback { color:var(--danger); font-size:0.75rem; margin-top:0.2rem; display:block; }
    .addr-wrap { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--porcelain-light); }
    .addr-top { display:flex; align-items:flex-start; justify-content:space-between; gap: 1rem; margin-bottom: 0.85rem; }
    .addr-top p { color: var(--text-light); font-size: 0.86rem; line-height: 1.5; margin: 0.25rem 0 0; }
    .grid2 { display:grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; }
    @media (max-width: 560px) { .grid2 { grid-template-columns: 1fr; } }

    /*profile w mobile responsive*/
    @media (max-width: 768px) {
        .profile-page { margin: 1rem auto !important; padding: 0 0.75rem !important; }
        .profile-card-header { flex-direction: column; align-items: flex-start; gap: 0.75rem !important; }
        .profile-field-row { grid-template-columns: 1fr !important; }
        .profile-card-body { padding: 1.25rem !important; }
    }
    @media (max-width: 480px) {
        .profile-avatar-large { width: 58px !important; height: 58px !important; font-size: 1.3rem !important; }
        .profile-card-header { padding: 1rem !important; }
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

    {{-- Row 2: Profile left, nav links right --}}
    <div class="navbar-links-row">
        <div class="navbar-left">
            <a href="{{ route('customer.profile') }}" class="user-chip active-nav">
                <div class="avatar">{{ substr(auth()->user()->firstName, 0, 1) }}{{ substr(auth()->user()->lastName, 0, 1) }}</div>
                <span>{{ auth()->user()->firstName }}</span>
            </a>
        </div>
        <div class="navbar-right">
            <a href="{{ route('customer.orders') }}" class="nav-link">My Orders</a>
            <a href="{{ route('customer.cart') }}" class="nav-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 001.4 1.4L9 15h8m-5 4a1 1 0 11-2 0 1 1 0 012 0zm5 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                Cart
                @php $cartCount = array_sum(array_column(session()->get('cart',[]),'quantity')); @endphp
                @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
            </a>
            <a href="{{ route('logout') }}" class="btn-logout">Log Out</a>
        </div>
    </div>
</nav>

<div class="page">
    <a href="{{ route('customer.home') }}" class="back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Shop
    </a>

    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-avatar-large">
                {{ substr($user->firstName,0,1) }}{{ substr($user->lastName,0,1) }}
            </div>
            <div class="profile-card-header-info">
                <h2>{{ $user->firstName }} {{ $user->lastName }}</h2>
                <p>{{ $user->email }}</p>
            </div>
        </div>
        <div class="profile-card-body">
            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf @method('PATCH')
                <div class="profile-section-title">Personal Information</div>
                <div class="profile-field-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="firstName" class="form-control" value="{{ old('firstName', $user->firstName) }}" required>
                        @error('firstName')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastName" class="form-control" value="{{ old('lastName', $user->lastName) }}" required>
                        @error('lastName')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="profile-field-row">
                    <div class="form-group">
                        <label>Mobile number (Philippines)</label>
                        <input type="text" name="phone" maxlength="11" inputmode="numeric" autocomplete="tel" pattern="09[0-9]{9}" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="09171234567">
                        <small style="display:block;margin-top:0.35rem;font-size:0.78rem;color:var(--text-light);">11 digits, starting with 09 (e.g. 09171234567).</small>
                        @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{--address--}}
                <div class="addr-wrap" id="shippingAddressSection">
                    <div class="profile-section-title" style="margin-bottom: 0.75rem;">Shipping Address</div>
                    <div class="addr-top" style="margin-bottom: 0.95rem;">
                        <div><p>Fill this once and we’ll auto-fill it in your cart during checkout.</p></div>
                    </div>

                    @php $addr = $defaultAddress ?? null; @endphp

                    <div class="grid2">
                        <div class="form-group">
                            <label>Address Label</label>
                            <input type="text" name="shipping_label" maxlength="30" class="form-control @error('shipping_label') is-invalid @enderror"
                                   value="{{ old('shipping_label', $addr->label ?? 'Home') }}" placeholder="e.g. Home, Dorm, Mama's house">
                            @error('shipping_label')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Postal Code (optional)</label>
                            <input type="text" name="shipping_postal" class="form-control @error('shipping_postal') is-invalid @enderror"
                                   value="{{ old('shipping_postal', $addr->postal ?? '') }}">
                            @error('shipping_postal')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="grid2">
                        <div class="form-group">
                            <label>Street</label>
                            <input type="text" name="shipping_addressLine" class="form-control @error('shipping_addressLine') is-invalid @enderror"
                                   value="{{ old('shipping_addressLine', $addr->addressLine ?? '') }}" placeholder="House no., Street, Barangay, etc.">
                            @error('shipping_addressLine')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="shipping_city" class="form-control @error('shipping_city') is-invalid @enderror"
                                   value="{{ old('shipping_city', $addr->city ?? '') }}">
                            @error('shipping_city')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="grid2">
                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" name="shipping_province" class="form-control @error('shipping_province') is-invalid @enderror"
                                   value="{{ old('shipping_province', $addr->province ?? '') }}">
                            @error('shipping_province')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="shipping_country" class="form-control @error('shipping_country') is-invalid @enderror"
                                   value="{{ old('shipping_country', $addr->country ?? 'Philippines') }}">
                            @error('shipping_country')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="profile-section-title" style="margin-top:1.5rem;">Change Password <span style="font-weight:400;font-size:0.75rem;text-transform:none;letter-spacing:0;">(leave blank to keep current)</span></div>
                <div class="profile-field-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="password-wrap">
                            <input type="password" name="password" class="form-control" placeholder="New password" autocomplete="new-password">
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
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <div class="password-wrap">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" autocomplete="new-password">
                            <button type="button" class="btn-eye" data-field="password_confirmation" data-icon="eyeIcon-password_confirmation">
                                <svg id="eyeIcon-password_confirmation" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <button type="submit" class="btn-profile-save">Save Changes</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-eye').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const fieldName = btn.getAttribute('data-field');
        const iconId = btn.getAttribute('data-icon');
        const input = document.querySelector('input[name="' + fieldName + '"]');
        const icon = document.getElementById(iconId);
        if (!input) return;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        if (icon) {
            icon.innerHTML = isHidden
                ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    });
});
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shouldOpen = {{ !empty($openAddress) ? 'true' : 'false' }};
        const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
        if (shouldOpen || hasErrors) {
            const section = document.getElementById('shippingAddressSection');
            if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const first = section ? section.querySelector('input[name="shipping_fullName"]') : null;
            if (first) first.focus();
        }
    });
</script>
@endpush
@endsection