<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="javascript:void(0);" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="javascript:void(0);" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="pc-h-item header-user-profile">
                    <!-- Simplified profile toggle button -->
                    <a href="javascript:void(0);" class="pc-head-link ms-0 profile-toggle-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="user-avtar">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>

                    <!-- Custom profile menu that doesn't rely on Bootstrap's dropdown -->
                    <div id="custom-profile-menu" class="custom-profile-menu" style="display: none;">
                        <div class="menu-header">
                            <h5 class="m-0">Profile</h5>
                        </div>
                        <div class="menu-body">
                            <div class="user-profile-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="user-avtar">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex-grow-1 mx-3">
                                        <h5 class="mb-0 user-name">{{ Auth::user()->name }}</h5>
                                        <p class="mb-0 user-email">{{ Auth::user()->email }}</p>
                                        <span
                                            class="badge role-badge">{{ Auth::user()->getRoleNames()->first() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="menu-items">
                                <a href="javascript:void(0);" id="direct-logout-btn" class="menu-item logout-btn">
                                    <i class="ti ti-logout me-2"></i>
                                    <span>Logout</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<style>
    /* Custom profile menu styling */
    .custom-profile-menu {
        position: absolute;
        right: 15px;
        top: 60px;
        width: 280px;
        background-color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        z-index: 1050;
        overflow: hidden;
    }

    .profile-toggle-btn {
        cursor: pointer;
    }

    /* Styling untuk header menu */
    .menu-header {
        background-color: #228B22;
        color: white;
        padding: 12px 15px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    /* Styling untuk info profil */
    .user-profile-info {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

    .user-name {
        color: #333;
        font-size: 16px;
        margin-bottom: 4px;
    }

    .user-email {
        color: #666;
        font-size: 13px;
    }

    .role-badge {
        background-color: #f0f8f0;
        color: #228B22;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
    }

    /* Styling untuk menu items */
    .menu-items {
        padding: 8px 0;
    }

    .menu-item {
        display: block;
        color: #555;
        padding: 10px 15px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .menu-item:hover {
        background-color: #f9f9f9;
        color: #228B22;
    }

    .logout-btn {
        display: flex;
        align-items: center;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-profile-menu.visible {
        display: block !important;
        animation: fadeIn 0.3s ease;
    }
</style>

<script>
    // Immediately execute this script
    document.addEventListener('DOMContentLoaded', function() {
        // Get elements for profile menu
        const toggleBtn = document.querySelector('.profile-toggle-btn');
        const menu = document.getElementById('custom-profile-menu');
        const logoutBtn = document.getElementById('direct-logout-btn');
        const logoutForm = document.getElementById('logout-form');

        // Toggle menu on button click
        if (toggleBtn && menu) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle visibility
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';

                // Add visible class for animation
                if (menu.style.display === 'block') {
                    menu.classList.add('visible');
                } else {
                    menu.classList.remove('visible');
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (menu.style.display === 'block' && !menu.contains(e.target) && !toggleBtn.contains(e
                        .target)) {
                    menu.style.display = 'none';
                    menu.classList.remove('visible');
                }
            });
        }

        // Handle logout button click
        if (logoutBtn && logoutForm) {
            logoutBtn.addEventListener('click', function(e) {
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
                        logoutForm.submit();
                    }
                });
            });
        }
    });
</script>
