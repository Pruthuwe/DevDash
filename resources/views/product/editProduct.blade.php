@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Edit Product</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage.products') }}">Products</a></li>
                <li class="breadcrumb-item active">Edit Product</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-outline"></iconify-icon> Back to Products
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-lg">Edit Product</h6>
                <span class="badge bg-warning-light text-warning">Editing</span>
            </div>

            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form id="productForm" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Keep SKU hidden so controller validation passes --}}
                    <input type="hidden" name="sku" value="{{ $product->sku }}">
                    <input type="hidden" name="removed_gallery_images" id="removedGalleryImages" value="">

                    {{-- ── Step 1: Basic Info ─────────────────────────────── --}}
                    <div class="section-block mb-4">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" id="categorySelect">
                                    <option value="">Select Fuel Type</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <select class="form-select" name="subcategory_id" id="subcategorySelect">
                                    <option value="">Select Fuel Type first</option>
                                    @if($product->subcategory)
                                        <option value="{{ $product->subcategory->id }}" selected>{{ $product->subcategory->name }}</option>
                                    @endif
                                </select>
                                <small class="text-secondary-light">Select Fuel Type above to load brands</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="active"   {{ old('status', $product->status) == 'active'   ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="draft"    {{ old('status', $product->status) == 'draft'    ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── Step 2: Pricing & Stock ────────────────────────── --}}
                    <div class="section-block mb-4">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Pricing & Stock</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Regular Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" class="form-control" name="price" id="regularPrice"
                                        value="{{ old('price', $product->price) }}" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" class="form-control" name="sale_price" id="salePrice"
                                        value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="text-secondary-light">Leave empty if no discount</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Discount % (auto-calculated)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" id="discountPercent" readonly placeholder="0%">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Purchase Cost <span class="text-muted small">(Private)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="text" inputmode="decimal" class="form-control decimal-input" name="cost_price"
                                        value="{{ old('cost_price', $product->cost_price) }}" placeholder="0.00">
                                </div>
                                <small class="text-secondary-light">For profit tracking only</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity"
                                    value="{{ old('quantity', $product->quantity) }}" min="0" placeholder="0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Low Stock Alert</label>
                                <input type="number" class="form-control" name="low_stock_alert"
                                    value="{{ old('low_stock_alert', $product->low_stock_alert ?? 2) }}" min="0" placeholder="2">
                                <small class="text-secondary-light">Alert when stock falls below this number</small>
                            </div>

                            {{--
                            ───────────────────────────────────────────────────────────
                            COMMENTED OUT: Loan Amount, RMV Fee, Service Charge, Interest Rate
                            Reason: These fields do NOT exist on the "Add Product" wizard
                            (resources/views/.../add-product.blade.php). The Edit page should
                            only show/edit fields that the Add page also collects, to keep both
                            forms in sync. If these are genuinely needed going forward, add them
                            to the Add Product wizard first, then un-comment here.
                            ───────────────────────────────────────────────────────────

                            <div class="col-md-6">
                                <label class="form-label">Loan Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="text" inputmode="decimal" class="form-control decimal-input" id="editLoanAmountInput"
                                        name="loan_amount" value="{{ old('loan_amount', $product->loan_amount) }}" placeholder="0.00">
                                </div>
                                <small class="text-secondary-light">Amount financed via loan (leave 0 for full cash)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">RMV Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="text" inputmode="decimal" class="form-control decimal-input" name="rmv"
                                        value="{{ old('rmv', $product->rmv ?? 10160) }}" placeholder="10160.00">
                                </div>
                                <small class="text-secondary-light">Revenue & Motor Vehicle Department registration fee</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Service Charge</label>
                                <div class="btn-group btn-group-sm mb-2" role="group">
                                    <input type="radio" class="btn-check" name="service_charge_type" id="scTypeFixed" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="scTypeFixed">Fixed Rs 25,000</label>
                                    <input type="radio" class="btn-check" name="service_charge_type" id="scTypePercent" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="scTypePercent">5% of Loan Amount</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="text" inputmode="decimal" class="form-control decimal-input" id="editServiceChargeInput"
                                        name="service_charge" value="{{ old('service_charge', $product->service_charge ?? 25000) }}" placeholder="25000.00">
                                </div>
                                <small class="text-secondary-light">Pick a mode above, or type a custom amount</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Interest Rate</label>
                                <div class="input-group">
                                    <input type="text" inputmode="decimal" class="form-control decimal-input" name="interest_rate"
                                        value="{{ old('interest_rate', $product->interest_rate ?? 1.5) }}" placeholder="1.5">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-secondary-light">Defaults to 1.5% — change if this bike's loan rate differs</small>
                            </div>
                            --}}
                        </div>
                    </div>

                    {{-- ── Step 3: Main Image ─────────────────────────────── --}}
                    <div class="section-block mb-4">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Product Image</h6>
                        <div class="row g-3 align-items-start">
                            <div class="col-md-4 text-center">
                                @if($product->main_image)
                                    <img id="imagePreview" src="{{ asset($product->main_image) }}"
                                        alt="Product Image" class="img-fluid rounded mb-2" style="max-height:200px;">
                                @else
                                    <div id="imagePreview" class="bg-light rounded d-flex align-items-center justify-content-center mb-2" style="height:200px;">
                                        <iconify-icon icon="solar:box-outline" style="font-size:3rem;" class="text-secondary-light"></iconify-icon>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="form-control mb-2" name="main_image" id="mainImageInput"
                                    accept="image/*" onchange="previewImage(this)">
                                <small class="text-secondary-light d-block mb-2">JPG, PNG — max 2MB</small>
                                @if($product->main_image)
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">
                                        <iconify-icon icon="solar:trash-bin-outline"></iconify-icon> Remove current image
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── Step 4: Description & Tags ─────────────────────── --}}
                    <div class="section-block mb-4">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Description & Tags</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="4"
                                    placeholder="Brief product summary (max 500 characters)" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-secondary-light">This will appear in product listings</small>
                                    <small class="text-secondary-light char-count">0/500</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Product Tags</label>
                                <input type="text" class="form-control" name="tags"
                                    value="{{ old('tags', $product->tags) }}"
                                    placeholder="e.g., KTM, Adventure, Petrol, 390cc (comma separated)">
                                <small class="text-secondary-light">Separate tags with commas — used for search</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Engine Specification</label>
                                <input type="text" class="form-control" name="engine_spec"
                                    value="{{ old('engine_spec', $product->engine_spec) }}"
                                    placeholder="e.g., 248.8cc, Single Cylinder, Liquid Cooled">
                                <small class="text-secondary-light">Shown below the product name on the storefront</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Highlights</label>
                                <input type="text" class="form-control" name="highlights"
                                    value="{{ old('highlights', is_array($product->highlights) ? implode(', ', $product->highlights) : $product->highlights) }}"
                                    placeholder="e.g., ABS, LED Lights, Digital Console (comma separated)">
                                <small class="text-secondary-light">Feature badges shown on the product card</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Rating (1–5)</label>
                                <input type="number" class="form-control" name="rating"
                                    value="{{ old('rating', $product->rating ?? 5) }}"
                                    placeholder="5" min="1" max="5" step="0.1">
                            </div>
                        </div>
                    </div>

                    {{-- ── Actions ────────────────────────────────────────── --}}
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('manage.products') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:save-outline"></iconify-icon> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('imagePreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'imagePreview';
                img.src = e.target.result;
                img.className = 'img-fluid rounded mb-2';
                img.style.maxHeight = '200px';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function () {
    // ── Fuel Type → Brand cascade ──────────────────────────────────────
    const categorySelect    = $('#categorySelect');
    const subcategorySelect = $('#subcategorySelect');

    categorySelect.on('change', function () {
        const categoryId = $(this).val();
        if (!categoryId) {
            subcategorySelect.html('<option value="">Select Fuel Type first</option>').prop('disabled', false);
            return;
        }
        subcategorySelect.html('<option value="">Loading...</option>');
        $.get('/categories/' + categoryId + '/subcategories', function (data) {
            subcategorySelect.html('<option value="">Select Brand</option>');
            (data.subcategories || []).forEach(function (sub) {
                subcategorySelect.append('<option value="' + sub.id + '">' + sub.name + '</option>');
            });
        }).fail(function () {
            subcategorySelect.html('<option value="">No brands found</option>');
        });
    });

    if (categorySelect.val()) categorySelect.trigger('change');

    // ── Discount auto-calc ─────────────────────────────────────────────
    function calcDiscount() {
        const price     = parseFloat($('#regularPrice').val()) || 0;
        const salePrice = parseFloat($('#salePrice').val())    || 0;
        if (price > 0 && salePrice > 0 && salePrice < price) {
            $('#discountPercent').val((((price - salePrice) / price) * 100).toFixed(2));
        } else {
            $('#discountPercent').val('0');
        }
    }
    $('#regularPrice, #salePrice').on('input', calcDiscount);
    calcDiscount();

    // ── Decimal-only inputs ────────────────────────────────────────────
    $('.decimal-input').on('input', function () {
        let val   = $(this).val().replace(/[^0-9.]/g, '');
        const pts = val.split('.');
        if (pts.length > 2) val = pts[0] + '.' + pts.slice(1).join('');
        $(this).val(val);
    });

    /*
    ───────────────────────────────────────────────────────────────────────
    COMMENTED OUT: Service Charge toggle logic (Fixed / 5% of Loan Amount)
    Reason: depends on #editLoanAmountInput / #editServiceChargeInput /
    #scTypeFixed / #scTypePercent, all of which were commented out above
    because loan_amount, rmv, service_charge and interest_rate are not
    part of the Add Product wizard's field set. Un-comment together with
    the matching HTML block above if these fields come back.
    ───────────────────────────────────────────────────────────────────────

    const scFixedRadio   = $('#scTypeFixed');
    const scPercentRadio = $('#scTypePercent');
    const scInput        = $('#editServiceChargeInput');
    const loanInput      = $('#editLoanAmountInput');

    function calcPercentSC() {
        return Math.min((parseFloat(loanInput.val()) || 0) * 0.05, 25000).toFixed(2);
    }

    // Guess initial mode
    (function () {
        const cur      = parseFloat(scInput.val()) || 0;
        const fivePerc = parseFloat(calcPercentSC());
        const loan     = parseFloat(loanInput.val()) || 0;
        if (loan > 0 && Math.abs(cur - fivePerc) < 0.01) {
            scPercentRadio.prop('checked', true);
        } else {
            scFixedRadio.prop('checked', true);
        }
    })();

    scFixedRadio.on('change',   function () { if (this.checked) scInput.val('25000.00'); });
    scPercentRadio.on('change', function () { if (this.checked) scInput.val(calcPercentSC()); });
    loanInput.on('input', function () { if (scPercentRadio.is(':checked')) scInput.val(calcPercentSC()); });
    */

    // ── Remove main image ──────────────────────────────────────────────
    $('#removeImageBtn').on('click', function () {
        if (!confirm('Remove the current image?')) return;
        const placeholder = $('<div id="imagePreview" class="bg-light rounded d-flex align-items-center justify-content-center mb-2" style="height:200px;"><iconify-icon icon="solar:box-outline" style="font-size:3rem;" class="text-secondary-light"></iconify-icon></div>');
        $('#imagePreview').replaceWith(placeholder);
        $('<input>').attr({ type: 'hidden', name: 'remove_image', value: '1' }).appendTo('#productForm');
        $(this).hide();
    });

    // ── Char counter for short description ────────────────────────────
    $('[maxlength]').on('input', function () {
        const max = parseInt($(this).attr('maxlength'));
        const cur = $(this).val().length;
        const counter = $(this).closest('.col-12').find('.char-count');
        counter.text(cur + '/' + max).toggleClass('text-danger', cur >= max);
    });
    // Init count for pre-filled textarea
    $('[maxlength]').trigger('input');
});
</script>
@endpush