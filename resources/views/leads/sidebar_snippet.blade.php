{{-- ═══════════════════════════════════════════════════════════════
    LEAD MANAGEMENT — REFERENCE ONLY
    Already applied directly into resources/views/partials/sidemenubar.blade.php
    (the real sidebar — main.blade.php includes that file, not this one).
    This file is not @include'd anywhere; kept as documentation only.
════════════════════════════════════════════════════════════════ --}}

@if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-leads')))
<li class="sidebar-menu-group-title">Lead Management</li>

<li class="dropdown">
    <a href="javascript:void(0)">
        <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
        <span>Lead Management</span>
        @php
            // Cached for 60s — this runs on every page load via the sidebar,
            // so we don't want an uncached COUNT(*) on every request.
            $leadCount = \Illuminate\Support\Facades\Cache::remember('sidebar_unassigned_lead_count', 60, function () {
                return \App\Models\Lead::where('status', 'Unassigned')->count();
            });
        @endphp
        @if($leadCount > 0)
            <span class="badge bg-danger ms-auto">{{ $leadCount }}</span>
        @endif
    </a>
    <ul class="sidebar-submenu">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-leads')))
        <li>
            <a href="{{ route('leads.capture') }}">
                <iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>
                Lead Capture
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('leads.list') }}">
                <iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon>
                Lead List
            </a>
        </li>
        <li>
            <a href="{{ route('leads.assignment') }}">
                <iconify-icon icon="solar:user-plus-bold" class="submenu-icon"></iconify-icon>
                Lead Assignment
            </a>
        </li>
        <li>
            <a href="{{ route('leads.follow-up') }}">
                <iconify-icon icon="solar:chat-line-outline" class="submenu-icon"></iconify-icon>
                Lead Follow-Up
            </a>
        </li>
    </ul>
</li>
@endif
