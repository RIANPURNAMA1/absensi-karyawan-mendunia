<!doctype html>
<html lang="en">

<head>
    <title>Absensi Mendunia</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Absensi Karyawan Mendunia - Face Recognition & Geolokasi" />
    <meta name="keywords" content="absensi, karyawan, face recognition, geolokasi" />
    <meta name="author" content="Mendunia" />

    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png">

    <link rel="stylesheet" href="../assets/css/plugins/jsvectormap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css" />
    <link rel="stylesheet" href="../assets/fonts/feather.css" />
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/fonts/material.css" />
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="../assets/css/style-preset.css" />
    <link rel="stylesheet" href="../assets/css/custom.css" />

</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('components.header')

    <div class="main-content">
        <div>
            @yield('contentTask')
        </div>
        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 my-1">
                    <p class="m-0">
                        &copy; <span id="year"></span>
                        <strong>Mendunia Absensi</strong>
                        | Face Recognition & Geolokasi
                    </p>
                </div>
                <div class="col-sm-6 ms-auto my-1">
                    <ul class="list-inline footer-link mb-0 justify-content-sm-end d-flex">
                        <li class="list-inline-item"><a href="#">Dashboard</a></li>
                        <li class="list-inline-item"><a href="#">Riwayat Absensi</a></li>
                        <li class="list-inline-item"><a href="#">Profil</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/plugins/apexcharts.min.js"></script>
    <script src="../assets/js/plugins/jsvectormap.min.js"></script>
    <script src="../assets/js/plugins/world.js"></script>
    <script src="../assets/js/plugins/world-merc.js"></script>
    <script src="../assets/js/pages/dashboard-sales.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>

    @stack('scripts')
</body>

</html>
