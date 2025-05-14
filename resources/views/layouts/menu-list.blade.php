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

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-tree-structure"></i></span>
        <span class="pc-mtext" style="font-size: smaller;">MASTER DATABASES</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('category-suppliers.index') }}">Category Supplier</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('suppliers.index') }}">Supplier</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('category-products.index') }}">Category Product</a></li>
        <li class="pc-item"><a class="pc-link" href="#">Product</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chart-line-up"></i></span>
        <span class="pc-mtext" style="font-size: smaller;">SALES</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('history-sales.index') }}">Scanner</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('history-sales.report') }}">Report</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-shield-check"></i></span>
        <span class="pc-mtext" style="font-size: smaller;">USER MANAGEMENT</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('users.index') }}">User</a></li>
        @if (auth()->user()->hasRole('Super Admin'))
            <li class="pc-item"><a class="pc-link" href="{{ route('roles.index') }}">Role</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('permissions.index') }}">Permission</a></li>
        @endif
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="{{ route('activity') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-clock-clockwise"></i>
        </span>
        <span class="pc-mtext">Activity</span>
    </a>
</li>
