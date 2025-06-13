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

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#masterDataSubmenu" aria-expanded="false"
        aria-controls="masterDataSubmenu">
        <span class="pc-micon"><i class="ph-duotone ph-database"></i></span>
        <span class="pc-mtext">Master Data</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu collapse" id="masterDataSubmenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('products.index') }}">
                <span class="pc-mtext">Products</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('bahan-baku.index') }}">
                <span class="pc-mtext">Bahan Baku</span>
            </a></li>
    </ul>
</li>

<!-- Transaction Tables -->
<li class="pc-item pc-caption">
    <label> Transaction Tables</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link" data-bs-toggle="collapse" data-bs-target="#allTransactionsSubmenu"
        aria-expanded="false" aria-controls="allTransactionsSubmenu">
        <span class="pc-micon"><i class="ph-duotone ph-swap"></i></span>
        <span class="pc-mtext">All Transactions</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu collapse" id="allTransactionsSubmenu">
        <!-- Purchase & Procurement -->
        <li class="pc-item"><a class="pc-link" href="{{ route('purchase.index') }}">
                <span class="pc-mtext">Purchase Items</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('purchase-sticker.index') }}">
                <span class="pc-mtext">Purchase Stiker</span>
            </a></li>

        <!-- Production -->
        <li class="pc-item"><a class="pc-link" href="{{ route('catatan-produksi.index') }}">
                <span class="pc-mtext">Catatan Produksi</span>
            </a></li>

        <!-- Inventory -->
        <li class="pc-item"><a class="pc-link" href="{{ route('inventory-bahan-baku.index') }}">
                <span class="pc-mtext">Inventory Bahan Baku</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('finished-goods.index') }}">
                <span class="pc-mtext">Finished Goods Stock</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('stickers.index') }}">
                <span class="pc-mtext">Sticker Stock</span>
            </a></li>

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

<!-- Sales & Transactions -->
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
        <li class="pc-item"><a class="pc-link" href="{{ route('scanner.index') }}">
                <span class="pc-mtext">Scanner</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('sales-management.index') }}">
                <span class="pc-mtext">Sales Management</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('sales-report.index') }}">
                <span class="pc-mtext">Sales Report</span>
            </a></li>
    </ul>
</li>

<!-- Reports & Analytics -->
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
    </ul>
</li>

<!-- System -->
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
        <li class="pc-item"><a class="pc-link" href="{{ route('users.index') }}">
                <span class="pc-mtext">Users</span>
            </a></li>
        @if (auth()->user()->hasRole('Super Admin'))
            <li class="pc-item"><a class="pc-link" href="{{ route('roles.index') }}">
                    <span class="pc-mtext">Roles</span>
                </a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('permissions.index') }}">
                    <span class="pc-mtext">Permissions</span>
                </a></li>
        @endif
    </ul>
</li>

<li class="pc-item">
    <a href="{{ route('activity') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-clock-clockwise"></i>
        </span>
        <span class="pc-mtext">Activity Log</span>
    </a>
</li>

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

    /* Arrow animation - works for both mobile and desktop */
    .pc-arrow {
        transition: transform 0.2s ease;
        display: inline-block;
    }

    .pc-hasmenu>.pc-link[aria-expanded="true"] .pc-arrow {
        transform: rotate(90deg);
    }
</style>
