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

    .manage-products-page .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .manage-products-page .admin-products-table { min-width: 920px; table-layout: fixed; width: 100%; }
    .manage-products-page .admin-products-table th,
    .manage-products-page .admin-products-table td { vertical-align: middle; overflow-wrap: anywhere; word-break: break-word; }
    .manage-products-page .col-thumb { width: 56px; }
    .manage-products-page .col-name { width: 22%; }
    .manage-products-page .col-cat { width: 14%; }
    .manage-products-page .col-price { width: 10%; }
    .manage-products-page .col-stock { width: 12%; }
    .manage-products-page .col-variants { width: 22%; }
    .manage-products-page .col-actions { width: 18%; text-align: right; }
    .manage-products-page .prod-thumb {
        width: 44px; height: 44px; border-radius: 10px; overflow: hidden;
        background: var(--porcelain-light); display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; opacity: 0.45;
    }
    .manage-products-page .prod-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .manage-products-page .prod-name { font-weight: 600; color: var(--violet-night); }
    .manage-products-page .variants-cell { font-size: 0.8rem; color: var(--text-mid); line-height: 1.35; max-height: 2.7em; overflow: hidden; }
    .manage-products-page.data-table { max-width: 100%; min-width: 0; }
    @media (max-width: 768px) {
        .page-actions { flex-direction: column !important; align-items: flex-start !important; gap: 0.75rem !important; }
        .page-actions h1 { font-size: 1.4rem !important; }
        .page-actions-btns { width: 100%; flex-wrap: wrap; }
        #addModal .modal,
        #editModal .modal {
            max-height: min(92dvh, 100%);
        }
        .manage-products-page .table-wrap {
            margin: 0 -0.25rem;
            padding: 0 0.25rem;
        }
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

{{--products (table + pagination)--}}
@if($products->isEmpty())
<div style="text-align:center;padding:5rem 2rem;color:var(--text-light);">
    <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">📦</div>
    <h3 style="font-size:1.5rem;color:var(--text-mid);margin-bottom:.5rem;">No products yet</h3>
    <p>Add your first product to get started.</p>
</div>
@else
<div class="data-table manage-products-page">
    <div class="table-wrap">
        <table class="admin-products-table">
            <thead>
                <tr>
                    <th class="col-thumb"></th>
                    <th class="col-name">Product</th>
                    <th class="col-cat">Category</th>
                    <th class="col-price">Price</th>
                    <th class="col-stock">Stock</th>
                    <th class="col-variants">Variants</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                @php
                    $stock = (int) $product->stock;
                    if ($stock <= 0) { $stockText = 'Out of Stock (0)'; $stockColor = 'var(--danger)'; }
                    elseif ($stock <= 9) { $stockText = "Low Stock ({$stock})"; $stockColor = 'var(--warning)'; }
                    else { $stockText = "In Stock ({$stock})"; $stockColor = 'var(--success)'; }
                    $variantStr = !empty($product->variants) ? implode(', ', $product->variants) : '—';
                    $editPayload = [
                        'id' => $product->productID,
                        'name' => $product->pName,
                        'categoryID' => $product->categoryID,
                        'price' => (float) $product->price,
                        'stock' => (int) $product->stock,
                        'descript' => $product->descript,
                        'variants' => implode(', ', $product->variants ?? []),
                    ];
                @endphp
                <tr>
                    <td class="col-thumb">
                        <div class="prod-thumb">
                            @if($product->image && $product->image !== 'example.image')
                                <img src="{{ asset($product->image) }}" alt="">
                            @else
                                <span aria-hidden="true">💄</span>
                            @endif
                        </div>
                    </td>
                    <td class="col-name">
                        <div class="prod-name">{{ $product->pName }}</div>
                    </td>
                    <td class="col-cat">{{ $product->category->cName ?? '—' }}</td>
                    <td class="col-price">₱{{ number_format($product->price, 2) }}</td>
                    <td class="col-stock"><span style="font-weight:600;font-size:0.82rem;color:{{ $stockColor }};">{{ $stockText }}</span></td>
                    <td class="col-variants">
                        <div class="variants-cell" title="{{ $variantStr }}">{{ $variantStr }}</div>
                    </td>
                    <td class="col-actions">
                        <div class="action-btns" style="justify-content:flex-end;">
                            <form action="{{ route('admin.products.archive', $product->productID) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Archive {{ addslashes($product->pName) }}?')">Archive</button>
                            </form>
                            <button type="button" class="btn btn-outline btn-sm open-edit-product" data-product='@json($editPayload)'>Edit</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{--pagination (same bar as Orders / Customers)--}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1.5rem;background:var(--violet-night);color:var(--porcelain);">
        @if($products->onFirstPage())
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">← Previous</span>
        @else
            <a href="{{ $products->previousPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;transition:opacity 0.18s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">← Previous</a>
        @endif
        <span style="font-size:0.82rem;">Page {{ $products->currentPage() }}</span>
        @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;transition:opacity 0.18s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Next →</a>
        @else
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">Next →</span>
        @endif
    </div>
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
function openEditProductFromPayload(p) {
    document.getElementById('editForm').action = '/admin/products/' + p.id;
    document.getElementById('editName').value = p.name;
    document.getElementById('editPrice').value = p.price;
    document.getElementById('editStock').value = p.stock;
    document.getElementById('editDesc').value = p.descript;
    document.getElementById('editVariants').value = p.variants || '';
    document.querySelectorAll('#editCatSelector .cat-btn').forEach(function (el) {
        el.classList.toggle('active', el.dataset.id == p.categoryID);
    });
    document.getElementById('editCatInput').value = p.categoryID;
    openModal('editModal');
}
function openEditProduct(btn) {
    openEditProductFromPayload(JSON.parse(btn.dataset.product));
}
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.open-edit-product');
    if (btn) {
        e.preventDefault();
        openEditProduct(btn);
    }
});
@if(session('openAdd')) openModal('addModal'); @endif
@if(session('openEdit'))
(function() {
    @foreach($products as $p)
        @if($p->productID == session('openEdit'))
            @php
                $openEditPayload = [
                    'id' => $p->productID,
                    'name' => old('pName', $p->pName),
                    'categoryID' => old('categoryID', $p->categoryID),
                    'price' => (float) old('price', $p->price),
                    'stock' => (int) old('stock', $p->stock),
                    'descript' => old('descript', $p->descript),
                    'variants' => old('variants', implode(', ', $p->variants ?? [])),
                ];
            @endphp
            openEditProductFromPayload(@json($openEditPayload));
        @endif
    @endforeach
})();
@endif
</script>
@endpush
