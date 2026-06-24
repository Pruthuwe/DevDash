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
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0ea5e9 !important;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
                <h6 class="mb-0 fw-semibold" style="color: #0369a1;">
                    <iconify-icon icon="solar:buildings-outline" class="me-2"></iconify-icon>
                    Finance Company
                </h6>
            </div>
            <div class="card-body">
                <label class="form-label fw-medium">Company Name</label>
                <select id="financeCompanySelect" class="form-select border-0 bg-light"
                       style="box-shadow: 0 0 0 2px #bae6fd;"
                       onchange="onCompanyChange()">
                    <option value="">— Select Finance Company —</option>
                    @foreach($financeCompanies as $fc)
                        <option value="{{ $fc->id }}">{{ $fc->name }}</option>
                    @endforeach
                </select>
                <small class="text-secondary-light">
                    Pick the company providing this loan.
                    <a href="{{ route('manage.finance-companies') }}" target="_blank">Manage companies</a>
                </small>
            </div>
        </div>
    </div>

    {{-- 2. Bike Selection --}}
    <div class="col-12">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0ea5e9 !important;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
                <h6 class="mb-0 fw-semibold" style="color: #0369a1;">
                    <iconify-icon icon="material-symbols:two-wheeler" class="me-2"></iconify-icon>
                    Bike Selection
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-medium">Fuel Type</label>
                        <select id="fuelType" class="form-select border-0 bg-light"
                                style="box-shadow: 0 0 0 2px #bae6fd;"
                                onchange="onFuelTypeChange()">
                            <option value="">— Select Fuel Type —</option>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft['id'] }}">{{ $ft['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-medium">Brand</label>
                        <select id="brand" class="form-select border-0 bg-light"
                                style="box-shadow: 0 0 0 2px #bae6fd;"
                                onchange="onBrandChange()" disabled>
                            <option value="">— Select Brand —</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-medium">Bike Model</label>
                        <select id="bikeModel" class="form-select border-0 bg-light"
                                style="box-shadow: 0 0 0 2px #bae6fd;"
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
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0ea5e9 !important;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
                <h6 class="mb-0 fw-semibold" style="color: #0369a1;">
                    <iconify-icon icon="solar:calculator-outline" class="me-2"></iconify-icon>
                    Loan Details
                </h6>
            </div>
            <div class="card-body">
                {{-- Row 1: Bike Price, Loan Amount, Interest Rate --}}
                <div class="row g-3 mb-3">
                    <div class="col-lg-4 col-md-4">
                        <label class="form-label fw-medium">Bike Price (Rs)</label>
                        <div class="input-group">
                            <span class="input-group-text text-white border-0" style="background:#0ea5e9;">Rs</span>
                            <input type="number" id="bikePrice" class="form-control border-0 bg-light"
                                   style="box-shadow: 0 0 0 2px #bae6fd;"
                                   placeholder="Auto-filled from bike" oninput="recalculate()">
                        </div>
                        <small class="text-secondary-light">Auto-filled when bike selected, or type manually</small>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label class="form-label fw-medium">Loan Amount</label>
                        <div class="input-group">
                            <span class="input-group-text text-white border-0" style="background:#0ea5e9;">Rs</span>
                            <input type="number" id="loanAmount" class="form-control border-0 bg-light"
                                   style="box-shadow: 0 0 0 2px #bae6fd;"
                                   placeholder="Auto-filled from bike" oninput="recalculate()">
                        </div>
                        <small class="text-secondary-light">Amount financed via loan</small>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label class="form-label fw-medium">Interest Rate</label>
                        <div class="input-group">
                            <input type="number" id="interestRate" class="form-control border-0 bg-light"
                                   style="box-shadow: 0 0 0 2px #bae6fd;"
                                   value="1.5" step="0.1" min="0" max="100" oninput="recalculate()">
                            <span class="input-group-text text-white border-0" style="background:#0ea5e9;">%</span>
                        </div>
                        <small class="text-secondary-light">Default 1.5% — auto-filled from bike.</small>
                    </div>
                </div>

                {{-- Row 2: RMV Fee + Save button --}}
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-medium">RMV Fee</label>
                        <div class="input-group">
                            <span class="input-group-text text-white border-0" style="background:#0ea5e9;">Rs</span>
                            <input type="number" id="rmv" class="form-control border-0 bg-light"
                                   style="box-shadow: 0 0 0 2px #bae6fd;"
                                   placeholder="10160.00" value="10160" oninput="recalculate()">
                        </div>
                        <small class="text-secondary-light">Revenue & Motor Vehicle Department fee</small>
                    </div>
                    <div class="col-lg-4 col-md-6 d-flex flex-column">
                        <label class="form-label fw-medium d-none d-lg-block opacity-0">Save</label>
                        <button type="button" id="saveLoanPlanBtn" class="btn w-100 mt-2 mt-md-0"
                                style="background:#0ea5e9; color:#fff;" onclick="saveLoanPlan()" disabled>
                            <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon>
                            Save Loan Plan
                        </button>
                        <div id="saveStatus" class="small mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 2 — Down Payment Breakdown
═══════════════════════════════════════════ --}}
<div class="card mb-4 border-0 shadow-sm" style="border-top: 4px solid #0ea5e9 !important;">
    <div class="card-header border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
        <h6 class="mb-0 fw-semibold" style="color: #0369a1;">
            <iconify-icon icon="solar:wallet-money-outline" class="me-2"></iconify-icon>
            Down Payment Breakdown
        </h6>
    </div>
    <div class="card-body" style="background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);">

        {{-- Row 1: Bike DP + Service Charge --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium" style="color: #0369a1;">Bike DP</label>
                <div class="input-group">
                    <span class="input-group-text text-white border-0" style="background: #0ea5e9;">Rs</span>
                    <input type="text" id="dispBikeDP" class="form-control border-0" readonly placeholder="—"
                           style="background: #e0f2fe; color: #0369a1; font-weight: 600;">
                </div>
                <small style="color: #38bdf8;">Price − Loan</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium" style="color: #0369a1;">Service Charge</label>
                <div class="input-group">
                    <span class="input-group-text text-white border-0" style="background: #0ea5e9;">Rs</span>
                    <input type="text" id="dispServiceCharge" class="form-control border-0" readonly placeholder="—"
                           style="background: #e0f2fe; color: #0369a1; font-weight: 600;">
                </div>
                <small id="serviceChargeHint" style="color: #38bdf8;">5% of loan (max Rs 25,000)</small>
            </div>
        </div>

        {{-- Row 2: RMV + Minimum DP --}}
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-medium" style="color: #0369a1;">RMV</label>
                <div class="input-group">
                    <span class="input-group-text text-white border-0" style="background: #0ea5e9;">Rs</span>
                    <input type="text" id="dispRMV" class="form-control border-0" readonly placeholder="—"
                           style="background: #e0f2fe; color: #0369a1; font-weight: 600;">
                </div>
                <small style="color: #38bdf8;">Auto-filled</small>
            </div>

            <div class="col-md-6 d-flex align-items-center">
                <div class="w-100 p-3 rounded-3 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
                    <div>
                        <small class="d-block text-white opacity-75 mb-1">Minimum Down Payment</small>
                        <small class="text-white opacity-75" style="font-size:10px;">Bike DP + Service Charge + RMV</small>
                    </div>
                    <span id="dispMinimumDP" class="fw-bold text-white fs-5 ms-3">—</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 3 — Calculation Results
═══════════════════════════════════════════ --}}
<div class="card border-0 shadow-sm" style="border-top: 4px solid #0ea5e9 !important;">
    <div class="card-header border-0 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
        <h6 class="mb-0 fw-semibold" style="color: #0369a1;">
            <iconify-icon icon="solar:chart-outline" class="me-2"></iconify-icon>
            Calculation Results
        </h6>
        <span id="companyBadge" class="badge d-none"
              style="background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc;"></span>
    </div>
    <div class="card-body">

        {{-- Empty state --}}
        <div id="emptyState" class="text-center py-5">
            <div class="mb-3" style="font-size: 3rem; color: #7dd3fc;">
                <iconify-icon icon="solar:calculator-outline"></iconify-icon>
            </div>
            <p class="mb-0 text-secondary-light">Fill in the details above to see the loan breakdown here.</p>
        </div>

        {{-- Results --}}
        <div id="resultsArea" class="d-none">

            {{-- 4 summary metric cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white"
                         style="background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
                        <p class="text-xs mb-1 opacity-75">Monthly Payment</p>
                        <h6 id="resMonthly" class="fw-bold text-white mb-0">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white"
                         style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
                        <p class="text-xs mb-1 opacity-75">Minimum DP</p>
                        <h6 id="resMinDP" class="fw-bold text-white mb-0">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white"
                         style="background: linear-gradient(135deg, #0ea5e9 0%, #7dd3fc 100%); box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
                        <p class="text-xs mb-1 opacity-75">Total Interest</p>
                        <h6 id="resTotalInterest" class="fw-bold text-white mb-0">—</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center text-white"
                         style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%); box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
                        <p class="text-xs mb-1 opacity-75">Total Payable</p>
                        <h6 id="resTotalPayable" class="fw-bold text-white mb-0">—</h6>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- Detail tables --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-semibold text-uppercase mb-3" style="font-size: 11px; color: #0ea5e9; letter-spacing: 1px;">
                        <iconify-icon icon="solar:home-smile-outline" class="me-1"></iconify-icon>
                        Bike & Down Payment
                    </h6>
                    <table class="table table-sm text-sm mb-0">
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
                            <tr style="background: #e0f2fe;">
                                <td class="fw-semibold" style="color: #0369a1;">Minimum Down Payment</td>
                                <td id="resMinimumDP" class="fw-bold text-end" style="color: #0369a1;">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold text-uppercase mb-3" style="font-size: 11px; color: #0ea5e9; letter-spacing: 1px;">
                        <iconify-icon icon="solar:chart-2-outline" class="me-1"></iconify-icon>
                        Loan Repayment
                    </h6>
                    <table class="table table-sm text-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="text-secondary-light">Interest Rate</td>
                                <td id="resInterestRate" class="fw-medium text-end">—</td>
                            </tr>
                            <tr>
                                <td class="text-secondary-light">Loan Term</td>
                                <td class="text-end">
                                    <select id="loanTerm" class="form-select form-select-sm"
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
                            <tr style="background: #e0f2fe;">
                                <td class="fw-semibold" style="color: #0369a1;">Total Payable</td>
                                <td id="resTotalPayableRow" class="fw-bold text-end" style="color: #0369a1;">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="resCompanyAlert" class="mt-3 mb-0 p-3 rounded-3 d-none d-flex align-items-center gap-2"
                 style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border-left: 4px solid #0ea5e9;">
                <iconify-icon icon="solar:buildings-outline" style="color: #0369a1; font-size: 18px;"></iconify-icon>
                <span class="text-sm" style="color: #0369a1;">Finance company: <strong id="resCompanyName"></strong></span>
            </div>

        </div>{{-- end resultsArea --}}

    </div>
</div>

<script>
const ALL_PRODUCTS   = @json($products);
const ALL_FUEL_TYPES = @json($fuelTypes);
</script>

@push('scripts')
<script>
const fmt = v => 'Rs ' + Number(parseFloat(v).toFixed(2)).toLocaleString('en-LK');
const val  = id => parseFloat(document.getElementById(id)?.value) || 0;
const set  = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

let currentBikeId = null;

function onFuelTypeChange() {
    const ftId    = parseInt(document.getElementById('fuelType').value);
    const brandEl = document.getElementById('brand');
    const bikeEl  = document.getElementById('bikeModel');

    brandEl.innerHTML = '<option value="">— Select Brand —</option>';
    bikeEl.innerHTML  = '<option value="">— Select Bike —</option>';
    brandEl.disabled  = true;
    bikeEl.disabled   = true;

    if (!ftId) { recalculate(); return; }

    const ft = ALL_FUEL_TYPES.find(f => f.id === ftId);
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

    const bikes = ALL_PRODUCTS.filter(p => p.subcategory_id === brandId);
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

    const bike = ALL_PRODUCTS.find(p => p.id === bikeId);
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

function recalculate() {
    const bikePrice    = val('bikePrice');
    const loanAmount   = val('loanAmount');
    const rmv          = val('rmv');
    const interestRate = val('interestRate');
    const months       = parseInt(document.getElementById('loanTerm').value) || 36;
    const companySel   = document.getElementById('financeCompanySelect');
    let company        = companySel.options[companySel.selectedIndex]?.text?.trim() || '';
    if (company === '— Select Finance Company —') company = '';

    const bikeDP        = (bikePrice > 0 && loanAmount > 0) ? bikePrice - loanAmount : 0;
    const rawSC         = loanAmount * 0.05;
    const serviceCharge = rawSC > 25000 ? 25000 : rawSC;
    const minimumDP     = bikeDP + serviceCharge + rmv;

    document.getElementById('dispBikeDP').value          = bikeDP > 0        ? bikeDP.toFixed(2)        : '';
    document.getElementById('dispServiceCharge').value   = serviceCharge > 0 ? serviceCharge.toFixed(2) : '';
    document.getElementById('dispRMV').value             = rmv > 0           ? rmv.toFixed(2)           : '';
    document.getElementById('dispMinimumDP').textContent = minimumDP > 0     ? fmt(minimumDP)           : '—';

    const scHint = document.getElementById('serviceChargeHint');
    if (loanAmount > 0) {
        scHint.innerHTML = rawSC > 25000
            ? `5% × ${fmt(loanAmount)} = ${fmt(rawSC)} → capped at <strong>Rs 25,000</strong>`
            : `5% × ${fmt(loanAmount)} = <strong>${fmt(serviceCharge)}</strong>`;
    } else {
        scHint.textContent = '5% of loan (max Rs 25,000)';
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

@endsection