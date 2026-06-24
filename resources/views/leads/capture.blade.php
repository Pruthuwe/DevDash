@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Add New Lead</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('leads.list') }}">Leads List</a></li>
                <li class="breadcrumb-item active">Add New Lead</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#importModal">
            <iconify-icon icon="solar:upload-minimalistic-outline"></iconify-icon> Import Excel
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('leads.store') }}" method="POST">
@csrf

<div class="card mb-24 lead-form-card">
    <div class="card-header">
        <h6 class="mb-0"><iconify-icon icon="solar:user-outline" class="me-2 text-primary-600"></iconify-icon>Basic Information</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="Enter full name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Contact Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}" placeholder="e.g. 077 123 4567" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="Enter email address">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Street / Town">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">City</label>
                <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. Colombo, Kandy">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Where did this lead come from? <span class="text-danger">*</span></label>
                <div class="lead-source-pills">
                    @foreach(['Walk-in','Web Enquiry','Social Media','Phone Call','Other'] as $src)
                    <input type="radio" class="btn-check" name="lead_source"
                        id="src_{{ $loop->index }}" value="{{ $src }}"
                        {{ old('lead_source','Walk-in') === $src ? 'checked' : '' }}>
                    <label class="lead-source-pill" for="src_{{ $loop->index }}">{{ $src }}</label>
                    @endforeach
                </div>
                @error('lead_source')<div class="text-danger text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-24 lead-form-card">
    <div class="card-header">
        <h6 class="mb-0"><iconify-icon icon="solar:buildings-2-outline" class="me-2 text-primary-600"></iconify-icon>Business Information</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Customer Type <span class="text-danger">*</span></label>
                <select name="customer_type" class="form-select @error('customer_type') is-invalid @enderror" required>
                    @foreach(['Individual','Dealer','Other'] as $t)
                        <option value="{{ $t }}" {{ old('customer_type','Individual') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('customer_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Enter company name (if applicable)">
            </div>
        </div>
    </div>
</div>

<div class="card mb-24 lead-form-card">
    <div class="card-header">
        <h6 class="mb-0"><iconify-icon icon="solar:wheel-outline" class="me-2 text-primary-600"></iconify-icon>Interest Details — Bikes</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Vehicle Brand</label>
                <select name="vehicle_brand_id" id="brandSelect" class="form-select">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('vehicle_brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Vehicle Model</label>
                <select name="vehicle_model_product_id" id="modelSelect" class="form-select" {{ old('vehicle_brand_id') ? '' : 'disabled' }}>
                    <option value="">Select Brand first</option>
                </select>
                <input type="hidden" name="vehicle_model_text" id="modelText">
                <small class="text-secondary-light">Model not listed yet? <a href="javascript:void(0)" id="typeModelLink">Type it manually</a></small>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Budget Range (LKR)</label>
                <input type="number" name="budget_range" class="form-control" value="{{ old('budget_range') }}" placeholder="e.g. 50000" min="0">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Quantity Needed</label>
                <input type="number" name="quantity_needed" class="form-control" value="{{ old('quantity_needed', 1) }}" min="1">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2">
    <a href="{{ route('leads.list') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary-600">
        <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> Save Lead
    </button>
</div>

</form>

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
.lead-form-card .card-header {
    background: var(--neutral-50, #f8f9fb);
}

/* Lead source — pill-style radio buttons instead of plain checkboxes */
.lead-source-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 4px;
}

.lead-source-pill {
    display: inline-flex;
    align-items: center;
    padding: 8px 18px;
    border: 1.5px solid var(--neutral-200, #e2e6ec);
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary-light, #6b7280);
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}

.lead-source-pill:hover {
    border-color: var(--primary-600, #0b60b0);
    color: var(--primary-600, #0b60b0);
}

.btn-check:checked + .lead-source-pill {
    background: var(--primary-600, #0b60b0);
    border-color: var(--primary-600, #0b60b0);
    color: #fff;
}

@media (max-width: 575px) {
    .lead-source-pill {
        padding: 7px 14px;
        font-size: 12px;
        flex: 1 1 auto;
        text-align: center;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// ── Brand → Model cascade (same pattern as the bike Add Product form) ──
const brandSelect = document.getElementById('brandSelect');
const modelSelect = document.getElementById('modelSelect');
const modelText   = document.getElementById('modelText');

function loadModelsForBrand(brandId, selectedModelId) {
    if (!brandId) {
        modelSelect.innerHTML = '<option value="">Select Brand first</option>';
        modelSelect.disabled = true;
        return;
    }
    modelSelect.disabled = false;
    modelSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`/categories/${brandId}/models`)
        .then(r => r.json())
        .then(data => {
            let opts = '<option value="">Select Model</option>';
            (data.models || []).forEach(m => {
                opts += `<option value="${m.id}" ${selectedModelId == m.id ? 'selected' : ''}>${m.name}</option>`;
            });
            opts += '<option value="__manual__">Other / not listed — type manually</option>';
            modelSelect.innerHTML = opts;
        });
}

brandSelect.addEventListener('change', function () {
    modelText.value = '';
    loadModelsForBrand(this.value, null);
});

modelSelect.addEventListener('change', function () {
    if (this.value === '__manual__') {
        const typed = prompt('Enter the model name:');
        modelText.value = typed || '';
        // Keep the select showing the manual option but don't submit it as an id
        this.value = '';
    } else {
        modelText.value = '';
    }
});

document.getElementById('typeModelLink').addEventListener('click', function () {
    const typed = prompt('Enter the model name:');
    if (typed) {
        modelText.value = typed;
        modelSelect.value = '';
    }
});

@if(old('vehicle_brand_id'))
loadModelsForBrand('{{ old('vehicle_brand_id') }}', '{{ old('vehicle_model_product_id') }}');
@endif

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
            document.getElementById('importMsg').innerHTML =
                `<div class="alert alert-${res.success ? 'success' : 'danger'} text-sm">${res.message}</div>`;
            if (res.success) setTimeout(() => window.location = '{{ route("leads.list") }}', 1500);
        });
});
</script>
@endpush
