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

{{-- Select Lead --}}
<div class="card mb-24">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Select Lead</h6>
        <form method="GET" action="{{ route('leads.follow-up') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-8">
                <select name="lead_id" class="form-select" id="leadSelect">
                    <option value="">Select a Lead</option>
                    @foreach($leads as $lead)
                        <option value="{{ $lead->id }}" @selected(request('lead_id') == $lead->id)>
                            #{{ $lead->id }} - {{ $lead->name }} ({{ $lead->phone }}) {{ $lead->city ? '- ' . $lead->city : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <button type="submit" class="btn btn-primary-600 w-100">Load Follow-Ups</button>
            </div>
        </form>
    </div>
</div>

{{-- Follow-Up History & Add Form --}}
@if($selectedLead)
<div class="row g-4">
    {{-- Lead Info Card --}}
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Lead Information</h6>
                <div class="mb-2"><strong>Name:</strong> {{ $selectedLead->name }}</div>
                <div class="mb-2"><strong>Phone:</strong> {{ $selectedLead->phone }}</div>
                <div class="mb-2"><strong>Email:</strong> {{ $selectedLead->email ?? '—' }}</div>
                <div class="mb-2"><strong>City:</strong> {{ $selectedLead->city ?? '—' }}</div>
                <div class="mb-2"><strong>Customer Type:</strong> {{ $selectedLead->customer_type }}</div>
                <div class="mb-2"><strong>Lead Source:</strong> {{ $selectedLead->lead_source }}</div>
                <div class="mb-2"><strong>Brand:</strong> {{ $selectedLead->brand?->name ?? '—' }}</div>
                <div class="mb-2"><strong>Model:</strong> {{ $selectedLead->vehicle_model_display ?? '—' }}</div>
                <div class="mb-2"><strong>Current Status:</strong> 
                    <span class="badge {{ $selectedLead->statusBadgeClass() }}">{{ $selectedLead->status }}</span>
                </div>
                @if($selectedLead->next_followup_at)
                <div class="mb-2"><strong>Next Follow-Up:</strong> 
                    <span class="text-warning-600">{{ $selectedLead->next_followup_at->format('Y-m-d H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Follow-Up History + Add Form --}}
    <div class="col-12 col-lg-8">
        {{-- History Table --}}
        <div class="card mb-24">
            <div class="card-header">
                <h6 class="fw-semibold mb-0">Follow-Up History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Feedback</th>
                                <th>Status</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($followups as $index => $followup)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $followup->followup_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-info-100 text-info-600">{{ $followup->method }}</span>
                                </td>
                                <td>{{ $followup->feedback ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $followup->status == 'Sales Done' ? 'bg-neutral-200 text-neutral-600' : ($followup->status == 'Converted' ? 'bg-success-100 text-success-600' : ($followup->status == 'Not Interested' ? 'bg-danger-100 text-danger-600' : 'bg-info-100 text-info-600')) }}">
                                        {{ $followup->status }}
                                    </span>
                                </td>
                                <td>{{ $followup->doneBy?->name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary-light">No follow-up history found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Add New Follow-Up Form --}}
        <div class="card">
            <div class="card-header">
                <h6 class="fw-semibold mb-0">Record Next Follow-Up</h6>
            </div>
            <div class="card-body">
                <form id="followupForm" data-lead-id="{{ $selectedLead->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium">Follow-Up Date &amp; Time *</label>
                            <input type="datetime-local" name="followup_at" id="fuDate" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium">Method *</label>
                            <select name="method" class="form-select" required>
                                <option value="">Select Method</option>
                                @foreach(['Call','WhatsApp','Visit','Email','Other'] as $m)
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
                            <select name="status" class="form-select" id="statusSelect" required>
                                <option value="">Select Status</option>
                                @foreach(['Interested','Need More Info','Follow Up Later','Not Interested','Converted','Sales Done'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Next Follow-Up Date (shown only for Need More Info or Follow Up Later) --}}
                        <div class="col-12 col-md-6" id="nextFollowupContainer" style="display: none;">
                            <label class="form-label fw-medium">Next Follow-Up Date</label>
                            <input type="datetime-local" name="next_followup_at" class="form-control">
                            <small class="text-secondary-light">When should we follow up again?</small>
                        </div>
                    </div>
                    <div id="fuFormMsg" class="mt-2"></div>
                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('followupForm').reset(); document.getElementById('nextFollowupContainer').style.display='none';">Clear</button>
                        <button type="submit" class="btn btn-primary-600">Save Follow-Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const STATUS_BADGE = {
    'Interested':      'bg-info-100 text-info-600',
    'Need More Info':  'bg-purple-100 text-purple-600',
    'Follow Up Later': 'bg-warning-100 text-warning-600',
    'Not Interested':  'bg-danger-100 text-danger-600',
    'Converted':       'bg-success-100 text-success-600',
    'Sales Done':      'bg-neutral-200 text-neutral-600',
};

// Show/hide next follow-up date based on status
document.getElementById('statusSelect')?.addEventListener('change', function () {
    const container = document.getElementById('nextFollowupContainer');
    if (this.value === 'Need More Info' || this.value === 'Follow Up Later') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
});

// Save follow-up
document.getElementById('followupForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Saving...';

    const id   = this.dataset.leadId;
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
            setTimeout(() => location.reload(), 1000);
        } else {
            const errs = Object.values(res.errors || {}).flat().join('<br>');
            msg.innerHTML = `<div class="alert alert-danger text-sm">${errs || 'Failed to save.'}</div>`;
        }
    });
});
</script>
@endpush