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
        <button type="button" class="btn btn-outline-success d-flex align-items-center gap-2" id="btnExport">
            <iconify-icon icon="solar:download-minimalistic-outline"></iconify-icon> Export
        </button>
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

{{-- Filters & Search Bar --}}
<div class="card mb-24">
    <div class="card-body">
        <form method="GET" action="{{ route('leads.list') }}" class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <input type="text" name="search" class="form-control" placeholder="Search name, phone, email..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    @foreach(['Walk-in','Web Enquiry','Social Media','Phone Call','Other'] as $src)
                        <option value="{{ $src }}" @selected(request('source')===$src)>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <select name="customer_type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['Individual','Dealer','Other'] as $t)
                        <option value="{{ $t }}" @selected(request('customer_type')===$t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-lg-1 d-flex flex-wrap gap-2">
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
            <table class="table table-hover mb-0" id="leadsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Location</th>
                        <th>Brand / Model</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><span class="fw-semibold">#{{ $lead->id }}</span></td>
                        <td>
                            <div class="fw-medium">{{ $lead->name }}</div>
                        </td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->city ?? '—' }}</td>
                        <td>
                            @if($lead->brand)
                                <div>{{ $lead->brand->name }}</div>
                                @if($lead->vehicle_model_display)
                                    <small class="text-secondary-light">{{ $lead->vehicle_model_display }}</small>
                                @endif
                            @else
                                <span class="text-secondary-light">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->lead_source === 'Web Enquiry')
                                <span class="badge bg-primary-100 text-primary-600">Web Enquiry</span>
                            @else
                                <span class="text-sm">{{ $lead->lead_source }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $lead->statusBadgeClass() }}">{{ $lead->status }}</span>
                        </td>
                        <td class="text-sm text-secondary-light">{{ $lead->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-info btn-view-lead"
                                    data-id="{{ $lead->id }}"
                                    title="View">
                                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                </button>
                                <button class="btn btn-sm btn-outline-primary btn-edit-lead"
                                    data-id="{{ $lead->id }}"
                                    title="Edit">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
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
                        <td colspan="9" class="text-center py-4 text-secondary-light">No leads found.</td>
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
                    <strong>Optional:</strong> email, address, city, customer_type, vehicle_brand, vehicle_model, budget_range, quantity_needed, lead_source
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
</style>
@endpush

@push('scripts')
<script>
const LEAD_BRANDS = @json($brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name]));

// ── Export to Excel ───────────────────────────────────────────────────────
document.getElementById('btnExport').addEventListener('click', function () {
    const table = document.getElementById('leadsTable');
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            let data = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(','));
    }
    
    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'leads_export_' + new Date().toISOString().slice(0,10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

// ── View lead modal ───────────────────────────────────────────────────────
document.querySelectorAll('.btn-view-lead').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        document.getElementById('leadDetailBody').innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary-600"></div></div>';
        document.getElementById('leadDetailTitle').textContent = 'Lead Details';
        const modal = new bootstrap.Modal(document.getElementById('leadDetailModal'));
        modal.show();

        fetch(`/leads/${id}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data;
                document.getElementById('leadDetailBody').innerHTML = buildLeadViewHTML(d);
            });
    });
});

// ── Edit lead modal ───────────────────────────────────────────────────────
document.querySelectorAll('.btn-edit-lead').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        document.getElementById('leadDetailBody').innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary-600"></div></div>';
        document.getElementById('leadDetailTitle').textContent = 'Edit Lead';
        const modal = new bootstrap.Modal(document.getElementById('leadDetailModal'));
        modal.show();

        fetch(`/leads/${id}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data;
                document.getElementById('leadDetailBody').innerHTML = buildLeadEditHTML(d);

                // Wire the brand -> model cascade
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

function buildLeadViewHTML(d) {
    const val = v => v ?? '—';
    return `
    <div class="row g-3">
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Full Name</label>
            <div class="fw-medium">${val(d.name)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Mobile Number</label>
            <div>${val(d.phone)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Email</label>
            <div>${val(d.email)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Location</label>
            <div>${val(d.city)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Customer Type</label>
            <div>${val(d.customer_type)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Lead Source</label>
            <div>${val(d.lead_source)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Brand / Model</label>
            <div>${d.brand ? d.brand.name : '—'}${d.vehicle_model_display ? ' / ' + d.vehicle_model_display : ''}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Budget Range</label>
            <div>${d.budget_range ? 'LKR ' + Number(d.budget_range).toLocaleString() : '—'}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Quantity Needed</label>
            <div>${val(d.quantity_needed)}</div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Status</label>
            <div><span class="badge ${d.status_badge_class || ''}">${val(d.status)}</span></div></div>
        <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Assigned To</label>
            <div>${d.officer?.name ?? 'Unassigned'}</div></div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
    </div>`;
}

function buildLeadEditHTML(d) {
    const val = v => v ?? '';
    const brandOptions = LEAD_BRANDS.map(b =>
        `<option value="${b.id}" ${d.vehicle_brand_id == b.id ? 'selected' : ''}>${b.name}</option>`
    ).join('');

    return `
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
    <div id="leadEditMsg" class="mt-2"></div>`;
}

// Save lead details via AJAX
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