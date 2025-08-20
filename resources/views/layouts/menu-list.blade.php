<li class="pc-item pc-caption">
    <label>Navigation</label>
</li>
<li class="pc-item">
    <a href="{{ route('dashboard') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-gauge"></i>
        </span>
        <span class="pc-mtext">Dashboard</span>
    </a>
</li>

<!-- Master Tables -->
@php
    $showMasterData = auth()->check() && (auth()->user()->can('Product List') || auth()->user()->can('Bahan Baku List'));
@endphp

@if ($showMasterData)
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#masterDataSubmenu"
            aria-expanded="false" aria-controls="masterDataSubmenu">
            <span class="pc-micon"><i class="ph-duotone ph-database"></i></span>
            <span class="pc-mtext">Master Data</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu collapse" id="masterDataSubmenu">
            @can('Product List')
                <li class="pc-item"><a class="pc-link" href="{{ route('products.index') }}">
                        <span class="pc-mtext">Products</span>
                    </a></li>
            @endcan
            @can('Bahan Baku List')
                <li class="pc-item"><a class="pc-link" href="{{ route('bahan-baku.index') }}">
                        <span class="pc-mtext">Bahan Baku</span>
                    </a></li>
            @endcan
        </ul>
    </li>
@endif

<!-- Transaction Tables -->
@php
    $showTransactions =
        auth()->user()->can('Purchase List') ||
        auth()->user()->can('Purchase Stiker List') ||
        auth()->user()->can('Catatan Produksi List') ||
        auth()->user()->can('Inventory Bahan Baku List') ||
        auth()->user()->can('Finished Goods List') ||
        // auth()->user()->can('Sticker List') ||
        auth()->user()->can('view-stock-opname');
@endphp

@if ($showTransactions)
    <li class="pc-item pc-caption">
        <label> Transaction Tables</label>
    </li>
    @php
        $isTransactionActive = request()->routeIs('purchase.*') || 
                              request()->routeIs('purchase-sticker.*') || 
                              request()->routeIs('catatan-produksi.*') || 
                              request()->routeIs('inventory-bahan-baku.*') || 
                              request()->routeIs('finished-goods.*') || 
                              request()->routeIs('stickers.*') || 
                              request()->routeIs('stock-opname.*');
    @endphp
    <li class="pc-item pc-hasmenu {{ $isTransactionActive ? 'pc-trigger' : '' }}">
        <a href="#!" class="pc-link {{ $isTransactionActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#allTransactionsSubmenu"
            aria-expanded="{{ $isTransactionActive ? 'true' : 'false' }}" aria-controls="allTransactionsSubmenu">
            <span class="pc-micon"><i class="ph-duotone ph-swap"></i></span>
            <span class="pc-mtext">All Transactions</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu collapse {{ $isTransactionActive ? 'show' : '' }}" id="allTransactionsSubmenu">
            <!-- Purchase & Procurement -->
            @can('Purchase List')
                <li class="pc-item"><a class="pc-link" href="{{ route('purchase.index') }}">
                        <span class="pc-mtext">Purchase Items</span>
                    </a></li>
            @endcan
            {{-- @can('Purchase Stiker List')
                <li class="pc-item"><a class="pc-link" href="{{ route('purchase-sticker.index') }}">
                        <span class="pc-mtext">Purchase Stiker</span>
                    </a></li>
            @endcan --}}

            <!-- Production -->
            @can('Catatan Produksi List')
                <li class="pc-item"><a class="pc-link" href="{{ route('catatan-produksi.index') }}">
                        <span class="pc-mtext">Catatan Produksi</span>
                    </a></li>
            @endcan

            <!-- Inventory -->
            @can('Inventory Bahan Baku List')
                <li class="pc-item"><a class="pc-link" href="{{ route('inventory-bahan-baku.index') }}">
                        <span class="pc-mtext">Inventory Bahan Baku</span>
                    </a></li>
            @endcan
            @can('Finished Goods List')
                <li class="pc-item"><a class="pc-link" href="{{ route('finished-goods.index') }}">
                        <span class="pc-mtext">Finished Goods Stock</span>
                    </a></li>
            @endcan
            {{-- @can('Sticker List')
                <li class="pc-item"><a class="pc-link" href="{{ route('stickers.index') }}">
                        <span class="pc-mtext">Sticker Stock</span>
                    </a></li>
            @endcan --}}
            @can('view-stock-opname')
                <li class="pc-item {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                    <a class="pc-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}" href="{{ route('stock-opname.index') }}">
                        <span class="pc-mtext">Stock Opname</span>
                    </a>
                </li>
            @endcan

            <!-- Reports & Analytics -->
            <!-- TODO: Route belum tersedia - Production Performance
        <li class="pc-item"><a class="pc-link disabled" href="#!"
                style="color: #6c757d; cursor: not-allowed; pointer-events: none;">
                <span class="pc-mtext">Production Performance</span>
            </a></li>
        -->
            <!-- TODO: Route belum tersedia - Material Usage
        <li class="pc-item"><a class="pc-link disabled" href="#!"
                style="color: #6c757d; cursor: not-allowed; pointer-events: none;">
                <span class="pc-mtext">Material Usage</span>
            </a></li>
        -->
            <!-- TODO: Route belum tersedia - Stock Movement
        <li class="pc-item"><a class="pc-link disabled" href="#!"
                style="color: #6c757d; cursor: not-allowed; pointer-events: none;">
                <span class="pc-mtext">Stock Movement</span>
            </a></li>
        -->
            <!-- TODO: Route belum tersedia - Low Stock Alert
        <li class="pc-item"><a class="pc-link disabled" href="#!"
                style="color: #6c757d; cursor: not-allowed; pointer-events: none;">
                <span class="pc-mtext">Low Stock Alert</span>
            </a></li>
        -->
        </ul>
    </li>
@endif

<!-- Sales & Transactions -->
@php
    $showSales =
        auth()->user()->can('Sales List') ||
        auth()
            ->user()
            ->hasAnyPermission(['scanner', 'sales management', 'sales report']);
@endphp

@if ($showSales)
    <li class="pc-item pc-caption">
        <label>Sales & Transactions</label>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#salesSubmenu" aria-expanded="false"
            aria-controls="salesSubmenu">
            <span class="pc-micon"><i class="ph-duotone ph-chart-line-up"></i></span>
            <span class="pc-mtext">Sales</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu collapse" id="salesSubmenu">
            @if (auth()->user()->can('Sales List') || auth()->user()->can('Sales Create'))
                <li class="pc-item"><a class="pc-link" href="{{ route('scanner.index') }}">
                        <span class="pc-mtext">Scanner</span>
                    </a></li>
            @endif
            @can('Sales List')
                <li class="pc-item"><a class="pc-link" href="{{ route('sales-management.index') }}">
                        <span class="pc-mtext">Sales Management</span>
                    </a></li>
            @endcan
            @if (auth()->user()->can('Sales List') || auth()->user()->can('Reports View'))
                <li class="pc-item"><a class="pc-link" href="{{ route('sales-report.index') }}">
                        <span class="pc-mtext">Sales Report</span>
                    </a></li>
            @endif
        </ul>
    </li>
@endif

<!-- Reports & Analytics -->
@can('Reports View')
    <li class="pc-item pc-caption">
        <label> Reports & Analytics</label>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#reportsSubmenu"
            aria-expanded="false" aria-controls="reportsSubmenu">
            <span class="pc-micon"><i class="ph-duotone ph-chart-bar"></i></span>
            <span class="pc-mtext">Reports</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu collapse" id="reportsSubmenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('reports.purchase.index') }}">
                    <span class="pc-mtext">Laporan Purchase</span>
                </a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('reports.catatan-produksi.index') }}">
                    <span class="pc-mtext">Laporan Catatan Produksi</span>
                </a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('reports.scanner.index') }}">
                    <span class="pc-mtext">Laporan Scanner</span>
                </a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('reports.scanner.summary') }}">
                    <span class="pc-mtext">Laporan Scanner Summary</span>
                </a></li>
        </ul>
    </li>
@endcan

<!-- System -->
@php
    $showUserManagement =
        auth()->user()->can('Users List') ||
        auth()->user()->can('Roles List') ||
        auth()->user()->can('Permissions List');
@endphp

@if ($showUserManagement)
    <li class="pc-item pc-caption">
        <label>System</label>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#systemSubmenu"
            aria-expanded="false" aria-controls="systemSubmenu">
            <span class="pc-micon"><i class="ph-duotone ph-shield-check"></i></span>
            <span class="pc-mtext">User Management</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu collapse" id="systemSubmenu">
            @can('Users List')
                <li class="pc-item"><a class="pc-link" href="{{ route('users.index') }}">
                        <span class="pc-mtext">Users</span>
                    </a></li>
            @endcan
            @can('Roles List')
                <li class="pc-item"><a class="pc-link" href="{{ route('roles.index') }}">
                        <span class="pc-mtext">Roles</span>
                    </a></li>
            @endcan
            @can('Permissions List')
                <li class="pc-item"><a class="pc-link" href="{{ route('permissions.index') }}">
                        <span class="pc-mtext">Permissions</span>
                    </a></li>
            @endcan
        </ul>
    </li>
@endif

@can('Activity List')
    <li class="pc-item">
        <a href="{{ route('activity') }}" class="pc-link">
            <span class="pc-micon">
                <i class="ph-duotone ph-clock-clockwise"></i>
            </span>
            <span class="pc-mtext">Activity Log</span>
        </a>
    </li>
@endcan

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuLinks = document.querySelectorAll('.pc-hasmenu > .pc-link');

        function isMobile() {
            return window.innerWidth < 992;
        }

        function initializeMenus() {
            if (isMobile()) {
                // Mobile: Hide all submenus initially, enable click events
                document.querySelectorAll('.pc-submenu').forEach(submenu => {
                    submenu.classList.remove('show');
                    submenu.style.display = 'none';
                });

                menuLinks.forEach(link => {
                    link.setAttribute('aria-expanded', 'false');
                    link.style.pointerEvents = 'auto';
                    link.style.cursor = 'pointer';
                });
            } else {
                // Desktop: Hide all submenus initially, enable hover
                document.querySelectorAll('.pc-submenu').forEach(submenu => {
                    submenu.classList.remove('show');
                    submenu.style.display = 'none';
                });

                menuLinks.forEach(link => {
                    link.setAttribute('aria-expanded', 'false');
                    link.style.pointerEvents = 'auto';
                    link.style.cursor = 'pointer';
                });
            }
        }

        // Handle menu clicks for mobile
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!isMobile()) {
                    // Desktop: Toggle on click
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        const isCurrentlyOpen = targetElement.classList.contains('show');

                        // Close all other submenus
                        document.querySelectorAll('.pc-submenu.show').forEach(submenu => {
                            if (submenu !== targetElement) {
                                submenu.classList.remove('show');
                                submenu.style.display = 'none';
                                const parentLink = document.querySelector(
                                    `[data-bs-target="#${submenu.id}"]`);
                                if (parentLink) {
                                    parentLink.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });

                        // Toggle current submenu
                        if (isCurrentlyOpen) {
                            targetElement.classList.remove('show');
                            targetElement.style.display = 'none';
                            this.setAttribute('aria-expanded', 'false');
                        } else {
                            targetElement.classList.add('show');
                            targetElement.style.display = 'block';
                            this.setAttribute('aria-expanded', 'true');
                        }
                    }
                } else {
                    // Mobile: Toggle on click
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        const isCurrentlyOpen = targetElement.classList.contains('show');

                        // Close all other submenus
                        document.querySelectorAll('.pc-submenu.show').forEach(submenu => {
                            if (submenu !== targetElement) {
                                submenu.classList.remove('show');
                                submenu.style.display = 'none';
                                const parentLink = document.querySelector(
                                    `[data-bs-target="#${submenu.id}"]`);
                                if (parentLink) {
                                    parentLink.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });

                        // Toggle current submenu
                        if (isCurrentlyOpen) {
                            targetElement.classList.remove('show');
                            targetElement.style.display = 'none';
                            this.setAttribute('aria-expanded', 'false');
                        } else {
                            targetElement.classList.add('show');
                            targetElement.style.display = 'block';
                            this.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
        });

        // Handle window resize with debounce
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(initializeMenus, 100);
        });

        // Initialize on load
        initializeMenus();
    });
</script>

<style>
    /* Base menu styles */
    .pc-submenu {
        transition: none;
        /* Remove transitions for better performance */
    }

    /* Desktop styles */
    @media (min-width: 992px) {
        .pc-submenu {
            display: none;
            /* Hidden by default */
        }

        .pc-submenu.show {
            display: block;
        }

        .pc-hasmenu>.pc-link {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .pc-hasmenu>.pc-link:hover {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
        }

        .pc-hasmenu>.pc-link[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 6px;
        }
    }

    /* Mobile/Tablet styles */
    @media (max-width: 991.98px) {
        .pc-submenu {
            display: none;
            background: rgba(255, 255, 255, 0.05);
            margin: 0.25rem 0 0.25rem 1rem;
            border-radius: 6px;
            border-left: 2px solid rgba(255, 255, 255, 0.1);
        }

        .pc-submenu.show {
            display: block;
        }

        .pc-hasmenu>.pc-link {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .pc-hasmenu>.pc-link:hover {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
        }

        .pc-submenu .pc-item {
            padding: 0.1rem 0;
        }

        .pc-submenu .pc-link {
            padding: 0.5rem 1rem;
            margin: 0.1rem 0.5rem;
            border-radius: 4px;
            transition: background-color 0.15s ease;
        }

        .pc-submenu .pc-link:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .pc-hasmenu>.pc-link[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 6px;
        }
    }

    /* Active state styling for parent menu items */
    .pc-hasmenu > .pc-link.active {
        background: rgba(34, 139, 34, 0.2) !important; /* Green background with transparency */
        color: #22c55e !important; /* Green text color */
        border-radius: 6px;
    }

    .pc-hasmenu > .pc-link.active:hover {
        background: rgba(34, 139, 34, 0.3) !important; /* Darker green on hover */
    }

    /* Active state styling for submenu items */
    .pc-submenu .pc-item.active > .pc-link,
    .pc-submenu .pc-link.active {
        background: rgba(34, 139, 34, 0.2) !important; /* Green background */
        color: #22c55e !important; /* Green text color */
        border-radius: 4px;
    }

    .pc-submenu .pc-item.active > .pc-link:hover,
    .pc-submenu .pc-link.active:hover {
        background: rgba(34, 139, 34, 0.3) !important; /* Darker green on hover */
    }

    /* Arrow animation - works for both mobile and desktop */
    .pc-arrow {
        transition: transform 0.2s ease;
        display: inline-block;
    }

    .pc-hasmenu>.pc-link[aria-expanded="true"] .pc-arrow {
        transform: rotate(90deg);
    }
</style>
