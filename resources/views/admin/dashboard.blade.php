@extends('app')

@section('content')
    <style>
        /* Custom style untuk kartu dengan outline biru tua */
        .card-outline-blue {
            background: transparent !important;
            border: 2px solid #1e3c72 !important;
            box-shadow: none !important;
            border-radius: 10px;
            transition: transform 0.2s ease;
        }

        .card-outline-blue:hover {
            transform: translateY(-5px);
            background: rgba(30, 60, 114, 0.02) !important;
        }

        /* Mengatur warna teks, icon, dan angka menjadi biru tua */
        .card-outline-blue h6,
        .card-outline-blue h2,
        .card-outline-blue span,
        .card-outline-blue p,
        .card-outline-blue i {
            color: #1e3c72 !important;
        }

        .m-b-0 {
            margin-bottom: 0;
        }
    </style>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Total Karyawan</h6>
                    <h2 class="text-end">
                        <i class="ph ph-users float-start"></i>
                        <span>{{ $totalKaryawan ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Aktif <span class="float-end">{{ $karyawanAktif ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Hadir Hari Ini</h6>
                    <h2 class="text-end">
                        <i class="ph ph-check-circle float-start"></i>
                        <span>{{ $hadirHariIni ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">On Time <span class="float-end">{{ $tepatWaktu ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Terlambat</h6>
                    <h2 class="text-end">
                        <i class="ph ph-clock float-start"></i>
                        <span>{{ $terlambat ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Hari Ini</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Tidak Hadir</h6>
                    <h2 class="text-end">
                        <i class="ph ph-x-circle float-start"></i>
                        <span>{{ $tidakHadir ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Hari Ini</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Project Aktif</h6>
                    <h2 class="text-end">
                        <i class="ph ph-kanban float-start"></i>
                        <span>{{ $projectAktif ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Selesai <span class="float-end">{{ $projectSelesai ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Total Task</h6>
                    <h2 class="text-end">
                        <i class="ph ph-check-square float-start"></i>
                        <span>{{ $totalTask ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Progress <span class="float-end">{{ $taskProgress ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Izin & Sakit </h6>
                    <h2 class="text-end">
                        <i class="ph ph-file-text float-start"></i>
                        <span>{{ $izinCuti ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Pending <span class="float-end">{{ $izinPending ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card order-card card-outline-blue">
                <div class="card-body">
                    <h6>Artikel Blog</h6>
                    <h2 class="text-end">
                        <i class="ph ph-article float-start"></i>
                        <span>{{ $totalArtikel ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0">Published <span class="float-end">{{ $artikelPublished ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Peta Lokasi Absensi -->
        <div class="col-md-6 col-xl-7">
            <div class="card">
                <div class="card-header">
                    <h5>Peta Lokasi Absensi</h5>
                </div>
                <div class="card-body">
                    <div id="world-map-markers" class="set-map" style="height: 365px; border-radius: 8px;"></div>
                </div>
            </div>
        </div>

        <!-- Leaflet CSS & JS (jika belum ada) -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

        <script>
            // Inisialisasi peta
            var mapAbsensi = L.map('world-map-markers').setView([-6.2, 106.8], 12);

            // Layer peta
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapAbsensi);

            // Icon untuk absensi
            var masukIcon = new L.Icon({
                iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                iconSize: [32, 32]
            });

            var pulangIcon = new L.Icon({
                iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                iconSize: [32, 32]
            });

            // Marker absensi
            @foreach ($absensis as $a)
                @if ($a->lat_masuk && $a->long_masuk)
                    L.marker([{{ $a->lat_masuk }}, {{ $a->long_masuk }}], {
                            icon: masukIcon
                        })
                        .addTo(mapAbsensi)
                        .bindPopup(`
                    <b>{{ $a->user->name }}</b><br>
                    Absen Masuk<br>
                    Jam: {{ $a->jam_masuk ?? '-' }}<br>
                    Cabang: {{ $a->cabang->nama_cabang ?? '-' }}
                `);
                @endif

                @if ($a->lat_pulang && $a->long_pulang)
                    L.marker([{{ $a->lat_pulang }}, {{ $a->long_pulang }}], {
                            icon: pulangIcon
                        })
                        .addTo(mapAbsensi)
                        .bindPopup(`
                    <b>{{ $a->user->name }}</b><br>
                    Absen Pulang<br>
                    Jam: {{ $a->jam_keluar ?? '-' }}<br>
                    Cabang: {{ $a->cabang->nama_cabang ?? '-' }}
                `);
                @endif
            @endforeach
        </script>

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
                    <div class="mt-4">
                        <canvas id="attendanceMonthlyChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

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
                                    <th>Jam Masuk</th>
                                    <th>Jam keluar</th>
                                    <th>Status</th>
                                    <th>Lokasi Cabang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensis as $a)
                                    <tr>
                                        <td>
                                            <img src="{{ $a->user->foto_profil && file_exists(public_path('foto-karyawan/' . $a->user->foto_profil))
                                                ? asset('foto-karyawan/' . $a->user->foto_profil)
                                                : asset('assets/images/avatar/avatar-1.jpg') }}"
                                                class="rounded-circle" width="40" height="40"
                                                style="object-fit: cover;" alt="Profil">
                                        </td>
                                        <td>{{ $a->user->nip }}</td>
                                        <td>{{ $a->user->name }}</td>
                                        <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>{{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $a->status == 'HADIR' ? 'bg-success' : ($a->status == 'TERLAMBAT' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $a->status ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $a->cabang->nama_cabang ?? '-' }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada absensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
