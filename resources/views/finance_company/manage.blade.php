@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Finance Company Management</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Finance Companies</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addFinanceCompanyModal">
            <iconify-icon icon="heroicons:plus-circle-20-solid"></iconify-icon>
            Add Finance Company
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card fc-table-card">
                <div class="card-header">
                    <h4 class="card-title">All Finance Companies</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Service Charge</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financeCompanies ?? [] as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fc-avatar">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                            <span class="fw-semibold">{{ $company->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($company->status === 'active')
                                            <span class="fc-status-badge fc-status-badge-active"><span class="fc-dot"></span>Active</span>
                                        @else
                                            <span class="fc-status-badge fc-status-badge-inactive"><span class="fc-dot"></span>Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->fixed_service_charge)
                                            <span class="fc-charge-badge fc-charge-badge-fixed">
                                                <iconify-icon icon="solar:wallet-money-bold"></iconify-icon>
                                                Fixed &middot; Rs {{ number_format($company->fixed_service_charge_amount, 2) }}
                                            </span>
                                        @else
                                            <span class="fc-charge-badge fc-charge-badge-percent">
                                                <iconify-icon icon="solar:calculator-bold"></iconify-icon>
                                                Percentage &middot; set in Loan Calculator
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-warning" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#editFinanceCompanyModal{{ $company->id }}">
                                                <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                            </button>
                                            <form action="{{ route('finance-companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this finance company?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal for this company -->
                                <div class="modal fade" id="editFinanceCompanyModal{{ $company->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content fc-modal-content">
                                            <div class="modal-header fc-modal-header">
                                                <div class="fc-modal-header-icon">
                                                    <iconify-icon icon="solar:pen-2-bold"></iconify-icon>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fc-modal-title">Edit Finance Company</h5>
                                                    <p class="fc-modal-subtitle">Update {{ $company->name }}'s details</p>
                                                </div>
                                                <button type="button" class="btn-close fc-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('finance-companies.update', $company) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body fc-modal-body">

                                                    <div class="fc-field">
                                                        <label class="fc-label">Company Name</label>
                                                        <input type="text" class="form-control fc-input" name="name" value="{{ $company->name }}" required>
                                                    </div>

                                                    <div class="fc-field">
                                                        <label class="fc-label">Status</label>
                                                        <div class="fc-status-toggle">
                                                            <input type="radio" class="fc-status-radio" name="status" value="active"
                                                                   id="status_active_{{ $company->id }}" {{ $company->status === 'active' ? 'checked' : '' }}>
                                                            <label for="status_active_{{ $company->id }}" class="fc-status-pill fc-status-pill-active">
                                                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Active
                                                            </label>
                                                            <input type="radio" class="fc-status-radio" name="status" value="inactive"
                                                                   id="status_inactive_{{ $company->id }}" {{ $company->status === 'inactive' ? 'checked' : '' }}>
                                                            <label for="status_inactive_{{ $company->id }}" class="fc-status-pill fc-status-pill-inactive">
                                                                <iconify-icon icon="solar:pause-circle-bold"></iconify-icon> Inactive
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="fc-charge-card">
                                                        <div class="fc-charge-row">
                                                            <div class="fc-charge-info">
                                                                <span class="fc-charge-title">Fixed Service Charge</span>
                                                                <span class="fc-charge-desc">Use one flat Rs amount instead of a percentage of the loan</span>
                                                            </div>
                                                            <label class="fc-switch">
                                                                <input type="checkbox" id="fixed_service_charge{{ $company->id }}"
                                                                       name="fixed_service_charge" value="1"
                                                                       {{ $company->fixed_service_charge ? 'checked' : '' }}
                                                                       onchange="toggleFixedAmount('fixed_service_charge{{ $company->id }}', 'fixedAmountWrap{{ $company->id }}')">
                                                                <span class="fc-switch-slider"></span>
                                                            </label>
                                                        </div>
                                                        <div class="fc-fixed-amount-wrap {{ $company->fixed_service_charge ? '' : 'd-none' }}" id="fixedAmountWrap{{ $company->id }}">
                                                            <label class="fc-label">Fixed Amount</label>
                                                            <div class="input-group fc-input-group">
                                                                <span class="input-group-text fc-input-prefix">Rs</span>
                                                                <input type="number" class="form-control fc-input" name="fixed_service_charge_amount"
                                                                       min="0" step="0.01" placeholder="e.g. 15000"
                                                                       value="{{ $company->fixed_service_charge_amount }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer fc-modal-footer">
                                                    <button type="button" class="btn fc-btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn fc-btn-primary">
                                                        <iconify-icon icon="solar:diskette-bold"></iconify-icon> Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No finance companies found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Finance Company Modal -->
<div class="modal fade" id="addFinanceCompanyModal" tabindex="-1" aria-labelledby="addFinanceCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fc-modal-content">
            <div class="modal-header fc-modal-header">
                <div class="fc-modal-header-icon">
                    <iconify-icon icon="solar:buildings-2-bold"></iconify-icon>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fc-modal-title" id="addFinanceCompanyModalLabel">Add New Finance Company</h5>
                    <p class="fc-modal-subtitle">Set up a company and how it charges service fees</p>
                </div>
                <button type="button" class="btn-close fc-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance-companies.store') }}" method="POST">
                @csrf
                <div class="modal-body fc-modal-body">

                    <div class="fc-field">
                        <label for="name" class="fc-label">Company Name</label>
                        <input type="text" class="form-control fc-input" id="name" name="name" placeholder="e.g. BOC, HNB, Sampath Bank" required>
                    </div>

                    <div class="fc-field">
                        <label class="fc-label">Status</label>
                        <div class="fc-status-toggle">
                            <input type="radio" class="fc-status-radio" name="status" value="active" id="status_active" checked>
                            <label for="status_active" class="fc-status-pill fc-status-pill-active">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Active
                            </label>
                            <input type="radio" class="fc-status-radio" name="status" value="inactive" id="status_inactive">
                            <label for="status_inactive" class="fc-status-pill fc-status-pill-inactive">
                                <iconify-icon icon="solar:pause-circle-bold"></iconify-icon> Inactive
                            </label>
                        </div>
                    </div>

                    <div class="fc-charge-card">
                        <div class="fc-charge-row">
                            <div class="fc-charge-info">
                                <span class="fc-charge-title">Fixed Service Charge</span>
                                <span class="fc-charge-desc">Use one flat Rs amount instead of a percentage of the loan</span>
                            </div>
                            <label class="fc-switch">
                                <input type="checkbox" id="fixed_service_charge" name="fixed_service_charge" value="1"
                                       onchange="toggleFixedAmount('fixed_service_charge', 'fixedAmountWrap')">
                                <span class="fc-switch-slider"></span>
                            </label>
                        </div>
                        <div class="fc-fixed-amount-wrap d-none" id="fixedAmountWrap">
                            <label class="fc-label">Fixed Amount</label>
                            <div class="input-group fc-input-group">
                                <span class="input-group-text fc-input-prefix">Rs</span>
                                <input type="number" class="form-control fc-input" name="fixed_service_charge_amount"
                                       min="0" step="0.01" placeholder="e.g. 15000">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer fc-modal-footer">
                    <button type="button" class="btn fc-btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn fc-btn-primary">
                        <iconify-icon icon="solar:diskette-bold"></iconify-icon> Save Finance Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleFixedAmount(checkboxId, wrapId) {
    const checkbox = document.getElementById(checkboxId);
    const wrap = document.getElementById(wrapId);
    if (!checkbox || !wrap) return;
    wrap.classList.toggle('d-none', !checkbox.checked);
}
</script>
@endpush

@push('styles')
<style>
/* ── Table polish ─────────────────────────────── */
.fc-avatar {
    width: 32px; height: 32px; border-radius: 9px;
    background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
    color: #fff; font-weight: 700; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.fc-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px;
    font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
}
.fc-status-badge-active   { background: #ECFDF5; color: #059669; }
.fc-status-badge-inactive { background: #FEF2F2; color: #DC2626; }
.fc-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }

.fc-charge-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px;
    font-size: .78rem; font-weight: 600;
}
.fc-charge-badge iconify-icon { font-size: 14px; }
.fc-charge-badge-fixed   { background: #EFF6FF; color: var(--primary-700); }
.fc-charge-badge-percent { background: var(--neutral-100); color: var(--neutral-600); }

/* ── Modal shell ──────────────────────────────── */
.fc-modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(15, 23, 42, .28);
}
.fc-modal-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 24px 26px;
    border: none;
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);
}
.fc-modal-header-icon {
    width: 42px; height: 42px; border-radius: 12px;
    background: rgba(255,255,255,.16);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 20px; flex-shrink: 0;
}
.fc-modal-title { color: #fff; font-weight: 700; font-size: 1.1rem; margin: 0 0 2px; }
.fc-modal-subtitle { color: rgba(255,255,255,.78); font-size: .8rem; margin: 0; }
.fc-modal-close { filter: invert(1) brightness(2); opacity: .85; }
.fc-modal-close:hover { opacity: 1; }

.fc-modal-body { padding: 26px; background: #fff; }
.fc-modal-footer { border: none; padding: 16px 26px 26px; background: #fff; }

/* ── Fields ───────────────────────────────────── */
.fc-field { margin-bottom: 20px; }
.fc-label {
    display: block; font-size: .76rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: var(--neutral-700); margin-bottom: 8px;
}
.fc-input {
    border-radius: 10px !important;
    border: 1.5px solid var(--neutral-200) !important;
    padding: 11px 14px !important;
    font-size: .95rem !important;
    background: var(--neutral-50) !important;
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
}
.fc-input:focus {
    border-color: var(--primary-600) !important;
    box-shadow: 0 0 0 3px rgba(72,127,255,.15) !important;
    background: #fff !important;
}
.fc-input-group { border-radius: 10px; overflow: hidden; }
.fc-input-prefix {
    background: var(--primary-600) !important;
    color: #fff !important;
    border: none !important;
    font-weight: 700;
    font-size: .85rem !important;
}

/* ── Status segmented control ────────────────── */
.fc-status-toggle { display: flex; gap: 8px; }
.fc-status-radio { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
.fc-status-pill {
    flex: 1;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid var(--neutral-200);
    background: var(--neutral-50);
    font-size: .85rem; font-weight: 600;
    color: var(--neutral-500);
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
}
.fc-status-radio:checked + .fc-status-pill-active {
    background: #ECFDF5; border-color: #34D399; color: #059669;
}
.fc-status-radio:checked + .fc-status-pill-inactive {
    background: #FEF2F2; border-color: #F87171; color: #DC2626;
}
.fc-status-radio:focus-visible + .fc-status-pill { outline: 2px solid var(--primary-600); outline-offset: 2px; }

/* ── Fixed service charge card ───────────────── */
.fc-charge-card {
    background: var(--neutral-50);
    border: 1.5px solid var(--neutral-200);
    border-radius: 14px;
    padding: 18px 20px;
}
.fc-charge-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.fc-charge-info { display: flex; flex-direction: column; gap: 2px; }
.fc-charge-title { font-weight: 700; font-size: .92rem; color: var(--neutral-800); }
.fc-charge-desc { font-size: .78rem; color: var(--neutral-500); line-height: 1.4; }

/* Real toggle switch */
.fc-switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; }
.fc-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
.fc-switch-slider {
    position: absolute; inset: 0;
    background: var(--neutral-300);
    border-radius: 999px;
    cursor: pointer;
    transition: background-color .2s ease;
}
.fc-switch-slider::before {
    content: "";
    position: absolute;
    width: 20px; height: 20px;
    left: 3px; top: 3px;
    background: #fff;
    border-radius: 50%;
    transition: transform .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.35);
}
.fc-switch input:checked + .fc-switch-slider { background: var(--primary-600); }
.fc-switch input:checked + .fc-switch-slider::before { transform: translateX(20px); }
.fc-switch input:focus-visible + .fc-switch-slider { outline: 2px solid var(--primary-600); outline-offset: 2px; }

.fc-fixed-amount-wrap {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--neutral-300);
    animation: fcFadeIn .18s ease;
}
@keyframes fcFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Buttons ──────────────────────────────────── */
.fc-btn-secondary {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    background: var(--neutral-100);
    border: none;
    color: var(--neutral-700);
}
.fc-btn-secondary:hover { background: var(--neutral-200); color: var(--neutral-800); }
.fc-btn-primary {
    display: flex; align-items: center; gap: 8px;
    border-radius: 10px;
    padding: 10px 22px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    border: none;
    color: #fff;
    box-shadow: 0 4px 12px rgba(72,127,255,.35);
    transition: filter .15s ease, transform .1s ease;
}
.fc-btn-primary:hover { filter: brightness(1.06); color: #fff; }
.fc-btn-primary:active { transform: translateY(1px); }
</style>
@endpush

@endsection
