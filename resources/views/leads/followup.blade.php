@extends('layouts.main')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-2">Lead Follow-Up System</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Lead Follow-Up</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Due Follow-Ups Reminder --}}
@if($dueFollowups->isNotEmpty())
<div class="alert alert-warning d-flex flex-wrap align-items-start gap-2 mb-24">
    <iconify-icon icon="solar:bell-bing-bold" class="fs-20 flex-shrink-0 mt-1"></iconify-icon>
    <div class="flex-grow-1">
        <strong>{{ $dueFollowups->count() }} follow-up{{ $dueFollowups->count() > 1 ? 's' : '' }} due:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($dueFollowups as $due)
            <li>
                <strong>{{ $due->name }}</strong> ({{ $due->phone }}) — was due
                {{ $due->next_followup_at->format('Y-m-d H:i') }}
                @if($due->officer)
                    <span class="text-secondary-light">· assigned to {{ $due->officer->name }}</span>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Select Sales Officer --}}
<div class="card mb-24">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Select Sales Officer</h6>
        <form method="GET" action="{{ route('leads.follow-up') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-5">
                <select name="officer_id" class="form-select">
                    <option value="">Select a Sales Officer</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" @selected(request('officer_id') == $officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('officer_id'))
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by name or phone"
                    value="{{ request('search') }}">
            </div>
            @endif
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary-600 w-100">Load Leads</button>
            </div>
        </form>
    </div>
</div>

{{-- Assigned Leads Table --}}
@if($selectedOfficer)
<div class="card">
    <div class="card-body p-0">
        <div class="px-24 py-16 border-bottom">
            <span class="fw-semibold">Assigned Leads
                <span class="badge bg-primary-100 text-primary-600 ms-1">{{ $leads->total() }}</span>
            </span>
            <span class="text-secondary-light text-sm ms-2">for <strong>{{ $selectedOfficer->name }}</strong></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Lead ID</th>
                        <th>Customer Name</th>
                        <th>Contact Number</th>
                        <th>Customer Type</th>
                        <th>Brand / Model</th>
                        <th>Next Follow Up</th>
                        <th>Follow Ups</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    @php
                        $followupCount = $lead->followups_count;
                    @endphp
                    <tr>
                        <td><span class="fw-semibold">{{ $lead->id }}</span></td>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->customer_type }}</td>
                        <td>
                            @if($lead->brand)
                                {{ $lead->brand->name }}{{ $lead->vehicle_model_display ? ' / '.$lead->vehicle_model_display : '' }}
                            @else
                                <span class="text-secondary-light">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->next_followup_at)
                                <span class="text-sm {{ $lead->next_followup_at->isPast() ? 'text-danger-600 fw-semibold' : '' }}">
                                    {{ $lead->next_followup_at->format('Y-m-d H:i') }}
                                </span>
                            @else
                                <span class="text-secondary-light text-sm">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-neutral-200 text-neutral-700 fs-12 fw-semibold px-10 py-5 radius-8">
                                {{ $followupCount }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-followup-detail"
                                data-id="{{ $lead->id }}"
                                data-name="{{ $lead->name }}"
                                title="View / Add Follow-Up">
                                <iconify-icon icon="solar:chat-line-outline"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-secondary-light">
                            No leads assigned to this officer.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-24 py-16 border-top">
            <span class="text-sm text-secondary-light">Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} records</span>
            {{ $leads->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endif

{{-- Follow-Up Detail Modal --}}
<div class="modal fade" id="followupModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followupModalTitle">Follow-Ups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Tabs --}}
                <ul class="nav nav-tabs mb-3" id="fuTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#fuLeadDetails">Lead Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#fuHistory">Follow-Up History &amp; Add</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Tab 1: Lead Details (read-only) --}}
                    <div class="tab-pane fade show active" id="fuLeadDetails">
                        <div id="fuLeadDetailsBody">
                            <div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>
                        </div>
                    </div>

                    {{-- Tab 2: Follow-Up History + Add --}}
                    <div class="tab-pane fade" id="fuHistory">
                        {{-- History table --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Feedback</th>
                                        <th>Status</th>
                                        <th>Next Call</th>
                                    </tr>
                                </thead>
                                <tbody id="fuHistoryRows">
                                    <tr><td colspan="6" class="text-center text-secondary-light">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Record Next Follow-Up form --}}
                        <h6 class="fw-semibold mb-3">Record Next Follow-Up</h6>
                        <form id="followupForm">
                            <input type="hidden" id="fuLeadId" name="lead_id">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium">Follow-Up Date &amp; Time *</label>
                                    <input type="datetime-local" name="followup_at" id="fuDate" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium">Method *</label>
                                    <select name="method" class="form-select" required>
                                        <option value="">Select Method</option>
                                        @foreach(['Call','Visit','WhatsApp','Email','Other'] as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium">Feedback</label>
                                    <input type="text" name="feedback" class="form-control" list="feedbackOptions"
                                        placeholder="Type or pick feedback...">
                                    <datalist id="feedbackOptions">
                                        @foreach(['Will visit soon','Needs more info','Wants price drop','Already purchased elsewhere','Not reachable','Interested - confirmed'] as $fb)
                                            <option value="{{ $fb }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium">Status Update *</label>
                                    <select name="status" id="fuStatusSelect" class="form-select" required>
                                        <option value="">Select Status</option>
                                        @foreach(['Interested','Need More Info','Follow Up Later','Not Interested','Converted','Sales Done'] as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6" id="fuNextDateWrap" style="display:none">
                                    <label class="form-label fw-medium">Next Call Date &amp; Time *</label>
                                    <input type="datetime-local" name="next_followup_at" id="fuNextDate" class="form-control">
                                    <small class="text-secondary-light">When the customer asked to be contacted again — e.g. "call me after 3 days."</small>
                                </div>
                            </div>
                            <div id="fuFormMsg" class="mt-2"></div>
                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-600">Save Follow-Up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const STATUS_BADGE = {
    'Interested':     'bg-info-100 text-info-600',
    'Need More Info': 'bg-purple-100 text-purple-600',
    'Follow Up Later':'bg-warning-100 text-warning-600',
    'Not Interested': 'bg-danger-100 text-danger-600',
    'Converted':      'bg-success-100 text-success-600',
    'Sales Done':     'bg-neutral-200 text-neutral-600',
};

// Next Call Date field only matters (and is only required) while the lead
// still needs a future callback.
const NEEDS_NEXT_DATE = ['Need More Info', 'Follow Up Later'];
const fuStatusSelect = document.getElementById('fuStatusSelect');
const fuNextDateWrap = document.getElementById('fuNextDateWrap');
const fuNextDate     = document.getElementById('fuNextDate');

fuStatusSelect.addEventListener('change', function () {
    const needsDate = NEEDS_NEXT_DATE.includes(this.value);
    fuNextDateWrap.style.display = needsDate ? '' : 'none';
    fuNextDate.required = needsDate;
    if (!needsDate) fuNextDate.value = '';
});

// Open modal
document.querySelectorAll('.btn-followup-detail').forEach(btn => {
    btn.addEventListener('click', function () {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        document.getElementById('followupModalTitle').textContent = `Details & Follow-Ups For ${name}`;
        document.getElementById('fuLeadId').value = id;
        document.getElementById('fuLeadDetailsBody').innerHTML =
            '<div class="text-center py-3"><div class="spinner-border text-primary-600"></div></div>';
        document.getElementById('fuHistoryRows').innerHTML =
            '<tr><td colspan="6" class="text-center text-secondary-light">Loading...</td></tr>';

        // Reset the "Record Next Follow-Up" form's conditional field state
        // from whatever lead was open before.
        fuStatusSelect.value = '';
        fuNextDateWrap.style.display = 'none';
        fuNextDate.required = false;
        fuNextDate.value = '';

        const modal = new bootstrap.Modal(document.getElementById('followupModal'));
        modal.show();

        // Load data
        fetch(`/leads/${id}/follow-up-detail`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data;
                const brandModel = d.brand ? `${d.brand.name}${d.model_product ? ' / ' + d.model_product.name : (d.vehicle_model_text ? ' / ' + d.vehicle_model_text : '')}` : '—';

                // Tab 1 — lead details
                document.getElementById('fuLeadDetailsBody').innerHTML = `
                <div class="row g-3">
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Full Name</label>
                        <div class="fw-medium">${d.name}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Mobile Number</label>
                        <div>${d.phone}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Email</label>
                        <div>${d.email ?? '—'}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Location</label>
                        <div>${d.city ?? '—'}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Customer Type</label>
                        <div>${d.customer_type}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Company Name</label>
                        <div>${d.company_name ?? '—'}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Brand / Model</label>
                        <div>${brandModel}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Budget Range</label>
                        <div>${d.budget_range ? 'LKR ' + Number(d.budget_range).toLocaleString() : '—'}</div></div>
                    <div class="col-12 col-md-6"><label class="form-label text-secondary-light text-sm">Lead Source</label>
                        <div>${d.lead_source}</div></div>
                </div>`;

                // Tab 2 — history
                const followups = d.followups || [];
                if (!followups.length) {
                    document.getElementById('fuHistoryRows').innerHTML =
                        '<tr><td colspan="6" class="text-center text-secondary-light">No follow-up history found.</td></tr>';
                } else {
                    document.getElementById('fuHistoryRows').innerHTML = followups.map((f, i) => `
                    <tr>
                        <td>${i+1}</td>
                        <td>${new Date(f.followup_at).toLocaleString()}</td>
                        <td>${f.method}</td>
                        <td>${f.feedback ?? '—'}</td>
                        <td><span class="badge ${STATUS_BADGE[f.status] ?? ''}">${f.status}</span></td>
                        <td>${f.next_followup_at ? new Date(f.next_followup_at).toLocaleString() : '—'}</td>
                    </tr>`).join('');
                }
            });
    });
});

// Save follow-up
document.getElementById('followupForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Saving...';

    const id   = document.getElementById('fuLeadId').value;
    const data = Object.fromEntries(new FormData(this));

    fetch(`/leads/${id}/follow-up`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false; btn.textContent = 'Save Follow-Up';
        const msg = document.getElementById('fuFormMsg');
        if (res.success) {
            msg.innerHTML = '<div class="alert alert-success text-sm">Follow-up saved successfully.</div>';
            // Prepend new row to history
            const f = res.followup;
            const newRow = `<tr>
                <td>—</td>
                <td>${new Date(f.followup_at).toLocaleString()}</td>
                <td>${f.method}</td>
                <td>${f.feedback ?? '—'}</td>
                <td><span class="badge ${STATUS_BADGE[f.status] ?? ''}">${f.status}</span></td>
                <td>${f.next_followup_at ? new Date(f.next_followup_at).toLocaleString() : '—'}</td>
            </tr>`;
            const tbody = document.getElementById('fuHistoryRows');
            if (tbody.querySelector('.text-secondary-light')) tbody.innerHTML = '';
            tbody.insertAdjacentHTML('afterbegin', newRow);
            this.reset();
            fuNextDateWrap.style.display = 'none';
            fuNextDate.required = false;
            setTimeout(() => msg.innerHTML = '', 3000);
        } else {
            const errs = Object.values(res.errors || {}).flat().join('<br>');
            msg.innerHTML = `<div class="alert alert-danger text-sm">${errs || 'Failed to save.'}</div>`;
        }
    });
});
</script>
@endpush
