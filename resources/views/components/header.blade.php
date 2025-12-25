
<style>
    /* Header Hover Effects */
    .pc-head-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 12px;
    }

    /* Dropdown Item Hover */
    .dropdown-item:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        transform: translateX(5px);
    }

    /* Search Button Hover */
    .btn-primary:hover {
        opacity: 0.9;
        transform: scale(1.02);
    }

    /* User Avatar Hover */
    .user-avtar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
    }

    /* Notification Badge Animation */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .badge {
        animation: pulse 2s infinite;
    }
</style>
<header class="pc-header"
    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-bottom: 3px solid rgba(255,255,255,0.1);">
    <div class="m-header">
        <a href="../dashboard/index.html" class="b-brand text-white">
            <!-- ========   Change your logo from here   ============ -->
            <img src="{{ asset('assets/images/logo/logo3.png') }}" width="150" alt="" class="m-3">
        </a>
    </div>
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0 text-white" id="sidebar-hide"
                        style="transition: all 0.3s;">
                        <i class="ph ph-list" style="font-size: 24px;"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0 text-white" id="mobile-collapse">
                        <i class="ph ph-list" style="font-size: 24px;"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0 text-white" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false"
                        style="transition: all 0.3s;">
                        <i class="ph ph-magnifying-glass" style="font-size: 20px;"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search"
                        style="background: #fff; border: 1px solid rgba(30, 60, 114, 0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <form class="px-3">
                            <div class="form-group mb-0 d-flex align-items-center">
                                <input type="search" class="form-control border-0 shadow-none"
                                    placeholder="Cari data..." style="background: #f8f9fa;" />
                                <button class="btn btn-primary"
                                    style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); border: none;">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <!-- Notification Icon -->
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0 text-white" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false"
                        style="position: relative; transition: all 0.3s;">
                        <i class="ph ph-bell" style="font-size: 22px;"></i>
                        <span class="badge bg-danger rounded-pill"
                            style="position: absolute; top: 8px; right: 8px; font-size: 10px; padding: 2px 5px;">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown"
                        style="background: #fff; border: 1px solid rgba(30, 60, 114, 0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 300px;">
                        <div class="dropdown-header"
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 1px solid #dee2e6;">
                            <h6 class="mb-0" style="color: #2a5298;">Notifikasi</h6>
                        </div>
                        <div class="dropdown-body" style="max-height: 300px; overflow-y: auto;">
                            <a href="#" class="dropdown-item"
                                style="border-bottom: 1px solid #f1f3f5; padding: 12px 20px; transition: all 0.3s;">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ph ph-info text-primary" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0" style="font-size: 13px; color: #495057;">Ada izin baru
                                            menunggu approval</p>
                                        <small class="text-muted">5 menit yang lalu</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item"
                                style="border-bottom: 1px solid #f1f3f5; padding: 12px 20px; transition: all 0.3s;">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ph ph-check-circle text-success" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0" style="font-size: 13px; color: #495057;">Task "Design UI"
                                            telah selesai</p>
                                        <small class="text-muted">1 jam yang lalu</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="dropdown-footer text-center" style="border-top: 1px solid #dee2e6; padding: 10px;">
                            <a href="#"
                                style="color: #2a5298; text-decoration: none; font-size: 13px; font-weight: 500;">Lihat
                                Semua Notifikasi</a>
                        </div>
                    </div>
                </li>

                <!-- User Profile -->
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false"
                        style="transition: all 0.3s;">
                        <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar"
                            style="border: 2px solid rgba(255,255,255,0.5); box-shadow: 0 2px 8px rgba(0,0,0,0.2);" />
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown"
                        style="background: #fff; border: 1px solid rgba(30, 60, 114, 0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div class="dropdown-header"
                            style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 20px; border-radius: 8px 8px 0 0;">
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="rounded-circle"
                                    style="width: 50px; height: 50px; border: 2px solid #fff;" />
                                <div class="ms-3">
                                    <h6 class="mb-0 text-white">Admin User</h6>
                                    <small class="text-white-50">admin@company.com</small>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-body">
                            <div class="profile-notification-scroll position-relative"
                                style="max-height: calc(100vh - 225px)">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item" style="border: none; padding: 0;">
                                        <a href="#" class="dropdown-item"
                                            style="padding: 12px 20px; transition: all 0.3s; border-radius: 6px;">
                                            <span class="d-flex align-items-center">
                                                <i class="ph ph-user-circle"
                                                    style="font-size: 20px; color: #2a5298;"></i>
                                                <span class="ms-2" style="color: #495057;">Profil Saya</span>
                                            </span>
                                        </a>
                                        <a href="#" class="dropdown-item"
                                            style="padding: 12px 20px; transition: all 0.3s; border-radius: 6px;">
                                            <span class="d-flex align-items-center">
                                                <i class="ph ph-bell" style="font-size: 20px; color: #2a5298;"></i>
                                                <span class="ms-2" style="color: #495057;">Notifikasi</span>
                                            </span>
                                        </a>
                                        <a href="#" class="dropdown-item"
                                            style="padding: 12px 20px; transition: all 0.3s; border-radius: 6px;">
                                            <span class="d-flex align-items-center">
                                                <i class="ph ph-gear-six"
                                                    style="font-size: 20px; color: #2a5298;"></i>
                                                <span class="ms-2" style="color: #495057;">Pengaturan</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="list-group-item"
                                        style="border-top: 1px solid #f1f3f5; padding: 8px 0; margin-top: 8px;">
                                        <a href="#" class="dropdown-item" id="logoutBtn"
                                            style="padding: 12px 20px; transition: all 0.3s; border-radius: 6px;">
                                            <span class="d-flex align-items-center">
                                                <i class="ph ph-power" style="font-size: 20px; color: #dc3545;"></i>
                                                <span class="ms-2"
                                                    style="color: #dc3545; font-weight: 500;">Logout</span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#logoutBtn').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika menggunakan POST logout di Laravel
                    $.post("{{ route('logout') }}", {
                        _token: "{{ csrf_token() }}"
                    }).done(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Anda berhasil logout',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('login') }}";
                        });
                    });
                }
            });
        });
    });
</script>
