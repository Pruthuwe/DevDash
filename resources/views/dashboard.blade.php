@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Dashboard</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
    </ul>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Statistics Cards --}}
<div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">

    {{-- Total Products --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Products</p>
                        <h6 class="mb-0">{{ $totalProducts }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="fluent:box-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.products') }}" class="text-primary">View all products</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Total Customers --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Customers</p>
                        <h6 class="mb-0">{{ $totalCustomers }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.customers') }}" class="text-primary">View all customers</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Total Loan Inquiries --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Loan Inquiries</p>
                        <h6 class="mb-0">{{ $totalLoanInquiries }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:wallet-money-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.loan-inquiries') }}" class="text-primary">View all loan inquiries</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Total Blogs --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Blogs</p>
                        <h6 class="mb-0">{{ $totalBlogs }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:document-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.blogs') }}" class="text-primary">View all blogs</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Total Product Enquiries --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Product Enquiries</p>
                        <h6 class="mb-0">{{ $totalProductEnquiries }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:chat-square-like-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.product-enquiries') }}" class="text-primary">View all product enquiries</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Total Contact Messages --}}
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Contact Messages</p>
                        <h6 class="mb-0">{{ $totalContactMessages }}</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:letter-bold-duotone" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                    <a href="{{ route('manage.contacts') }}" class="text-primary">View all messages</a>
                </p>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- NEW: Today's Due Follow-Ups Widget                             --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row gy-4 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0">
                    <iconify-icon icon="solar:bell-bold" class="text-warning-600 me-2"></iconify-icon>
                    Today's Due Follow-Ups
                </h6>
                <a href="{{ route('leads.follow-up') }}" class="text-primary-600 hover-text-primary text-sm">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Lead ID</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Brand / Model</th>
                                <th>Next Follow-Up</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="dueFollowupsBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary-light">
                                    <div class="spinner-border spinner-border-sm text-primary-600"></div> Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tables Row --}}
<div class="row gy-4 mt-1">

    {{-- Latest Customers --}}
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                    <h6 class="text-lg mb-0 fw-semibold">Latest Customers</h6>
                    <a href="{{ route('manage.customers') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                        View All
                        <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                    </a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestCustomers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email ?? '-' }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>{{ $customer->created_at ? $customer->created_at->format('d M Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No customers yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest Products --}}
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-16">
                    <h6 class="text-lg mb-0 fw-semibold">Latest Products</h6>
                    <a href="{{ route('manage.products') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                        View All
                        <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                    </a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Fuel Type</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>${{ $product->sale_price ?? $product->price }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    <span class="bg-{{ $product->status == 'active' ? 'success' : 'danger' }}-focus text-{{ $product->status == 'active' ? 'success' : 'danger' }}-main px-12 py-4 rounded-pill fw-medium text-sm">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No products yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Load Today's Due Follow-Ups ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/today-followups-due')
        .then(r => r.json())
        .then(res => {
            const tbody = document.getElementById('dueFollowupsBody');
            if (!res.success || !res.data.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-secondary-light">No follow-ups due today.</td></tr>';
                return;
            }
            
            tbody.innerHTML = res.data.map(lead => `
                <tr>
                    <td><span class="fw-semibold">#${lead.id}</span></td>
                    <td>${lead.name}</td>
                    <td>${lead.phone}</td>
                    <td>${lead.brand?.name ?? '—'}${lead.vehicle_model_display ? ' / ' + lead.vehicle_model_display : ''}</td>
                    <td><span class="text-warning-600 fw-medium">${lead.next_followup_at ? new Date(lead.next_followup_at).toLocaleString() : '—'}</span></td>
                    <td><span class="badge ${lead.status_badge_class || ''}">${lead.status}</span></td>
                    <td>
                        <a href="{{ route('leads.follow-up') }}?lead_id=${lead.id}" class="btn btn-sm btn-primary-600">
                            <iconify-icon icon="solar:chat-round-dots-bold" class="me-1"></iconify-icon> Follow Up
                        </a>
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById('dueFollowupsBody').innerHTML = 
                '<tr><td colspan="7" class="text-center py-4 text-secondary-light">Failed to load follow-ups.</td></tr>';
        });
});
</script>
@endpush