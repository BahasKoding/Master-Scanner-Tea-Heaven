<li class="pc-item pc-caption">
    <label>Navigation</label>
</li>
<li class="pc-item pc-hasmenu">
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
        <li class="pc-item"><a class="pc-link" href="{{ route('category-products.index') }}">Product Categories</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('products.index') }}">Products</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('finished-goods.index') }}">Finished Goods</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('bahan-baku.index') }}">Bahan Baku</a></li>
    </ul>
</li>

<!-- Transactions -->
<li class="pc-item pc-caption">
    <label>Transactions</label>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-factory"></i></span>
        <span class="pc-mtext">Production</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('catatan-produksi.index') }}">Production Records</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chart-line-up"></i></span>
        <span class="pc-mtext">Sales</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('history-sales.index') }}">Scanner</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('history-sales.report') }}">Report</a></li>
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
        <li class="pc-item"><a class="pc-link" href="{{ route('users.index') }}">Users</a></li>
        @if (auth()->user()->hasRole('Super Admin'))
            <li class="pc-item"><a class="pc-link" href="{{ route('roles.index') }}">Roles</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('permissions.index') }}">Permissions</a></li>
        @endif
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="{{ route('activity') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-clock-clockwise"></i>
        </span>
        <span class="pc-mtext">Activity Log</span>
    </a>
</li>
