<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/dashboard" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('img/tea-heaven.webp') }}" width="100px" alt="">
                <span class="badge bg-brand-color-2 rounded-pill ms-2 theme-version">v1.0</span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                @include('layouts.menu-list')
            </ul>
            {{-- <div class="card nav-action-card bg-brand-color-4">
                <div class="card-body" style="background-image: url('/build/images/layout/nav-card-bg.svg')">
                    <h5 class="text-dark">Help Center</h5>
                    <p class="text-dark text-opacity-75">Please contact us for more questions.</p>
                    <a href="mailto:jokosaputro616@gmail.com" class="btn btn-primary" target="_blank">Go to help
                        Center</a>
                </div>
            </div> --}}
        </div>
        <div class="card pc-user-card"
            style="margin-top: auto; border-radius: 0; border: none; border-top: 1px solid #f0f0f0;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="user-avtar rounded-circle" style="background-color: #f8f9fa; padding: 10px;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                        <small class="text-muted">{{ Auth::user()->getRoleNames()->first() }}</small>
                    </div>
                    <div class="flex-shrink-0">
                        <a class="btn btn-icon btn-link-secondary" href="javascript:void(0);" id="sidebar-logout-btn">
                            <i class="ph-duotone ph-sign-out"></i>
                        </a>
                        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the sidebar logout button and form
        const sidebarLogoutBtn = document.getElementById('sidebar-logout-btn');
        const sidebarLogoutForm = document.getElementById('sidebar-logout-form');

        // Handle sidebar logout button click
        if (sidebarLogoutBtn && sidebarLogoutForm) {
            sidebarLogoutBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar dari sistem?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#228B22',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sidebarLogoutForm.submit();
                    }
                });
            });
        }
    });
</script>
