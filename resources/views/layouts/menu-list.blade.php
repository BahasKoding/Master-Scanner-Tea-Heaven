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

<!-- Master Data -->
<li class="pc-item pc-caption">
    <label>Master Data</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-archive-box"></i></span>
        <span class="pc-mtext">Products</span>
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

<!-- Procurement -->
<li class="pc-item pc-caption">
    <label>Procurement</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-shopping-cart"></i></span>
        <span class="pc-mtext">Purchase Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('purchase.index') }}">
                <span class="pc-mtext">Purchase Bahan Baku</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Purchase Stiker</span>
            </a></li>
    </ul>
</li>

<!-- Production -->
<li class="pc-item pc-caption">
    <label>Production</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-factory"></i></span>
        <span class="pc-mtext">Production Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('catatan-produksi.index') }}">
                <span class="pc-mtext">Catatan Produksi</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Production Planning</span>
            </a></li>
    </ul>
</li>

<!-- Inventory Management -->
<li class="pc-item pc-caption">
    <label>Inventory Management</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-warehouse"></i></span>
        <span class="pc-mtext">Raw Materials Inventory</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Stock Bahan Baku</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Inventory Bahan Baku</span>
            </a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-package"></i></span>
        <span class="pc-mtext">Finished Goods</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('finished-goods.index') }}">
                <span class="pc-mtext">Finished Goods Stock</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Stock Opname</span>
            </a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-sticker"></i></span>
        <span class="pc-mtext">Sticker Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('stickers.index') }}">
                <span class="pc-mtext">Sticker Stock</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Sticker Purchase</span>
            </a></li>
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
    <label>Reports & Analytics</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chart-bar"></i></span>
        <span class="pc-mtext">Production Reports</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Production Performance</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Material Usage</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Defect Analysis</span>
            </a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-trend-up"></i></span>
        <span class="pc-mtext">Inventory Reports</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Stock Movement</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Low Stock Alert</span>
            </a></li>
        <li class="pc-item"><a class="pc-link" href="#!">
                <span class="pc-mtext">Forecasting</span>
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
