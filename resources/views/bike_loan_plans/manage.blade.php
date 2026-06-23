@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">View All Loan Plans</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Loan Management</li>
                <li class="breadcrumb-item active" aria-current="page">View All Loan Plans</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('loan.calculator') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
            Add / Edit a Loan Plan
        </a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Finance Plans with bikes</h4>
                           </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" class="row g-3 mb-24 align-items-end">
    <div class="col-md-4">
        <label class="form-label">Bike</label>
        <select name="product_id" class="form-select">
            <option value="">All Bikes</option>
            @foreach($bikes as $bike)
                <option value="{{ $bike->id }}" {{ request('product_id') == $bike->id ? 'selected' : '' }}>
                    {{ $bike->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Finance Company</label>
        <select name="finance_company_id" class="form-select">
            <option value="">All Companies</option>
            @foreach($financeCompanies as $company)
                <option value="{{ $company->id }}" {{ request('finance_company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="height: 38px; white-space: nowrap;">
            <iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon> Apply Filters
        </button>
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 38px; white-space: nowrap;">
            Clear
        </a>
    </div>
</form>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bike</th>
                                    <th>Finance Company</th>
                                    <th>Loan Amount</th>
                                    <th>Interest Rate</th>
                                    <th>RMV</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allLoanPlans ?? [] as $plan)
                                <tr>
                                    <td>{{ $plan->product->name ?? 'Deleted bike' }}</td>
                                    <td>{{ $plan->financeCompany->name ?? 'Deleted company' }}</td>
                                    <td>Rs {{ number_format($plan->loan_amount, 2) }}</td>
                                    <td>{{ $plan->interest_rate }}% / month</td>
                                    <td>Rs {{ number_format($plan->rmv, 2) }}</td>
                                    <td>
                                        <a href="{{ route('loan.calculator') }}" class="btn btn-sm btn-outline-primary" title="Edit on Loan Calculator page">
                                            <iconify-icon icon="solar:pen-outline"></iconify-icon>
                                        </a>
                                        <form action="{{ route('bike-loan-plans.destroy', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this finance company from this bike?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No loan plans saved yet. Go to the Loan Calculator page to attach a finance company to a bike.</td>
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
@endsection