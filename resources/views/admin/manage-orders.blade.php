@extends('layouts.admin')
@section('title', 'Manage Orders')

@push('styles')
<style>
    .admin-main {
        overflow-y: visible;
    }
    .btn-view-icon {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: 1.5px solid #d8cce0;
        background: #fff;
        color: var(--violet-night);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.18s;
    }
    .btn-view-icon:hover {
        border-color: var(--violet-mid);
        background: #faf7fc;
    }
    /* Match Add Product modal: same max width, flex column, scroll body */
    #orderDetailsModal .modal {
        width: 100%;
        max-width: 460px;
        max-height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
    }
    #orderDetailsModal .modal-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 1.5rem;
    }
    #orderDetailsModal .modal-fieldset-heading {
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text-mid);
        margin: 0 0 0.85rem;
    }
    #orderDetailsModal .order-modal-block {
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--porcelain-light);
    }
    #orderDetailsModal .order-modal-block:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    #orderDetailsModal .form-control-static.order-total-emphasis {
        font-weight: 700;
        font-size: 1rem;
        color: var(--violet-night);
        background: var(--porcelain-light);
    }
    #orderDetailsModal .modal-footer {
        justify-content: stretch;
    }
    #orderDetailsModal .order-detail-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
    }
    .manage-orders-tabs {
        display: flex;
        align-items: flex-end;
        gap: 0;
        flex: 1;
        min-width: 0;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        border-bottom: 2px solid rgba(216, 204, 224, 0.4);
        padding-bottom: 0;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }
    .manage-orders-tabs::-webkit-scrollbar {
        height: 6px;
    }
    .manage-orders-tabs::-webkit-scrollbar-thumb {
        background: rgba(45, 28, 66, 0.25);
        border-radius: 4px;
    }
    .manage-orders-page .data-table-head form {
        flex: 0 0 auto;
        min-width: 0;
    }
    .manage-orders-page .admin-orders-table {
        width: 100%;
        min-width: 980px;
        table-layout: fixed;
    }
    .manage-orders-page .admin-orders-table th,
    .manage-orders-page .admin-orders-table td {
        overflow-wrap: anywhere;
        word-break: break-word;
        vertical-align: top;
    }
    .manage-orders-page .admin-orders-table th:nth-child(1),
    .manage-orders-page .admin-orders-table td:nth-child(1) { width: 10%; }
    .manage-orders-page .admin-orders-table th:nth-child(2),
    .manage-orders-page .admin-orders-table td:nth-child(2) { width: 14%; }
    .manage-orders-page .admin-orders-table th:nth-child(3),
    .manage-orders-page .admin-orders-table td:nth-child(3) { width: 13%; }
    .manage-orders-page .admin-orders-table th:nth-child(4),
    .manage-orders-page .admin-orders-table td:nth-child(4) { width: 24%; }
    .manage-orders-page .admin-orders-table th:nth-child(5),
    .manage-orders-page .admin-orders-table td:nth-child(5) { width: 9%; }
    .manage-orders-page .admin-orders-table th:nth-child(6),
    .manage-orders-page .admin-orders-table td:nth-child(6) { width: 11%; }
    .manage-orders-page .admin-orders-table th:nth-child(7),
    .manage-orders-page .admin-orders-table td:nth-child(7) { width: 10%; }
    .manage-orders-page .admin-orders-table th:nth-child(8),
    .manage-orders-page .admin-orders-table td:nth-child(8) { width: 9%; text-align: center; }
    .manage-orders-page .admin-orders-table td:nth-child(8) { vertical-align: middle; }
    @media (max-width: 640px) {
        .manage-orders-page h1[style] { font-size: 1.45rem !important; }
        #orderDetailsModal .order-detail-grid-2 { grid-template-columns: 1fr; }
        #orderDetailsModal .modal-footer .form-control { min-width: 0; }
        #orderDetailsModal .modal {
            width: 100%;
            max-width: 100%;
            max-height: 100vh;
            border-radius: 12px 12px 0 0;
        }
        #orderDetailsModal .modal-body {
            padding: 1.25rem 1rem;
        }
        #orderDetailsModal .modal-header {
            padding: 1rem 1rem;
        }
        #orderDetailsModal .modal-footer {
            flex-direction: column;
            align-items: stretch;
        }
    }
    .status-text {
        font-size: 0.82rem;
        font-weight: 600;
    }
    .status-text.pending { color: #ff8a00; }
    .status-text.accepted { color: #2f6bff; }
    .status-text.shipped { color: #7b4dff; }
    .status-text.delivered { color: #22c55e; }
    .status-text.canceled { color: #ff2f2f; }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 400px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card-custom {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(45,28,66,0.07);
        padding: 1.1rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-icon {
        flex-shrink: 0;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .stat-icon.orders   { background: #eef6ff; color: #2563eb; }
    .stat-icon.customers{ background: #f0fdf4; color: #16a34a; }
    .stat-body { flex: 1; min-width: 0; }
    .stat-big {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: var(--violet-night);
        text-align: right;
    }
    .stat-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-light);
        margin-top: 0.25rem;
        text-align: right;
    }

    /*manage orders w mobile responsive*/
    @media (max-width: 768px) {
        .page-heading-section h1 { font-size: 1.4rem !important; }
        .data-table-head { flex-direction: column !important; align-items: stretch !important; gap: 0.5rem; }
        .data-table-head form { width: 100% !important; margin-left: 0 !important; }
        .filter-tabs { overflow-x: auto; flex-wrap: nowrap !important; -webkit-overflow-scrolling: touch; }
        .filter-tab  { white-space: nowrap; }
        .table-wrap  { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table        { min-width: 560px; }
    }
</style>
@endpush

@section('content')
<div class="manage-orders-page">
{{--page heading--}}
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.75rem;font-weight:700;color:var(--violet-night);margin-bottom:0.25rem;">Manage Orders</h1>
    <p style="color:var(--text-light);font-size:0.875rem;">Overview of all orders by status.</p>
</div>

{{--analytics--}}
<div class="stats-grid">

    <div class="stat-card-custom">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ (int)($analytics['Pending'] ?? 0) }}</div>
            <div class="stat-title">Pending</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon orders">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ (int)($analytics['Accepted'] ?? 0) }}</div>
            <div class="stat-title">Accepted</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon" style="background:#f3eeff;color:#7c3aed;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l3-1 2 1 2-1 3 1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11h5l1 9H3l1-9h3"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ (int)($analytics['Shipped'] ?? 0) }}</div>
            <div class="stat-title">Shipped</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon customers">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ (int)($analytics['Delivered'] ?? 0) }}</div>
            <div class="stat-title">Delivered</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon" style="background:#fef2f2;color:#dc2626;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ (int)($analytics['Canceled'] ?? 0) }}</div>
            <div class="stat-title">Canceled</div>
        </div>
    </div>

</div>

{{--table--}}
<div class="data-table">
    <div class="data-table-head" style="border-bottom:1px solid var(--porcelain-light);">
        <div class="manage-orders-tabs">
            @foreach(['all' => 'ALL', 'pending' => 'PENDING', 'accepted' => 'ACCEPTED', 'shipped' => 'SHIPPED', 'delivered' => 'DELIVERED', 'canceled' => 'CANCELED'] as $val => $lbl)
            <a href="{{ route('admin.orders', array_merge(request()->query(), ['status' => $val])) }}"
               style="flex-shrink:0;padding:0.55rem 1.1rem;text-decoration:none;font-size:0.8rem;font-weight:{{ (request('status','all') === $val) ? '700' : '500' }};color:{{ (request('status','all') === $val) ? 'var(--violet-night)' : 'var(--text-mid)' }};border-bottom:3px solid {{ (request('status','all') === $val) ? 'var(--violet-night)' : 'transparent' }};margin-bottom:-2px;white-space:nowrap;transition:all 0.18s;letter-spacing:0.04em;">{{ $lbl }}</a>
            @endforeach
        </div>
        <form action="{{ route('admin.orders') }}" method="GET" style="display:flex;align-items:center;gap:0.5rem;margin-left:auto;flex-shrink:0;">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" class="search-input" placeholder="Search order # or customer…" value="{{ request('search') }}" style="border-radius:4px;">
            <button type="submit" style="background:var(--violet-night);border:none;border-radius:4px;padding:0.48rem 0.75rem;cursor:pointer;color:#fff;display:flex;align-items:center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/></svg>
            </button>
        </form>
    </div>

    <div class="manage-orders-table-wrap" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table class="admin-orders-table">
            <thead>
                <tr>
                    <th>ORDER #</th>
                    <th>CUSTOMER</th>
                    <th>ITEM/S</th>
                    <th>ADDRESS</th>
                    <th>TOTAL</th>
                    <th>PAYMENT</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <span class="order-num">#{{ $order->orderID }}</span>
                        <div style="font-size:0.75rem;color:var(--text-light);margin-top:2px;">{{ \Carbon\Carbon::parse($order->createdAt)->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <span style="font-weight:500;">{{ $order->user->firstName ?? '' }} {{ $order->user->lastName ?? '' }}</span>
                        <div style="font-size:0.75rem;color:var(--text-light);">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:0.85rem;color:var(--violet-night);">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}</div>
                        <div class="items-list">
                            @foreach($order->items->take(2) as $item)
                                {{ $item->product->pName ?? 'Product' }}<br>
                            @endforeach
                            @if($order->items->count() > 2)
                                <span style="color:var(--text-light);">+{{ $order->items->count() - 2 }} more</span>
                            @endif
                        </div>
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-mid);line-height:1.5;">
                        <div style="font-weight:600;color:var(--violet-night);">{{ $order->deliveryAdd }}</div>
                        <div>{{ $order->city }}, {{ $order->province }}</div>
                        <div style="color:var(--text-light);">{{ $order->country }}@if($order->postal) • {{ $order->postal }}@endif</div>
                    </td>
                    <td style="font-weight:600;color:var(--violet-mid);">₱{{ number_format($order->total, 2) }}</td>
                    <td style="font-size:0.825rem;">{{ $order->paymentMethod === 'cod' ? 'Cash on Delivery' : 'GCash' }}</td>
                    <td>
                        @php
                            $sc = strtolower($order->status);
                            $itemsSummary = $order->items->map(function ($item) {
                                $name = $item->product->pName ?? 'Product';
                                return $name . ' x' . $item->quantity;
                            })->implode("\n");
                            $customerName = trim(($order->user->firstName ?? '') . ' ' . ($order->user->lastName ?? ''));
                        @endphp
                        <span class="status-text {{ $sc }}">{{ $order->status }}</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn-view-icon"
                            title="View order #{{ $order->orderID }}"
                            aria-label="View order #{{ $order->orderID }}"
                            onclick="openOrderModal(this)"
                            data-order-id="{{ $order->orderID }}"
                            data-order-date="{{ \Carbon\Carbon::parse($order->createdAt)->format('M d, Y h:i A') }}"
                            data-customer="{{ $customerName ?: 'N/A' }}"
                            data-contact="{{ $order->contactNo ?? ($order->user->phone ?? 'N/A') }}"
                            data-payment-method="{{ $order->paymentMethod }}"
                            data-reference-number="{{ $order->referenceNumber ?? 'N/A' }}"
                            data-sender-name="{{ $order->gcashName ?? 'N/A' }}"
                            data-gcash-number="{{ $order->gcashNumber ?? 'N/A' }}"
                            data-status="{{ $order->status }}"
                            data-status-lower="{{ strtolower($order->status) }}"
                            data-status-url="{{ route('admin.orders.status', $order->orderID) }}"
                            data-total="{{ number_format($order->total, 2) }}"
                            data-address="{{ $order->deliveryAdd }}, {{ $order->city }}, {{ $order->province }}, {{ $order->country }}{{ $order->postal ? ' ' . $order->postal : '' }}"
                            data-items="{{ $itemsSummary }}"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s3.8-7 10.5-7 10.5 7 10.5 7-3.8 7-10.5 7S1.5 12 1.5 12z"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--text-light);">
                        <div style="font-size:2rem;margin-bottom:.75rem;opacity:.3;">📋</div>
                        <div>No orders found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--pagination--}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1.5rem;background:var(--violet-night);color:var(--porcelain);">
        @if($orders->onFirstPage())
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">← Previous</span>
        @else
            <a href="{{ $orders->previousPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;transition:opacity 0.18s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">← Previous</a>
        @endif
        <span style="font-size:0.82rem;">Page {{ $orders->currentPage() }}</span>
        @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;transition:opacity 0.18s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Next →</a>
        @else
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">Next →</span>
        @endif
    </div>
</div>

<div class="modal-backdrop" id="orderDetailsModal" style="display:none;" onclick="closeOrderModal(event)">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalOrderTitle">Order Details</h3>
            <button type="button" class="modal-close" onclick="closeOrderModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="order-modal-block">
                <p class="modal-fieldset-heading">Order details</p>
                <div class="form-group">
                    <label>Item/s</label>
                    <div class="form-control-static" id="modalItems"></div>
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <div class="form-control-static order-total-emphasis" id="modalTotal"></div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Date</label>
                    <div class="form-control-static" id="modalDate"></div>
                </div>
            </div>

            <div class="order-modal-block">
                <p class="modal-fieldset-heading">Customer details</p>
                <div class="order-detail-grid-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Name</label>
                        <div class="form-control-static" id="modalCustomer"></div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Number</label>
                        <div class="form-control-static" id="modalContact"></div>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Address</label>
                    <div class="form-control-static" id="modalAddress"></div>
                </div>
            </div>

            <div class="order-modal-block" id="modalPaymentSection">
                <p class="modal-fieldset-heading">Payment details</p>
                <div class="order-detail-grid-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Sender name</label>
                        <div class="form-control-static" id="modalSenderName"></div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>GCash number</label>
                        <div class="form-control-static" id="modalGcashNumber"></div>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Reference number</label>
                    <div class="form-control-static" id="modalReferenceNumber"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <form id="modalStatusForm" method="POST" style="width:100%;">
                @csrf
                @method('PATCH')
                <div class="form-group" style="margin-bottom:0;">
                    <label for="modalStatusSelect">Update order status</label>
                    <div style="display:flex;gap:0.75rem;align-items:flex-end;flex-wrap:wrap;">
                        <select name="status" id="modalStatusSelect" class="form-control" style="flex:1;min-width:0;">
                            @foreach(['Pending', 'Accepted', 'Shipped', 'Delivered', 'Canceled'] as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
function openOrderModal(button) {
    document.getElementById('modalOrderTitle').textContent = 'Order #' + (button.dataset.orderId || '');
    document.getElementById('modalCustomer').textContent = button.dataset.customer || 'N/A';
    document.getElementById('modalContact').textContent = button.dataset.contact || 'N/A';
    document.getElementById('modalDate').textContent = button.dataset.orderDate || 'N/A';
    document.getElementById('modalTotal').textContent = '₱' + (button.dataset.total || '0.00');
    document.getElementById('modalReferenceNumber').textContent = button.dataset.referenceNumber || '—';
    document.getElementById('modalSenderName').textContent = button.dataset.senderName || '—';
    document.getElementById('modalGcashNumber').textContent = button.dataset.gcashNumber || '—';
    document.getElementById('modalAddress').textContent = button.dataset.address || 'N/A';
    document.getElementById('modalItems').textContent = button.dataset.items || 'No items.';
    document.getElementById('modalStatusForm').action = button.dataset.statusUrl || '';
    document.getElementById('modalStatusSelect').value = button.dataset.status || 'Pending';
    const isCashOnDelivery = (button.dataset.paymentMethod || '') === 'cod';
    document.getElementById('modalPaymentSection').style.display = isCashOnDelivery ? 'none' : 'block';
    const modal = document.getElementById('orderDetailsModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeOrderModal(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (!event || event.target === modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}
</script>
@endsection