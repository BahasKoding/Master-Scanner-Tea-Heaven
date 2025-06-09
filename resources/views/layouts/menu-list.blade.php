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
<li class="pc-item pc-caption">
    <label>📋 Master Tables</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-database"></i></span>
        <span class="pc-mtext">Master Data</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
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
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-swap"></i></span>
        <span class="pc-mtext">All Transactions</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
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
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chart-line-up"></i></span>
        <span class="pc-mtext">Sales</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
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
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chart-bar"></i></span>
        <span class="pc-mtext">Reports</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
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
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-shield-check"></i></span>
        <span class="pc-mtext">User Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
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
