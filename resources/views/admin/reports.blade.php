@extends('layouts.admin')
@section('title', 'Reports')

@push('styles')
<style>
    .admin-main { overflow-y: visible; }

    .reports-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
    }
    .reports-summary-card {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        background: #fff;
        border-radius: 10px;
        padding: 1rem 1.2rem;
        box-shadow: 0 2px 10px rgba(45,28,66,0.07);
    }
    .sum-val {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--violet-night);
        line-height: 1;
    }
    .sum-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-light);
        margin-top: 0.25rem;
    }

    .badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-pending   { background: #fef3c7; color: #92400e; }
    .badge-accepted  { background: #dbeafe; color: #1e3a8a; }
    .badge-delivered { background: #d1fae5; color: #064e3b; }
    .badge-canceled  { background: #fee2e2; color: #7f1d1d; }
    .badge-shipped   { background: #ede9fe; color: #4c1d95; }

    tr.best-day td {
        background: #f3f0ff !important;
    }
    .best-day-tag {
        display: inline-block;
        margin-left: 0.4rem;
        padding: 1px 7px;
        border-radius: 99px;
        font-size: 0.65rem;
        font-weight: 700;
        background: var(--violet-night);
        color: #fff;
        letter-spacing: 0.04em;
        vertical-align: middle;
    }

    .top-customers { margin-top: 1.5rem; }
    .top-customers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.75rem;
        margin-top: 0.75rem;
    }
    .customer-card {
        background: #fff;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        box-shadow: 0 2px 8px rgba(45,28,66,0.06);
        text-align: center;
    }
    .customer-card .name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--violet-night);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .customer-card .spent {
        font-size: 1rem;
        font-weight: 700;
        color: var(--violet-night);
        margin-top: 0.25rem;
    }
    .customer-card .orders-count {
        font-size: 0.7rem;
        color: var(--text-light);
        margin-top: 0.15rem;
    }

    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.5rem;
        background: var(--violet-night);
        color: var(--porcelain);
    }
    .pagination-bar a {
        color: var(--porcelain);
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 500;
        transition: opacity 0.15s;
    }
    .pagination-bar a:hover { opacity: 0.7; }
    .pagination-bar span { font-size: 0.82rem; }
    .pagination-bar .disabled {
        color: rgba(233,213,230,0.4);
        font-size: 0.82rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')

<div class="page-heading-section" style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.75rem;font-weight:700;color:var(--violet-night);margin-bottom:0.25rem;">Reports</h1>
    <p style="color:var(--text-light);font-size:0.875rem;">Generate and download PDF reports filtered by date range.</p>
</div>

{{--filter--}}
<div class="reports-filter-card">
    <form method="GET" action="{{ route('admin.reports') }}" id="reportFilterForm">
        <div class="reports-filter-row">
            <div class="reports-filter-group">
                <label for="date_from">From</label>
                <input type="date" id="date_from" name="date_from"
                    value="{{ request('date_from', now('Asia/Manila')->startOfMonth()->toDateString()) }}">
            </div>
            <div class="reports-filter-group">
                <label for="date_to">To</label>
                <input type="date" id="date_to" name="date_to"
                    value="{{ request('date_to', now('Asia/Manila')->toDateString()) }}">
            </div>
            <div class="reports-filter-group">
                <label for="report_type">Report Type</label>
                <select id="report_type" name="report_type">
                    <option value="sales" {{ request('report_type', 'sales') === 'sales' ? 'selected' : '' }}>
                        Sales Summary
                    </option>
                    <option value="orders" {{ request('report_type') === 'orders' ? 'selected' : '' }}>
                        Orders List
                    </option>
                </select>
            </div>
            <div style="display:flex;gap:0.6rem;align-items:flex-end;flex-wrap:wrap;">
                <button type="submit" class="btn-reports-generate">Apply Filter</button>
                <button type="button" class="btn-reports-pdf" onclick="downloadPdf()">Download PDF</button>
            </div>
        </div>
        <div style="margin-top:0.75rem;font-size:0.8rem;color:var(--text-light);">
            Showing results from
            <strong>{{ \Carbon\Carbon::parse(request('date_from', now('Asia/Manila')->startOfMonth()))->format('M d, Y') }}</strong>
            to
            <strong>{{ \Carbon\Carbon::parse(request('date_to', now('Asia/Manila')))->format('M d, Y') }}</strong>
        </div>
    </form>
</div>

{{--summary cards--}}
<div class="reports-summary-grid">
    <div class="reports-summary-card">
        <div class="sum-val">₱{{ number_format($totalRevenue, 2) }}</div>
        <div class="sum-label">Total Revenue</div>
    </div>
    <div class="reports-summary-card">
        <div class="sum-val">{{ number_format($totalOrders) }}</div>
        <div class="sum-label">Total Orders</div>
    </div>
    <div class="reports-summary-card">
        <div class="sum-val">{{ number_format($completedOrders) }}</div>
        <div class="sum-label">Delivered</div>
    </div>
    <div class="reports-summary-card">
        <div class="sum-val">{{ number_format($canceledOrders) }}</div>
        <div class="sum-label">Canceled</div>
    </div>
</div>

{{--sales summary--}}
@if($reportType === 'sales')

    @php
        $bestDay = $dailySales->sortByDesc('revenue')->first();
    @endphp

    <div class="data-table">
        <div class="data-table-head" style="border-bottom:1px solid var(--porcelain-light);">
            <div>
                <div style="font-size:0.95rem;font-weight:700;color:var(--violet-night);">Sales Summary</div>
                <div style="font-size:0.78rem;color:var(--text-light);margin-top:0.15rem;">
                    Daily breakdown of revenue and average order value. Use this to spot your best and slowest days.
                </div>
            </div>
            <div style="font-size:0.8rem;color:var(--text-light);">{{ $dailySales->total() }} day(s) with activity</div>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>ORDERS</th>
                        <th>REVENUE</th>
                        <th>AVG. ORDER VALUE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailySales as $day)
                        @php
                            $avg = $day->order_count > 0 ? $day->revenue / $day->order_count : 0;
                            $isBest = $bestDay && $day->sale_date === $bestDay->sale_date;
                        @endphp
                        <tr class="{{ $isBest ? 'best-day' : '' }}">
                            <td>
                                {{ \Carbon\Carbon::parse($day->sale_date)->format('M d, Y') }}
                                @if($isBest)
                                    <span class="best-day-tag">Best Day</span>
                                @endif
                            </td>
                            <td>{{ $day->order_count }}</td>
                            <td style="font-weight:600;">₱{{ number_format($day->revenue, 2) }}</td>
                            <td style="color:var(--text-light);">₱{{ number_format($avg, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:3rem;color:var(--text-light);">
                                No sales data for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{--pagination--}}
        @php $salesQuery = http_build_query(request()->except('page')); @endphp
        <div class="pagination-bar">
            @if($dailySales->onFirstPage())
                <span class="disabled">← Previous</span>
            @else
                <a href="{{ $dailySales->previousPageUrl() }}&{{ $salesQuery }}">← Previous</a>
            @endif
            <span>Page {{ $dailySales->currentPage() }} of {{ $dailySales->lastPage() }}</span>
            @if($dailySales->hasMorePages())
                <a href="{{ $dailySales->nextPageUrl() }}&{{ $salesQuery }}">Next →</a>
            @else
                <span class="disabled">Next →</span>
            @endif
        </div>
    </div>

    @if($topCustomers->isNotEmpty())
    <div class="top-customers">
        <div style="font-size:0.95rem;font-weight:700;color:var(--violet-night);margin-bottom:0.25rem;">Top Customers</div>
        <div style="font-size:0.8rem;color:var(--text-light);margin-bottom:0.75rem;">Highest spenders in this period (excluding canceled orders)</div>
        <div class="top-customers-grid">
            @foreach($topCustomers as $i => $c)
            <div class="customer-card">
                <div style="font-size:0.7rem;font-weight:700;color:var(--text-light);letter-spacing:0.05em;">#{{ $i + 1 }}</div>
                <div class="name">{{ $c->user->firstName ?? 'N/A' }} {{ $c->user->lastName ?? '' }}</div>
                <div class="spent">₱{{ number_format($c->total_spent, 2) }}</div>
                <div class="orders-count">{{ $c->order_count }} order(s)</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

{{--orders list--}}
@else

    <div class="data-table">
        <div class="data-table-head" style="border-bottom:1px solid var(--porcelain-light);">
            <div>
                <div style="font-size:0.95rem;font-weight:700;color:var(--violet-night);">Orders List</div>
                <div style="font-size:0.78rem;color:var(--text-light);margin-top:0.15rem;">
                    Full record of individual orders. Use this to track specific transactions, customers, and statuses.
                </div>
            </div>
            <div style="font-size:0.8rem;color:var(--text-light);">{{ $orders->total() }} record(s) found</div>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ORDER #</th>
                        <th>CUSTOMER</th>
                        <th>ITEMS</th>
                        <th>TOTAL</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $badgeClass = match($order->status) {
                                'Delivered' => 'badge-delivered',
                                'Canceled'  => 'badge-canceled',
                                'Pending'   => 'badge-pending',
                                'Accepted'  => 'badge-accepted',
                                'Shipped'   => 'badge-shipped',
                                default     => '',
                            };
                        @endphp
                        <tr>
                            <td style="font-weight:600;">#{{ $order->orderID }}</td>
                            <td>{{ trim(($order->user->firstName ?? '') . ' ' . ($order->user->lastName ?? '')) ?: 'N/A' }}</td>
                            <td style="font-size:0.8rem;line-height:1.45;max-width:14rem;">
                                @foreach($order->items as $line)
                                    <div>{{ $line->summaryLine() }}</div>
                                @endforeach
                            </td>
                            <td>₱{{ number_format($order->total, 2) }}</td>
                            <td><span class="badge {{ $badgeClass }}">{{ $order->status }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($order->createdAt)->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3rem;color:var(--text-light);">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @php $ordersQuery = http_build_query(request()->except('page')); @endphp
        <div class="pagination-bar">
            @if($orders->onFirstPage())
                <span class="disabled">← Previous</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}&{{ $ordersQuery }}">← Previous</a>
            @endif
            <span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}&{{ $ordersQuery }}">Next →</a>
            @else
                <span class="disabled">Next →</span>
            @endif
        </div>
    </div>

@endif

<script>
    document.getElementById('report_type').addEventListener('change', function () {
        document.getElementById('reportFilterForm').submit();
    });

    function downloadPdf() {
        const params = new URLSearchParams({
            date_from:   document.getElementById('date_from').value,
            date_to:     document.getElementById('date_to').value,
            report_type: document.getElementById('report_type').value,
        });
        window.location.href = '{{ route("admin.reports.pdf") }}?' + params.toString();
    }
</script>

@endsection