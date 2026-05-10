@extends('layouts.app')
@section('title', 'Shop')

@push('styles')
<style>
    body { background: var(--snow); display: flex; flex-direction: column; min-height: 100vh; }
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

    .hero {
        background: url('{{ asset("images/hero.png") }}') center center / cover no-repeat;
        background-color: var(--violet-dark);
        padding: 4.5rem 2rem;
        text-align: left;
        position: relative;
        overflow: hidden;
    }
    .hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(45, 28, 66, 0.45); 
    z-index: 0;
}
    .hero-content { position: relative; z-index: 1; ... } 
    .hero::before {
        content: '';
        position: absolute;
        width: 600px; height: 600px; border-radius: 50%;
        background: radial-gradient(circle, rgba(233,213,230,0.07) 0%, transparent 70%);
        top: -200px; right: -100px;
    }
    .hero-content { position: relative; z-index: 1; max-width: 560px; padding: 0 2rem; }
    .hero-eyebrow {
        display: inline-block; background: rgba(233,213,230,0.15); color: var(--porcelain);
        font-size: 0.72rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase;
        padding: 0.35rem 0.9rem; border-radius: 3px; margin-bottom: 1.25rem;
        border: 1px solid rgba(233,213,230,0.25);
    }
    .hero h1 { font-size: 2.8rem; color: var(--porcelain); line-height: 1.1; margin-bottom: 0.75rem; font-weight: 700; }
    .hero p   { color: rgba(233,213,230,0.7); font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.6; }
    .btn-hero {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: var(--porcelain); color: var(--violet-night);
        padding: 0.8rem 1.8rem; border-radius: 4px;
        font-weight: 700; font-size: 0.875rem; text-decoration: none;
        transition: all 0.2s; letter-spacing: 0.02em;
    }
    .btn-hero:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(45,28,66,0.3); }
    .shop-section { max-width: 1400px; margin: 0 auto; padding: 2.5rem 1.5rem; flex: 1; }

    .shop-toolbar {
        display: flex; align-items: flex-end; justify-content: space-between;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
        border-bottom: 2px solid rgba(216,204,224,0.4);
        width: 100%;
    }
    .category-tabs { display: flex; gap: 0; flex-wrap: wrap; margin-bottom: 0; flex: 1 1 auto; min-width: 0; }
    .cat-tab {
        padding: 0.55rem 1.1rem; border-radius: 0; border: none;
        border-bottom: 3px solid transparent; background: transparent;
        font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 500;
        color: var(--text-mid); cursor: pointer; text-decoration: none;
        transition: all 0.18s; margin-bottom: -2px;
    }
    .cat-tab:hover { color: var(--violet-night); border-bottom-color: rgba(45,28,66,0.25); }
    .cat-tab.active { color: var(--violet-night); border-bottom: 3px solid var(--violet-night); font-weight: 700; }

    .search-form { margin: 0 0 4px; flex: 0 0 auto; }
    .search-wrap { position: relative; }
    .search-wrap svg { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-light); pointer-events: none; }
    .search-input {
        padding: 0.5rem 0.9rem 0.5rem 2.25rem; border: 1.5px solid #d8cce0; border-radius: 4px;
        font-family: 'Inter', sans-serif; font-size: 0.875rem; outline: none;
        transition: border-color 0.2s; width: 220px; background: #fff;
    }
    .search-input:focus { border-color: var(--violet-mid); }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        width: 100%;
        align-items: stretch;
    }

    .product-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 10px rgba(45,28,66,0.07);
        overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;
        display: flex; flex-direction: column;
        cursor: pointer;
    }
    .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(45,28,66,0.14); }

    .product-img {
        width: 100%; aspect-ratio: 1 / 1;
        background: linear-gradient(135deg, var(--porcelain-light), var(--porcelain));
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .product-img img { width: 100%; height: 100%; object-fit: cover; }
    .product-img-placeholder { font-size: 2.2rem; opacity: 0.4; }

    .product-body { padding: 0.75rem; flex: 1; display: flex; flex-direction: column; }
    .product-cat  { font-size: 0.67rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--text-light); margin-bottom: 0.2rem; }
    .product-name { font-size: 0.88rem; font-weight: 600; color: var(--violet-night); margin-bottom: 0.2rem; line-height: 1.3; }
    .product-desc { font-size: 0.73rem; color: var(--text-light); margin-bottom: 0.45rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; }
    .product-price { font-weight: 700; color: var(--violet-mid); font-size: 0.9rem; margin-bottom: 0.6rem; }
    .btn-addcart {
        width: 100%; padding: 0.48rem; background: var(--violet-night); color: var(--snow);
        border: none; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 0.78rem;
        font-weight: 600; cursor: pointer; transition: background 0.2s; letter-spacing: 0.02em;
    }
    .btn-addcart:hover { background: var(--violet-mid); }

    .empty-state { grid-column: 1 / -1; text-align: center; padding: 5rem 2rem; color: var(--text-light); }
    .empty-state h3 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-mid); }

    .quickview-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(45,28,66,0.58);
        backdrop-filter: blur(2px);
        z-index: 220;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .quickview-backdrop.open { display: flex; }
    .quickview-modal {
        width: 100%;
        max-width: 780px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(45,28,66,0.28);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .quickview-img {
        background: linear-gradient(135deg, var(--porcelain-light), var(--porcelain));
        min-height: 340px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quickview-img img { width: 100%; height: 100%; object-fit: cover; }
    .quickview-body { padding: 1.4rem 1.5rem; display:flex; flex-direction:column; min-height:340px; }
    .quickview-head { display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem; margin-bottom:0.25rem; }
    .quickview-head-left { min-width:0; }
    .quickview-close { border: none; background: none; font-size: 1.4rem; cursor: pointer; color: var(--text-light); line-height:1; padding:0.1rem; margin-top:-0.05rem; }
    .quickview-close:hover { color: var(--text-dark); }
    .quickview-title { font-size: 1.35rem; color: var(--violet-night); margin-bottom: 0.35rem; }
    .quickview-desc { color: var(--text-mid); font-size: 0.88rem; line-height: 1.5; margin-bottom: 0.9rem; }
    .quickview-price { color: var(--violet-mid); font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; }
    #quickViewAddForm { display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto; padding-top: 1rem; }
    .variant-label { font-size: 0.76rem; text-transform: uppercase; color: var(--text-light); letter-spacing: 0.05em; margin-bottom: 0.4rem; }
    .variant-list { display: flex; flex-wrap: wrap; gap: 0.45rem; margin-bottom: 0.2rem; }
    .variant-chip { border: 1px solid #d8cce0; border-radius: 20px; padding: 0.28rem 0.62rem; font-size: 0.78rem; color: var(--text-mid); background: #fff; cursor: pointer; }
    .variant-chip.active { border-color: var(--violet-night); color: var(--violet-night); background: rgba(45,28,66,0.06); }
    .quickview-msg { font-size: 0.78rem; color: var(--danger); margin-top: 0; margin-bottom: 0; display: none; }
    .quickview-add {
        width: 100%;
        padding: 0.62rem;
        border: none;
        border-radius: 8px;
        background: var(--violet-night);
        color: #fff;
        font-family: 'Inter', sans-serif;
        font-size: 0.86rem;
        font-weight: 600;
        cursor: pointer;
    }
    .quickview-add:hover { background: var(--violet-mid); }
    @media (max-width: 760px) { .quickview-modal { grid-template-columns: 1fr; } .quickview-img { min-height: 220px; } }
    /*home w mobile responsive*/
    @media (max-width: 900px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 0.85rem !important;
        }
    }

    @media (max-width: 768px) {
        .hero { padding: 2rem 1rem !important; }
        .hero-content { padding: 0 !important; max-width: 100% !important; }
        .hero-title { font-size: 1.6rem !important; }
        .hero-sub   { font-size: 0.88rem !important; }
        .hero-cta   { flex-wrap: wrap; gap: 0.5rem; }

        .shop-section { padding: 1.25rem 0.85rem !important; }

        .section-header { flex-direction: column; align-items: flex-start; gap: 0.35rem; }
        .section-title  { font-size: 1.15rem !important; }
        .cat-tabs {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            flex-wrap: nowrap !important;
            gap: 0.3rem !important;
            padding-bottom: 0.3rem;
            scrollbar-width: none;
        }
        .cat-tabs::-webkit-scrollbar { display: none; }
        .cat-tab { white-space: nowrap; font-size: 0.78rem !important; padding: 0.28rem 0.7rem !important; }
        .products-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.75rem !important;
        }
        .product-card-img,
        .product-img { height: 130px !important; min-height: 0 !important; }
        .product-card-body { padding: 0.6rem 0.65rem 0.75rem !important; }
        .product-cat-label { font-size: 0.62rem !important; margin-bottom: 0.2rem !important; }
        .product-card-name { font-size: 0.8rem !important; line-height: 1.3 !important; margin-bottom: 0.25rem !important; }
        .product-card-desc { font-size: 0.72rem !important; -webkit-line-clamp: 2; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
        .product-card-price { font-size: 0.88rem !important; margin-bottom: 0.4rem !important; }
        .btn-atc { font-size: 0.72rem !important; padding: 0.42rem 0.5rem !important; }
        .quickview-modal { grid-template-columns: 1fr !important; }
        .quickview-img   { min-height: 180px !important; max-height: 240px !important; }
    }

    @media (max-width: 480px) {
        .hero-title { font-size: 1.3rem !important; }
        .shop-section { padding: 1rem 0.65rem !important; }
        .products-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.6rem !important;
        }
        .product-card-img,
        .product-img { height: 115px !important; }
        .product-card-name { font-size: 0.75rem !important; }
        .product-card-price { font-size: 0.82rem !important; }
        .btn-atc { font-size: 0.68rem !important; padding: 0.38rem 0.4rem !important; }
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
            <a href="{{ route('customer.cart') }}" class="nav-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 001.4 1.4L9 15h8m-5 4a1 1 0 11-2 0 1 1 0 012 0zm5 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                Cart
                @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Log Out</button>
            </form>
        </div>
    </div>
</nav>

@if(!empty($showAddressWelcome))
    <div class="modal-backdrop" id="welcomeAddressModal" style="display:none;" onclick="if(event.target===this)closeWelcomeAddressModal()">
        <div class="modal" style="max-width: 520px;">
            <div class="modal-header">
                <h3>Welcome to Mayari! 👋</h3>
                <button type="button" class="modal-close" onclick="closeWelcomeAddressModal()">✕</button>
            </div>
            <div class="modal-body" style="color: var(--text-mid); line-height: 1.6;">
                <p style="margin-bottom: 0.85rem;">
                    To make your shopping faster and easier, please add your shipping address now.
                    This will be saved in your account and automatically appear in your cart during checkout.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeWelcomeAddressModal()">Do it Later</button>
                <a class="btn btn-primary" href="{{ route('customer.profile', ['openAddress' => 1, 'redirect' => '/shop']) }}">Add Shipping Address Now</a>
            </div>
        </div>
    </div>
@endif

<section class="hero" id="top">
    <div class="hero-content">
        <span class="hero-eyebrow">Summer Eve ✦ New Collection ✦ March 2026</span>
        <h1>Colors that don't dim when the lights do.</h1>
        <p>The kind of gorgeous that only comes after sundown.</p>
        <a href="#shop" class="btn-hero">Shop Now</a>
    </div>
</section>

<section class="shop-section" id="shop">
    <div class="shop-toolbar">
        <div class="category-tabs">
            <a href="{{ route('customer.home') }}"
               class="cat-tab {{ !request('category') || request('category') === 'all' ? 'active' : '' }}">
                ALL
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('customer.home', ['category' => $cat->categoryID, 'search' => request('search')]) }}"
                   class="cat-tab {{ request('category') == $cat->categoryID ? 'active' : '' }}">
                    {{ strtoupper($cat->cName) }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('customer.home') }}" method="GET" class="search-form">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="search-wrap">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" name="search" class="search-input"
                       placeholder="Search products…" value="{{ request('search') }}">
            </div>
        </form>
    </div>

    <div class="products-grid">
        @forelse($products as $product)
        <div class="product-card"
            onclick="openQuickView(this)"
            data-name="{{ e($product->pName) }}"
            data-category="{{ e($product->category->cName ?? 'PRODUCT') }}"
            data-desc="{{ e($product->descript) }}"
            data-price="₱{{ number_format($product->price, 2) }}"
            data-image="{{ $product->image && $product->image !== 'example.image' ? asset($product->image) : '' }}"
            data-variants='@json($product->variants ?? [])'
            data-add-url="{{ route('customer.cart.add', $product->productID) }}">
            <div class="product-img">
                @if($product->image && $product->image !== 'example.image')
                    <img src="{{ asset($product->image) }}" alt="{{ $product->pName }}">
                @else
                    <span class="product-img-placeholder">💄</span>
                @endif
            </div>
            <div class="product-body">
                <div class="product-cat">{{ $product->category->cName ?? '' }}</div>
                <div class="product-name">{{ $product->pName }}</div>
                <div class="product-desc">{{ $product->descript }}</div>
                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                <form action="{{ route('customer.cart.add', $product->productID) }}" method="POST">
                    @csrf
                    <button type="{{ !empty($product->variants) ? 'button' : 'submit' }}" class="btn-addcart"
                        onclick="@if(!empty($product->variants)) event.preventDefault(); openQuickView(this.closest('.product-card')); @else event.stopPropagation(); @endif"
                        @if($product->stock <= 0) disabled style="opacity:.5;cursor:not-allowed" @endif>
                        @if($product->stock <= 0)
                            Out of Stock
                        @else
                            Add to Cart
                        @endif
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">🔍</div>
            <h3>No products found</h3>
            <p>Try a different category or search term.</p>
        </div>
        @endforelse
    </div>
</section>

<div class="quickview-backdrop" id="quickView" onclick="if(event.target===this)closeQuickView()">
    <div class="quickview-modal">
        <div class="quickview-img" id="quickViewImgWrap">
            <span style="font-size:4rem;opacity:.35;">💄</span>
        </div>
        <div class="quickview-body">
            <div class="quickview-head">
                <div class="quickview-head-left">
                    <div class="product-cat" id="quickViewCat">PRODUCT</div>
                    <h3 class="quickview-title" id="quickViewName"></h3>
                </div>
                <button type="button" class="quickview-close" onclick="closeQuickView()">✕</button>
            </div>
            <p class="quickview-desc" id="quickViewDesc"></p>
            <div class="quickview-price" id="quickViewPrice"></div>
            <form id="quickViewAddForm" method="POST" onsubmit="return handleQuickViewSubmit(event)">
                @csrf
                <input type="hidden" name="variant" id="quickViewVariantInput" value="">
                <div id="quickViewVariants">
                    <div class="variant-label" id="quickViewVariantLabel">Select shade / color</div>
                    <div class="variant-list" id="quickViewVariantList"></div>
                </div>
                <div class="quickview-msg" id="quickViewMsg">Please select a shade or color.</div>
                <button type="submit" class="quickview-add">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openQuickView(card) {
    const modal = document.getElementById('quickView');
    const imgWrap = document.getElementById('quickViewImgWrap');
    const variantsWrap = document.getElementById('quickViewVariants');
    const variantLabel = document.getElementById('quickViewVariantLabel');
    const variantsList = document.getElementById('quickViewVariantList');
    const variantInput = document.getElementById('quickViewVariantInput');
    const quickViewMsg = document.getElementById('quickViewMsg');
    const quickForm = document.getElementById('quickViewAddForm');
    document.getElementById('quickViewName').textContent = card.dataset.name || '';
    document.getElementById('quickViewCat').textContent = (card.dataset.category || 'PRODUCT').toUpperCase();
    document.getElementById('quickViewDesc').textContent = card.dataset.desc || '';
    document.getElementById('quickViewPrice').textContent = card.dataset.price || '';
    quickForm.action = card.dataset.addUrl || '';
    variantInput.value = '';
    quickViewMsg.style.display = 'none';

    if (card.dataset.image) {
        imgWrap.innerHTML = '<img src="' + card.dataset.image + '" alt="">';
    } else {
        imgWrap.innerHTML = '<span style="font-size:4rem;opacity:.35;">💄</span>';
    }

    variantsList.innerHTML = '';
    let variants = [];
    try {
        const rawVariants = card.dataset.variants || '[]';
        const parsed = JSON.parse(rawVariants);
        variants = Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        variants = [];
    }

    variants = variants
        .map(function(v) { return String(v).trim(); })
        .filter(function(v) { return v.length > 0; });

    if (Array.isArray(variants) && variants.length > 0) {
        variantLabel.textContent = 'Select shade / color';
        variants.forEach(function(v) {
            const chip = document.createElement('span');
            chip.className = 'variant-chip';
            chip.textContent = v;
            chip.addEventListener('click', function() {
                variantsList.querySelectorAll('.variant-chip').forEach(function(el) {
                    el.classList.remove('active');
                });
                chip.classList.add('active');
                variantInput.value = v;
                quickViewMsg.style.display = 'none';
            });
            variantsList.appendChild(chip);
        });
    } else {
        variantLabel.textContent = 'Color / shade';
        const empty = document.createElement('span');
        empty.style.fontSize = '0.78rem';
        empty.style.color = 'var(--text-light)';
        empty.textContent = 'No color/shade options for this item.';
        variantsList.appendChild(empty);
    }
    variantsWrap.style.display = 'block';

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    document.getElementById('quickView').classList.remove('open');
    document.body.style.overflow = '';
}

function handleQuickViewSubmit(event) {
    const variantsVisible = document.getElementById('quickViewVariants').style.display !== 'none';
    const selected = document.getElementById('quickViewVariantInput').value.trim();
    if (variantsVisible && !selected) {
        event.preventDefault();
        document.getElementById('quickViewMsg').style.display = 'block';
        return false;
    }
    return true;
}

function closeWelcomeAddressModal() {
    const el = document.getElementById('welcomeAddressModal');
    if (el) el.style.display = 'none';
    document.body.style.overflow = '';
    try { sessionStorage.setItem('hide_welcome_address_modal', '1'); } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('welcomeAddressModal');
    if (!el) return;
    let hide = false;
    try { hide = sessionStorage.getItem('hide_welcome_address_modal') === '1'; } catch (e) { hide = false; }
    if (hide) return;
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
});
</script>
@endpush