<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            
            {{-- Inventory Management --}}
            <li class="sidebar-menu-group-title">Inventory Management</li>

            {{-- Category --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-categories')))
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="material-symbols:category" class="menu-icon"></iconify-icon>
                    <span>Categories</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-categories')))
                    <li>
                        <a href="{{ route('add.category') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon> Add Fuel Type</a>
                    </li>
                    <li>
                        <a href="{{ route('add.subcategory') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon> Add Brand</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-categories')))
                    <li>
                        <a href="{{ route('manage.category') }}"><iconify-icon icon="solar:settings-bold" class="submenu-icon"></iconify-icon> View All Categories</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            
            {{-- Products --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-products')))
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="material-symbols:order-approve-sharp" class="menu-icon"></iconify-icon>
                    <span>Motor Bikes

                    </span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-products')))
                    <li>
                        <a href="{{ route('add.product') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon> Add Bike</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-products')))
                    <li>
                        <a href="{{ route('manage.products') }}"><iconify-icon icon="solar:settings-bold" class="submenu-icon"></iconify-icon> Manage all Bikes</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            
            
            
            <!-- {{-- Order --}}
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:clipboard-list" class="menu-icon"></iconify-icon>
                    <span>Order</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Order List</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Place Order</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Manage Order</a>
                    </li>
                </ul>
            </li>
            
            {{-- Barcode Print --}}
            <li>
                <a href="#">
                    <iconify-icon icon="material-symbols:barcode-reader-sharp" class="menu-icon"></iconify-icon>
                    <span>Barcode Print</span>
                </a>
            </li> -->

            <!-- {{-- Billing & Invoicing --}}
            <li class="sidebar-menu-group-title">Billing & Invoicing</li> -->
            
            <!-- {{-- POS Cashier --}}
            <li>
                <a href="#">
                    <iconify-icon icon="eos-icons:installing" class="menu-icon"></iconify-icon>
                    <span>POS - Cashier</span>
                </a>
            </li>
            
            {{-- Invoice --}}
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="ri-coins-fill" class="menu-icon"></iconify-icon>
                    <span>Invoice</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Invoice</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Manage Invoice</a>
                    </li>
                </ul>
            </li> -->

            <!-- {{-- Quotation --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-quotations')))
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:invoice-03" class="menu-icon"></iconify-icon>
                    <span>Quotation</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-quotations')))
                    <li>
                        <a href="{{ route('add.quotation') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon> Add Quotation</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-quotations')))
                    <li>
                        <a href="{{ route('manage.quotations') }}"><iconify-icon icon="solar:settings-bold" class="submenu-icon"></iconify-icon> Manage Quotation</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif -->
            
            {{-- Features --}}
            <li class="sidebar-menu-group-title">Features</li>

            {{-- Loan Management --}}
<li class="dropdown">
    <a href="javascript:void(0)">
        <iconify-icon icon="solar:wallet-money-outline" class="menu-icon"></iconify-icon>
        <span>Loan Management</span>
    </a>
    <ul class="sidebar-submenu">
        <li>
            <a href="{{ route('manage.finance-companies') }}"><iconify-icon icon="solar:buildings-2-bold" class="submenu-icon"></iconify-icon> Add Finance Company</a>
        </li>
        <li>
            <a href="{{ route('loan.calculator') }}"><iconify-icon icon="solar:calculator-outline" class="submenu-icon"></iconify-icon> Loan Calculator</a>
        </li>
        <li>
            <a href="{{ route('manage.loan-plans') }}"><iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon> View All Loan Plans</a>
        </li>
    </ul>
</li>

            
            <!-- {{-- Purchase Section --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-purchases')))
            <li class="dropdown">
                <a href="javascript:void(1)">
                    <iconify-icon icon="solar:cart-outline" class="menu-icon"></iconify-icon>
                    <span>Purchase Section</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-purchases')))
                    <li>
                        <a href="{{ route('add.purchase') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon> Add Purchase</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-purchases')))
                    <li>
                        <a href="{{ route('manage.purchases') }}"><iconify-icon icon="solar:settings-bold" class="submenu-icon"></iconify-icon> Manage Purchase</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif -->
            
            <!-- {{-- Contact Section --}}
            <li class="dropdown">
                <a href="javascript:void(2)">
                    <iconify-icon icon="eos-icons:products-outlined" class="menu-icon"></iconify-icon>
                    <span>Contact Section</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Overview</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Quick Actions</a>
                    </li>
                </ul>
            </li> -->
            
           
            {{-- {{-- Appointment Management 
@if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-blogs')))
<li>
    <a href="{{ route('manage.appointments') }}">
        <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
        <span>Appointments</span>
    </a>
</li>
@endif --}}


            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- LEAD MANAGEMENT                                     --}}
            {{-- ═══════════════════════════════════════════════════ --}}
         
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-leads')))
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
                    <span>Lead Management</span>
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

            {{-- All Inquiries (Loan + Product combined) --}}
@if(Auth::user()->user_type === 'admin' || (Auth::user()->role && (Auth::user()->role->permissions->contains('name', 'view-loan-inquiries') || Auth::user()->role->permissions->contains('name', 'view-product-enquiries'))))
<li>
    <a href="{{ route('manage.all-inquiries') }}">
        <iconify-icon icon="solar:clipboard-list-outline" class="menu-icon"></iconify-icon>
        <span>All Inquiries</span>
    </a>
</li>
@endif

{{-- Contact Messages --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-contacts')))
<li class="sidebar-menu-item">
    <a href="{{ route('manage.contacts') }}">
        <iconify-icon icon="solar:letter-bold-duotone" class="menu-icon"></iconify-icon>
        <span>Contact Messages</span>
        @php $unreadCount = \App\Models\Contact::where('status','unread')->count(); @endphp
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
        @endif
    </a>
</li>
@endif
            {{-- Blog Management --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-blogs')))
            <li class="dropdown">
                <a href="javascript:void(4)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Happy Customers Blogs</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-blogs')))
                    <li>
                        <a href="{{ route('add.blog') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>Add Blog</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-blogs')))
                    <li>
                        <a href="{{ route('manage.blogs') }}"><iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon>Manage Blogs</a>
                    </li>
                    @endif
                    
                </ul>
            </li>
            @endif
            
            {{-- {{-- Service Management 
@if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-services')))
<li class="dropdown">
    <a href="javascript:void(4)">
        <iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon>
        <span>Service Management</span>
    </a>
    <ul class="sidebar-submenu">
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-services')))
        <li>
            <a href="{{ route('manage.services') }}"><iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon>Manage Services</a>
        </li>
        @endif
        @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-services')))
        <li>
            <a href="{{ route('add.service') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>Add Service</a>
        </li>
        @endif
        <li>
            <a href="{{ route('manage.finance-companies') }}"><iconify-icon icon="solar:buildings-2-bold" class="submenu-icon"></iconify-icon>Finance Companies</a>
        </li>
    </ul>
</li>
@endif --}}

            {{-- Settings --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && (Auth::user()->role->permissions->contains('name', 'view-roles') || Auth::user()->role->permissions->contains('name', 'view-users'))))
            <li class="dropdown">
                <a href="javascript:void(4)">
                    <iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon>
                    <span>Settings</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-roles')))
                    <li>
                        <a href="{{ route('roles.create') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>Add Role</a>
                    </li>
                    @endif
                
                @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-roles')))
                    <li>
                        <a href="{{ route('roles.index') }}"><iconify-icon icon="solar:shield-bold" class="submenu-icon"></iconify-icon> View Roles</a>
                    </li>
                    @endif
                    
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-users')))
                    <li>
                        <a href="{{ route('users.index') }}"><iconify-icon icon="solar:users-group-two-rounded-bold" class="submenu-icon"></iconify-icon>Users</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'edit-users')))
                    <li>
                        <a href="{{ route('users.assign-roles') }}"><iconify-icon icon="solar:user-plus-bold" class="submenu-icon"></iconify-icon>User Assign Role</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
             {{-- Customer Management --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-customers')))
            <li class="dropdown">
                <a href="javascript:void(3)">
                    <iconify-icon icon="solar:user-plus-outline" class="menu-icon"></iconify-icon>
                    <span>Customer Management</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-customers')))
                    <li>
                        <a href="{{ route('add.customer') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>Add Customer</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-customers')))
                    <li>
                        <a href="{{ route('manage.customers') }}"><iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon>Manage Customers</a>
                    </li>
                    @endif
                    
                </ul>
            </li>
            @endif            
            {{-- Supplier Management --}}
            @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-suppliers')))
            <li class="dropdown">
                <a href="javascript:void(4)">
                    <iconify-icon icon="solar:delivery-outline" class="menu-icon"></iconify-icon>
                    <span>Supplier Management</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'create-suppliers')))
                    <li>
                        <a href="{{ route('add.supplier') }}"><iconify-icon icon="solar:add-circle-bold" class="submenu-icon"></iconify-icon>Add Supplier</a>
                    </li>
                    @endif
                    @if(Auth::user()->user_type === 'admin' || (Auth::user()->role && Auth::user()->role->permissions->contains('name', 'view-suppliers')))
                    <li>
                        <a href="{{ route('manage.suppliers') }}"><iconify-icon icon="solar:list-bold" class="submenu-icon"></iconify-icon>Supplier Management</a>
                    </li>
                    @endif
                    
                </ul>
            </li>
            @endif
            
            <!-- {{-- Sales & Transactions --}}
            <li class="dropdown">
                <a href="javascript:void(5)">
                    <iconify-icon icon="eos-icons:products-outlined" class="menu-icon"></iconify-icon>
                    <span>Sales & Transactions</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Overview</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Quick Actions</a>
                    </li>
                </ul>
            </li>
            
            {{-- Reports & Analytics --}}
            <li class="dropdown">
                <a href="javascript:void(6)">
                    <iconify-icon icon="eos-icons:products-outlined" class="menu-icon"></iconify-icon>
                    <span>Reports & Analytics</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Overview</a>
                    </li>
                    <li>
                        <a href="#"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Quick Actions</a>
                    </li>
                </ul>
            </li> -->
        </ul>
    </div>
</aside>
<style>
    .sidebar-menu > li {
        margin-bottom: 6px;
    }

    .sidebar-menu > li.dropdown > a,
    .sidebar-menu > li > a {
        border-radius: 8px;
    }

    /* Give the arrow (::after at inset-inline-end: 0.75rem, ~1.125rem wide)
       enough clearance so it never sits on top of the label text */
    .sidebar-menu li.dropdown > a {
        padding-inline-end: 2.25rem;
    }

    .sidebar-menu .sidebar-submenu > li {
        margin-bottom: 2px;
    }

    .sidebar-menu-group-title {
        margin-top: 14px;
        margin-bottom: 6px;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownLinks = document.querySelectorAll('.sidebar-menu .dropdown > a');
    
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const parentLi = this.parentElement;
            const isActive = parentLi.classList.contains('open');
            
            // Close all dropdowns
            document.querySelectorAll('.sidebar-menu .dropdown').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
            
            // If this dropdown wasn't active, open it
            if (!isActive) {
                parentLi.classList.add('open');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar-menu')) {
            document.querySelectorAll('.sidebar-menu .dropdown').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });
});
</script>