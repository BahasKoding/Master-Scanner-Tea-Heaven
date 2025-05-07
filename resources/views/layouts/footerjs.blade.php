<!-- Required Js that aren't loaded in main.blade.php -->
<script src="{{ URL::asset('build/js/fonts/custom-font.js') }}"></script>
<script src="{{ URL::asset('build/js/pcoded.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/feather.min.js') }}"></script>

@if (env('APP_DARK_LAYOUT') == 'default')
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            dark_layout = 'true';
        } else {
            dark_layout = 'false';
        }
        layout_change_default();
        if (dark_layout == 'true') {
            layout_change('dark');
        } else {
            layout_change('light');
        }
    </script>
@endif

@if (env('APP_DARK_LAYOUT') != 'default')
    @if (env('APP_DARK_LAYOUT') == 'true')
        <script>
            layout_change('dark');
        </script>
    @endif
    @if (env('APP_DARK_LAYOUT') == false)
        <script>
            layout_change('light');
        </script>
    @endif
@endif


@if (env('APP_DARK_NAVBAR') == 'true')
    <script>
        layout_sidebar_change('dark');
    </script>
@endif

@if (env('APP_DARK_NAVBAR') == false)
    <script>
        layout_sidebar_change('light');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('true');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('false');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == 'true')
    <script>
        layout_caption_change('true');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == false)
    <script>
        layout_caption_change('false');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == 'true')
    <script>
        layout_rtl_change('true');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == false)
    <script>
        layout_rtl_change('false');
    </script>
@endif

@if (env('APP_PRESET_THEME') != '')
    <script>
        preset_change("{{ env('APP_PRESET_THEME') }}");
    </script>
@endif

<!-- Script Logout dengan SweetAlert -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gunakan delegation agar bekerja untuk semua halaman dan elemen yang mungkin dimuat secara dinamis
        document.body.addEventListener('click', function(e) {
            // Cek apakah yang diklik adalah tombol logout atau elemen di dalamnya
            const logoutBtn = e.target.closest('.logout-button');
            if (logoutBtn) {
                e.preventDefault();

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
                        document.getElementById('logout-form').submit();
                    }
                });
            }
        });
    });
</script>

<!-- Script untuk memastikan dropdown topbar berfungsi pada semua halaman -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Secara manual menginisialisasi dropdown topbar
        const profileToggler = document.querySelector('.header-user-profile .dropdown-toggle');

        if (profileToggler) {
            // Inisialisasi Dropdown secara manual jika Bootstrap dropdown tidak berfungsi
            profileToggler.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu) {
                    // Toggle class show untuk menampilkan atau menyembunyikan dropdown
                    dropdownMenu.classList.toggle('show');
                    this.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));

                    // Tambahkan click listener pada document untuk menutup dropdown saat klik di luar
                    if (dropdownMenu.classList.contains('show')) {
                        document.addEventListener('click', function closeDropdown(event) {
                            if (!dropdownMenu.contains(event.target) && !profileToggler
                                .contains(event.target)) {
                                dropdownMenu.classList.remove('show');
                                profileToggler.setAttribute('aria-expanded', 'false');
                                document.removeEventListener('click', closeDropdown);
                            }
                        });
                    }
                }
            });
        }

        // Pastikan dropdown Bootstrap diinisialisasi kembali jika ada
        if (typeof bootstrap !== 'undefined') {
            const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            dropdownElements.forEach(element => {
                new bootstrap.Dropdown(element);
            });
        }
    });
</script>
