@extends('layouts.admin')
@section('title', 'Customers')

@push('styles')
<style>
    .admin-main {
        overflow-y: visible;
    }

    @media (max-width: 768px) {
        .page-heading-section h1 { font-size: 1.4rem !important; }
        .data-table-head { flex-direction: column !important; align-items: stretch !important; gap: 0.5rem; }
        .customers-table-wrap { overflow-x: auto; }
        table { min-width: 540px; }
    }
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.75rem;font-weight:700;color:var(--violet-night);">Customers</h1>
    <p style="color:var(--text-light);font-size:0.875rem;margin-top:0.25rem;">All registered customer accounts.</p>
</div>

<div class="data-table">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--porcelain-light);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
        <span style="font-size:0.875rem;color:var(--text-mid);font-weight:500;">{{ $customers->total() }} registered customers</span>
        <form action="{{ route('admin.customers') }}" method="GET" style="display:flex;align-items:center;gap:0.5rem;">
            <input type="text" name="search" class="search-input" placeholder="Search name or email…" value="{{ request('search') }}" style="border-radius:4px;">
            <button type="submit" style="background:var(--violet-night);border:none;border-radius:4px;padding:0.48rem 0.75rem;cursor:pointer;color:#fff;display:flex;align-items:center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/></svg>
            </button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>PHONE</th>
                    <th>JOINED</th>
                    <th>ORDERS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--violet-night);display:flex;align-items:center;justify-content:center;color:var(--porcelain);font-weight:700;font-size:0.72rem;text-transform:uppercase;flex-shrink:0;">
                                {{ substr($customer->firstName,0,1) }}{{ substr($customer->lastName,0,1) }}
                            </div>
                            <span style="font-weight:500;color:var(--text-dark);">{{ $customer->firstName }} {{ $customer->lastName }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-mid);font-size:0.875rem;">{{ $customer->email }}</td>
                    <td style="color:var(--text-mid);font-size:0.875rem;">{{ $customer->phone ?? '—' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-light);">{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</td>
                    <td>
                        <span style="font-weight:600;color:var(--violet-night);">{{ $customer->orders_count }}</span>
                        <span style="color:var(--text-light);font-size:0.8rem;"> orders</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:3.5rem;color:var(--text-light);">
                        <div style="font-size:2rem;margin-bottom:.75rem;opacity:.3;">👤</div>
                        <div>No customers found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--pagination--}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1.5rem;background:var(--violet-night);color:var(--porcelain);">
        @if($customers->onFirstPage())
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">← Previous</span>
        @else
            <a href="{{ $customers->previousPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;">← Previous</a>
        @endif
        <span style="font-size:0.82rem;">Page {{ $customers->currentPage() }}</span>
        @if($customers->hasMorePages())
            <a href="{{ $customers->nextPageUrl() }}" style="color:var(--porcelain);text-decoration:none;font-size:0.82rem;font-weight:500;">Next →</a>
        @else
            <span style="color:rgba(233,213,230,0.4);font-size:0.82rem;font-weight:500;">Next →</span>
        @endif
    </div>
</div>
@endsection
