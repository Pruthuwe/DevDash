@extends('layouts.main')

@section('content')

{{-- Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Leads</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Lead List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('leads.capture') }}" class="btn btn-primary-600 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Add New Lead
        </a>
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <iconify-icon icon="solar:upload-minimalistic-outline"></iconify-icon> Import Excel
        </button>
        <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:file-download-outline"></iconify-icon> Export
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-24">
    @foreach([
        ['label'=>'Total Leads',   'value'=>$stats['total'],      'icon'=>'solar:users-group-two-rounded-outline', 'color'=>'primary'],
        ['label'=>'Unassigned',    'value'=>$stats['unassigned'], 'icon'=>'solar:user-cross-outline',             'color'=>'warning'],
        ['label'=>'Interested',    'value'=>$stats['interested'], 'icon'=>'solar:like-outline',                   'color'=>'info'],
        ['label'=>'Converted',     'value'=>$stats['converted'],  'icon'=>'solar:check-circle-outline',           'color'=>'success'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-lg bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 rounded">
                            <iconify-icon icon="{{ $stat['icon'] }}" class="fs-24"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3 min-w-0">
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
        <form method="GET" action="{{ route('leads.list') }}" class="row g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label text-sm text-secondary-light mb-1">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search name, phone, email..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label text-sm text-secondary-light mb-1">Source</label>
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    @foreach(['Walk-in','Web Enquiry','Social Media','Phone Call','Other'] as $src)
                        <option value="{{ $src }}" @selected(request('source')===$src)>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label text-sm text-secondary-light mb-1">From date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label text-sm text-secondary-light mb-1">To date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-self-end">
                <button type="submit" class="btn btn-primary-600 flex-fill">Filter</button>
                <a href="{{ route('leads.list') }}" class="btn btn-outline-secondary flex-fill">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Lead Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th class="lead-col-location">Location</th>
                        <th class="d-none d-md-table-cell">Brand / Model</th>
                        <th class="lead-col-source">Source</th>
                        <th class="d-none d-sm-table-cell">Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div class="fw-medium text-sm">{{ $lead->name }}</div>
                            @if($lead->company_name)
                                <small class="text-secondary-light">{{ $lead->company_name }}</small>
                            @endif
                            <div class="text-secondary-light d-sm-none" style="font-size:0.75rem">{{ $lead->created_at->format('Y-m-d') }}</div>
                        </td>
                        <td class="text-sm">{{ $lead->phone }}</td>
                        <td class="text-sm lead-col-location">{{ $lead->city ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">
                            @if($lead->brand)
                                <div class="text-sm">{{ $lead->brand->name }}</div>
                                @if($lead->vehicle_model_display)
                                    <small class="text-secondary-light">{{ $lead->vehicle_model_display }}</small>
                                @endif
                            @else
                                <span class="text-secondary-light">—</span>
                            @endif
                        </td>
                        <td class="lead-col-source">
                            <span class="badge {{ $lead->lead_source === 'Web Enquiry' ? 'bg-primary-100 text-primary-600' : 'bg-neutral-100 text-neutral-600' }}">
                                {{ $lead->lead_source }}
                            </span>
                        </td>
                        <td class="text-sm text-secondary-light d-none d-sm-table-cell">{{ $lead->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-info btn-view-lead"
                                    data-id="{{ $lead->id }}"
                                    title="View Details">
                                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                </button>
                                <button class="btn btn-sm btn-outline-primary btn-view-lead"
                                    data-id="{{ $lead->id }}"
                                    title="Edit Lead">
                                    <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-lead"
                                    data-id="{{ $lead->id }}"
                                    data-name="{{ $lead->name }}"
                                    title="Delete">
                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-secondary-light">No leads found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($leads->hasPages())
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-24 py-16 border-top">
            <span class="text-sm text-secondary-light">
                Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} records
            </span>
            {{ $leads->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════ MODALS ══════════════════════════ --}}

{{-- View/Edit Lead Modal --}}
<div class="modal fade" id="leadDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadDetailTitle">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="leadDetailBody">
                <div class="text-center py-4"><div class="spinner-border text-primary-600"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- Import Excel Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Leads From Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info text-sm mb-3">
                    <strong>Required columns:</strong> name, phone<br>
                    <strong>Optional:</strong> email, address, city, customer_type, company_name, vehicle_brand, vehicle_model, budget_range, quantity_needed, lead_source
                </div>
                <input type="file" class="form-control" id="excelFile" accept=".xlsx,.xls,.csv">
                <div id="importMsg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary-600" id="btnImport">Import</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 575px) {
    .avatar.avatar-lg {
        width: 40px;
        height: 40px;
    }
    .avatar.avatar-lg .iconify {
        font-size: 18px !important;
    }
}

/* ── Follow-Up History tab: compact table rows ──────────── */
#ldFollowupRows td {
    padding-top: 0.4rem;
    padding-bottom: 0.4rem;
    vertical-align: middle;
    font-size: 0.8125rem;
}

/* ── "entries" badge: tight, inline ────────────────────── */
#ldFollowupCount {
    display: inline-flex;
    align-items: center;
    line-height: 1;
    padding: 0.3rem 0.65rem;
    font-size: 0.75rem;
    height: auto;
}

/* ── Modal history table: horizontal scroll on small screens */
#ldHistory .table-responsive {
    max-height: 340px;
    overflow-y: auto;
}

/* ── Lead list main table: tighter rows ─────────────────── */
.card .table td,
.card .table th {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    vertical-align: middle;
}

/* ── Mobile: hide less important columns in main table ───── */
@media (max-width: 767px) {
    .lead-col-source,
    .lead-col-location {
        display: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
const LEAD_BRANDS = @json($brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name]));

// ── View/Edit lead modal ────────────────────────────────────────────────
document.querySelectorAll('.btn-view-lead').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        document.getElementById('leadDetailBody').innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary-600"></div></div>';
        const modal = new bootstrap.Modal(document.getElementById('leadDetailModal'));
        modal.show();

        fetch(`/leads/${id}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data;
                document.getElementById('leadDetailTitle').textContent = `Details & Follow-Ups For ${d.name}`;
                document.getElementById('leadDetailBody').innerHTML = buildLeadDetailHTML(d);

                // Populate follow-up history table from already-loaded data
                renderFollowupRows(d.followups || []);

                // Wire the brand -> model cascade now that the form exists in the DOM
                const brandSel = document.getElementById('editBrandSelect');
                const modelSel = document.getElementById('editModelSelect');
                if (brandSel && modelSel) {
                    loadEditModels(d.vehicle_brand_id, d.vehicle_model_product_id, modelSel);
                    brandSel.addEventListener('change', function () {
                        loadEditModels(this.value, null, modelSel);
                    });
                }
            });
    });
});

function loadEditModels(brandId, selectedModelId, modelSel) {
    if (!brandId) {
        modelSel.innerHTML = '<option value="">Select Brand first</option>';
        modelSel.disabled = true;
        return;
    }
    modelSel.disabled = false;
    modelSel.innerHTML = '<option value="">Loading...</option>';
    fetch(`/categories/${brandId}/models`)
        .then(r => r.json())
        .then(data => {
            let opts = '<option value="">Select Model</option>';
            (data.models || []).forEach(m => {
                opts += `<option value="${m.id}" ${selectedModelId == m.id ? 'selected' : ''}>${m.name}</option>`;
            });
            modelSel.innerHTML = opts;
        });
}

const STATUS_BADGE = {
    'Converted':       'bg-success-100 text-success-600',
    'Interested':      'bg-info-100 text-info-600',
    'Sales Done':      'bg-neutral-200 text-neutral-600',
    'Not Interested':  'bg-danger-100 text-danger-600',
    'Follow Up Later': 'bg-warning-100 text-warning-600',
    'Need More Info':  'bg-lilac-100 text-lilac-600',
};

function renderFollowupRows(followups) {
    const tbody = document.getElementById('ldFollowupRows');
    const badge = document.getElementById('ldFollowupCount');
    if (!tbody) return;

    if (!followups.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-3 text-secondary-light">No follow-ups recorded yet.</td></tr>';
        if (badge) badge.textContent = '0 entries';
        return;
    }

    if (badge) badge.textContent = followups.length + (followups.length === 1 ? ' entry' : ' entries');

    tbody.innerHTML = followups.map((f, i) => `
        <tr>
            <td class="text-sm text-secondary-light">${followups.length - i}</td>
            <td class="text-sm">${f.followup_at ? new Date(f.followup_at).toLocaleString() : '—'}</td>
            <td class="text-sm">${f.method ?? '—'}</td>
            <td class="text-sm">${f.feedback ? `<span title="${f.feedback}">${f.feedback.length > 60 ? f.feedback.slice(0,60) + '…' : f.feedback}</span>` : '<span class="text-secondary-light">—</span>'}</td>
            <td><span class="badge ${STATUS_BADGE[f.status] ?? 'bg-neutral-100 text-neutral-600'}">${f.status ?? '—'}</span></td>
            <td class="text-sm">${f.next_followup_at ? new Date(f.next_followup_at).toLocaleString() : '<span class="text-secondary-light">—</span>'}</td>
            <td class="text-sm">${f.done_by?.name ?? '<span class="text-secondary-light">—</span>'}</td>
        </tr>
    `).join('');
}

function buildLeadDetailHTML(d) {
    const val = v => v ?? '';
    const brandOptions = LEAD_BRANDS.map(b =>
        `<option value="${b.id}" ${d.vehicle_brand_id == b.id ? 'selected' : ''}>${b.name}</option>`
    ).join('');

    return `
    <ul class="nav nav-tabs mb-3" id="ldTab">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#ldBasic">Lead Details</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#ldHistory">Follow-Up History</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="ldBasic">
            <form id="leadEditForm" data-lead-id="${d.id}">
                <h6 class="fw-semibold mb-3">Basic Information</h6>
                <div class="row g-3">
                    <div class="col-12 col-md-6"><label class="form-label">Full Name *</label>
                        <input name="name" class="form-control" value="${val(d.name)}" required></div>
                    <div class="col-12 col-md-6"><label class="form-label">Mobile Number *</label>
                        <input name="phone" class="form-control" value="${val(d.phone)}" required></div>
                    <div class="col-12 col-md-6"><label class="form-label">Email</label>
                        <input name="email" class="form-control" value="${val(d.email)}"></div>
                    <div class="col-12 col-md-6"><label class="form-label">Address</label>
                        <input name="address" class="form-control" value="${val(d.address)}"></div>
                    <div class="col-12 col-md-6"><label class="form-label">City</label>
                        <input name="city" class="form-control" value="${val(d.city)}"></div>
                </div>
                <h6 class="fw-semibold mt-4 mb-3">Business Information</h6>
                <div class="row g-3">
                    <div class="col-12 col-md-6"><label class="form-label">Customer Type</label>
                        <select name="customer_type" class="form-select">
                            ${['Individual','Dealer','Other'].map(t=>`<option value="${t}" ${d.customer_type===t?'selected':''}>${t}</option>`).join('')}
                        </select></div>
                    <div class="col-12 col-md-6"><label class="form-label">Company Name</label>
                        <input name="company_name" class="form-control" value="${val(d.company_name)}"></div>
                </div>
                <h6 class="fw-semibold mt-4 mb-3">Interest Details</h6>
                <div class="row g-3">
                    <div class="col-12 col-md-6"><label class="form-label">Vehicle Brand</label>
                        <select name="vehicle_brand_id" id="editBrandSelect" class="form-select">
                            <option value="">Select Brand</option>
                            ${brandOptions}
                        </select></div>
                    <div class="col-12 col-md-6"><label class="form-label">Vehicle Model</label>
                        <select name="vehicle_model_product_id" id="editModelSelect" class="form-select" ${d.vehicle_brand_id ? '' : 'disabled'}>
                            <option value="">Select Brand first</option>
                        </select></div>
                    <div class="col-6"><label class="form-label">Budget Range</label>
                        <input name="budget_range" type="number" class="form-control" value="${val(d.budget_range)}"></div>
                    <div class="col-6"><label class="form-label">Quantity Needed</label>
                        <input name="quantity_needed" type="number" class="form-control" value="${val(d.quantity_needed)}"></div>
                </div>
                <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-600">Save Lead Details</button>
                </div>
            </form>
            <div id="leadEditMsg" class="mt-2"></div>
        </div>
        <div class="tab-pane fade" id="ldHistory">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0">Follow-Up History</h6>
                <span id="ldFollowupCount" class="badge bg-primary-100 text-primary-600 fs-12 fw-semibold px-10 py-5 radius-8"></span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-sm">#</th>
                            <th class="text-sm">Date &amp; Time</th>
                            <th class="text-sm">Method</th>
                            <th class="text-sm">Feedback</th>
                            <th class="text-sm">Status</th>
                            <th class="text-sm">Next Follow-Up</th>
                            <th class="text-sm">Done By</th>
                        </tr>
                    </thead>
                    <tbody id="ldFollowupRows">
                        <tr><td colspan="7" class="text-center py-3 text-secondary-light">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
}

// Save lead details via AJAX — delegated, and reads the lead id from the
// form's own data-lead-id attribute (set when the form HTML is built above).
document.addEventListener('submit', function (e) {
    if (e.target.id !== 'leadEditForm') return;
    e.preventDefault();

    const form = e.target;
    const btn  = form.querySelector('[type=submit]');
    const id   = form.dataset.leadId;

    btn.disabled = true;
    btn.textContent = 'Saving...';

    const data = Object.fromEntries(new FormData(form));

    fetch(`/leads/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save Lead Details';
        const msg = document.getElementById('leadEditMsg');
        if (res.success) {
            msg.innerHTML = '<div class="alert alert-success text-sm">Saved successfully.</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            const errs = Object.values(res.errors || {}).flat().join('<br>');
            msg.innerHTML = `<div class="alert alert-danger text-sm">${errs || 'Failed to save.'}</div>`;
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Save Lead Details';
        document.getElementById('leadEditMsg').innerHTML =
            '<div class="alert alert-danger text-sm">Network error — please try again.</div>';
    });
});

// ── Delete lead ─────────────────────────────────────────────────────────
document.querySelectorAll('.btn-delete-lead').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!confirm(`Delete lead "${this.dataset.name}"? This cannot be undone.`)) return;
        fetch(`/leads/${this.dataset.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); });
    });
});

// ── Import Excel ────────────────────────────────────────────────────────
document.getElementById('btnImport').addEventListener('click', function () {
    const file = document.getElementById('excelFile').files[0];
    if (!file) { alert('Please choose a file first.'); return; }
    this.disabled = true; this.textContent = 'Importing...';

    const fd = new FormData();
    fd.append('excel_file', file);
    fd.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("leads.import-excel") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            this.disabled = false; this.textContent = 'Import';
            const msg = document.getElementById('importMsg');
            msg.innerHTML = `<div class="alert alert-${res.success ? 'success' : 'danger'} text-sm">${res.message}</div>`;
            if (res.success) setTimeout(() => location.reload(), 1500);
        });
});
</script>
@endpush