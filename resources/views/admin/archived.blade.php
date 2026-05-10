@extends('layouts.admin')
@section('title', 'Archive History')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <p style="font-size:0.875rem;color:var(--text-light);margin-top:0.25rem;">Products removed from the active catalog. Restore to make them available again.</p>
    </div>
    <a href="{{ route('admin.products') }}" class="btn btn-outline btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Products
    </a>
</div>

@if($products->isEmpty())
<div style="text-align:center;padding:5rem 2rem;color:var(--text-light);background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(45,28,66,0.07);">
    <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">🗃️</div>
    <h3 style="font-size:1.5rem;color:var(--text-mid);margin-bottom:.5rem;">No archived items</h3>
    <p>All your products are active.</p>
</div>
@else
<div class="data-table">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock at Archive</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.85rem;">
                            <div style="width:44px;height:44px;border-radius:8px;background:var(--porcelain-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem;overflow:hidden;">
                                @if($product->image && $product->image !== 'example.image')
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->pName }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    💄
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--violet-night);font-size:.9rem;">{{ $product->pName }}</div>
                                <div style="font-size:.75rem;color:var(--text-light);margin-top:.1rem;">ID #{{ $product->productID }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.85rem;color:var(--text-mid);">{{ $product->category->cName ?? '—' }}</td>
                    <td style="font-weight:600;color:var(--violet-mid);">₱{{ number_format($product->price, 2) }}</td>
                    <td style="font-size:.85rem;color:var(--text-mid);">{{ $product->stock }} units</td>
                    <td>
                        <form action="{{ route('admin.products.unarchive', $product->productID) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm" style="background:rgba(76,175,135,0.12);color:#1e6048;border:1px solid rgba(76,175,135,0.25);">
                                Restore
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{--pagination (same bar as Orders / Customers / Manage Products)--}}
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
@endsection
