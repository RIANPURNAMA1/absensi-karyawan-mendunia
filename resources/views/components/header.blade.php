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
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0 text-white" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false"
                        style="position: relative; transition: all 0.3s;">
                        <i class="ph ph-bell" style="font-size: 22px;"></i>

                        {{-- Logika Badge: Hitung total izin + lembur secara aman --}}
                        @php
                            $totalNotif =
                                (isset($notifIzin) ? $notifIzin->count() : 0) +
                                (isset($notifLembur) ? $notifLembur->count() : 0);
                        @endphp

                        @if ($totalNotif > 0)
                            <span class="badge bg-danger rounded-pill"
                                style="position: absolute; top: 8px; right: 8px; font-size: 10px; padding: 2px 5px;">
                                {{ $totalNotif }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown"
                        style="background: #fff; border: 1px solid rgba(30, 60, 114, 0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 320px;">
                        <div class="dropdown-header"
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 1px solid #dee2e6;">
                            <h6 class="mb-0" style="color: #2a5298;">Notifikasi Pengajuan</h6>
                        </div>
                        <div class="dropdown-body" style="max-height: 350px; overflow-y: auto;">

                            {{-- Gunakan isset() untuk mencegah error 'Undefined variable' --}}
                            @if ((isset($notifIzin) && $notifIzin->count() > 0) || (isset($notifLembur) && $notifLembur->count() > 0))

                                {{-- Render Notifikasi Izin --}}
                                @isset($notifIzin)
                                    @foreach ($notifIzin as $item)
                                        <a href="{{ url('/izin-cuti') }}" class="dropdown-item"
                                            style="border-bottom: 1px solid #f1f3f5; padding: 12px 20px; transition: all 0.3s;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ph ph-envelope-simple {{ $item->jenis_izin == 'SAKIT' ? 'text-danger' : 'text-primary' }}"
                                                        style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="mb-0" style="font-size: 13px; color: #495057;">
                                                        <strong>{{ $item->user->name }}</strong>: {{ $item->jenis_izin }}
                                                    </p>
                                                    <small
                                                        class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endisset

                                {{-- Render Notifikasi Lembur --}}
                                @isset($notifLembur)
                                    @foreach ($notifLembur as $lembur)
                                        <a href="{{ url('/approval-lembur') }}" class="dropdown-item"
                                            style="border-bottom: 1px solid #f1f3f5; padding: 12px 20px; transition: all 0.3s;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ph ph-timer text-success" style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="mb-0" style="font-size: 13px; color: #495057;">
                                                        <strong>{{ $lembur->user->name }}</strong>: LEMBUR
                                                    </p>
                                                    <small
                                                        class="text-muted">{{ $lembur->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endisset
                            @else
                                <div class="p-4 text-center">
                                    <i class="ph ph-bell-slash text-muted" style="font-size: 30px;"></i>
                                    <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Tidak ada pengajuan baru
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="dropdown-footer text-center" style="border-top: 1px solid #dee2e6; padding: 10px;">
                            <div class="d-flex justify-content-around">
                                <a href="{{ url('/izin-cuti') }}"
                                    style="color: #2a5298; font-size: 12px; font-weight: 500;">Semua Izin</a>
                                <span class="text-muted">|</span>
                                <a href="{{ url('/approval-lembur') }}"
                                    style="color: #2a5298; font-size: 12px; font-weight: 500;">Semua Lembur</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- User Profile -->
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false"
                        style="transition: all 0.3s;">
                        <img src="{{ Auth::user()->foto_profil ? asset('uploads/profil/' . Auth::user()->foto_profil) : asset('assets/images/user/avatar-2.jpg') }}"
                            alt="user-image" class="user-avtar"
                            style="border: 2px solid rgba(255,255,255,0.5); box-shadow: 0 2px 8px rgba(0,0,0,0.2); width: 40px; height: 40px; object-fit: cover;" />
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown"
                        style="background: #fff; border: 1px solid rgba(30, 60, 114, 0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div class="dropdown-header"
                            style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 20px; border-radius: 8px 8px 0 0;">
                            <div class="d-flex align-items-center">
                                <img src="{{ Auth::user()->foto_profil ? asset('uploads/profil/' . Auth::user()->foto_profil) : asset('assets/images/user/avatar-2.jpg') }}"
                                    alt="user-image" class="rounded-circle"
                                    style="width: 50px; height: 50px; border: 2px solid #fff; object-fit: cover;" />
                                <div class="ms-3">
                                    <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                                    <small class="text-white-50">{{ Auth::user()->role }} |
                                        {{ Auth::user()->email }}</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#btnLogout').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('logout') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Redirect setelah timer selesai
                            setTimeout(() => {
                                window.location.href = res.redirect;
                            }, 1500);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan pada sistem.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
