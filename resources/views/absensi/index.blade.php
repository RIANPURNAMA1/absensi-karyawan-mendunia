<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    {{-- <link rel="icon" href="{{ asset('assets/compiled/png/LOGO/logo4.png') }}" type="image/x-icon"> --}}
    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png" style="width: 40px">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Face-api.js CDN - WAJIB ADA -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
    <style>
        #loaderFace {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 60;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- STATUS BAR -->
    <div class="bg-white px-4 pt-3 pb-2">
        <div class="flex items-center justify-between text-xs text-gray-600">
            <span id="statusTime">--:--</span>
            <div class="flex gap-1">
                <div class="w-4 h-3 border border-gray-400 rounded-sm relative">
                    <div class="absolute inset-0.5 bg-gray-800 rounded-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();

            let hours = now.getHours().toString().padStart(2, '0');
            let minutes = now.getMinutes().toString().padStart(2, '0');

            document.getElementById("statusTime").textContent = `${hours}:${minutes}`;
        }

        // Jalankan pertama kali
        updateTime();

        // Update tiap 1 detik
        setInterval(updateTime, 1000);
    </script>


    <!-- HEADER -->
    <div class="bg-white px-5 pt-4 pb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <!-- PROFILE INFO (CLICKABLE) -->
            <a href="/absensi/profile" class="flex items-center gap-3 hover:opacity-80 transition">
                <!-- FOTO PROFIL (DINAMIS DARI BACKEND) -->
                <div class="w-10 h-10 rounded-full overflow-hidden border border-blue-500">
                    <img src="{{ auth()->user() && auth()->user()->foto_profil
                        ? asset('uploads/foto_profil/' . auth()->user()->foto_profil)
                        : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png' }}"
                        alt="Foto Karyawan" class="w-full h-full object-cover">
                </div>

                <!-- NAMA & DIVISI -->
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ auth()->user()->name ?? auth()->user()->name }}
                    </p>

                    <p class="text-xs text-black font-medium">
                        {{ auth()->user()->divisi->nama_divisi ?? 'Divisi belum diatur' }}
                    </p>
                </div>
            </a>


            <button class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center relative">
                <i data-lucide="bell" class="w-4 h-4 text-gray-700"></i>
                <span
                    class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">3</span>
            </button>
        </div>
        {{-- <div class="flex justify-center py-5 ">

            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="logo image" class="logo-lg" width="200" />
        </div> --}}

        <!-- SEARCH BAR -->
        {{-- <div class="relative mb-8 px-2">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-32 h-32 bg-blue-100/50 rounded-full blur-3xl -z-10">
            </div>

            <div class="flex flex-col items-center justify-center text-center">
                <div
                    class="p-4transition-all duration-300 group">
                    <img src="{{ asset('assets/images/logo/logo.png') }}" alt="logo image"
                        class="logo-lg object-contain group-hover:scale-105 transition-transform duration-300"
                        width="180" />
                </div>
            </div>
        </div> --}}
    </div>

    <div class="px-5 py-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-gray-900">Jadwal Shift Hari Ini</h2>
                <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-xs font-bold">!</span>
                </div>
            </div>
            <button class="text-blue-600 text-sm font-semibold">See All</button>
        </div>

        @php
            $userShifts = isset($userShifts) ? $userShifts : collect();
            $firstShift = $userShifts->first();
        @endphp

        @if ($userShifts && $userShifts->count() > 0)
            <div class="bg-gradient-to-br from-[#00c0ff] to-blue-700 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base">{{ $firstShift->nama_shift }}</h3>
                            <p class="text-blue-100 text-sm">Status: {{ $firstShift->status }}</p>
                            @if ($userShifts->count() > 1)
                                <p class="text-blue-200 text-xs mt-1">+ {{ $userShifts->count() - 1 }} shift lainnya</p>
                            @endif
                        </div>
                    </div>
                    <button onclick="mulaiAbsenFoto()"
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md">
                        <i data-lucide="camera" class="w-5 h-5 text-blue-600"></i>
                    </button>
                </div>

                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-200"></i>
                        <span class="text-sm">{{ \Carbon\Carbon::now()->translatedFormat('l, d M') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-200"></i>
                        <span class="text-sm">
                            {{ \Carbon\Carbon::parse($firstShift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($firstShift->jam_pulang)->format('H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-100 rounded-2xl p-5 text-gray-500 border-2 border-dashed border-gray-200 text-center">
                <p class="text-sm">Jadwal shift belum ditentukan.</p>
            </div>
        @endif
    </div>

    <div class="px-5 pb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Quick Actions</h2>
            <button class="text-blue-600 text-sm font-semibold">See All</button>
        </div>

        <div class="grid grid-cols-4 gap-2">
            {{-- <button onclick="openAbsen()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition border border-blue-50">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center relative overflow-hidden">
                    <i data-lucide="scan-face" class="w-5 h-5 text-blue-600 relative z-10"></i>

                    <div class="absolute inset-0 bg-blue-400/10 animate-pulse"></div>
                </div>
                <span class="text-[11px] font-bold text-gray-800">Face Scan</span>
            </button> --}}

            <button type="button" onclick="showUnderDevelopment()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="qr-code" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Scan QR</span>
            </button>

            <button onclick="location.href='/absensi/izin'"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Izin/Sakit</span>
            </button>

            <button onclick="location.href='/absensi/riwayat'"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="history" class="w-5 h-5 text-amber-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Riwayat</span>
            </button>

            <button  onclick="location.href= '/absensi/lembur'"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock-alert" class="w-5 h-5 text-red-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Lembur</span>
            </button>

            <button onclick="toggleModalJadwal(true)"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Jadwal</span>
            </button>
            <button onclick="openModalSensei()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-violet-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Sensei</span>
            </button>

            <button onclick="openModalAgenda()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-amber-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Agenda</span>
            </button>
        </div>
    </div>

    <!-- AGENDA HARI INI -->
    @if($agendaHariIni && $agendaHariIni->count() > 0)
    <div class="px-5 pb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Agenda Hari Ini</h2>
            <button onclick="openModalAgenda()" class="text-amber-600 text-sm font-semibold flex items-center gap-1">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
            </button>
        </div>
        <div class="space-y-3">
            @foreach($agendaHariIni as $agenda)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100">
                <div class="flex items-start gap-3">
                    @if($agenda->foto)
                    <img src="{{ asset('uploads/agenda/'.$agenda->foto) }}" alt="Foto" class="w-16 h-16 object-cover rounded-xl">
                    @else
                    <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="camera" class="w-8 h-8 text-amber-600"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            @if($agenda->keterangan)
                            <h3 class="font-semibold text-gray-900">{{ $agenda->keterangan }}</h3>
                            @else
                            <h3 class="font-semibold text-gray-900">Agenda tanpa keterangan</h3>
                            @endif
                            @if($agenda->jam_absen_masuk && $agenda->jam_absen_keluar)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">Selesai</span>
                            @elseif($agenda->jam_absen_masuk)
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">Hadir</span>
                            @else
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">Terjadwal</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 mt-2">
                            <i data-lucide="clock" class="w-3 h-3 text-gray-400"></i>
                            <span class="text-[10px] text-gray-500">
                                {{ $agenda->jam_absen_masuk ? \Carbon\Carbon::parse($agenda->jam_absen_masuk)->format('H:i') : '' }}
                                {{ $agenda->jam_absen_keluar ? '- '.\Carbon\Carbon::parse($agenda->jam_absen_keluar)->format('H:i') : '' }}
                            </span>
                        </div>
                    </div>
                </div>
                @if($agenda->jam_absen_masuk && !$agenda->jam_absen_keluar)
                <button onclick="absenAgendaPulang({{ $agenda->id }})" class="mt-3 w-full py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold active:scale-95 transition flex items-center justify-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Absen Pulang
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="px-5 pb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Agenda Hari Ini</h2>
            <button onclick="openModalAgenda()" class="text-amber-600 text-sm font-semibold flex items-center gap-1">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
            </button>
        </div>
        <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-2xl">
            <i data-lucide="camera" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
            <p class="text-sm">Belum ada agenda hari ini</p>
            <p class="text-xs mt-1">Klik + untuk absen agenda</p>
        </div>
    </div>
    @endif

    <!-- KELAS SENSEI AKTIF -->
    <div class="px-5 pb-5" id="sectionKelasSensei">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Kelas Sensei</h2>
            @if(!$isLibur)
            <button onclick="openModalSensei()" class="text-violet-600 text-sm font-semibold flex items-center gap-1">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kelas
            </button>
            @endif
        </div>

        <div id="kelasSenseiContainer" class="space-y-3">
            <!-- Loaded via JS -->
        </div>
        @if($isLibur)
            <div id="serverKelasSensei" class="text-center py-8 text-gray-400 bg-gray-50 rounded-2xl">
                <i data-lucide="calendar-off" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm">Hari ini sedang libur</p>
                <p class="text-xs mt-1">Absensi sensei tidak tersedia</p>
            </div>
        @elseif($kelasSenseiAktif && $kelasSenseiAktif->count() > 0)
            <div id="serverKelasSensei" class="space-y-3">
                @foreach($kelasSenseiAktif as $kelas)
                    @php
                        $absensiHariIni = $kelas->absensi->first();
                        $sudahMasuk = $absensiHariIni && $absensiHariIni->jam_masuk;
                        $sudahPulang = $absensiHariIni && $absensiHariIni->jam_keluar;
                    @endphp
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-violet-100" data-kelas-id="{{ $kelas->id }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                    <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $kelas->nama_kelas }}</h3>
                                    <p class="text-xs text-gray-500">Level {{ $kelas->level }} - {{ \Carbon\Carbon::parse($kelas->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($kelas->tanggal_selesai)->format('d M') }}</p>
                                </div>
                            </div>
                            @if($sudahMasuk && !$sudahPulang)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">HADIR</span>
                            @elseif($sudahMasuk && $sudahPulang)
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full">SELESAI</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if(!$sudahMasuk)
                                <button onclick="absenSenseiMasuk({{ $kelas->id }})" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold active:scale-95 transition">
                                    Absen Masuk
                                </button>
                            @elseif(!$sudahPulang)
                                <button onclick="absenSenseiPulang({{ $kelas->id }})" class="flex-1 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl text-sm font-bold active:scale-95 transition">
                                    Absen Pulang
                                </button>
                            @else
                                <div class="flex-1 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-sm font-semibold text-center">
                                    {{ $absensiHariIni->jam_masuk }} - {{ $absensiHariIni->jam_keluar }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-400" id="serverEmptyKelasSensei">
                <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm">Belum ada kelas aktif</p>
                <p class="text-xs mt-1">Tambahkan kelas baru untuk mulai absen</p>
            </div>
        @endif
    </div>

    {{-- Modal Jadwal Dinamis --}}
    <div id="modalJadwal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="toggleModalJadwal(false)"></div>

        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-[40px] p-6 shadow-2xl transition-transform duration-300 translate-y-full"
            id="modalContent">
            <div class="flex flex-col items-center">
                <div class="w-12 h-1.5 bg-gray-200 rounded-full mb-6"></div>

                <div class="flex justify-between items-center w-full mb-6 text-center">
                    <div class="text-left">
                        <h2 class="text-lg font-black text-gray-900">Jadwal Shift Kerja</h2>
                        <p class="text-[10px] text-gray-400">Daftar waktu operasional kantor</p>
                    </div>
                    <button onclick="toggleModalJadwal(false)" class="p-2 bg-gray-100 rounded-full text-gray-500">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="w-full space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @php
                        $userShifts = $userShifts ?? collect();
                    @endphp
                    
                    @forelse($userShifts as $shift)
                        @php
                            // Cek apakah ini shift yang sedang berjalan (currentShift dari controller)
                            $isCurrent = isset($currentShift) && $currentShift->id == $shift->id;
                        @endphp

                        <div
                            class="flex items-center gap-4 p-4 {{ $isCurrent ? 'bg-indigo-50 border-indigo-100' : 'bg-gray-50 border-gray-100' }} rounded-2xl border transition-all">
                            <div
                                class="w-12 h-12 {{ $isCurrent ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200' }} rounded-xl flex flex-col items-center justify-center shadow-sm">
                                <span
                                    class="text-[9px] font-bold uppercase">{{ substr($shift->nama_shift, 0, 3) }}</span>
                                <i data-lucide="clock-4" class="w-5 h-5 mt-0.5"></i>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-gray-800">{{ $shift->nama_shift }}</p>
                                    @if ($isCurrent)
                                        <span class="animate-pulse flex h-2 w-2 rounded-full bg-green-500"></span>
                                    @endif
                                </div>
                                <p
                                    class="text-[11px] {{ $isCurrent ? 'text-indigo-600' : 'text-gray-500' }} font-medium">
                                    {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}
                                </p>
                            </div>

                            @if ($isCurrent)
                                <span
                                    class="text-[9px] font-black text-indigo-600 bg-white border border-indigo-100 px-2 py-1 rounded-lg uppercase tracking-wider">Aktif</span>
                            @else
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                                    @php
                                        $jamMasuk = $shift->jam_masuk ? \Carbon\Carbon::parse($shift->jam_masuk) : null;
                                        $jamPulang = $shift->jam_pulang ? \Carbon\Carbon::parse($shift->jam_pulang) : null;
                                        if ($jamMasuk && $jamPulang) {
                                            $diffMinutes = $jamMasuk->diffInMinutes($jamPulang);
                                            $hours = floor($diffMinutes / 60);
                                            $minutes = $diffMinutes % 60;
                                            echo $hours . ' Jam' . ($minutes > 0 ? ' ' . $minutes . ' Menit' : '');
                                        } else {
                                            echo '-';
                                        }
                                    @endphp
                                    </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-400 text-xs">Belum ada jadwal shift yang ditentukan.</p>
                        </div>
                    @endforelse
                </div>

                <button onclick="toggleModalJadwal(false)"
                    class="w-full mt-6 bg-gray-900 text-white py-4 rounded-2xl font-bold text-sm active:scale-95 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <!-- RIWAYAT ABSENSI -->
    <div class="px-5 pb-24">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Riwayat Absensi</h2>
            <button class="text-blue-600 text-sm font-semibold">See All</button>
        </div>

        <div class="space-y-3">
            <div id="riwayatContainer">
                @forelse ($riwayat as $a)
                    <div class="bg-white rounded-2xl p-4 mb-2 shadow-sm flex items-center gap-4 border-l-4 border-blue-500">
                        <a href="{{ route('absensi.riwayat') }}"
                            class="flex items-center gap-4 p-4 hover:bg-gray-50 transition w-full">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-xs text-blue-600 font-medium">
                                        {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('D') }}
                                    </div>
                                    <div class="text-xl font-bold text-blue-700">
                                        {{ \Carbon\Carbon::parse($a->tanggal)->format('d') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">KARYAWAN</span>
                                    <h3 class="font-semibold text-gray-900">{{ $a->status }}</h3>
                                </div>
                                <div class="flex gap-4 text-xs text-gray-500">
                                    <span>In: {{ $a->jam_masuk ?? '-' }}</span>
                                    <span>Out: {{ $a->jam_keluar ?? '-' }}</span>
                                </div>
                            </div>

                            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center text-gray-500 text-sm">
                        Belum ada riwayat absensi karyawan
                    </div>
                @endforelse

                {{-- Riwayat Sensei --}}
                @forelse ($riwayatSensei as $a)
                    <a href="{{ route('absensi.detailSensei', ['tanggal' => $a->tanggal, 'kelasId' => $a->kelas_sensei_id]) }}"
                        class="block bg-white rounded-xl p-3 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <span class="text-sm font-semibold text-blue-600">{{ \Carbon\Carbon::parse($a->tanggal)->format('d') }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-blue-500">SENSEI</span>
                                    <span class="text-xs text-gray-400">{{ $a->kelasSensei->nama_kelas ?? '-' }}</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $a->jam_masuk ?? '-' }} - {{ $a->jam_keluar ?? '-' }}
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
                        </div>
                    </a>
                @empty
                @endforelse
            </div>

        </div>
    </div>


<!-- BOTTOM NAV -->
    @include('components.bottom_Nav')
    @include('absensi.modal_manual')
    @include('absensi.modal_sensei')
    @include('absensi.modal_agenda')

    <!-- Modal Kamera untuk Agenda -->
    <div id="modalKameraAgenda" class="fixed inset-0 z-[9999] bg-black hidden">
        <video id="videoPreviewAgenda" class="w-full h-full object-cover transform scale-x-[-1]" autoplay playsinline muted></video>
        
        <div class="absolute inset-0 flex flex-col justify-between items-center p-6">
            <div class="w-full flex justify-end">
                <button onclick="hentikanKameraAgenda()" class="bg-black/50 backdrop-blur-md p-3 rounded-full text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="flex flex-col items-center gap-6 pb-12">
                <button onclick="ambilFotoAgenda()" class="group relative w-20 h-20 bg-white rounded-full p-1 shadow-[0_0_30px_rgba(255,255,255,0.6)] active:scale-95">
                    <div class="w-full h-full rounded-full border-[3px] border-gray-800"></div>
                </button>
            </div>
        </div>
    </div>
    <canvas id="canvasAgenda" class="hidden"></canvas>

    <!-- Modal Kamera Absensi - FIXED VERSION -->
    <div id="modalKameraAbsen" class="fixed inset-0 z-[9999] bg-black hidden items-center justify-center">

        <!-- Video Preview - PERBAIKAN: Hapus absolute, gunakan relative -->
        <video id="videoPreviewAbsen" class="w-full h-full object-cover transform scale-x-[-1]" autoplay playsinline
            muted>
        </video>



        <!-- UI Controls Overlay -->
        <div class="absolute inset-0 flex flex-col justify-between items-center p-6 pointer-events-none">

            <!-- Top Bar - Close Button -->
            <div class="w-full flex justify-end pointer-events-auto">
                <button onclick="hentikanKameraAbsen()"
                    class="bg-black/50 backdrop-blur-md p-3 rounded-full text-white shadow-lg hover:bg-black/70 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Bottom Bar - Instruction & Shutter -->
            <div class="flex flex-col items-center gap-6 pb-12 pointer-events-auto">
                <!-- Instruction Text -->
                <!-- Shutter Button -->
                <button id="btnShutterAbsen" onclick="eksekusiAmbilFoto()"
                    class="group relative w-20 h-20 bg-white rounded-full p-1 shadow-[0_0_30px_rgba(255,255,255,0.6)] active:scale-95 transition-transform">
                    <div
                        class="w-full h-full rounded-full border-[3px] border-gray-800 group-active:border-gray-600 transition-colors">
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Canvas tersembunyi untuk capture -->
    <canvas id="canvasSimpanFoto" class="hidden"></canvas>

    <style>
        /* Pastikan video selalu tampil penuh */
        #videoPreviewAbsen {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
        }

        /* Animasi untuk shutter button */
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        #btnShutterAbsen:hover {
            animation: pulse-ring 1.5s infinite;
        }

        /* Smooth transitions */
        #modalKameraAbsen {
            transition: opacity 0.3s ease;
        }

        #modalKameraAbsen.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #modalKameraAbsen:not(.hidden) {
            opacity: 1;
        }
    </style>
    {{-- MODAL REGISTRASI WAJAH
    <div id="modalRegistrasiWajah"
        class="hidden fixed inset-0 bg-black/80 items-center justify-center z-[60] p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 relative w-full max-w-sm flex flex-col items-center shadow-2xl">



            <div id="mainContentReg" class="hidden w-full flex flex-col items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="user-check" id="statusIcon" class="w-8 h-8 text-blue-600"></i>
                </div>

                <h3 class="font-bold text-xl text-gray-900 mb-1 text-center">Pendaftaran Wajah</h3>
                <p id="instructionText" class="text-gray-500 text-sm text-center mb-5">
                    Posisikan wajah di tengah lingkaran dan diam sebentar...
                </p>

                <div
                    class="relative w-full aspect-square max-w-[280px] bg-gray-900 rounded-full overflow-hidden border-4 border-blue-100 shadow-xl mx-auto">
                    <video id="videoReg" autoplay muted playsinline
                        class="w-full h-full object-cover scale-x-[-1]"></video>
                    <canvas id="canvasReg" class="absolute inset-0 w-full h-full"></canvas>

                    <svg class="absolute inset-0 w-full h-full pointer-events-none -rotate-90">
                        <circle cx="140" cy="140" r="135" stroke="currentColor" stroke-width="8"
                            fill="transparent" class="text-blue-600/30" />
                        <circle id="progressCircle" cx="140" cy="140" r="135" stroke="currentColor"
                            stroke-width="8" fill="transparent" stroke-dasharray="848" stroke-dashoffset="848"
                            class="text-blue-600 transition-all duration-200 ease-linear" />
                    </svg>
                </div>

                <div class="mt-6 w-full">
                    <p id="timerText" class="text-center font-bold text-blue-600 text-lg h-7"></p>
                    <p class="text-[10px] text-center text-gray-400 italic">Sistem akan mengambil foto otomatis saat
                        posisi stabil</p>
                </div>
            </div>
        </div>
    </div> --}}


    <!-- IMPORTANT: Include Html5Qrcode Library BEFORE your script -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- Face API dan SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/lucide.min.js"></script>

    <!-- File JS eksternal absensi -->
    <script src="{{ asset('js/absensi.js') }}" defer></script>

    <script>
        function showUnderDevelopment() {
            Swal.fire({
                title: 'Fitur Dalam Pengembangan',
                text: 'Mohon maaf, fitur Scan QR saat ini sedang dalam tahap pengembangan.',
                icon: 'info',
                confirmButtonText: 'Oke, Mengerti',
                confirmButtonColor: '#2563eb', // Warna biru menyesuaikan tema tombol Anda
                showClass: {
                    popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown'
                }
            });
        }
        // ============================================
        // QR CODE SCANNER - TAILWIND VERSION (NO BOOTSTRAP)
        // ============================================
        // function closeAbsenManual() {
        //     const modal = document.getElementById("modalAbsenManual");
        //     modal.classList.add("hidden");
        //     modal.classList.remove("flex");

        //     if (stream) stream.getTracks().forEach((track) => track.stop());
        //     if (detectionInterval) clearInterval(detectionInterval);

        //     // reset state
        //     faceDetectedCount = 0;
        //     absensiProcessing = false;
        //     document.getElementById("instructionTextAbsen").textContent =
        //         "Posisikan wajah Anda di depan kamera...";
        // }

        // ============================================
        // CLOSE MODAL - VANILLA JS (NO JQUERY MODAL)
        // ============================================

        function closeAbsenManual() {
            if (isModalClosing) return;

            isModalClosing = true;
            const modal = document.getElementById("modalAbsenManual");
            const card = modal.querySelector(".modal-card");

            // Start fade out animation
            card.classList.remove("modal-fade-in");
            card.classList.add("modal-fade-out");

            // Cleanup resources
            setTimeout(() => {
                stopAllIntervals();
                stopStream();

                // HIDE MODAL - VANILLA JS ONLY!
                modal.classList.add("hidden");
                modal.classList.remove("flex");

                // Restore body scroll
                document.body.style.overflow = "auto";

                // Reset state
                resetModalState();

                // Reset flag
                isModalClosing = false;
            }, 250);
        }


        // Tambahkan sisa fungsi (sendAbsensiData, playBeepSound) dari kode Anda sebelumnya di sini...
        // Tangkap tombol
        const btnOvertime = document.getElementById('btnOvertime');

        btnOvertime.addEventListener('click', function(e) {
            e.preventDefault(); // cegah redirect / href

            Swal.fire({
                icon: 'info',
                title: 'Fitur Dalam Pengembangan',
                text: 'Fitur Lembur sedang dikembangkan, silakan coba nanti.',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'text-lg font-bold',
                    htmlContainer: 'text-sm'
                }
            });
        });

        // Menjaga session tetap aktif selama tab browser masih terbuka
        setInterval(function() {
            fetch('/keep-alive')
                .then(response => response.json())
                .then(data => console.log('Session refreshed'));
        }, 15 * 60 * 1000); // Setiap 15 menit


        $(document).ready(async function() {
            // Ambil status face_embedding dari Laravel
            const hasFace = @json(auth()->user()->face_embedding != null);

            initFaceEngine().then(() => {
                if (!hasFace) {
                    showModalRegistrasi();
                }
            });
        });

        // 🔥 DATA CABANG DARI SERVER
        const CABANG = {
            nama: "{{ $namaCabang }}",
            lat: {{ $cabangLat }},
            long: {{ $cabangLong }},
            radius: {{ $radiusMeter }}
        };

        // 🔥 DATA VALIDASI SENSEI
        window.HAS_UNABSENSED_SENSEI = @json($hasUnabsensedSensei ?? false);

        window.routes = {
            updateFace: "{{ route('user.update-face') }}",
            absensiStatus: "{{ url('/absensi/status') }}",
            absenMasuk: "{{ url('/absensi/masuk') }}",
            absenPulang: "{{ url('/absensi/pulang') }}"
        };

        // Sensei Functions
        function loadKelasAktif() {
            // Hide server-rendered content first
            const serverContent = document.getElementById('serverKelasSensei');
            const serverEmpty = document.getElementById('serverEmptyKelasSensei');
            if (serverContent) serverContent.classList.add('hidden');
            if (serverEmpty) serverEmpty.classList.add('hidden');

            // Add cache-busting timestamp
            const timestamp = new Date().getTime();
            fetch('/absensi/sensei/kelas-aktif?_=' + timestamp)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('kelasSenseiContainer');
                    
                    if (!data || data.length === 0) {
                        if (container) {
                            container.innerHTML = `<div class="text-center py-8 text-gray-400">
                                <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">Belum ada kelas aktif</p>
                                <p class="text-xs mt-1">Tambahkan kelas baru untuk mulai absen</p>
                            </div>`;
                            lucide.createIcons();
                        }
                        return;
                    }

                    let html = '';
                    data.forEach(kelas => {
                        const absensi = kelas.absensi && kelas.absensi[0] ? kelas.absensi[0] : null;
                        const sudahMasuk = absensi && absensi.jam_masuk;
                        const sudahPulang = absensi && absensi.jam_keluar;
                        const status = absensi ? absensi.status : '';
                        
                        let statusBadge = '';
                        let buttonHtml = '';

                        // Status badges sesuai dengan AbsensiController
                        const statusConfig = {
                            'HADIR': { bg: 'bg-green-100', text: 'text-green-700', label: 'HADIR' },
                            'TERLAMBAT': { bg: 'bg-red-100', text: 'text-red-700', label: 'TERLAMBAT' },
                            'PULANG LEBIH AWAL': { bg: 'bg-amber-100', text: 'text-amber-700', label: 'PULANG LEBIH AWAL' },
                            'TIDAK ABSEN PULANG': { bg: 'bg-red-100', text: 'text-red-700', label: 'TIDAK ABSEN PULANG' }
                        };

                        if (sudahMasuk && !sudahPulang) {
                            const cfg = statusConfig[status] || statusConfig['HADIR'];
                            statusBadge = `<span class="px-2 py-1 ${cfg.bg} ${cfg.text} text-[10px] font-bold rounded-full">${cfg.label}</span>`;
                            buttonHtml = `<button onclick="absenSenseiPulang(${kelas.id})" class="flex-1 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl text-sm font-bold active:scale-95 transition">Absen Pulang</button>`;
                        } else if (sudahMasuk && sudahPulang) {
                            const cfg = statusConfig[status] || { bg: 'bg-gray-100', text: 'text-gray-600', label: status };
                            statusBadge = `<span class="px-2 py-1 ${cfg.bg} ${cfg.text} text-[10px] font-bold rounded-full">${cfg.label}</span>`;
                            buttonHtml = `<div class="flex-1 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-sm font-semibold text-center">${absensi.jam_masuk} - ${absensi.jam_keluar}</div>`;
                        } else {
                            buttonHtml = `<button onclick="absenSenseiMasuk(${kelas.id})" class="flex-1 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl text-sm font-bold active:scale-95 transition">Absen Masuk</button>`;
                        }

                        const levelLabels = {
                            'pemula': 'Pemula',
                            'menengah': 'Menengah',
                            'mahir': 'Mahir',
                            'lanjutan': 'Lanjutan'
                        };

                        const tglMulai = new Date(kelas.tanggal_mulai).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
                        const tglSelesai = new Date(kelas.tanggal_selesai).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });

                        html += `
                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-violet-100" data-kelas-id="${kelas.id}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                            <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900">${kelas.nama_kelas}</h3>
                                            <p class="text-xs text-gray-500">${levelLabels[kelas.level] || kelas.level} - ${tglMulai} - ${tglSelesai}</p>
                                        </div>
                                    </div>
                                    ${statusBadge}
                                </div>
                                <div class="flex gap-2">
                                    ${buttonHtml}
                                </div>
                            </div>
                        `;
                    });

                    if (container) {
                        container.innerHTML = html;
                        lucide.createIcons();
                    }
                });
        }

        function absenSenseiMasuk(kelasId) {
            openModalAbsenSensei(kelasId, 'masuk');
        }

        function absenSenseiPulang(kelasId) {
            openModalAbsenSensei(kelasId, 'pulang');
        }

        // Load kelas aktif saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            loadKelasAktif();
        });

        function loadRiwayatRealtime() {
            $.ajax({
                url: "{{ route('absensi.riwayat.json') }}",
                method: "GET",
                cache: false, // penting biar gak ambil cache lama
                success: function(response) {
                    const dataKaryawan = response.karyawan || [];
                    const dataSensei = response.sensei || [];

                    let html = '';

                    if ((!dataKaryawan || dataKaryawan.length === 0) && (!dataSensei || dataSensei.length === 0)) {
                        html = `<div class="text-center text-gray-500 text-sm">
                            Belum ada riwayat absensi
                        </div>`;
                    } else {
                        // Karyawan
                        dataKaryawan.forEach(a => {
                            const date = new Date(a.tanggal);
                            const day = date.toLocaleDateString('id-ID', {
                                weekday: 'short'
                            });
                            const dayNumber = String(date.getDate()).padStart(2, '0');

                            const jamMasuk = a.jam_masuk ? a.jam_masuk : '-';
                            const jamKeluar = a.jam_keluar ? a.jam_keluar : '-';

                            html += `
                            <div class="bg-white rounded-2xl p-4 mb-2 shadow-sm flex items-center gap-4 border-l-4 border-blue-500 animate-fadeIn">
                                <a href="/absensi/riwayat"
                                   class="flex items-center gap-4 p-4 hover:bg-gray-50 transition w-full">

                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-xs text-blue-600 font-medium">${day}</div>
                                            <div class="text-xl font-bold text-blue-700">${dayNumber}</div>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">KARYAWAN</span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 mb-1">${a.status || '-'}</h3>
                                        <div class="flex gap-4 text-xs text-gray-500">
                                            <span>In: ${jamMasuk}</span>
                                            <span>Out: ${jamKeluar}</span>
                                        </div>
                                    </div>

                                    <i class="w-5 h-5 text-gray-400" data-lucide="chevron-right"></i>
                                </a>
                            </div>`;
                        });

                        // Sensei
                        dataSensei.forEach(a => {
                            const date = new Date(a.tanggal);
                            const day = date.toLocaleDateString('id-ID', {
                                weekday: 'short'
                            });
                            const dayNumber = String(date.getDate()).padStart(2, '0');

                            const jamMasuk = a.jam_masuk ? a.jam_masuk : '-';
                            const jamKeluar = a.jam_keluar ? a.jam_keluar : '-';
                            const kelasNama = a.kelas_sensei ? a.kelas_sensei.nama_kelas : '-';

                            html += `
                            <div class="bg-white rounded-2xl p-4 mb-2 shadow-sm flex items-center gap-4 border-l-4 border-violet-500 animate-fadeIn">
                                <a href="/absensi/riwayat"
                                   class="flex items-center gap-4 p-4 hover:bg-gray-50 transition w-full">

                                    <div class="w-16 h-16 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-xs text-violet-600 font-medium">${day}</div>
                                            <div class="text-xl font-bold text-violet-700">${dayNumber}</div>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold text-violet-600 bg-violet-100 px-2 py-0.5 rounded">SENSEI</span>
                                            <span class="text-xs text-gray-500">${kelasNama}</span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 mb-1">${a.status || '-'}</h3>
                                        <div class="flex gap-4 text-xs text-gray-500">
                                            <span>In: ${jamMasuk}</span>
                                            <span>Out: ${jamKeluar}</span>
                                        </div>
                                    </div>

                                    <i class="w-5 h-5 text-gray-400" data-lucide="chevron-right"></i>
                                </a>
                            </div>`;
                        });
                    }

                    $('#riwayatContainer').fadeOut(100, function() {
                        $(this).html(html).fadeIn(200);
                        if (window.lucide) lucide.createIcons();
                    });
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data dari Laravel Controller
            const showNotif = "{{ $showNotification }}";
            const message = "{{ $notifMessage }}";

            console.log("Status Notifikasi:", showNotif); // Cek di F12 (Console)

            if (showNotif == "1") {
                // 1. Minta Izin ke User
                if (Notification.permission === "default") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            playNotif(message);
                        }
                    });
                }
                // 2. Jika sudah diizinkan, langsung jalankan
                else if (Notification.permission === "granted") {
                    playNotif(message);
                }
                // 3. Jika diblokir
                else {
                    console.warn("Notifikasi diblokir oleh user.");
                }
            }
        });

        function playNotif(msg) {
            const options = {
                body: msg,
                icon: "https://cdn-icons-png.flaticon.com/512/1827/1827347.png", // Icon sementara
                vibrate: [200, 100, 200],
                requireInteraction: true
            };

            const n = new Notification("PENGINGAT ABSENSI", options);

            n.onclick = function() {
                window.focus();
                this.close();
            };
                }
            }
        });
    </script>

    <script>
        window.absenAgendaPulang = function(id) {
            fetch('/absensi/agenda/absen-pulang', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Berhasil', 'Absen pulang agenda berhasil', 'success').then(() => {
                        location.reload();
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal absen pulang', 'error');
            });
        };
    </script>

    <script>
        // Agenda functions - globally accessible
        function openModalAgenda() {
            const modal = document.getElementById('modalAgenda');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                startAgendaCamera();
            }
        }

        function closeModalAgenda() {
            const modal = document.getElementById('modalAgenda');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                stopAgendaCamera();
            }
        }

        function startAgendaCamera() {
            const video = document.getElementById('videoAgenda');
            if (video && !video.srcObject) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(s => {
                        window.streamAgenda = s;
                        video.srcObject = s;
                    })
                    .catch(err => {
                        console.error('Kamera error:', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Tidak dapat akses kamera', 'error');
                        }
                    });
            }
        }

        function stopAgendaCamera() {
            if (window.streamAgenda) {
                window.streamAgenda.getTracks().forEach(track => track.stop());
                window.streamAgenda = null;
            }
        }

        function captureAgendaPhoto() {
            const video = document.getElementById('videoAgenda');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            return canvas.toDataURL('image/jpeg', 0.8);
        }

        function handleAmbilFoto() {
            const photoData = captureAgendaPhoto();
            stopAgendaCamera();
            
            const photoPreview = document.getElementById('agendaPhotoPreview');
            const previewImg = document.getElementById('previewAgendaImg');
            const cameraPreview = document.getElementById('agendaCameraPreview');
            const btnSimpan = document.getElementById('btnSimpanAgenda');
            
            if (photoPreview && previewImg) {
                previewImg.src = photoData;
                photoPreview.classList.remove('hidden');
                cameraPreview.classList.add('hidden');
                
                // Hide Ambil Foto button, show Simpan button
                const btnAmbil = document.querySelector('button[onclick="startAgendaCamera()"]');
                if (btnAmbil) btnAmbil.classList.add('hidden');
                if (btnSimpan) btnSimpan.classList.remove('hidden');
                
                // Save to hidden input
                fetch(photoData)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], 'agenda_foto.jpg', { type: 'image/jpeg' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        const fotoInput = document.getElementById('agendaFotoInput');
                        if (fotoInput) fotoInput.files = dataTransfer.files;
                    });
            }
        }

        function retakeAgendaPhoto() {
            const photoPreview = document.getElementById('agendaPhotoPreview');
            const cameraPreview = document.getElementById('agendaCameraPreview');
            const btnAmbil = document.querySelector('button[onclick="startAgendaCamera()"]');
            const btnSimpan = document.getElementById('btnSimpanAgenda');
            
            if (photoPreview) photoPreview.classList.add('hidden');
            if (cameraPreview) cameraPreview.classList.remove('hidden');
            if (btnAmbil) btnAmbil.classList.remove('hidden');
            if (btnSimpan) btnSimpan.classList.add('hidden');
            
            startAgendaCamera();
        }

        // Override button onclick after page load
        document.addEventListener('DOMContentLoaded', function() {
            const btnAmbil = document.querySelector('button[onclick="startAgendaCamera()"]');
            if (btnAmbil) {
                btnAmbil.onclick = handleAmbilFoto;
            }
            
            // Agenda form submit handler
            const agendaForm = document.getElementById('agendaForm');
            if (agendaForm) {
                agendaForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('/absensi/agenda/store', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            closeModalAgenda();
                            Swal.fire('Berhasil', 'Agenda berhasil disimpan', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Peringatan', data.message, 'warning');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        Swal.fire('Error', 'Gagal menyimpan agenda', 'error');
                    });
                });
            }
        });
    </script>

    @include('absensi.modal_absen_sensei')

</body>

</html>
