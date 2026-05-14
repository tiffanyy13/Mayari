<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mayari – {{ ucfirst($reportType) }} Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #ffffff;
            line-height: 1.5;
        }

        .page { padding: 2rem 2.25rem; }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1a1a2e;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right { text-align: right; }
        .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #1a1a2e;
        }
        .brand-sub {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #6b7280;
            margin-top: 2px;
        }
        .report-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .report-subtitle {
            font-size: 8.5px;
            color: #6b7280;
            margin-top: 2px;
        }
        .report-period {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }
        .report-generated {
            font-size: 8.5px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 1.5rem;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            text-align: right;
            background: #f9fafb;
        }
        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }
        .summary-label {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-top: 4px;
        }

        .section-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 6px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
            margin-bottom: 1.5rem;
        }
        table.report-table thead { display: table-header-group; }
        table.report-table thead tr th {
            background: #1a1a2e;
            color: #ffffff;
            padding: 7px 10px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-align: left;
        }
        table.report-table thead tr th:first-child { border-radius: 4px 0 0 0; }
        table.report-table thead tr th:last-child  { border-radius: 0 4px 0 0; }
        table.report-table tbody tr { page-break-inside: avoid; }
        table.report-table tbody tr:nth-child(even) td { background: #f9fafb; }
        table.report-table tbody tr.best-day td { background: #f3f0ff !important; }
        table.report-table tbody td {
            padding: 6px 10px;
            font-size: 10px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        table.report-table tbody tr:last-child td { border-bottom: none; }
        .best-day-tag {
            display: inline-block;
            margin-left: 5px;
            padding: 1px 6px;
            border-radius: 99px;
            font-size: 7.5px;
            font-weight: 700;
            background: #1a1a2e;
            color: #fff;
            letter-spacing: 0.04em;
        }
        .avg-val { color: #6b7280; }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 8.5px;
            font-weight: 700;
        }
        .status-pending   { background: #fef3c7; color: #92400e; }
        .status-accepted  { background: #dbeafe; color: #1e3a8a; }
        .status-delivered { background: #d1fae5; color: #064e3b; }
        .status-canceled  { background: #fee2e2; color: #7f1d1d; }
        .status-shipped   { background: #ede9fe; color: #4c1d95; }

        .top-customers-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-top: 1rem;
        }
        .top-customers-table td {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.6rem 0.8rem;
            background: #f9fafb;
            text-align: center;
            vertical-align: top;
        }
        .tc-rank  { font-size: 8px; font-weight: 700; letter-spacing: 1px; color: #9ca3af; text-transform: uppercase; }
        .tc-name  { font-size: 10px; font-weight: 700; color: #1a1a2e; margin-top: 3px; }
        .tc-spent { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-top: 4px; }
        .tc-orders { font-size: 8px; color: #9ca3af; margin-top: 2px; }

        .footer {
            margin-top: 1.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }
        .footer-left, .footer-right {
            display: table-cell;
            font-size: 8px;
            color: #9ca3af;
            vertical-align: middle;
        }
        .footer-right { text-align: right; }

        .empty {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
            font-size: 10px;
        }

        @media print {
            .page { padding: 1.25rem 1.5rem; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-left">
            <div class="brand">MAYARI</div>
            <div class="brand-sub">Admin Report System</div>
        </div>
        <div class="header-right">
            <div class="report-title">
                @if($reportType === 'sales') Sales Summary @else Orders List @endif
            </div>
            <div class="report-subtitle">
                @if($reportType === 'sales')
                    Daily revenue breakdown with avg. order value
                @else
                    Full individual order records
                @endif
            </div>
            <div class="report-period">{{ $fromLabel }} &ndash; {{ $toLabel }}</div>
            <div class="report-generated">Generated: {{ now('Asia/Manila')->format('M d, Y h:i A') }} PHT</div>
        </div>
    </div>

    {{--summary--}}
    <table class="summary">
        <tr>
            <td class="summary-cell">
                <div class="summary-value">&#8369;{{ number_format($totalRevenue, 2) }}</div>
                <div class="summary-label">Total Revenue</div>
            </td>
            <td class="summary-cell">
                <div class="summary-value">{{ number_format($totalOrders) }}</div>
                <div class="summary-label">Total Orders</div>
            </td>
            <td class="summary-cell">
                <div class="summary-value">{{ number_format($completedOrders) }}</div>
                <div class="summary-label">Delivered</div>
            </td>
            <td class="summary-cell">
                <div class="summary-value">{{ number_format($canceledOrders) }}</div>
                <div class="summary-label">Canceled</div>
            </td>
        </tr>
    </table>

    {{--sales summary PDF--}}
    @if($reportType === 'sales')

        @php $bestDay = $dailySales->sortByDesc('revenue')->first(); @endphp

        <div class="section-label">Daily Revenue Breakdown</div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Avg. Order Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailySales as $day)
                    @php
                        $avg    = $day->order_count > 0 ? $day->revenue / $day->order_count : 0;
                        $isBest = $bestDay && $day->sale_date === $bestDay->sale_date;
                    @endphp
                    <tr class="{{ $isBest ? 'best-day' : '' }}">
                        <td>
                            {{ \Carbon\Carbon::parse($day->sale_date)->format('M d, Y') }}
                            @if($isBest)<span class="best-day-tag">Best Day</span>@endif
                        </td>
                        <td>{{ $day->order_count }}</td>
                        <td style="font-weight:700;">&#8369;{{ number_format($day->revenue, 2) }}</td>
                        <td class="avg-val">&#8369;{{ number_format($avg, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No sales data for the selected period.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{--top customers--}}
        @if(isset($topCustomers) && $topCustomers->isNotEmpty())
            <div class="section-label" style="margin-top:0.5rem;">Top Customers &mdash; by spend</div>
            <table class="top-customers-table">
                <tr>
                    @foreach($topCustomers as $i => $c)
                    <td>
                        <div class="tc-rank">#{{ $i + 1 }}</div>
                        <div class="tc-name">{{ trim(($c->user->firstName ?? '') . ' ' . ($c->user->lastName ?? '')) ?: 'N/A' }}</div>
                        <div class="tc-spent">&#8369;{{ number_format($c->total_spent, 2) }}</div>
                        <div class="tc-orders">{{ $c->order_count }} order(s)</div>
                    </td>
                    @endforeach
                </tr>
            </table>
        @endif

    {{--order list PDF--}}
    @else

        <div class="section-label">Order Details &mdash; {{ $orders->count() }} records</div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusClass = match($order->status) {
                            'Delivered' => 'status-delivered',
                            'Canceled'  => 'status-canceled',
                            'Pending'   => 'status-pending',
                            'Accepted'  => 'status-accepted',
                            'Shipped'   => 'status-shipped',
                            default     => '',
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:700;">#{{ $order->orderID }}</td>
                        <td>{{ trim(($order->user->firstName ?? '') . ' ' . ($order->user->lastName ?? '')) ?: 'N/A' }}</td>
                        <td style="font-size:9px;line-height:1.4;vertical-align:top;">
                            @foreach($order->items as $line)
                                {{ $line->summaryLine() }}@if(!$loop->last)<br>@endif
                            @endforeach
                        </td>
                        <td>&#8369;{{ number_format($order->total, 2) }}</td>
                        <td><span class="status {{ $statusClass }}">{{ $order->status }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($order->createdAt)->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No records found for the selected period.</td></tr>
                @endforelse
            </tbody>
        </table>

    @endif

    <div class="footer">
        <div class="footer-left">Mayari &ndash; Admin Report System</div>
        <div class="footer-right">
            Total Revenue: &#8369;{{ number_format($totalRevenue, 2) }} &nbsp;|&nbsp; {{ $fromLabel }} to {{ $toLabel }}
        </div>
    </div>

</div>
</body>
</html>