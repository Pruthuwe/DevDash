@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Loan Calculator</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Loan Calculator</li>
            </ol>
        </nav>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 1 — Finance Company + Bike Selection + Loan Details
═══════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- 1. Finance Company --}}
    <div class="col-12">
        <div class="card lc-card">
            <div class="card-header lc-card-header">
                <iconify-icon icon="solar:buildings-outline" class="lc-header-icon"></iconify-icon>
                <span class="lc-header-title">Finance Company</span>
            </div>
            <div class="card-body">
                <label class="lc-label">Company Name</label>
                <select id="financeCompanySelect" class="form-select lc-select"
                       onchange="onCompanyChange()">
                    <option value="">— Select Finance Company —</option>
                    @foreach($financeCompanies as $fc)
                        <option value="{{ $fc->id }}">{{ $fc->name }}</option>
                    @endforeach
                </select>
                <small class="lc-hint mt-6 d-block">
                    Pick the company providing this loan.
                    <a href="{{ route('manage.finance-companies') }}" target="_blank" class="lc-hint-link">Manage companies</a>
                </small>
            </div>
        </div>
    </div>

    {{-- 2. Bike Selection --}}
    <div class="col-12">
        <div class="card lc-card">
            <div class="card-header lc-card-header">
                <iconify-icon icon="material-symbols:two-wheeler" class="lc-header-icon"></iconify-icon>
                <span class="lc-header-title">Bike Selection</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="lc-label">Fuel Type</label>
                        <select id="fuelType" class="form-select lc-select"
                                onchange="onFuelTypeChange()">
                            <option value="">— Select Fuel Type —</option>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft['id'] }}">{{ $ft['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="lc-label">Brand</label>
                        <select id="brand" class="form-select lc-select"
                                onchange="onBrandChange()" disabled>
                            <option value="">— Select Brand —</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="lc-label">Bike Model</label>
                        <select id="bikeModel" class="form-select lc-select"
                                onchange="onBikeSelect()" disabled>
                            <option value="">— Select Bike —</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Loan Details --}}
    <div class="col-12">
        <div class="card lc-card">
            <div class="card-header lc-card-header">
                <iconify-icon icon="solar:calculator-outline" class="lc-header-icon"></iconify-icon>
                <span class="lc-header-title">Loan Details</span>
            </div>
            <div class="card-body">
                {{-- Row 1: Bike Price, Loan Amount, Interest Rate --}}
                <div class="row g-3 mb-3">
                    <div class="col-lg-4 col-md-4">
                        <label class="lc-label">Bike Price (Rs)</label>
                        <div class="input-group lc-input-group">
                            <span class="input-group-text lc-input-prefix">Rs</span>
                            <input type="number" id="bikePrice" class="form-control lc-input"
                                   placeholder="Auto-filled from bike" oninput="recalculate()">
                        </div>
                        <small class="lc-hint mt-6 d-block">Auto-filled when bike selected, or type manually</small>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label class="lc-label">Loan Amount</label>
                        <div class="input-group lc-input-group">
                            <span class="input-group-text lc-input-prefix">Rs</span>
                            <input type="number" id="loanAmount" class="form-control lc-input"
                                   placeholder="Enter loan amount" oninput="recalculate()">
                        </div>
                        <small class="lc-hint mt-6 d-block">Amount financed via loan</small>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label class="lc-label">Interest Rate</label>
                        <div class="input-group lc-input-group">
                            <input type="number" id="interestRate" class="form-control lc-input"
                                   value="1.5" step="0.1" min="0" max="100" oninput="recalculate()">
                            <span class="input-group-text lc-input-prefix">%</span>
                        </div>
                        <small class="lc-hint mt-6 d-block">Default: 1.5% (change if different)</small>
                    </div>
                </div>

                {{-- Row 2: Service Charge % + Service Charge (Rs) --}}
                <div class="row g-3 mb-3">
                    <div class="col-lg-6 col-md-6">
                        <label class="lc-label">Service Charge %</label>
                        <div class="input-group lc-input-group">
                            <input type="number" id="serviceChargePercent" class="form-control lc-input"
                                   value="5" step="0.1" min="0" max="100" oninput="recalculate()">
                            <span class="input-group-text lc-input-prefix">%</span>
                        </div>
                        <small id="serviceChargePercentHint" class="lc-hint mt-6 d-block">Used when the finance company doesn't have a fixed service charge</small>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <label class="lc-label">Service Charge (Rs)</label>
                        <div class="input-group lc-input-group">
                            <span class="input-group-text lc-input-prefix">Rs</span>
                            <input type="text" id="dispServiceCharge" class="form-control lc-input lc-readonly" readonly placeholder="—">
                        </div>
                        <small id="serviceChargeHint" class="lc-hint mt-6 d-block">% of loan (no cap)</small>
                    </div>
                </div>

                {{-- Row 3: RMV Fee + Save button --}}
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4 col-md-6">
                        <label class="lc-label">RMV Fee</label>
                        <div class="input-group lc-input-group">
                            <span class="input-group-text lc-input-prefix">Rs</span>
                            <input type="number" id="rmv" class="form-control lc-input"
                                   placeholder="10160.00" value="10160" oninput="recalculate()">
                        </div>
                        <small class="lc-hint mt-6 d-block">Revenue & Motor Vehicle Department fee</small>
                    </div>
                    <div class="col-lg-4 col-md-6 d-flex flex-column">
                        <label class="lc-label d-none d-lg-block opacity-0">Save</label>
                        <button type="button" id="saveLoanPlanBtn" class="lc-save-btn mt-2 mt-md-0"
                                onclick="saveLoanPlan()" disabled>
                            <iconify-icon icon="solar:diskette-bold" class="lc-save-btn-icon"></iconify-icon>
                            <span>Save Loan Plan</span>
                        </button>
                        <div id="saveStatus" class="lc-hint mt-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 2 — Down Payment Breakdown
═══════════════════════════════════════════ --}}
<div class="card lc-card mb-4">
    <div class="card-header lc-card-header">
        <iconify-icon icon="solar:wallet-money-outline" class="lc-header-icon"></iconify-icon>
        <span class="lc-header-title">Down Payment Breakdown</span>
    </div>
    <div class="card-body lc-dp-body">

        {{-- Row 1: Bike DP + RMV --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="lc-label lc-label-blue">Bike DP</label>
                <div class="input-group lc-input-group">
                    <span class="input-group-text lc-input-prefix">Rs</span>
                    <input type="text" id="dispBikeDP" class="form-control lc-input lc-readonly" readonly placeholder="—">
                </div>
                <small class="lc-hint-blue mt-6 d-block">Price − Loan</small>
            </div>

            <div class="col-md-6">
                <label class="lc-label lc-label-blue">RMV</label>
                <div class="input-group lc-input-group">
                    <span class="input-group-text lc-input-prefix">Rs</span>
                    <input type="text" id="dispRMV" class="form-control lc-input lc-readonly" readonly placeholder="—">
                </div>
                <small class="lc-hint-blue mt-6 d-block">Auto-filled</small>
            </div>
        </div>

        {{-- Row 2: Minimum DP --}}
        <div class="row g-3">
            <div class="col-12">
                <div class="w-100 p-3 rounded-3 d-flex justify-content-between align-items-center lc-dp-total">
                    <div>
                        <small class="d-block text-white opacity-75 mb-1" style="font-size:var(--font-xs); font-weight:600;">Minimum Down Payment</small>
                        <small class="text-white opacity-75" style="font-size:var(--font-xxs);">Bike DP + Service Charge + RMV</small>
                    </div>
                    <span id="dispMinimumDP" class="fw-bold text-white ms-3" style="font-size:var(--font-xl);">—</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 3 — Calculation Results
═══════════════════════════════════════════ --}}
<div class="card lc-card">
    <div class="card-header lc-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:chart-outline" class="lc-header-icon"></iconify-icon>
            <span class="lc-header-title">Calculation Results</span>
        </div>
        <span id="companyBadge" class="badge d-none lc-company-badge"></span>
    </div>
    <div class="card-body">

        {{-- Empty state --}}
        <div id="emptyState" class="text-center py-5">
            <div class="mb-3" style="font-size: 3rem; color: #7dd3fc;">
                <iconify-icon icon="solar:calculator-outline"></iconify-icon>
            </div>
            <p class="mb-0 text-secondary-light" style="font-size:var(--font-sm);">Fill in the details above to see the loan breakdown here.</p>
        </div>

        {{-- Results --}}
        <div id="resultsArea" class="d-none">

            {{-- 4 summary metric cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white lc-metric lc-metric-1">
                        <p class="lc-metric-label">Monthly Payment</p>
                        <h6 id="resMonthly" class="fw-bold text-white mb-0" style="font-size:var(--font-sm);">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white lc-metric lc-metric-2">
                        <p class="lc-metric-label">Minimum DP</p>
                        <h6 id="resMinDP" class="fw-bold text-white mb-0" style="font-size:var(--font-sm);">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white lc-metric lc-metric-3">
                        <p class="lc-metric-label">Total Interest</p>
                        <h6 id="resTotalInterest" class="fw-bold text-white mb-0" style="font-size:var(--font-sm);">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white lc-metric lc-metric-4">
                        <p class="lc-metric-label">Total Payable</p>
                        <h6 id="resTotalPayable" class="fw-bold text-white mb-0" style="font-size:var(--font-sm);">—</h6>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- Detail tables --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="lc-table-heading">
                        <iconify-icon icon="solar:home-smile-outline" class="me-1"></iconify-icon>
                        Bike &amp; Down Payment
                    </h6>
                    <table class="table table-sm lc-detail-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-secondary-light">Bike Price</td>
                                <td id="resBikePrice" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Loan Amount</td>
                                <td id="resLoanAmount" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Bike Down Payment</td>
                                <td id="resBikeDP" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Service Charge</td>
                                <td id="resServiceCharge" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">RMV Fee</td>
                                <td id="resRMV" class="fw-medium text-end">—</td>
                            </tr>
                            <tr class="lc-highlight-row">
                                <td class="fw-semibold">Minimum Down Payment</td>
                                <td id="resMinimumDP" class="fw-bold text-end">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="lc-table-heading">
                        <iconify-icon icon="solar:chart-2-outline" class="me-1"></iconify-icon>
                        Loan Repayment
                    </h6>
                    <table class="table table-sm lc-detail-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-secondary-light">Interest Rate</td>
                                <td id="resInterestRate" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Loan Term</td>
                                <td class="text-end">
                                    <select id="loanTerm" class="form-select form-select-sm lc-term-select"
                                            onchange="recalculate()">
                                        <option value="3">3 Months</option>
                                        <option value="6">6 Months</option>
                                        <option value="12">12 Months</option>
                                        <option value="24">24 Months</option>
                                        <option value="36" selected>36 Months</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Monthly Payment</td>
                                <td id="resMonthlyRow" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Total Interest</td>
                                <td id="resTotalInterestRow" class="fw-medium text-end">—</td>
                            </tr>
                            <tr class="lc-highlight-row">
                                <td class="fw-semibold">Total Payable</td>
                                <td id="resTotalPayableRow" class="fw-bold text-end">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="resCompanyAlert" class="mt-3 mb-0 p-3 rounded-3 d-none d-flex align-items-center gap-2 lc-company-alert">
                <iconify-icon icon="solar:buildings-outline" style="font-size: 18px;"></iconify-icon>
                <span style="font-size:var(--font-sm);">Finance company: <strong id="resCompanyName"></strong></span>
            </div>

        </div>{{-- end resultsArea --}}

    </div>
</div>

<script>
const ALL_PRODUCTS         = @json($products);
const ALL_FUEL_TYPES       = @json($fuelTypes);
const ALL_FINANCE_COMPANIES = @json($financeCompanies);
</script>

@push('scripts')
<script>
const fmt = v => 'Rs ' + Number(parseFloat(v).toFixed(2)).toLocaleString('en-LK');
const val  = id => parseFloat(document.getElementById(id)?.value) || 0;
const set  = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

let currentBikeId = null;
let currentServiceCharge = 0;
let currentServiceChargePercent = null;

function onFuelTypeChange() {
    const ftId    = parseInt(document.getElementById('fuelType').value);
    const brandEl = document.getElementById('brand');
    const bikeEl  = document.getElementById('bikeModel');

    brandEl.innerHTML = '<option value="">— Select Brand —</option>';
    bikeEl.innerHTML  = '<option value="">— Select Bike —</option>';
    brandEl.disabled  = true;
    bikeEl.disabled   = true;

    if (!ftId) { recalculate(); return; }

    const ft = ALL_FUEL_TYPES.find(f => parseInt(f.id) === ftId);
    if (!ft) return;

    ft.brands.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.id;
        opt.text  = b.name;
        brandEl.appendChild(opt);
    });
    brandEl.disabled = false;
    recalculate();
}

function onBrandChange() {
    const brandId = parseInt(document.getElementById('brand').value);
    const bikeEl  = document.getElementById('bikeModel');

    bikeEl.innerHTML = '<option value="">— Select Bike —</option>';
    bikeEl.disabled  = true;

    if (!brandId) { recalculate(); return; }

    const bikes = ALL_PRODUCTS.filter(p => parseInt(p.subcategory_id) === brandId);
    if (bikes.length === 0) {
        bikeEl.innerHTML = '<option value="">No bikes found for this brand</option>';
        bikeEl.disabled  = true;
        recalculate();
        return;
    }
    bikes.forEach(bike => {
        const opt = document.createElement('option');
        opt.value = bike.id;
        opt.text  = bike.name;
        bikeEl.appendChild(opt);
    });
    bikeEl.disabled = false;
    recalculate();
}

function onBikeSelect() {
    const bikeId = parseInt(document.getElementById('bikeModel').value);
    if (!bikeId) return;
    currentBikeId = bikeId;

    const bike = ALL_PRODUCTS.find(p => parseInt(p.id) === bikeId);
    if (!bike) return;

    set('bikePrice',    bike.price         || '');
    set('loanAmount',   bike.loan_amount   || '');
    set('rmv',          bike.rmv           || 10160);
    set('interestRate', bike.interest_rate || 1.5);

    document.getElementById('saveLoanPlanBtn').disabled = false;
    recalculate();
}

function onCompanyChange() {
    recalculate();
}

function saveLoanPlan() {
    const statusEl  = document.getElementById('saveStatus');
    const companyId = document.getElementById('financeCompanySelect').value;

    if (!currentBikeId) { statusEl.innerHTML = '<span class="text-danger">Select a bike first.</span>'; return; }
    if (!companyId)     { statusEl.innerHTML = '<span class="text-danger">Select a finance company first.</span>'; return; }

    statusEl.innerHTML = '<span class="text-secondary">Saving...</span>';

    fetch('/bike-loan-plans', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({
            product_id:         currentBikeId,
            finance_company_id: companyId,
            loan_amount:        val('loanAmount'),
            interest_rate:      val('interestRate'),
            rmv:                val('rmv'),
            service_charge:         currentServiceCharge,
            service_charge_percent: currentServiceChargePercent,
        }),
    })
        .then(r => r.json())
        .then(data => {
            if (data.success || data.id || data.message) {
                statusEl.innerHTML = '<span class="text-success">Saved ✓</span>';
            } else {
                statusEl.innerHTML = '<span class="text-danger">Failed to save.</span>';
            }
        })
        .catch(() => {
            statusEl.innerHTML = '<span class="text-danger">Failed to save.</span>';
        });
}

function getSelectedFinanceCompany() {
    const companySel = document.getElementById('financeCompanySelect');
    const companyId  = parseInt(companySel.value);
    if (!companyId) return null;
    return ALL_FINANCE_COMPANIES.find(c => parseInt(c.id) === companyId) || null;
}

function recalculate() {
    const bikePrice    = val('bikePrice');
    const loanAmount   = val('loanAmount');
    const rmv          = val('rmv');
    const interestRate = val('interestRate');
    const months       = parseInt(document.getElementById('loanTerm').value) || 36;
    const companySel   = document.getElementById('financeCompanySelect');
    let company        = companySel.options[companySel.selectedIndex]?.text?.trim() || '';
    if (company === '— Select Finance Company —') company = '';

    const selectedCompany = getSelectedFinanceCompany();
    const isFixed          = !!(selectedCompany && selectedCompany.fixed_service_charge);
    const percentInput     = document.getElementById('serviceChargePercent');
    const percent          = val('serviceChargePercent');

    let serviceCharge = 0;
    if (isFixed) {
        serviceCharge = parseFloat(selectedCompany.fixed_service_charge_amount) || 0;
        percentInput.disabled = true;
    } else {
        serviceCharge = loanAmount > 0 ? (percent / 100) * loanAmount : 0;
        percentInput.disabled = false;
    }

    currentServiceCharge        = serviceCharge;
    currentServiceChargePercent = isFixed ? null : percent;

    const bikeDP        = (bikePrice > 0 && loanAmount > 0) ? bikePrice - loanAmount : 0;
    const minimumDP     = bikeDP + serviceCharge + rmv;

    document.getElementById('dispBikeDP').value          = bikeDP > 0        ? bikeDP.toFixed(2)        : '';
    document.getElementById('dispServiceCharge').value   = serviceCharge > 0 ? serviceCharge.toFixed(2) : '';
    document.getElementById('dispRMV').value             = rmv > 0           ? rmv.toFixed(2)           : '';
    document.getElementById('dispMinimumDP').textContent = minimumDP > 0     ? fmt(minimumDP)           : '—';

    const scHint = document.getElementById('serviceChargeHint');
    const scPercentHint = document.getElementById('serviceChargePercentHint');
    if (isFixed) {
        scHint.innerHTML = `Fixed service charge set by <strong>${company}</strong>: <strong>${fmt(serviceCharge)}</strong>`;
        scPercentHint.innerHTML = `<strong>${company}</strong> uses a fixed service charge — percentage is ignored`;
    } else if (loanAmount > 0) {
        scHint.innerHTML = `${percent}% × ${fmt(loanAmount)} = <strong>${fmt(serviceCharge)}</strong>`;
        scPercentHint.textContent = "Used when the finance company doesn't have a fixed service charge";
    } else {
        scHint.textContent = `${percent}% of loan (no cap)`;
        scPercentHint.textContent = "Used when the finance company doesn't have a fixed service charge";
    }

    let monthlyPayment = 0;
    if (loanAmount > 0 && interestRate > 0 && months > 0) {
        const totalInterest = loanAmount * (interestRate / 100) * months;
        monthlyPayment = (loanAmount + totalInterest) / months;
    }

    const totalPayable  = monthlyPayment * months;
    const totalInterest = totalPayable - loanAmount;

    const badge = document.getElementById('companyBadge');
    if (company) {
        badge.textContent = company;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }

    if (bikePrice <= 0 && loanAmount <= 0) {
        document.getElementById('emptyState').classList.remove('d-none');
        document.getElementById('resultsArea').classList.add('d-none');
        return;
    }

    document.getElementById('emptyState').classList.add('d-none');
    document.getElementById('resultsArea').classList.remove('d-none');

    document.getElementById('resMonthly').textContent          = fmt(monthlyPayment);
    document.getElementById('resMinDP').textContent            = fmt(minimumDP);
    document.getElementById('resTotalInterest').textContent    = fmt(totalInterest);
    document.getElementById('resTotalPayable').textContent     = fmt(totalPayable);

    document.getElementById('resBikePrice').textContent        = fmt(bikePrice);
    document.getElementById('resLoanAmount').textContent       = fmt(loanAmount);
    document.getElementById('resBikeDP').textContent           = fmt(bikeDP);
    document.getElementById('resServiceCharge').textContent    = fmt(serviceCharge);
    document.getElementById('resRMV').textContent              = fmt(rmv);
    document.getElementById('resMinimumDP').textContent        = fmt(minimumDP);
    document.getElementById('resInterestRate').textContent     = interestRate + '% per month';

    document.getElementById('resMonthlyRow').textContent       = fmt(monthlyPayment);
    document.getElementById('resTotalInterestRow').textContent = fmt(totalInterest);
    document.getElementById('resTotalPayableRow').textContent  = fmt(totalPayable);

    const alertEl = document.getElementById('resCompanyAlert');
    if (company) {
        document.getElementById('resCompanyName').textContent = company;
        alertEl.classList.remove('d-none');
    } else {
        alertEl.classList.add('d-none');
    }
}
</script>
@endpush

@push('styles')
<style>
/* ── Card shell ─────────────────────────────── */
.lc-card {
    border: none !important;
    border-radius: 12px;
    border-top: 4px solid var(--brand) !important;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.lc-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--neutral-50);
    border-bottom: 1px solid var(--neutral-200);
    padding: 14px 20px;
    border-radius: 12px 12px 0 0;
}
.lc-header-icon  { font-size: 1.1rem; color: var(--brand); flex-shrink: 0; }
.lc-header-title { font-size: var(--font-sm); font-weight: 700; color: var(--neutral-800); }

/* ── Labels ─────────────────────────────────── */
.lc-label {
    display: block;
    font-size: var(--font-xs);
    font-weight: 700;
    color: var(--neutral-700);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.lc-label-blue { color: var(--brand) !important; }

/* ── Hints ──────────────────────────────────── */
.lc-hint      { font-size: var(--font-xs); color: var(--neutral-400); }
.lc-hint-blue { font-size: var(--font-xs); color: #38bdf8; }
.lc-hint-link { color: var(--brand); text-decoration: none; }
.lc-hint-link:hover { text-decoration: underline; }

/* ── Select ─────────────────────────────────── */
.lc-select {
    font-size: var(--font-sm) !important;
    color: var(--neutral-800) !important;
    border-color: var(--neutral-200) !important;
    background-color: var(--neutral-50) !important;
    border-radius: 8px !important;
}
.lc-select:focus {
    border-color: var(--brand) !important;
    box-shadow: 0 0 0 3px var(--primary-light) !important;
}

/* ── Input group ────────────────────────────── */
.lc-input-group { border-radius: 8px; overflow: hidden; }
.lc-input-group:focus-within {
    box-shadow: 0 0 0 3px var(--primary-light);
    border-radius: 8px;
}
.lc-input-prefix {
    background: var(--brand) !important;
    color: #fff !important;
    border: none !important;
    font-size: var(--font-xs) !important;
    font-weight: 700;
}
.lc-input {
    font-size: var(--font-sm) !important;
    color: var(--neutral-900) !important;
    border: none !important;
    background: var(--neutral-50) !important;
}
.lc-input:focus { box-shadow: none !important; }
.lc-readonly {
    background: #e0f2fe !important;
    color: var(--brand) !important;
    font-weight: 600 !important;
}

/* ── Save Loan Plan button (3D style) ───────── */
.lc-save-btn {
    width: 100%;
    height: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    border-radius: 10px;
    padding: 13px 20px 16px;
    font-size: var(--font-sm);
    font-weight: 700;
    letter-spacing: .2px;
    color: #fff;
    background: #0369a1;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.25),
        inset 0 -3px 0 rgba(2,46,71,.45),
        0 4px 0 #023e5c,
        0 8px 16px rgba(2,46,71,.4);
    transition: transform .1s ease, box-shadow .1s ease, filter .1s ease;
    cursor: pointer;
    position: relative;
}
.lc-save-btn-icon { font-size: 18px; filter: drop-shadow(0 1px 0 rgba(0,0,0,.15)); }
.lc-save-btn:hover:not(:disabled) {
    filter: brightness(1.04);
}
.lc-save-btn:active:not(:disabled) {
    transform: translateY(3px);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.25),
        inset 0 -2px 0 rgba(2,82,122,.3),
        0 1px 0 #0369a1,
        0 2px 6px rgba(2,82,122,.3);
}
.lc-save-btn:disabled {
    background: #93c5fd;
    color: #eff6ff;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.35),
        inset 0 -3px 0 rgba(29,78,138,.35),
        0 4px 0 #60a5fa,
        0 6px 12px rgba(29,78,138,.25);
    cursor: not-allowed;
}

/* ── Down Payment section body ──────────────── */
.lc-dp-body { background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%); }
.lc-dp-total {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
    box-shadow: 0 4px 15px rgba(14,165,233,0.3);
}

/* ── Metric summary cards ───────────────────── */
.lc-metric { box-shadow: 0 4px 15px rgba(14,165,233,0.3); }
.lc-metric-label { font-size: var(--font-xxs); margin-bottom: 4px; opacity: .85; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
.lc-metric-1 { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
.lc-metric-2 { background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); }
.lc-metric-3 { background: linear-gradient(135deg, #0ea5e9 0%, #7dd3fc 100%); }
.lc-metric-4 { background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%); }

/* ── Detail tables ──────────────────────────── */
.lc-table-heading {
    font-size: var(--font-xxs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #0ea5e9;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}
.lc-detail-table { font-size: var(--font-sm); }
.lc-detail-table td { padding: 9px 12px; border-color: var(--neutral-100); }
.lc-detail-table td.text-secondary-light { font-size: var(--font-xs); }
.lc-highlight-row td {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    font-weight: 700;
}
.lc-term-select {
    font-size: var(--font-xs) !important;
    border-color: var(--neutral-200) !important;
}

/* ── Company badge & alert ──────────────────── */
.lc-company-badge {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #7dd3fc;
    font-size: var(--font-xs);
}
.lc-company-alert {
    background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
    border-left: 4px solid #0ea5e9;
    color: #0369a1;
}
.lc-company-alert iconify-icon { color: #0369a1; }
</style>
@endpush

@endsection