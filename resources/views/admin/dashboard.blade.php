@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
    .admin-main { overflow-y: visible; }

    /*stat cards*/
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .sales-card { grid-column: 1 / -1; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px) { .stats-grid { grid-template-columns: 1fr; } .sales-card { grid-column: 1; } }

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
    .stat-icon.sales    { background: #f3eeff; color: #7c3aed; }
    .stat-icon.orders   { background: #eef6ff; color: #2563eb; }
    .stat-icon.customers{ background: #f0fdf4; color: #16a34a; }
    .stat-icon.stock    { background: #fff7ed; color: #ea580c; }

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
    .stat-note {
        font-size: 0.76rem;
        color: var(--text-mid);
        margin-top: 0.2rem;
        text-align: right;
    }

    /*total sales card*/
    .stat-card-custom.sales-card {
        flex-direction: row;
        align-items: center;
        gap: 1.25rem;
    }
    .sales-left {
        min-width: 160px;
        margin-left: 5rem;
    }
    .sales-left .stat-big  
    .sales-left .stat-title 
    .sales-left .stat-note  { text-align: right; }
    .sales-mini-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.6rem;
        flex: 1;
        border-left: 1px solid var(--porcelain-light);
        padding-left: 1.25rem;
    }
    .sales-mini {
        background: #faf7fc;
        border: 1px solid var(--porcelain-light);
        border-radius: 8px;
        padding: 0.65rem 0.75rem;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .sales-mini strong {
        display: block;
        color: var(--violet-night);
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.1;
    }
    .sales-mini span {
        display: block;
        font-size: 0.66rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-top: 0.2rem;
    }

    .top-panels {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 1080px) { .top-panels { grid-template-columns: 1fr; } }

    .panel {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(45,28,66,0.07);
        overflow: hidden;
    }
    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1.2rem;
        border-bottom: 1px solid var(--porcelain-light);
    }
    .panel-header h3 { margin: 0; font-size: 1rem; font-weight: 700; color: var(--violet-night); }
    .panel-header span { font-size: 0.78rem; color: var(--text-light); }
    .panel-content { padding: 1rem 1.2rem; }

    /*chart*/
    .chart-wrap { width: 100%; }
    .chart-svg { width: 100%; height: 240px; display: block; }
    .chart-grid-line { stroke: #eee5f4; stroke-width: 1; }
    .chart-line { fill: none; stroke: #5a3b7e; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
    .chart-area { fill: rgba(90,59,126,0.12); }
    .chart-point { fill: #fff; stroke: #5a3b7e; stroke-width: 2; }
    .chart-label { fill: #9d8aaa; font-size: 11px; }

    /*low-stock list*/
    .low-stock-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f0eaf5;
    }
    .low-stock-item:last-child { border-bottom: none; }
    .stock-pill {
        border-radius: 20px;
        padding: 0.15rem 0.55rem;
        font-size: 0.72rem;
        font-weight: 700;
        background: #fdf2f8;
        color: #9333ea;
        border: 1px solid #e9d5ff;
        white-space: nowrap;
    }
    .stock-pill.out { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

    /*recent orders table*/
    .order-total { font-weight: 600; color: var(--violet-mid); }

    /*mobile responsive dashboard*/
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr !important; }
        .sales-card { grid-column: 1 / -1 !important; }
        .stat-card-custom.sales-card { flex-direction: column !important; align-items: flex-start !important; }
        .sales-left { margin-left: 0 !important; min-width: unset !important; }
        .sales-mini-grid {
            border-left: none !important; border-top: 1px solid var(--porcelain-light);
            padding-left: 0 !important; padding-top: 0.75rem; width: 100%;
            grid-template-columns: 1fr 1fr !important;
        }
        .chart-card { overflow-x: auto; }
        .recent-orders-wrap { overflow-x: auto; }
        .recent-orders-wrap table { min-width: 520px; }
        .page-heading-section h1 { font-size: 1.4rem !important; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr !important; }
        .sales-mini-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.75rem;font-weight:700;color:var(--violet-night);margin-bottom:0.25rem;">Dashboard</h1>
    <p style="color:var(--text-light);font-size:0.875rem;">Overview of sales, orders, customers, and stock health.</p>
</div>

{{--stats row--}}
<div class="stats-grid">

    {{--total sales--}}
    <div class="stat-card-custom sales-card" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:11rem;">
            <div class="stat-icon sales" style="flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="sales-left" style="margin-left:0;min-width:unset;text-align:right;">
                <div class="stat-big">₱{{ number_format($salesMonth, 2) }}</div>
                <div class="stat-title">Total Sales</div>
                <div class="stat-note">Month-to-date</div>
            </div>
        </div>
        <div class="sales-mini-grid" style="min-width:180px;">
            <div class="sales-mini">
                <strong>₱{{ number_format($salesToday, 2) }}</strong>
                <span>Today</span>
            </div>
            <div class="sales-mini">
                <strong>₱{{ number_format($salesWeek, 2) }}</strong>
                <span>This Week</span>
            </div>
            <div class="sales-mini">
                <strong>₱{{ number_format($salesMonth, 2) }}</strong>
                <span>This Month</span>
            </div>
        </div>
    </div>

    {{--total orders--}}
    <div class="stat-card-custom">
        <div class="stat-icon orders">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ number_format($totalOrders) }}</div>
            <div class="stat-title">Total Orders</div>
            <div class="stat-note">{{ number_format($ordersToday) }} today &bull; {{ number_format($ordersThisMonth) }} this month</div>
        </div>
    </div>

    {{--total customers--}}
    <div class="stat-card-custom">
        <div class="stat-icon customers">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5.477-3.72M9 20H4v-2a4 4 0 015.477-3.72M15 8a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ number_format($totalCustomers) }}</div>
            <div class="stat-title">Total Customers</div>
            <div class="stat-note">{{ number_format($newCustomersThisMonth) }} new this month</div>
        </div>
    </div>

    {{-- low stock alerts --}}
    <div class="stat-card-custom">
        <div class="stat-icon stock">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-big">{{ number_format($lowStock->count()) }}</div>
            <div class="stat-title">Low Stock Alerts</div>
            <div class="stat-note">Products at 9 or below</div>
        </div>
    </div>

</div>

{{--charts and low stock--}}
<div class="top-panels">
    <div class="panel">
        <div class="panel-header">
            <h3>Sales Trend (Last 14 Days)</h3>
            <span>Canceled orders excluded</span>
        </div>
        <div class="panel-content">
            @php
                $maxSale      = max(1, (float) $salesGraph->max('total'));
                $chartWidth   = 720;
                $chartHeight  = 240;
                $paddingLeft  = 44;
                $paddingRight = 18;
                $paddingTop   = 16;
                $paddingBottom= 36;
                $plotWidth    = $chartWidth  - $paddingLeft  - $paddingRight;
                $plotHeight   = $chartHeight - $paddingTop   - $paddingBottom;
                $points = [];
                foreach ($salesGraph->values() as $index => $point) {
                    $x = $paddingLeft + (($plotWidth / max(1, $salesGraph->count() - 1)) * $index);
                    $y = $paddingTop  + ($plotHeight - (($point['total'] / $maxSale) * $plotHeight));
                    $points[] = ['x' => round($x,2), 'y' => round($y,2), 'date' => $point['date'], 'total' => $point['total']];
                }
                $linePath = '';
                foreach ($points as $i => $p) {
                    $linePath .= ($i === 0 ? 'M' : 'L') . $p['x'] . ' ' . $p['y'] . ' ';
                }
                $areaPath = $linePath
                    . 'L ' . ($paddingLeft + $plotWidth) . ' ' . ($paddingTop + $plotHeight) . ' '
                    . 'L ' . $paddingLeft . ' '            . ($paddingTop + $plotHeight) . ' Z';
                $yGuide = [0.25, 0.5, 0.75, 1];
            @endphp
            <div class="chart-wrap">
                <svg class="chart-svg" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" preserveAspectRatio="none" aria-label="Sales trend chart">
                    @foreach($yGuide as $ratio)
                        @php $y = $paddingTop + ($plotHeight - ($plotHeight * $ratio)); @endphp
                        <line class="chart-grid-line" x1="{{ $paddingLeft }}" y1="{{ $y }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $y }}"></line>
                        <text class="chart-label" x="6" y="{{ $y + 4 }}">₱{{ number_format($maxSale * $ratio, 0) }}</text>
                    @endforeach
                    <path class="chart-area" d="{{ $areaPath }}"></path>
                    <path class="chart-line"  d="{{ $linePath }}"></path>
                    @foreach($points as $i => $p)
                        <circle class="chart-point" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="3">
                            <title>{{ \Carbon\Carbon::parse($p['date'])->format('M d') }}: ₱{{ number_format($p['total'], 2) }}</title>
                        </circle>
                        @if($i % 2 === 0 || $i === count($points) - 1)
                            <text class="chart-label" x="{{ $p['x'] - 14 }}" y="{{ $chartHeight - 14 }}">{{ \Carbon\Carbon::parse($p['date'])->format('M d') }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Low Stock Alerts</h3>
            <span>Need restock soon</span>
        </div>
        <div class="panel-content">
            @if($lowStock->isEmpty())
                <p style="font-size:0.84rem;color:var(--text-light);margin:0;">All active products are sufficiently stocked.</p>
            @else
                @foreach($lowStock->take(10) as $item)
                    <div class="low-stock-item">
                        <div>
                            <div style="font-weight:600;color:var(--violet-night);">{{ $item->pName }}</div>
                            <div style="font-size:0.76rem;color:var(--text-light);">Current stock: {{ $item->stock }}</div>
                        </div>
                        <span class="stock-pill {{ $item->stock <= 0 ? 'out' : '' }}">
                            {{ $item->stock <= 0 ? 'Out of Stock' : $item->stock . ' left' }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{--recent orders--}}
<div class="data-table">
    <div class="data-table-head" style="border-bottom:1px solid var(--porcelain-light);">
        <div style="font-size:0.95rem;font-weight:700;color:var(--violet-night);">Recent Orders</div>
        <div style="font-size:0.8rem;color:var(--text-light);">Latest {{ $recentOrders->count() }} records</div>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ORDER #</th>
                    <th>CUSTOMER</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                    <th>DATE</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td><span class="order-num">#{{ $order->orderID }}</span></td>
                        <td>{{ $order->user->firstName ?? 'N/A' }} {{ $order->user->lastName ?? '' }}</td>
                        <td><span class="order-total">₱{{ number_format($order->total, 2) }}</span></td>
                        <td style="font-weight:600;color:{{ $order->statusColor() }};">{{ $order->status }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->createdAt)->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:3rem;color:var(--text-light);">No recent orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="height:46px;background:var(--violet-night);"></div>
</div>
@endsection