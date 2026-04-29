@extends('layouts.admin')
@section('title', 'Manage Products')

@push('styles')
<style>
    .page-actions { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
    .page-actions h1 { font-size:1.75rem; font-weight:700; color:var(--violet-night); }
    .page-actions-btns { display:flex; gap:0.75rem; }
    #addModal .modal,
    #editModal .modal {
        max-height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
    }
    #addModal .modal form,
    #editModal .modal form {
        display: flex;
        flex-direction: column;
        min-height: 0;
        flex: 1;
    }
    #addModal .modal-body,
    #editModal .modal-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
    }

    /* manage products w mobile reponsive*/
    @media (max-width: 768px) {
        .page-actions { flex-direction: column !important; align-items: flex-start !important; gap: 0.75rem !important; }
        .page-actions h1 { font-size: 1.4rem !important; }
        .page-actions-btns { width: 100%; flex-wrap: wrap; }
        .products-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 0.85rem !important; }
    }
    @media (max-width: 480px) {
        .products-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div class="page-actions">
    <h1>Manage Products</h1>
    <div class="page-actions-btns">
        <a href="{{ route('admin.archived') }}" class="btn btn-outline btn-sm">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
            Archive Items
        </a>
        <button class="btn btn-primary btn-sm" onclick="openModal('addModal')">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </button>
    </div>
</div>

{{--products--}}
@if($products->isEmpty())
<div style="text-align:center;padding:5rem 2rem;color:var(--text-light);">
    <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">📦</div>
    <h3 style="font-size:1.5rem;color:var(--text-mid);margin-bottom:.5rem;">No products yet</h3>
    <p>Add your first product to get started.</p>
</div>
@else
<div class="products-grid">
    @foreach($products as $product)
    <div class="product-card">
        <div class="product-card-img">
            @if($product->image && $product->image !== 'example.image')
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->pName }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <span style="font-size:2.5rem;opacity:.3;">💄</span>
            @endif
        </div>
        <div class="product-card-body">
            <div class="product-cat-label">{{ strtoupper($product->category->cName ?? '') }}</div>
            <div class="product-card-name">{{ $product->pName }}</div>
            <div class="product-card-price">₱{{ number_format($product->price, 2) }}</div>
            @if(!empty($product->variants))
                <div style="font-size:0.72rem;color:var(--text-light);margin-bottom:0.45rem;">
                    {{ implode(' · ', $product->variants) }}
                </div>
            @endif
            @php
                $stock = (int) $product->stock;
                if ($stock <= 0) { $stockText = "Out of Stock (0)"; $stockColor = 'var(--danger)'; }
                elseif ($stock <= 9) { $stockText = "Low Stock ({$stock})"; $stockColor = 'var(--warning)'; }
                elseif ($stock > 10) { $stockText = "In Stock ({$stock})"; $stockColor = 'var(--success)'; }
                else { $stockText = "In Stock ({$stock})"; $stockColor = 'var(--success)'; }
            @endphp
            <div class="product-stock" style="color:{{ $stockColor }};">
                {{ $stockText }}
            </div>
            <div class="product-card-actions">
                <form action="{{ route('admin.products.archive', $product->productID) }}" method="POST" style="flex:1;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-archive-card" onclick="return confirm('Archive {{ addslashes($product->pName) }}?')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                        Archive
                    </button>
                </form>
                <button class="btn btn-outline btn-sm" style="flex:1;"
                    onclick="openEditModal({{ $product->productID }}, '{{ addslashes($product->pName) }}', '{{ $product->categoryID }}', {{ $product->price }}, {{ $product->stock }}, '{{ addslashes($product->descript) }}', '{{ addslashes(implode(', ', $product->variants ?? [])) }}')">
                    Edit
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{--add product modal--}}
<div class="modal-backdrop" id="addModal" style="display:none;" onclick="if(event.target===this)closeModal('addModal')">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Product</h3>
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        </div>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                @if(session('openAdd') && $errors->any())
                <div style="background:rgba(217,95,95,0.1);border:1px solid rgba(217,95,95,0.3);border-radius:8px;padding:0.75rem 1rem;font-size:0.82rem;color:#9b3535;margin-bottom:1rem;">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
                @endif
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="pName" class="form-control" placeholder="e.g. Velvet Lip Rouge" value="{{ session('openAdd') ? old('pName') : '' }}" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <div class="category-selector" id="addCatSelector">
                        @foreach($categories as $cat)
                        <button type="button" class="cat-btn {{ session('openAdd') && old('categoryID') == $cat->categoryID ? 'active' : '' }}" data-id="{{ $cat->categoryID }}" onclick="selectCat(this,'addCatInput')">{{ $cat->cName }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="categoryID" id="addCatInput" value="{{ session('openAdd') ? old('categoryID') : '' }}" required>
                </div>
                <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;">
                    <div>
                        <label>Price (₱)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" value="{{ session('openAdd') ? old('price') : '' }}" required min="0">
                    </div>
                    <div>
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control" placeholder="0" value="{{ session('openAdd') ? old('stock') : '' }}" required min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="descript" class="form-control" rows="3" placeholder="Brief product description…" required>{{ session('openAdd') ? old('descript') : '' }}</textarea>
                </div>
                <div class="form-group">
                    <label>Product Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <small style="color:var(--text-light);font-size:0.75rem;">Upload JPG, PNG, or WEBP (max 2MB).</small>
                </div>
                <div class="form-group">
                    <label>Colors / Shades / Tones (optional)</label>
                    <input type="text" name="variants" class="form-control" placeholder="e.g. Ivory, Beige, Honey, Rose Nude" value="{{ session('openAdd') ? old('variants') : '' }}">
                    <small style="color:var(--text-light);font-size:0.75rem;">Enter options separated by commas. These will appear as selectable color/shade chips to customers.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Add Product</button>
            </div>
        </form>
    </div>
</div>

{{--edit product modal--}}
<div class="modal-backdrop" id="editModal" style="display:none;" onclick="if(event.target===this)closeModal('editModal')">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="modal-body">
                @if(session('openEdit') && $errors->any())
                <div style="background:rgba(217,95,95,0.1);border:1px solid rgba(217,95,95,0.3);border-radius:8px;padding:0.75rem 1rem;font-size:0.82rem;color:#9b3535;margin-bottom:1rem;">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
                @endif
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="pName" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <div class="category-selector" id="editCatSelector">
                        @foreach($categories as $cat)
                        <button type="button" class="cat-btn" data-id="{{ $cat->categoryID }}" onclick="selectCat(this,'editCatInput')">{{ $cat->cName }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="categoryID" id="editCatInput" required>
                </div>
                <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;">
                    <div>
                        <label>Price (₱)</label>
                        <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required min="0">
                    </div>
                    <div>
                        <label>Stock</label>
                        <input type="number" name="stock" id="editStock" class="form-control" required min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="descript" id="editDesc" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Product Image (optional)</label>
                    <input type="file" name="image" id="editImage" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <small style="color:var(--text-light);font-size:0.75rem;">Upload a new image to replace the current one (max 2MB).</small>
                </div>
                <div class="form-group">
                    <label>Colors / Shades / Tones (optional)</label>
                    <input type="text" name="variants" id="editVariants" class="form-control" placeholder="e.g. Rose, Coral, Nude, Mauve">
                    <small style="color:var(--text-light);font-size:0.75rem;">Enter options separated by commas. These will appear as selectable color/shade chips to customers.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
function selectCat(btn, inputId) {
    btn.closest('.category-selector').querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(inputId).value = btn.dataset.id;
}
function openEditModal(id, name, catId, price, stock, desc, variants) {
    document.getElementById('editForm').action = `/admin/products/${id}`;
    document.getElementById('editName').value = name;
    document.getElementById('editPrice').value = price;
    document.getElementById('editStock').value = stock;
    document.getElementById('editDesc').value = desc;
    document.getElementById('editVariants').value = variants || '';
    document.querySelectorAll('#editCatSelector .cat-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.id == catId);
    });
    document.getElementById('editCatInput').value = catId;
    openModal('editModal');
}
@if(session('openAdd')) openModal('addModal'); @endif
@if(session('openEdit'))
(function() {
    @foreach($products as $p)
        @if($p->productID == session('openEdit'))
            openEditModal(
                {{ $p->productID }},
                '{{ addslashes(old('pName', $p->pName)) }}',
                '{{ old('categoryID', $p->categoryID) }}',
                {{ old('price', $p->price) }},
                {{ old('stock', $p->stock) }},
                '{{ addslashes(old('descript', $p->descript)) }}',
                '{{ addslashes(old('variants', implode(', ', $p->variants ?? []))) }}'
            );
        @endif
    @endforeach
})();
@endif
</script>
@endpush
