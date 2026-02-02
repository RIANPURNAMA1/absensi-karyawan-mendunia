@extends('app')

@section('content')
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Stagger animation delays */
        .delay-100 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-200 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-300 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .delay-400 {
            animation-delay: 0.4s;
            opacity: 0;
        }

        /* Custom Card Hover Effects */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
        }

        /* Leaflet Popup Styling */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 1rem;
            font-family: inherit;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    <div class="container-fluid px-4 py-6">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
            <p class="text-gray-600">Ringkasan sistem absensi dan manajemen karyawan</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-users text-2xl"></i>
                        </div>
                        <span
                            class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                            <i class="ph ph-trend-up mr-1"></i> Aktif
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Karyawan</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-3xl font-bold text-gray-800">{{ $totalKaryawan ?? 0 }}</h2>
                            <span class="text-xs text-gray-400">orang</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Karyawan Aktif</span>
                        <span class="font-semibold text-gray-700">{{ $karyawanAktif ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-check-circle text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Hadir Hari Ini</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-3xl font-bold text-gray-800">{{ $hadirHariIni ?? 0 }}</h2>
                            <span class="text-xs text-emerald-500 font-medium">On Time</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tepat Waktu</span>
                        <span class="font-semibold text-emerald-600">{{ $tepatWaktu ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-clock text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Terlambat</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-3xl font-bold text-gray-800">{{ $terlambat ?? 0 }}</h2>
                            <span class="text-xs text-gray-400">hari ini</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-x-circle text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Tidak Hadir</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-3xl font-bold text-gray-800">{{ $tidakHadir ?? 0 }}</h2>
                            <span class="text-xs text-rose-500 font-medium">Alpa</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status Absensi</span>
                        <span class="font-semibold text-rose-600 font-medium">Perlu Cek</span>
                    </div>
                </div>
            </div>

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-kanban text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Project Aktif</p>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $projectAktif ?? 0 }}</h2>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Project Selesai</span>
                        <span class="font-semibold text-indigo-600">{{ $projectSelesai ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div
                class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-violet-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-file-text text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Izin & Sakit</p>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $izinCuti ?? 0 }}</h2>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Menunggu Persetujuan</span>
                        <span class="font-semibold text-orange-500">{{ $izinPending ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>
        <!-- Map and Mini Stats Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="ph ph-map-pin text-blue-600 mr-2"></i>
                                Sebaran Lokasi Absensi
                            </h2>
                            <p class="text-gray-400 text-xs mt-0.5 font-medium uppercase tracking-wider">Tracking Real-time
                                Karyawan</p>
                        </div>
                        <div class="flex space-x-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                                Live
                            </span>
                        </div>
                    </div>
                    <div class="p-2">
                        <div id="world-map-markers" class="rounded-lg bg-slate-50 border border-gray-50"
                            style="height: 480px;"></div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4">

                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-colors duration-300">
                            <i class="ph ph-users text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Karyawan Aktif</p>
                            <div class="flex items-baseline space-x-1">
                                <h3 class="text-2xl font-bold text-gray-800">{{ $karyawanAktif ?? 0 }}</h3>
                                <span class="text-xs text-gray-400">Total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-colors duration-300">
                            <i class="ph ph-check-circle text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Hadir Hari Ini</p>
                            <div class="flex items-baseline space-x-1">
                                <h3 class="text-2xl font-bold text-emerald-600">{{ $hadirHariIni ?? 0 }}</h3>
                                <span class="text-xs text-gray-400">Karyawan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-slate-300 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition-colors duration-300">
                            <i class="ph ph-clock-countdown text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Belum Absen</p>
                            <div class="flex items-baseline space-x-1">
                                <h3 class="text-2xl font-bold text-gray-600">{{ $belumAbsen ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-amber-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-colors duration-300">
                            <i class="ph ph-hourglass text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Izin Pending</p>
                            <div class="flex items-baseline space-x-1">
                                <h3 class="text-2xl font-bold text-amber-600">{{ $izinPending ?? 0 }}</h3>
                                <span class="text-xs text-amber-500 font-medium ml-1">Review</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">


            <!--Chart -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">

                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ph ph-chart-bar text-slate-600 text-xl"></i>
                                </div>
                                Tren Kehadiran Bulanan
                            </h2>
                            <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Statistik 6 Bulan
                                Terakhir</p>
                        </div>

                        <div class="hidden sm:flex items-center space-x-4">
                            <div class="flex items-center text-xs font-semibold text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-800 mr-2"></span> Hadir
                            </div>
                            <div class="flex items-center text-xs font-semibold text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300 mr-2"></span> Terlambat
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div style="height: 350px;" class="relative">
                            <canvas id="attendanceBarChart"></canvas>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
                        <p class="text-xs text-gray-400 italic">
                            *Data diperbarui secara otomatis berdasarkan rekaman absensi bulanan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Donut Chart -->
            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="ph ph-chart-donut text-2xl mr-3 text-gray-700"></i>
                            Komposisi Hari Ini
                        </h2>
                        <p class="text-gray-400 text-sm mt-1">{{ date('d M Y') }}</p>
                    </div>
                    <div class="p-6 flex items-center justify-center">
                        <div style="height: 350px; width: 100%;">
                            <canvas id="compositionDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance History Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-white px-6 py-5 border-b border-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="ph ph-clock-clockwise text-slate-600 text-xl"></i>
                    </div>
                    Riwayat Absensi Terbaru
                </h2>
                <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Data absensi terkini karyawan
                </p>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full">
                    <thead class="bg-slate-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Foto
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">NIK
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama
                                Karyawan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jam
                                Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jam
                                Keluar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Cabang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absensis as $a)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <img src="{{ $a->user->foto_profil && file_exists(public_path('uploads/foto_profil/' . $a->user->foto_profil))
                                        ? asset('uploads/foto_profil/' . $a->user->foto_profil)
                                        : asset('assets/images/avatar/avatar-1.jpg') }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm"
                                        alt="{{ $a->user->name }}">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-700">{{ $a->user->nip }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-800">{{ $a->user->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-slate-600">
                                        <i class="ph ph-sign-in text-slate-400 mr-2"></i>
                                        {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-slate-600">
                                        <i class="ph ph-sign-out text-slate-400 mr-2"></i>
                                        {{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($a->status == 'HADIR')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            HADIR
                                        </span>
                                    @elseif($a->status == 'TERLAMBAT')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                            TERLAMBAT
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                            {{ $a->status ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-slate-600 font-medium">
                                        <i class="ph ph-buildings text-slate-400 mr-2"></i>
                                        {{ $a->cabang->nama_cabang ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="ph ph-database text-3xl text-slate-300"></i>
                                        </div>
                                        <p class="text-slate-500 font-bold">Belum ada data absensi</p>
                                        <p class="text-slate-400 text-xs mt-1 uppercase tracking-tighter">Data akan muncul
                                            secara real-time</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leaflet Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Map
            var mapAbsensi = L.map('world-map-markers').setView([-6.2, 106.8], 12);

            // Add Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(mapAbsensi);

            // Custom Icons
            var masukIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var pulangIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Add Markers
            @foreach ($absensis as $a)
                @if ($a->lat_masuk && $a->long_masuk)
                    L.marker([{{ $a->lat_masuk }}, {{ $a->long_masuk }}], {
                            icon: masukIcon
                        })
                        .addTo(mapAbsensi)
                        .bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold text-base mb-2">{{ $a->user->name }}</h3>
                                <div class="space-y-1 text-sm">
                                    <p><strong>Absen Masuk</strong></p>
                                    <p>⏰ {{ $a->jam_masuk ?? '-' }}</p>
                                    <p>🏢 {{ $a->cabang->nama_cabang ?? '-' }}</p>
                                </div>
                            </div>
                        `);
                @endif

                @if ($a->lat_pulang && $a->long_pulang)
                    L.marker([{{ $a->lat_pulang }}, {{ $a->long_pulang }}], {
                            icon: pulangIcon
                        })
                        .addTo(mapAbsensi)
                        .bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold text-base mb-2">{{ $a->user->name }}</h3>
                                <div class="space-y-1 text-sm">
                                    <p><strong>Absen Pulang</strong></p>
                                    <p>⏰ {{ $a->jam_keluar ?? '-' }}</p>
                                    <p>🏢 {{ $a->cabang->nama_cabang ?? '-' }}</p>
                                </div>
                            </div>
                        `);
                @endif
            @endforeach
        });
    </script>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Bar Chart Configuration
            const ctxBar = document.getElementById('attendanceBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: @json($labelsBar),
                    datasets: [{
                            label: 'Tepat Waktu',
                            data: @json($dataHadirBar),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(34, 197, 94, 1)',
                        },
                        {
                            label: 'Terlambat',
                            data: @json($dataTerlambatBar),
                            backgroundColor: 'rgba(251, 146, 60, 0.8)',
                            borderColor: 'rgba(251, 146, 60, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(251, 146, 60, 1)',
                        },
                        {
                            label: 'Alpa',
                            data: @json($dataAlpaBar),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(239, 68, 68, 1)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            borderColor: 'rgba(255, 255, 255, 0.3)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });

            // Donut Chart Configuration
            const ctxDonut = document.getElementById('compositionDonutChart').getContext('2d');
            const donutValues = Object.values(@json($donutData));

            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['HADIR', 'TERLAMBAT', 'IZIN', 'ALPA'],
                    datasets: [{
                        data: donutValues,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.9)',
                            'rgba(251, 191, 36, 0.9)',
                            'rgba(251, 146, 60, 0.9)',
                            'rgba(107, 114, 128, 0.9)'
                        ],
                        borderColor: [
                            'rgba(34, 197, 94, 1)',
                            'rgba(251, 191, 36, 1)',
                            'rgba(251, 146, 60, 1)',
                            'rgba(107, 114, 128, 1)'
                        ],
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            borderColor: 'rgba(255, 255, 255, 0.3)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
