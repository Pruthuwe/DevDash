<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
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
            
            {{-- Products --}}
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="material-symbols:order-approve-sharp" class="menu-icon"></iconify-icon>
                    <span>Products</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('product.list') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Product List</a>
                    </li>
                    <li>
                        <a href="{{ route('add.product') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Add Product</a>
                    </li>
                    <li>
                        <a href="{{ route('manage.products') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Manage Product</a>
                    </li>
                </ul>
            </li>
            
            {{-- Category --}}
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="material-symbols:add-shopping-cart-sharp" class="menu-icon"></iconify-icon>
                    <span>Category</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('category.list') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Category List</a>
                    </li>
                    <li>
                        <a href="{{ route('add.category') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Add Category</a>
                    </li>
                    <li>
                        <a href="{{ route('manage.category') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Manage Category</a>
                    </li>
                </ul>
            </li>
            
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

            {{-- Billing & Invoicing --}}
            <li class="sidebar-menu-group-title">Billing & Invoicing</li>
            
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

            {{-- Quotation --}}
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:invoice-03" class="menu-icon"></iconify-icon>
                    <span>Quotation</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('add.quotation') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Quotation</a>
                    </li>
                    <li>
                        <a href="{{ route('manage.quotations') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Manage Quotation</a>
                    </li>
                </ul>
            </li>
            
            {{-- Features --}}
            <li class="sidebar-menu-group-title">Features</li>
            
            {{-- Purchase Section --}}
            <li class="dropdown">
                <a href="javascript:void(1)">
                    <iconify-icon icon="solar:cart-outline" class="menu-icon"></iconify-icon>
                    <span>Purchase Section</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('purchase.list') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Purchase List</a>
                    </li>
                    <li>
                        <a href="{{ route('add.purchase') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Purchase</a>
                    </li>
                    <li>
                        <a href="{{ route('manage.purchases') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Manage Purchase</a>
                    </li>
                </ul>
            </li>
            
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
            
            {{-- Customer Management --}}
            <li class="dropdown">
                <a href="javascript:void(3)">
                    <iconify-icon icon="solar:user-plus-outline" class="menu-icon"></iconify-icon>
                    <span>Customer Management</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('customer.list') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>Customer List</a>
                    </li>
                    <li>
                        <a href="{{ route('add.customer') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i>Add Customer</a>
                    </li>
                </ul>
            </li>            
            {{-- Supplier Management --}}
            <li class="dropdown">
                <a href="javascript:void(4)">
                    <iconify-icon icon="solar:delivery-outline" class="menu-icon"></iconify-icon>
                    <span>Supplier Management</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('supplier.list') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>Supplier List</a>
                    </li>
                    <li>
                        <a href="{{ route('add.supplier') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i>Add Supplier</a>
                    </li>   
                </ul>
            </li>
            {{-- Blog Management --}}
            <li>
                <a href="{{ route('manage.blogs') }}">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Blog Management</span>
                </a>
            </li>
            
            {{-- Service Management --}}
            <li>
                <a href="{{ route('manage.services') }}">
                    <iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon>
                    <span>Service Management</span>
                </a>
            </li>
            
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
