@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Lead Assignment</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Lead Assignment</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-24">
    @foreach([
        ['label'=>'Total Leads',        'value'=>$stats['total'],             'icon'=>'solar:users-group-two-rounded-outline','color'=>'primary'],
        ['label'=>'Unassigned',         'value'=>$stats['unassigned'],        'icon'=>'solar:user-cross-outline',             'color'=>'warning'],
        ['label'=>'Assigned',           'value'=>$stats['assigned'],          'icon'=>'solar:user-check-outline',             'color'=>'success'],
        ['label'=>'Leads Per Officer',  'value'=>$stats['leads_per_officer'], 'icon'=>'solar:chart-2-outline',                'color'=>'info'],
        ['label'=>'Assigned Today',     'value'=>$stats['assigned_today'],    'icon'=>'solar:calendar-outline',               'color'=>'secondary'],
    ] as $stat)
    <div class="col-6 col-md-4 col-lg">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 rounded flex-shrink-0">
                        <iconify-icon icon="{{ $stat['icon'] }}" class="fs-24"></iconify-icon>
                    </div>
                    <div class="ms-3 min-w-0">
                        <h6 class="mb-1">{{ $stat['value'] }}</h6>
                        <p class="mb-0 text-sm text-secondary-light fw-medium text-truncate">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card mb-24">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Filter Leads</h6>
        <form method="GET" action="{{ route('leads.assignment') }}" class="row g-3">
            <div class="col-6 col-md-4 col-lg-2">
                <select name="customer_type" class="form-select">
                    <option value="">All Customer Types</option>
                    @foreach(['Individual','Dealer','Other'] as $t)
                        <option value="{{ $t }}" @selected(request('customer_type')===$t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="vehicle_brand_id" id="filterBrandSelect" class="form-select">
                    <option value="">All Brands</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected(request('vehicle_brand_id')==$b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="vehicle_model_product_id" id="filterModelSelect" class="form-select" {{ request('vehicle_brand_id') ? '' : 'disabled' }}>
                    <option value="">All Models</option>
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['Unassigned','Interested','Need More Info','Follow Up Later','Not Interested','Converted','Sales Done'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="sales_officer" class="form-select">
                    <option value="">All Officers</option>
                    @foreach($officers as $o)
                        <option value="{{ $o->id }}" @selected(request('sales_officer')==$o->id)>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <input type="text" name="search" class="form-control" placeholder="ID / Name / Phone"
                    value="{{ request('search') }}">
            </div>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary-600">Filter</button>
                <a href="{{ route('leads.assignment') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Leads Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-24 py-16 border-bottom">
            <span class="fw-semibold">Leads List <span class="badge bg-primary-100 text-primary-600 ms-1">{{ $leads->total() }}</span></span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select id="selectOfficer" class="form-select form-select-sm assign-officer-select">
                    <option value="">Select Sales Officer</option>
                    @foreach($officers as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary-600 btn-sm" id="btnAssign">
                    <iconify-icon icon="solar:user-plus-bold" class="me-1"></iconify-icon> Assign Leads
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Lead ID</th>
                        <th>Customer Name</th>
                        <th>Customer Type</th>
                        <th>Brand / Model</th>
                        <th>Contact Number</th>
                        <th>Created Date</th>
                        <th>Current Officer</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><input type="checkbox" class="lead-checkbox" value="{{ $lead->id }}"></td>
                        <td><span class="fw-semibold">#{{ $lead->id }}</span></td>
                        <td>
                            <div class="fw-medium">{{ $lead->name }}</div>
                            @if($lead->lead_source === 'Web Enquiry')
                                <span class="badge bg-primary-100 text-primary-600" style="font-size:10px">Web</span>
                            @endif
                        </td>
                        <td>{{ $lead->customer_type }}</td>
                        <td>
                            @if($lead->brand)
                                {{ $lead->brand->name }}{{ $lead->vehicle_model_display ? ' / '.$lead->vehicle_model_display : '' }}
                            @else
                                <span class="text-secondary-light">—</span>
                            @endif
                        </td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                        <td>{{ $lead->officer?->name ?? 'Unassigned' }}</td>
                        <td>
                            <span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->status }}</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-view-assignment"
                                data-id="{{ $lead->id }}" title="View Details">
                                <iconify-icon icon="solar:eye-outline"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-secondary-light">No leads found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-24 py-16 border-top">
            <span class="text-sm text-secondary-light">Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} records</span>
            {{ $leads->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- Assignment Detail Modal (2 tabs: Lead Details + Assignment History) --}}
<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalTitle">Lead Details &amp; Assignment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Tabs --}}
                <ul class="nav nav-tabs mb-3" id="assignmentTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tabLeadDetails">Lead Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tabAssignmentHistory">Assignment History</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabLeadDetails">
                        <div id="leadDetailsContent">
                            <div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabAssignmentHistory">
                        <div id="assignmentHistoryContent">
                            <div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.assign-officer-select {
    min-width: 180px;
}

@media (max-width: 575px) {
    .assign-officer-select {
        min-width: 0;
        flex: 1 1 auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
// ── Filter bar: Brand -> Model cascade ──────────────────────────────────
const filterBrandSelect = document.getElementById('filterBrandSelect');
const filterModelSelect = document.getElementById('filterModelSelect');
const PRESELECTED_MODEL = '{{ request('vehicle_model_product_id') }}';

function loadFilterModels(brandId, selectedModelId) {
    if (!brandId) {
        filterModelSelect.innerHTML = '<option value="">All Models</option>';
        filterModelSelect.disabled = true;
        return;
    }
    filterModelSelect.disabled = false;
    filterModelSelect.innerHTML = '<option value="">Loading...</option>';
    fetch(`/categories/${brandId}/models`)
        .then(r => r.json())
        .then(data => {
            let opts = '<option value="">All Models</option>';
            (data.models || []).forEach(m => {
                opts += `<option value="${m.id}" ${selectedModelId == m.id ? 'selected' : ''}>${m.name}</option>`;
            });
            filterModelSelect.innerHTML = opts;
        });
}

filterBrandSelect.addEventListener('change', function () {
    loadFilterModels(this.value, null);
});

@if(request('vehicle_brand_id'))
loadFilterModels('{{ request('vehicle_brand_id') }}', PRESELECTED_MODEL);
@endif

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.lead-checkbox').forEach(c => c.checked = this.checked);
});

// Assign leads
document.getElementById('btnAssign').addEventListener('click', function () {
    const officerId = document.getElementById('selectOfficer').value;
    if (!officerId) { alert('Please select a sales officer first.'); return; }

    const ids = [...document.querySelectorAll('.lead-checkbox:checked')].map(c => c.value);
    if (!ids.length) { alert('Please select at least one lead.'); return; }

    this.disabled = true; this.textContent = 'Assigning...';

    fetch('{{ route("leads.assign") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ lead_ids: ids, assigned_to: officerId }),
    })
    .then(r => r.json())
    .then(res => {
        this.disabled = false;
        this.innerHTML = '<iconify-icon icon="solar:user-plus-bold" class="me-1"></iconify-icon> Assign Leads';
        if (res.success) { alert(res.message); location.reload(); }
        else alert('Assignment failed.');
    });
});

// View details modal (2 tabs: Lead Details + Assignment History)
document.querySelectorAll('.btn-view-assignment').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('leadDetailsContent').innerHTML =
            '<div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>';
        document.getElementById('assignmentHistoryContent').innerHTML =
            '<div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>';
        
        const modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
        modal.show();

        fetch(`/leads/${this.dataset.id}/assignment-history`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data;
                const followups = d.followups || [];
                const brandModel = d.brand ? `${d.brand.name}${d.model_product ? ' / ' + d.model_product.name : (d.vehicle_model_text ? ' / ' + d.vehicle_model_text : '')}` : '—';

                // Tab 1: Lead Details
                document.getElementById('leadDetailsContent').innerHTML = `
                <div class="row g-2">
                    <div class="col-12 col-md-6"><strong>Customer Name:</strong> ${d.name}</div>
                    <div class="col-12 col-md-6"><strong>Contact Number:</strong> ${d.phone}</div>
                    <div class="col-12 col-md-6"><strong>Email:</strong> ${d.email ?? '—'}</div>
                    <div class="col-12 col-md-6"><strong>Location:</strong> ${d.city ?? '—'}</div>
                    <div class="col-12 col-md-6"><strong>Customer Type:</strong> ${d.customer_type}</div>
                    <div class="col-12 col-md-6"><strong>Lead Source:</strong> ${d.lead_source}</div>
                    <div class="col-12 col-md-6"><strong>Brand / Model:</strong> ${brandModel}</div>
                    <div class="col-12 col-md-6"><strong>Budget Range:</strong> ${d.budget_range ? 'LKR ' + Number(d.budget_range).toLocaleString() : '—'}</div>
                    <div class="col-12 col-md-6"><strong>Current Officer:</strong> ${d.officer?.name ?? '<span class="text-warning-600">Unassigned</span>'}</div>
                    <div class="col-12 col-md-6"><strong>Status:</strong> <span class="badge">${d.status}</span></div>
                </div>`;

                // Tab 2: Assignment History (follow-ups)
                document.getElementById('assignmentHistoryContent').innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Date</th><th>Method</th><th>Feedback</th><th>Status</th><th>By</th></tr></thead>
                        <tbody>
                            ${followups.length ? followups.map(f => `<tr>
                                <td>${new Date(f.created_at).toLocaleDateString()}</td>
                                <td>${f.method}</td>
                                <td>${f.feedback ?? '—'}</td>
                                <td><span class="badge ${getStatusBadge(f.status)}">${f.status}</span></td>
                                <td>${f.done_by?.name ?? '—'}</td>
                            </tr>`).join('') : '<tr><td colspan="5" class="text-center text-secondary-light">No follow-up history found.</td></tr>'}
                        </tbody>
                    </table>
                </div>`;
            });
    });
});

function getStatusBadge(status) {
    const badges = {
        'Interested': 'bg-info-100 text-info-600',
        'Need More Info': 'bg-purple-100 text-purple-600',
        'Follow Up Later': 'bg-warning-100 text-warning-600',
        'Not Interested': 'bg-danger-100 text-danger-600',
        'Converted': 'bg-success-100 text-success-600',
        'Sales Done': 'bg-neutral-200 text-neutral-600',
    };
    return badges[status] || 'bg-secondary-100 text-secondary-600';
}
</script>
@endpush