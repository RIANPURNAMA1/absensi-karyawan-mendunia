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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }

        .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .leaflet-popup-content { margin: 1rem; font-family: inherit; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }

        .chart-container { position: relative; width: 100%; }

        /* Rasio Bar Styling */
        .rasio-row { transition: background 0.2s; }
        .rasio-row:hover { background: #f8fafc; }
        .progress-bar-hadir  { background: linear-gradient(90deg, #059669, #34d399); border-radius: 4px 0 0 4px; }
        .progress-bar-terlambat { background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 0 4px 4px 0; }
        .progress-bar-only { border-radius: 4px; }
    </style>

    <div class="container-fluid px-4 py-6 max-w-[1920px] mx-auto">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
            <p class="text-gray-600">Ringkasan sistem absensi dan manajemen karyawan</p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

            <!-- Total Karyawan -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-users text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full uppercase">Aktif</span>
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

            <!-- Hadir -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
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

            <!-- Total Kehadiran -->
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

            <!-- Terlambat -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                            <i class="ph ph-clock text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Terlambat</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $terlambat ?? 0 }}</h2>
                    </div>
                    <div class="mt-4">
                        @php $persenTerlambat = $karyawanAktif > 0 ? round(($terlambat / $karyawanAktif) * 100) : 0; @endphp
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-amber-500 h-1 rounded-full" style="width: {{ $persenTerlambat }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alpa -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
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

            <!-- Project -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
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

            <!-- Izin/Sakit -->
            <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 group-hover:bg-violet-600 group-hover:text-white transition-colors duration-300">
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

        <!-- ============================================================ -->
        <!--  RASIO KETERLAMBATAN — Stacked Horizontal Bar (% Hadir vs %) -->
        <!-- ============================================================ -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1 bg-amber-500 h-5 rounded-full"></span>
                        Rasio Keterlambatan per Divisi
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Persentase Hadir Tepat Waktu vs Terlambat</p>
                </div>
                <!-- Legend -->
                <div class="flex items-center gap-5 text-xs font-semibold">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm" style="background: linear-gradient(90deg,#059669,#34d399)"></span>
                        Tepat Waktu
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm" style="background: linear-gradient(90deg,#f59e0b,#fbbf24)"></span>
                        Terlambat
                    </span>
                </div>
            </div>

            <div class="p-6">
                @php
                    $labelsRasio            = $labelsRasio ?? [];
                    $dataPersenHadir        = $dataPersenHadir ?? [];
                    $dataPersentaseTerlambat= $dataPersentaseTerlambat ?? [];
                    $dataTotalKehadiran     = $dataTotalKehadiran ?? [];
                    $dataHadir              = $dataHadir ?? [];
                    $dataTerlambat          = $dataTerlambat ?? [];
                @endphp

                @if(count($labelsRasio) > 0)
                    <div class="space-y-4">
                        @foreach($labelsRasio as $i => $label)
                            @php
                                $persenH = $dataPersenHadir[$i] ?? 0;
                                $persenT = $dataPersentaseTerlambat[$i] ?? 0;
                                $total   = $dataTotalKehadiran[$i] ?? 0;
                                $jmlH    = $dataHadir[$i] ?? 0;
                                $jmlT    = $dataTerlambat[$i] ?? 0;

                                // Warning level
                                $warnClass = $persenT >= 40 ? 'text-rose-600' : ($persenT >= 20 ? 'text-amber-600' : 'text-emerald-600');
                                $warnBg    = $persenT >= 40 ? 'bg-rose-50 border-rose-100' : ($persenT >= 20 ? 'bg-amber-50 border-amber-100' : 'bg-emerald-50 border-emerald-100');
                            @endphp
                            <div class="rasio-row rounded-lg p-3 border border-gray-50">
                                <!-- Label & summary row -->
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-sm font-bold text-gray-700 truncate">{{ $label }}</span>
                                        <span class="text-[10px] text-gray-400 whitespace-nowrap">({{ $total }} absensi)</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                                        <span class="text-[11px] font-semibold text-emerald-600">{{ $persenH }}% hadir</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-[11px] font-bold {{ $warnClass }} border px-1.5 py-0.5 rounded {{ $warnBg }}">
                                            {{ $persenT }}% terlambat
                                        </span>
                                    </div>
                                </div>

                                <!-- Stacked Progress Bar -->
                                <div class="flex w-full h-5 rounded overflow-hidden bg-gray-100">
                                    @if($persenH > 0)
                                        <div class="progress-bar-hadir flex items-center justify-center text-[10px] font-bold text-white"
                                             style="width: {{ $persenH }}%">
                                            @if($persenH >= 12) {{ $persenH }}% @endif
                                        </div>
                                    @endif
                                    @if($persenT > 0)
                                        <div class="progress-bar-terlambat flex items-center justify-center text-[10px] font-bold text-white
                                                    {{ $persenH == 0 ? 'progress-bar-only' : '' }}"
                                             style="width: {{ $persenT }}%">
                                            @if($persenT >= 12) {{ $persenT }}% @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Sub-detail -->
                                <div class="flex justify-between mt-1.5 text-[10px] text-gray-400">
                                    <span>✅ {{ $jmlH }} tepat waktu</span>
                                    <span>⏰ {{ $jmlT }} terlambat</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Chart.js Stacked Bar di bawah -->
                    <div class="mt-8 border-t border-gray-50 pt-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Visualisasi Grafik Persentase</p>
                        <div class="chart-container" style="height: {{ max(250, count($labelsRasio) * 52) }}px;">
                            <canvas id="rasioChart"></canvas>
                        </div>
                    </div>

                @else
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <i class="ph ph-chart-bar text-5xl mb-3 text-gray-200"></i>
                        <p class="text-sm font-medium">Belum ada data divisi untuk ditampilkan.</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- ============================================================ -->

        <!-- Map and Mini Stats Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Map -->
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                            Live
                        </span>
                    </div>
                    <div class="p-2">
                        <div id="world-map-markers" class="rounded-lg bg-slate-50 border border-gray-50" style="height: 480px;"></div>
                    </div>
                </div>
            </div>

            <!-- Mini Stats -->
            <div class="flex flex-col gap-4">

                <div class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600">
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

                <div class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
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

                <div class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-slate-300 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600">
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

                <div class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-amber-200 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600">
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
                            <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Statistik 6 Bulan Terakhir</p>
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
                        <p class="text-xs text-gray-400 italic">*Data diperbarui secara otomatis berdasarkan rekaman absensi bulanan.</p>
                    </div>
                </div>
            </div>

            <!-- Donut Chart -->
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

        <!-- Izin & Lembur -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Riwayat Izin/Sakit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[480px]">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center mr-3">
                                <i class="ph ph-calendar-blank text-amber-600 text-xl"></i>
                            </div>
                            Riwayat Izin & Sakit
                        </h2>
                        <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Daftar Pengajuan Terbaru</p>
                    </div>
                    <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">
                        {{ $dataIzinSakit->count() }} Total
                    </span>
                </div>
                <div class="flex-grow overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300">
                    <ul class="divide-y divide-gray-50">
                        @forelse($dataIzinSakit as $izin)
                            <li class="p-4 hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="{{ $izin->user->foto_profil ? asset('uploads/foto_profil/' . $izin->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($izin->user->name) . '&background=random' }}"
                                             class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm">
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $izin->user->name }}</p>
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
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black tracking-wider {{ $izin->status == 'SAKIT' ? 'bg-rose-50 text-rose-700' : 'bg-blue-50 text-blue-700' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $izin->status == 'SAKIT' ? 'bg-rose-500' : 'bg-blue-500' }}"></span>
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

            <!-- Pengajuan Lembur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[480px]">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
                                <i class="ph ph-timer text-emerald-600 text-xl"></i>
                            </div>
                            Pengajuan Lembur
                        </h2>
                        <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Menunggu Persetujuan</p>
                    </div>
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">
                        {{ $notifLembur->count() }} Request
                    </span>
                </div>
                <div class="flex-grow overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200 hover:scrollbar-thumb-gray-300">
                    <ul class="divide-y divide-gray-50">
                        @forelse($notifLembur as $lembur)
                            <li class="p-4 hover:bg-emerald-50/30 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                                            {{ strtoupper(substr($lembur->user->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $lembur->user->name }}</p>
                                            <p class="text-[11px] text-gray-500 mt-1">
                                                <span class="text-emerald-600 font-semibold">{{ $lembur->total_jam }} Jam</span> •
                                                {{ \Carbon\Carbon::parse($lembur->tanggal)->format('d M') }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="/approval-lembur" class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-emerald-600 transition-all">
                                        <i class="ph ph-arrow-right font-bold"></i>
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="p-12 text-center text-gray-400 italic text-sm">Tidak ada pengajuan lembur.</li>
                        @endforelse
                    </ul>
                </div>
                @if($notifLembur->count() > 0)
                    <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-50 text-center">
                        <a href="/approval-lembur" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Absensi Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="ph ph-clock-clockwise text-slate-600 text-xl"></i>
                    </div>
                    Riwayat Absensi Terbaru
                </h2>
                <p class="text-gray-400 text-xs mt-1 font-medium uppercase tracking-widest">Data absensi terkini karyawan</p>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse" id="dashboardTable">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">Foto</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">NIK</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">Nama Karyawan</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">Masuk</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">Keluar</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider bg-gray-50">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider bg-gray-50">Cabang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($absensis as $a)
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
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $a->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">
                                        <span class="font-mono">{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '--:--' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">
                                        <span class="font-mono">{{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i') : '--:--' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusConfig = [
                                                'HADIR'     => ['bg'=>'bg-emerald-50','text'=>'text-emerald-700','border'=>'border-emerald-100'],
                                                'TERLAMBAT' => ['bg'=>'bg-amber-50','text'=>'text-amber-700','border'=>'border-amber-100'],
                                                'IZIN'      => ['bg'=>'bg-blue-50','text'=>'text-blue-700','border'=>'border-blue-100'],
                                                'ALPA'      => ['bg'=>'bg-rose-50','text'=>'text-rose-700','border'=>'border-rose-100'],
                                            ];
                                            $config = $statusConfig[$a->status] ?? ['bg'=>'bg-slate-50','text'=>'text-slate-700','border'=>'border-slate-100'];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            {{ $a->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ $a->cabang->nama_cabang ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- end container -->

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // ==========================================
        // DataTable
        // ==========================================
        $(document).ready(function () {
            $('#dashboardTable').DataTable({
                pageLength: 10,
                lengthMenu: [[10,25,50,-1],[10,25,50,"Semua"]],
                order: [[3,"desc"]],
                language: {
                    search: "",
                    searchPlaceholder: "Cari data absensi...",
                    lengthMenu: "_MENU_ entri per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak ditemukan",
                    zeroRecords: "Tidak ada data yang sesuai",
                    paginate: { previous: "<i class='ph ph-caret-left'></i>", next: "<i class='ph ph-caret-right'></i>" }
                },
                dom: '<"flex flex-col md:flex-row items-center justify-between gap-4 mb-4"lf>rt<"flex flex-col md:flex-row items-center justify-between gap-4 mt-4"ip>',
            });
            $('.dataTables_filter input').addClass('bg-slate-50 border border-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5');
            $('.dataTables_length select').addClass('bg-slate-50 border border-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1 px-2');
        });

        // ==========================================
        // Main Init
        // ==========================================
        document.addEventListener('DOMContentLoaded', function () {

            // --- Leaflet Map ---
            var mapAbsensi = L.map('world-map-markers').setView([-6.2, 106.8], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(mapAbsensi);

            var masukIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25,41], iconAnchor: [12,41], popupAnchor: [1,-34], shadowSize: [41,41]
            });
            var pulangIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25,41], iconAnchor: [12,41], popupAnchor: [1,-34], shadowSize: [41,41]
            });

            @foreach($absensis as $a)
                @if($a->lat_masuk && $a->long_masuk)
                    L.marker([{{ $a->lat_masuk }}, {{ $a->long_masuk }}], { icon: masukIcon })
                        .addTo(mapAbsensi)
                        .bindPopup(`<div class="p-2"><h3 class="font-bold text-base mb-2">{{ $a->user->name }}</h3><div class="space-y-1 text-sm"><p><strong>Absen Masuk</strong></p><p>⏰ {{ $a->jam_masuk ?? '-' }}</p><p>🏢 {{ $a->cabang->nama_cabang ?? '-' }}</p></div></div>`);
                @endif
                @if($a->lat_pulang && $a->long_pulang)
                    L.marker([{{ $a->lat_pulang }}, {{ $a->long_pulang }}], { icon: pulangIcon })
                        .addTo(mapAbsensi)
                        .bindPopup(`<div class="p-2"><h3 class="font-bold text-base mb-2">{{ $a->user->name }}</h3><div class="space-y-1 text-sm"><p><strong>Absen Pulang</strong></p><p>⏰ {{ $a->jam_keluar ?? '-' }}</p><p>🏢 {{ $a->cabang->nama_cabang ?? '-' }}</p></div></div>`);
                @endif
            @endforeach

            // --- Charts ---
            initCharts();
        });

        function initCharts() {

            // ==========================================
            // 1. RASIO KETERLAMBATAN — Stacked Horizontal Bar (%)
            // ==========================================
            const rasioCanvas = document.getElementById('rasioChart');
            if (rasioCanvas) {
                const ctxRasio  = rasioCanvas.getContext('2d');
                const labels    = {!! json_encode($labelsRasio ?? []) !!};
                const persenH   = {!! json_encode($dataPersenHadir ?? []) !!};
                const persenT   = {!! json_encode($dataPersentaseTerlambat ?? []) !!};
                const jmlH      = {!! json_encode($dataHadir ?? []) !!};
                const jmlT      = {!! json_encode($dataTerlambat ?? []) !!};
                const totals    = {!! json_encode($dataTotalKehadiran ?? []) !!};

                new Chart(ctxRasio, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Tepat Waktu (%)',
                                data: persenH,
                                backgroundColor: 'rgba(5, 150, 105, 0.85)',   // emerald-600
                                borderColor:     'rgba(5, 150, 105, 1)',
                                borderWidth: 1,
                                borderRadius: { topLeft: 4, bottomLeft: 4, topRight: 0, bottomRight: 0 },
                                borderSkipped: false,
                                stack: 'rasio',
                            },
                            {
                                label: 'Terlambat (%)',
                                data: persenT,
                                backgroundColor: context => {
                                    const v = context.raw;
                                    return v >= 40 ? 'rgba(239,68,68,0.85)'    // rose — kritis
                                         : v >= 20 ? 'rgba(245,158,11,0.85)'   // amber — waspada
                                                   : 'rgba(251,191,36,0.7)';   // yellow — aman
                                },
                                borderColor: context => {
                                    const v = context.raw;
                                    return v >= 40 ? 'rgba(239,68,68,1)'
                                         : v >= 20 ? 'rgba(245,158,11,1)'
                                                   : 'rgba(251,191,36,1)';
                                },
                                borderWidth: 1,
                                borderRadius: { topLeft: 0, bottomLeft: 0, topRight: 4, bottomRight: 4 },
                                borderSkipped: false,
                                stack: 'rasio',
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    font: { size: 12, weight: 'bold' },
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyle: 'rectRounded',
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    title: ctx => ctx[0].label,
                                    label: ctx => {
                                        const i = ctx.dataIndex;
                                        if (ctx.datasetIndex === 0) {
                                            return ` Tepat Waktu: ${persenH[i]}%  (${jmlH[i]} org)`;
                                        } else {
                                            return ` Terlambat:   ${persenT[i]}%  (${jmlT[i]} org)`;
                                        }
                                    },
                                    afterLabel: ctx => {
                                        if (ctx.datasetIndex === 1) {
                                            return ` Total kehadiran: ${totals[ctx.dataIndex]} absensi`;
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                min: 0,
                                max: 100,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: {
                                    callback: v => v + '%',
                                    font: { size: 11 }
                                },
                                title: { display: true, text: 'Persentase (%)', font: { size: 11, weight: 'bold' }, color: '#6b7280' }
                            },
                            y: {
                                stacked: true,
                                grid: { display: false },
                                ticks: { font: { size: 12, weight: '600' } }
                            }
                        }
                    },
                    // Plugin: tampilkan label % di dalam bar jika cukup lebar
                    plugins: [{
                        id: 'barLabels',
                        afterDatasetsDraw(chart) {
                            const { ctx } = chart;
                            chart.data.datasets.forEach((ds, di) => {
                                const meta = chart.getDatasetMeta(di);
                                meta.data.forEach((bar, bi) => {
                                    const val = ds.data[bi];
                                    if (val < 8) return; // terlalu sempit
                                    const { x, y, width, height } = bar.getProps(['x','y','width','height'], true);
                                    ctx.save();
                                    ctx.fillStyle = '#fff';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    // Posisi: tengah segmen
                                    const segW  = width; // lebar segmen sudah relatif
                                    const baseX = di === 0
                                        ? bar.x - width / 2   // hadir: mulai dari 0
                                        : bar.x - width / 2;  // terlambat: lanjutan
                                    ctx.fillText(val + '%', bar.x - width / 2 + width / 2, bar.y);
                                    ctx.restore();
                                });
                            });
                        }
                    }]
                });
            }

            // ==========================================
            // 2. TREN KEHADIRAN BULANAN
            // ==========================================
            const ctxBar = document.getElementById('attendanceBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: @json($labelsBar ?? []),
                    datasets: [
                        {
                            label: 'Tepat Waktu',
                            data: @json($dataHadirBar ?? []),
                            backgroundColor: 'rgba(34,197,94,0.8)',
                            borderColor: 'rgba(34,197,94,1)',
                            borderWidth: 2,
                            borderRadius: 8,
                        },
                        {
                            label: 'Terlambat',
                            data: @json($dataTerlambatBar ?? []),
                            backgroundColor: 'rgba(251,146,60,0.8)',
                            borderColor: 'rgba(251,146,60,1)',
                            borderWidth: 2,
                            borderRadius: 8,
                        },
                        {
                            label: 'Alpa',
                            data: @json($dataAlpaBar ?? []),
                            backgroundColor: 'rgba(239,68,68,0.8)',
                            borderColor: 'rgba(239,68,68,1)',
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
                            labels: { font: { size: 12, weight: 'bold' }, padding: 15, usePointStyle: true }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });

            // ==========================================
            // 3. DONUT — Komposisi Hari Ini
            // ==========================================
            const ctxDonut   = document.getElementById('compositionDonutChart').getContext('2d');
            const donutValues = Object.values(@json($donutData ?? []));
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['HADIR', 'TERLAMBAT', 'IZIN', 'ALPA'],
                    datasets: [{
                        data: donutValues,
                        backgroundColor: [
                            'rgba(34,197,94,0.9)',
                            'rgba(251,191,36,0.9)',
                            'rgba(251,146,60,0.9)',
                            'rgba(107,114,128,0.9)'
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
                            labels: { font: { size: 12, weight: 'bold' }, padding: 15, usePointStyle: true }
                        }
                    }
                }
            });
        }
    </script>
@endsection