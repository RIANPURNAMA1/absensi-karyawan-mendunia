@extends('app')

@section('content')
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

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

        /* Custom Card Hover Effects */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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

        /* Chart Container */
        .chart-container {
            position: relative;
            width: 100%;
        }
    </style>

    <div class="container-fluid px-4 py-6 max-w-[1920px] mx-auto">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
            <p class="text-gray-600">Ringkasan sistem absensi dan manajemen karyawan</p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

            <!-- Total Karyawan Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-users text-xl"></i>
                        </div>
                        <span
                            class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full uppercase">Aktif</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Karyawan</p>
                        <div class="flex items-baseline gap-1">
                            <h2 class="text-2xl font-bold text-gray-800">{{ $totalKaryawan ?? 0 }}</h2>
                            <span class="text-[10px] text-gray-400">org</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                        <span class="text-gray-400">Aktif</span>
                        <span class="font-bold text-gray-700">{{ $karyawanAktif ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Hadir Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-check-circle text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Hadir</p>
                        <div class="flex items-baseline gap-1">
                            <h2 class="text-2xl font-bold text-gray-800">{{ $hadirHariIni ?? 0 }}</h2>
                            <span class="text-[10px] text-emerald-500 font-medium">Masuk</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                        <span class="text-gray-400">On-Time</span>
                        <span class="font-bold text-emerald-600">{{ $tepatWaktu ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="ph ph-check-circle text-xl"></i>
                    </div>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Kehadiran</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $totalHadirSemua ?? 0 }}</h2>
                <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                    <span class="text-gray-400">Tepat Waktu</span>
                    <span class="font-bold text-emerald-600">{{ $tepatWaktu ?? 0 }}</span>
                </div>
            </div>

            <!-- Terlambat Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-clock text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Terlambat</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $terlambat ?? 0 }}</h2>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            @php
                                $persenTerlambat = $karyawanAktif > 0 ? round(($terlambat / $karyawanAktif) * 100) : 0;
                            @endphp
                            <div class="bg-amber-500 h-1 rounded-full" style="width: {{ $persenTerlambat }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alpa Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-x-circle text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Alpa</p>
                        <div class="flex items-baseline gap-1">
                            <h2 class="text-2xl font-bold text-gray-800">{{ $tidakHadir ?? 0 }}</h2>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                        <span class="text-gray-400">Status</span>
                        <span class="font-bold text-rose-600 italic">Cek</span>
                    </div>
                </div>
            </div>

            <!-- Project Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-kanban text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Project</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $projectAktif ?? 0 }}</h2>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                        <span class="text-gray-400">Done</span>
                        <span class="font-bold text-indigo-600">{{ $projectSelesai ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Izin/Sakit Card -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-violet-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-file-text text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Izin/Sakit</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $izinCuti ?? 0 }}</h2>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex justify-between items-center text-xs">
                        <span class="text-gray-400">Pending</span>
                        <span class="font-bold text-orange-500">{{ $izinPendingCount ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Rasio Keterlambatan Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div
                class="p-5 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 bg-blue-600 h-5 rounded-full"></span>
                    Rasio Keterlambatan
                </h3>
                <div class="flex gap-2">
                    <input type="text" class="text-xs border-gray-200 rounded-lg px-3 py-2 bg-gray-50"
                        value="01-02-2026 s/d 14-02-2026" readonly>
                </div>
            </div>
            <div class="p-6">
                <div class="chart-container" style="height: 400px;">
                    <canvas id="rasioChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Map and Mini Stats Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Map Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="ph ph-map-pin text-blue-600 mr-2"></i>
                                Sebaran Lokasi Absensi
                            </h2>
                            <p class="text-gray-400 text-xs mt-0.5 font-medium uppercase tracking-wider">
                                Tracking Real-time Karyawan
                            </p>
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

            <!-- Mini Stats Cards -->
            <div class="flex flex-col gap-4">

                <!-- Karyawan Aktif -->
                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600">
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

                <!-- Hadir Hari Ini -->
                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
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

                <!-- Belum Absen -->
                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-slate-300 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600">
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

                <!-- Izin Pending -->
                <div
                    class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-amber-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <i class="ph ph-hourglass text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Izin Pending</p>
                            <div class="flex items-baseline space-x-1">
                                <h3 class="text-2xl font-bold text-amber-600">{{ $izinPendingCount ?? 0 }}</h3>
                                <span class="text-xs text-amber-500 font-medium ml-1">Review</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Bar Chart - Tren Kehadiran -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ph ph-chart-bar text-slate-600 text-xl"></i>
                                </div>
                                Tren Kehadiran Bulanan
                            </h2>
                            <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">
                                Statistik 6 Bulan Terakhir
                            </p>
                        </div>
                        <div class="hidden sm:flex items-center space-x-4">
                            <div class="flex items-center text-xs font-semibold text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-2"></span> Hadir
                            </div>
                            <div class="flex items-center text-xs font-semibold text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span> Terlambat
                            </div>
                            <div class="flex items-center text-xs font-semibold text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span> Alpa
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="chart-container" style="height: 350px;">
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

            <!-- Donut Chart - Komposisi Hari Ini -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-full">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="ph ph-chart-donut text-2xl mr-3 text-gray-700"></i>
                            Komposisi Hari Ini
                        </h2>
                        <p class="text-gray-400 text-sm mt-1">{{ date('d M Y') }}</p>
                    </div>
                    <div class="p-6 flex items-center justify-center">
                        <div class="chart-container" style="height: 350px; width: 100%;">
                            <canvas id="compositionDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- izin dan lembur --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[480px]">
                <div
                    class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center mr-3">
                                <i class="ph ph-calendar-blank text-amber-600 text-xl"></i>
                            </div>
                            Riwayat Izin & Sakit
                        </h2>
                        <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">
                            Daftar Pengajuan Terbaru
                        </p>
                    </div>
                    <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">
                        {{ $dataIzinSakit->count() }} Total
                    </span>
                </div>

                <div
                    class="flex-grow overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300">
                    <ul class="divide-y divide-gray-50">
                        @forelse($dataIzinSakit as $izin)
                            <li class="p-4 hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="{{ $izin->user->foto_profil ? asset('uploads/foto_profil/' . $izin->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($izin->user->name) . '&background=random' }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm">
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-800 leading-none">
                                                {{ $izin->user->name }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 mt-1.5 flex items-center">
                                                <i class="ph ph-calendar mr-1 text-amber-600"></i>
                                                {{ \Carbon\Carbon::parse($izin->tanggal)->format('d M Y') }}
                                                <span class="mx-1.5">•</span>
                                                <i class="ph ph-buildings mr-1"></i>
                                                {{ $izin->cabang->nama_cabang ?? 'Pusat' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black tracking-wider {{ $izin->status == 'SAKIT' ? 'bg-rose-50 text-rose-700' : 'bg-blue-50 text-blue-700' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $izin->status == 'SAKIT' ? 'bg-rose-500' : 'bg-blue-500' }}"></span>
                                            {{ $izin->status }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="p-12 text-center text-gray-400 italic text-sm">Tidak ada data ditemukan.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-50 text-center">
                    <p class="text-[10px] text-gray-400">Menampilkan {{ $dataIzinSakit->count() }} data terakhir</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[480px]">
                <div
                    class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
                                <i class="ph ph-timer text-emerald-600 text-xl"></i>
                            </div>
                            Pengajuan Lembur
                        </h2>
                        <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Menunggu Persetujuan
                        </p>
                    </div>
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">
                        {{ $notifLembur->count() }} Request
                    </span>
                </div>

                <div
                    class="flex-grow overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300">
                    <ul class="divide-y divide-gray-50">
                        @forelse($notifLembur as $lembur)
                            <li class="p-4 hover:bg-emerald-50/30 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                                            {{ strtoupper(substr($lembur->user->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-800 leading-none">
                                                {{ $lembur->user->name }}</p>
                                            <p class="text-[11px] text-gray-500 mt-1">
                                                <span class="text-emerald-600 font-semibold">{{ $lembur->total_jam }}
                                                    Jam</span> •
                                                {{ \Carbon\Carbon::parse($lembur->tanggal)->format('d M') }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="/approval-lembur"
                                        class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-emerald-600 transition-all">
                                        <i class="ph ph-arrow-right font-bold"></i>
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="p-12 text-center text-gray-400 italic text-sm">Tidak ada pengajuan lembur.</li>
                        @endforelse
                    </ul>
                </div>

                @if ($notifLembur->count() > 0)
                    <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-50 text-center">
                        <a href="/approval-lembur"
                            class="text-xs font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Absensi Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center mr-3">
                            <i class="ph ph-clock-clockwise text-slate-600 text-xl"></i>
                        </div>
                        Riwayat Absensi Terbaru
                    </h2>
                    <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">
                        Data absensi terkini karyawan
                    </p>
                </div>
            </div>

            <div class="p-4">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse" id="dashboardTable">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">Foto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">NIK
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">Nama
                                    Karyawan</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">
                                    Masuk</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">
                                    Keluar</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">
                                    Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">
                                    Cabang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($absensis as $a)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <img src="{{ $a->user->foto_profil && file_exists(public_path('uploads/foto_profil/' . $a->user->foto_profil))
                                            ? asset('uploads/foto_profil/' . $a->user->foto_profil)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($a->user->name) . '&background=random' }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm"
                                            alt="{{ $a->user->name }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-700">{{ $a->user->nip }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">
                                        {{ $a->user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">
                                        <span class="font-mono">
                                            {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '--:--' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">
                                        <span class="font-mono">
                                            {{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i') : '--:--' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusConfig = [
                                                'HADIR' => [
                                                    'bg' => 'bg-emerald-50',
                                                    'text' => 'text-emerald-700',
                                                    'border' => 'border-emerald-100',
                                                ],
                                                'TERLAMBAT' => [
                                                    'bg' => 'bg-amber-50',
                                                    'text' => 'text-amber-700',
                                                    'border' => 'border-amber-100',
                                                ],
                                                'IZIN' => [
                                                    'bg' => 'bg-blue-50',
                                                    'text' => 'text-blue-700',
                                                    'border' => 'border-blue-100',
                                                ],
                                                'ALPA' => [
                                                    'bg' => 'bg-rose-50',
                                                    'text' => 'text-rose-700',
                                                    'border' => 'border-rose-100',
                                                ],
                                            ];
                                            $config = $statusConfig[$a->status] ?? [
                                                'bg' => 'bg-slate-50',
                                                'text' => 'text-slate-700',
                                                'border' => 'border-slate-100',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            {{ $a->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                                        {{ $a->cabang->nama_cabang ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTable Initialization
            $('#dashboardTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                "order": [
                    [3, "desc"]
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari data absensi...",
                    "lengthMenu": "_MENU_ entri per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Data tidak ditemukan",
                    "zeroRecords": "Tidak ada data yang sesuai",
                    "paginate": {
                        "previous": "<i class='ph ph-caret-left'></i>",
                        "next": "<i class='ph ph-caret-right'></i>"
                    }
                },
                "dom": '<"flex flex-col md:flex-row items-center justify-between gap-4 mb-4"lf>rt<"flex flex-col md:flex-row items-center justify-between gap-4 mt-4"ip>',
            });

            // Custom styling for DataTables
            $('.dataTables_filter input').addClass(
                'bg-slate-50 border border-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5'
            );
            $('.dataTables_length select').addClass(
                'bg-slate-50 border border-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1 px-2'
            );
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Leaflet Map
            var mapAbsensi = L.map('world-map-markers').setView([-6.2, 106.8], 12);

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

            // Initialize Charts
            initializeCharts();
        });

        function initializeCharts() {
            // Rasio Chart
            const ctxRasio = document.getElementById('rasioChart').getContext('2d');
            new Chart(ctxRasio, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsRasio ?? []) !!},
                    datasets: [{
                        label: 'Total Kehadiran',
                        data: {!! json_encode($dataTotalKehadiran ?? []) !!},
                        backgroundColor: '#1e40af',
                        borderRadius: 4,
                        barThickness: 15,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let percent = {!! json_encode($dataPersentaseTerlambat ?? []) !!}[context.dataIndex];
                                    return `Total: ${context.raw} | Terlambat: ${percent}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            suggestedMax: 130
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'labelsOnBar',
                    afterDatasetsDraw(chart) {
                        const {
                            ctx,
                            data
                        } = chart;
                        const percentages = {!! json_encode($dataPersentaseTerlambat ?? []) !!};

                        chart.getDatasetMeta(0).data.forEach((bar, index) => {
                            const val = percentages[index];
                            const total = data.datasets[0].data[index];

                            ctx.fillStyle = val > 50 ? '#ef4444' : '#1e293b';
                            ctx.font = 'bold 10px sans-serif';
                            ctx.fillText(`${val}%`, bar.x + 5, bar.y + 4);

                            ctx.fillStyle = '#60a5fa';
                            ctx.font = '10px sans-serif';
                            ctx.fillText(`${total} Kehadiran`, bar.x + 40, bar.y + 4);
                        });
                    }
                }]
            });

            // Bar Chart
            const ctxBar = document.getElementById('attendanceBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: @json($labelsBar ?? []),
                    datasets: [{
                            label: 'Tepat Waktu',
                            data: @json($dataHadirBar ?? []),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                        },
                        {
                            label: 'Terlambat',
                            data: @json($dataTerlambatBar ?? []),
                            backgroundColor: 'rgba(251, 146, 60, 0.8)',
                            borderColor: 'rgba(251, 146, 60, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                        },
                        {
                            label: 'Alpa',
                            data: @json($dataAlpaBar ?? []),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
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
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });

            // Donut Chart
            const ctxDonut = document.getElementById('compositionDonutChart').getContext('2d');
            const donutValues = Object.values(@json($donutData ?? []));

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
                        borderColor: '#ffffff',
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
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
