@extends('layouts.main')

@section('title', 'Add Quotation - devPOS')

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Add New Quotation</h6>
</div>

<form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
@csrf

<div class="row">
    {{-- LEFT --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">

                <div class="row">

                    {{-- Quotation Number --}}
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Quotation Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                               name="quotation_number"
                               value="QUO-{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}" required>
                    </div>

                    {{-- Quotation Date --}}
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control"
                               name="quotation_date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Valid Until --}}
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Valid Until <span class="text-danger">*</span></label>
                        <input type="date" class="form-control"
                               name="valid_until"
                               value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                    </div>

                    {{-- Customer --}}
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                        <select class="form-control" id="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    data-name="{{ $customer->name }}"
                                    data-email="{{ $customer->email }}"
                                    data-phone="{{ $customer->mobile ?? $customer->phone }}"
                                    data-address="{{ $customer->address }}">
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_id" id="hidden_customer_id">
                    </div>

                    {{-- Auto filled --}}
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Customer Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" readonly>
                    </div>

                    <div class="col-md-6 mb-20">
                        <label class="form-label">Customer Phone</label>
                        <input type="text" class="form-control" id="customer_phone" name="customer_phone" readonly>
                    </div>

                    <div class="col-12 mb-20">
                        <label class="form-label">Customer Address</label>
                        <textarea class="form-control" id="customer_address" name="customer_address" readonly></textarea>
                    </div>

                    {{-- Items --}}
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Qty</th>
                                    <th width="120">Price</th>
                                    <th width="120">Total</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <select class="form-control product">
                                            <option value="">Select</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-price="{{ $product->price }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="product[]">
                                    </td>
                                    <td><input type="number" class="form-control qty" name="quantity[]" value="1"></td>
                                    <td><input type="number" class="form-control price" name="price[]"></td>
                                    <td><input type="number" class="form-control total" readonly></td>
                                    <td><button type="button" class="btn btn-danger remove">×</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" id="addRow" class="btn btn-secondary btn-sm">Add Item</button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span id="subtotal">0.00</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>
                    <input type="number" class="form-control w-50" name="discount" id="discount" value="0">
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong id="grandTotal">0.00</strong>
                </div>

                <select class="form-control mt-3" name="status">
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                </select>

                <button class="btn btn-primary w-100 mt-3">Create Quotation</button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* CUSTOMER AUTO FILL */
    const customerSelect = document.getElementById('customer_id');
    const hiddenCustomerId = document.getElementById('hidden_customer_id');
    const customerEmail = document.getElementById('customer_email');
    const customerPhone = document.getElementById('customer_phone');
    const customerAddress = document.getElementById('customer_address');
    
    customerSelect.addEventListener('change', function () {
        const o = this.selectedOptions[0];
        hiddenCustomerId.value = this.value;
        customerEmail.value = o.dataset.email || '';
        customerPhone.value = o.dataset.phone || '';
        customerAddress.value = o.dataset.address || '';
    });

    /* TOTALS */
    const subtotalEl = document.getElementById('subtotal');
    const grandTotalEl = document.getElementById('grandTotal');
    const discountInput = document.getElementById('discount');
    
    function calculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const q = parseFloat(row.querySelector('.qty').value) || 0;
            const p = parseFloat(row.querySelector('.price').value) || 0;
            const t = q * p;
            row.querySelector('.total').value = t.toFixed(2);
            subtotal += t;
        });

        const discount = parseFloat(discountInput.value) || 0;

        subtotalEl.textContent = subtotal.toFixed(2);
        grandTotalEl.textContent = (subtotal - discount).toFixed(2);
    }

    document.addEventListener('input', calculate);

    /* PRODUCT CHANGE */
    document.addEventListener('change', e => {
        if (e.target.classList.contains('product')) {
            const row = e.target.closest('tr');
            row.querySelector('[name="product[]"]').value = e.target.value;
            row.querySelector('.price').value = e.target.selectedOptions[0].dataset.price || 0;
            calculate();
        }
    });

    /* ADD ROW */
    const addRowBtn = document.getElementById('addRow');
    const itemsBody = document.getElementById('itemsBody');
    
    addRowBtn.onclick = () => {
        const row = document.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelector('.qty').value = 1;
        itemsBody.appendChild(row);
    };

    /* REMOVE */
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove') && document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('tr').remove();
            calculate();
        }
    });
    
    // Initial calculation
    calculate();

});
</script>
@endpush
