<!doctype html>
<html lang="en">
<!-- [Head] start -->


<head>
    <title>Home | Gradient Able Dashboard Template</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description"
        content="Gradient Able is trending dashboard template made using Bootstrap 5 design framework. Gradient Able is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies." />
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard" />
    <meta name="author" content="codedthemes" />

    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon" />

    <!-- map-vector css -->
    <link rel="stylesheet" href="../assets/css/plugins/jsvectormap.min.css" />
    <!-- [Google Font : Poppins] icon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css" />
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css" />
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css" />
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="../assets/css/style-preset.css" />

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-header="header-1" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true"
    data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="../dashboard/index.html" class="b-brand text-primary">
                    <!-- ========   Change your logo from here   ============ -->
                    <img src="../assets/images/logo-white.svg" alt="logo image" class="logo-lg" />
                </a>
            </div>
            {{-- header --}}
            @include('components.sidebar')

        </div>
    </nav>

    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('components.header')
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- Total Karyawan -->
                <div class="col-md-6 col-xl-3">
                    <div class="card bg-grd-primary order-card">
                        <div class="card-body">
                            <h6 class="text-white">Total Karyawan</h6>
                            <h2 class="text-end text-white">
                                <i class="feather icon-users float-start"></i>
                                <span>{{ $totalKaryawan ?? 0 }}</span>
                            </h2>
                            <p class="m-b-0">Aktif<span class="float-end">{{ $karyawanAktif ?? 0 }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Hadir Hari Ini -->
                <div class="col-md-6 col-xl-3">
                    <div class="card bg-grd-success order-card">
                        <div class="card-body">
                            <h6 class="text-white">Hadir Hari Ini</h6>
                            <h2 class="text-end text-white">
                                <i class="feather icon-check-circle float-start"></i>
                                <span>{{ $hadirHariIni ?? 0 }}</span>
                            </h2>
                            <p class="m-b-0">On Time<span class="float-end">{{ $tepatWaktu ?? 0 }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Terlambat -->
                <div class="col-md-6 col-xl-3">
                    <div class="card bg-grd-warning order-card">
                        <div class="card-body">
                            <h6 class="text-white">Terlambat</h6>
                            <h2 class="text-end text-white">
                                <i class="feather icon-clock float-start"></i>
                                <span>{{ $terlambat ?? 0 }}</span>
                            </h2>
                            <p class="m-b-0">Hari Ini</p>
                        </div>
                    </div>
                </div>

                <!-- Tidak Hadir -->
                <div class="col-md-6 col-xl-3">
                    <div class="card bg-grd-danger order-card">
                        <div class="card-body">
                            <h6 class="text-white">Tidak Hadir</h6>
                            <h2 class="text-end text-white">
                                <i class="feather icon-x-circle float-start"></i>
                                <span>{{ $tidakHadir ?? 0 }}</span>
                            </h2>
                            <p class="m-b-0">Hari Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h5>Peta Lokasi Absensi</h5>
                        </div>
                        <div class="card-body">
                            <div id="world-map-markers" class="set-map" style="height: 365px"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-5">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between py-3">
                            <h5>Statistik Kehadiran Karyawan</h5>
                            <div class="dropdown">
                                <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                                    data-bs-toggle="dropdown">
                                    <i class="material-icons-two-tone f-18">more_vert</i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Detail</a>
                                    <a class="dropdown-item" href="#">Export</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="media align-items-center">
                                <div class="avtar avtar-s bg-light-primary flex-shrink-0">
                                    <i class="ph ph-user-check f-20"></i>
                                </div>
                                <div class="media-body ms-3">
                                    <p class="mb-0 text-muted">Total Kehadiran Bulan Ini</p>
                                    <h5 class="mb-0">128 Kehadiran</h5>
                                </div>
                            </div>
                            <div class="mt-4 text-center text-muted">
                                <div class="mt-4">
                                    <canvas id="attendanceMonthlyChart" height="80"></canvas>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Kehadiran -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="media align-items-center">
                                        <div class="avtar avtar-s flex-shrink-0"
                                            style="background: rgba(40, 167, 69, 0.7);">
                                            <i class="ph ph-check-circle f-20 text-white"></i>
                                        </div>
                                        <div class="media-body ms-2">
                                            <p class="mb-0 text-muted">Hadir</p>
                                            <h6 class="mb-0">96</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="media align-items-center">
                                        <div class="avtar avtar-s flex-shrink-0"
                                            style="background: rgba(255, 193, 7, 0.7);">
                                            <i class="ph ph-clock f-20 text-white"></i>
                                        </div>
                                        <div class="media-body ms-2">
                                            <p class="mb-0 text-muted">Terlambat</p>
                                            <h6 class="mb-0">32</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="media align-items-center">
                                        <div class="avtar avtar-s flex-shrink-0"
                                            style="background: rgba(220, 53, 69, 0.7);">
                                            <i class="ph ph-x-circle f-20 text-white"></i>
                                        </div>
                                        <div class="media-body ms-2">
                                            <p class="mb-0 text-muted">Tidak Hadir</p>
                                            <h6 class="mb-0">12</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- table riwayat --}}

                <div class="col-sm-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h5>Riwayat Absensi Terbaru</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Foto Wajah</th>
                                            <th>NIK</th>
                                            <th>Nama Karyawan</th>
                                            <th>Waktu Absen</th>
                                            <th>Status</th>
                                            <th>Lokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DATA DUMMY -->
                                        <tr>
                                            <td>
                                                <img src="../assets/images/avatar/avatar-1.jpg" class="rounded-circle"
                                                    width="40" alt="foto">
                                            </td>
                                            <td>EMP001</td>
                                            <td>Andi Pratama</td>
                                            <td>2025-01-15 07:58</td>
                                            <td><span class="badge bg-success">Hadir</span></td>
                                            <td>Cabang Cianjur</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <img src="../assets/images/avatar/avatar-2.jpg" class="rounded-circle"
                                                    width="40" alt="foto">
                                            </td>
                                            <td>EMP002</td>
                                            <td>Siti Aisyah</td>
                                            <td>2025-01-15 08:12</td>
                                            <td><span class="badge bg-warning">Terlambat</span></td>
                                            <td>Cabang Cianjur Selatan</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <img src="../assets/images/avatar/avatar-3.jpg" class="rounded-circle"
                                                    width="40" alt="foto">
                                            </td>
                                            <td>EMP003</td>
                                            <td>Budi Santoso</td>
                                            <td>2025-01-15 00:00</td>
                                            <td><span class="badge bg-primary">Izin</span></td>
                                            <td>-</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <img src="../assets/images/avatar/avatar-4.jpg" class="rounded-circle"
                                                    width="40" alt="foto">
                                            </td>
                                            <td>EMP004</td>
                                            <td>Dewi Lestari</td>
                                            <td>2025-01-15 00:00</td>
                                            <td><span class="badge bg-danger">Alpha</span></td>
                                            <td>-</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 my-1">
                    <p class="m-0">
                        © <span id="year"></span>
                        <strong>Mendunia Absensi </strong>
                        | Face Recognition & Geolokasi
                    </p>
                </div>

                <div class="col-sm-6 ms-auto my-1">
                    <ul class="list-inline footer-link mb-0 justify-content-sm-end d-flex">
                        <li class="list-inline-item">
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#">Riwayat Absensi</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#">Profil</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- [Page Specific JS] start -->
    <script src="../assets/js/plugins/apexcharts.min.js"></script>
    <script src="../assets/js/plugins/jsvectormap.min.js"></script>
    <script src="../assets/js/plugins/world.js"></script>
    <script src="../assets/js/plugins/world-merc.js"></script>
    <script src="../assets/js/pages/dashboard-sales.js"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>
    <script>
        const ctxMonthly = document.getElementById('attendanceMonthlyChart').getContext('2d');

        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Jumlah Kehadiran',
                    data: [420, 398, 450, 470, 490, 510], // DATA DUMMY
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.15)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Kehadiran'
                        }
                    }
                }
            }
        });
    </script>


    <script>
        layout_change('light');
    </script>

    <script>
        layout_sidebar_change('light');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_caption_change('true');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change('preset-1');
    </script>

    <script>
        header_change('header-1');
    </script>


</body>
<!-- [Body] end -->

</html>
