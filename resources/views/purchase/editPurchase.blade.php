@extends('layouts.main')

@section('content')

<!-- Header with breadcrumb -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Edit Purchase</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Purchase</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <iconify-icon icon="solar:eye-outline"></iconify-icon>
            View Purchase
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-lg">Edit Purchase Order #{{ $purchase->reference_number }}</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning-light text-warning">Editing</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Display Success/Error Messages -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchaseForm">
                    @csrf
                    @method('PUT')

                    <!-- Purchase Information -->
                    <div class="row gy-3 mb-4">
                        <div class="col-md-3">
                            <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                   value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number"
                                   value="{{ old('reference_number', $purchase->reference_number) }}" placeholder="Auto-generated">
                            @error('reference_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ old('status', $purchase->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $purchase->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $purchase->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Selection Section -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Edit Products for Purchase</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="productsTable">
                                    <thead>
                                        <tr>
                                            <th width="30%">Product <i class="text-danger">*</i></th>
                                            <th width="12%">SKU</th>
                                            <th width="13%">Unit Price <i class="text-danger">*</i></th>
                                            <th width="10%">Quantity <i class="text-danger">*</i></th>
                                            <th width="13%">Total</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productRows">
                                        @foreach($purchase->purchaseItems as $index => $item)
                                        <tr class="product-row">
                                            <td>
                                                <select class="form-control product-select" name="products[{{ $index }}][product_id]" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}"
                                                                data-sku="{{ $product->sku }}"
                                                                data-price="{{ $product->cost_price ?? $product->price }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control product-sku" value="{{ $item->product->sku ?? '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control unit-price"
                                                       name="products[{{ $index }}][unit_price]" step="0.01" min="0"
                                                       value="{{ old('products.' . $index . '.unit_price', $item->unit_price) }}" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control quantity"
                                                       name="products[{{ $index }}][quantity]" min="1"
                                                       value="{{ old('products.' . $index . '.quantity', $item->quantity) }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control row-total"
                                                       readonly value="{{ number_format($item->quantity * $item->unit_price, 2) }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                            <td><strong id="grandTotal">{{ number_format($purchase->total_amount, 2) }}</strong></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                                                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="row gy-3 mb-4">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Enter any additional notes...">{{ old('notes', $purchase->notes) }}</textarea>
                            @error('notes')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                            Update Purchase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* PRODUCT CHANGE */
    document.addEventListener('change', e => {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const option = e.target.selectedOptions[0];
            row.querySelector('.product-sku').value = option.dataset.sku || '';
            row.querySelector('.unit-price').value = option.dataset.price || 0;
            calculate();
        }
    });

    /* TOTALS */
    function calculate() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const q = parseFloat(row.querySelector('.quantity').value) || 0;
            const p = parseFloat(row.querySelector('.unit-price').value) || 0;
            const t = q * p;
            row.querySelector('.row-total').value = t.toFixed(2);
            total += t;
        });

        document.getElementById('grandTotal').textContent = total.toFixed(2);
    }

    document.addEventListener('input', calculate);

    /* ADD ROW */
    const addRowBtn = document.getElementById('addRowBtn');
    const productRows = document.getElementById('productRows');

    addRowBtn.onclick = () => {
        const rowCount = document.querySelectorAll('.product-row').length;
        const row = document.querySelector('.product-row').cloneNode(true);

        // Update field names with correct index
        row.querySelector('.product-select').name = `products[${rowCount}][product_id]`;
        row.querySelector('.unit-price').name = `products[${rowCount}][unit_price]`;
        row.querySelector('.quantity').name = `products[${rowCount}][quantity]`;

        // Clear values
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelector('.quantity').value = 1;
        row.querySelector('select').value = '';

        productRows.appendChild(row);
        updateRemoveButtons();
    };

    /* REMOVE */
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-row') && document.querySelectorAll('.product-row').length > 1) {
            e.target.closest('tr').remove();
            calculate();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-row');
            btn.disabled = rows.length === 1;
        });
    }

    // Initial setup
    updateRemoveButtons();

    // Initial calculation
    calculate();

    // Form validation
    const form = document.getElementById('purchaseForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasProduct = false;
            document.querySelectorAll('.product-select').forEach(select => {
                if (select.value) hasProduct = true;
            });

            if (!hasProduct) {
                e.preventDefault();
                alert('Please select at least one product for the purchase order.');
                return false;
            }
        });
    }

});
</script>
@endpush