@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Manage Quotations</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium">Manage Quotations</li>
    </ul>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">All Quotations</h5>
        <a href="{{ route('add.quotation') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <iconify-icon icon="ic:baseline-plus" class="icon"></iconify-icon>
            <span>Add New Quotation</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Quotation ID</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Date</th>
                        <th scope="col">Valid Until</th>
                        <th scope="col">Total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations ?? [] as $quotation)
                        <tr>
                            <td>
                                <a href="{{ route('quotations.show', $quotation->id) }}" class="text-primary-600">
                                    {{ $quotation->quotation_number }}
                                </a>
                            </td>
                            <td>{{ $quotation->customer_name }}</td>
                            <td>{{ $quotation->quotation_date->format('M d, Y') }}</td>
                            <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                            <td>${{ number_format($quotation->total, 2) }}</td>
                            <td>
                                <span class="badge
                                    @if($quotation->status == 'draft') bg-secondary
                                    @elseif($quotation->status == 'sent') bg-primary
                                    @elseif($quotation->status == 'accepted') bg-success
                                    @elseif($quotation->status == 'rejected') bg-danger
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-outline-primary">
                                        <iconify-icon icon="ic:outline-visibility"></iconify-icon>
                                    </a>
                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-outline-warning">
                                        <iconify-icon icon="ic:outline-edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this quotation?')">
                                            <iconify-icon icon="ic:outline-delete"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <iconify-icon icon="ic:outline-inventory-2" class="icon text-2xl text-muted mb-2"></iconify-icon>
                                    <span class="text-muted">No quotations found</span>
                                    <a href="{{ route('add.quotation') }}" class="btn btn-primary btn-sm mt-2">
                                        Create Your First Quotation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($quotations) && $quotations->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection