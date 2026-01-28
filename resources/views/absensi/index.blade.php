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
                        ? asset('foto-karyawan/' . auth()->user()->foto_profil)
                        : asset('images/default-user.png') }}"
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
        <div class="bg-gray-100 rounded-xl px-4 py-3 flex items-center gap-3 mb-4">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Search"
                class="bg-transparent flex-1 outline-none text-sm text-gray-700" />
            <button class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </button>
        </div>
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

        @if (Auth::user()->shift)
            <div class="bg-gradient-to-br from-[#00c0ff] to-blue-700 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base">{{ Auth::user()->shift->nama_shift }}</h3>
                            <p class="text-blue-100 text-sm">Status: {{ Auth::user()->shift->status }}</p>
                        </div>
                    </div>
                    <button onclick="openAbsen()"
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
                            {{ \Carbon\Carbon::parse(Auth::user()->shift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse(Auth::user()->shift->jam_pulang)->format('H:i') }}
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

            <button onclick="location.href='/overtime'"
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

            {{-- <button onclick="openAbsen()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition border border-dashed border-blue-400">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-blue-600">Manual Testing </span>
            </button> --}}
        </div>
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
                    @forelse($shifts as $shift)
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
                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">{{ $shift->total_jam }}
                                    Jam</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-400 text-xs">Belum ada data shift yang tersedia.</p>
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
                    <div class="bg-white rounded-2xl p-4 mb-2 shadow-sm flex items-center gap-4">
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
                                <h3 class="font-semibold text-gray-900 mb-1">{{ $a->status }}</h3>
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
                        Belum ada riwayat absensi
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- modal camera --}}
    <div id="camera-modal" class="fixed inset-0 z-[999] bg-black hidden flex-col">
        <div class="relative flex-1 flex items-center justify-center overflow-hidden">
            <video id="video" class="absolute w-full h-full object-cover" autoplay muted playsinline></video>

            <div class="relative w-72 h-72 border-2 border-white/20 rounded-full flex items-center justify-center">
                <div id="scanner-ring" class="absolute inset-0 border-4 border-blue-500 rounded-full animate-pulse">
                </div>

                <div class="w-full h-1 bg-blue-500 absolute top-0 animate-scan shadow-[0_0_15px_rgba(59,130,246,0.8)]">
                </div>
            </div>

            <button onclick="closeCamera()"
                class="absolute top-10 right-6 bg-black/50 text-white p-2 rounded-full hover:bg-black">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="bg-white p-8 rounded-t-[2.5rem] text-center shadow-[0_-10px_25px_rgba(0,0,0,0.1)]">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>
            <h3 id="status-title" class="font-bold text-xl text-gray-800 mb-1">Siap Memindai</h3>
            <p id="status-desc" class="text-sm text-gray-500 mb-8 px-10">Dekatkan wajah ke kamera untuk absensi
                instan.</p>

            <button type="button" onclick="closeCamera()"
                class="w-full py-4 bg-gray-100 text-gray-600 rounded-2xl font-bold transition-all active:scale-95">
                Batalkan
            </button>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                top: 0;
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .animate-scan {
            animation: scan 2s linear infinite;
        }
    </style>

    <!-- BOTTOM NAV -->
    @include('components.bottom_Nav')
    {{-- modal absensi manual --}}
    @include('absensi.modal_manual')


    {{-- MODAL REGISTRASI WAJAH --}}
    {{-- MODAL REGISTRASI WAJAH --}}
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
    </div>

    <!-- Face API dan SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/lucide.min.js"></script>

    <!-- File JS eksternal absensi -->
    <script src="{{ asset('js/absensi.js') }}" defer></script>

    <script>
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


        window.routes = {
            updateFace: "{{ route('user.update-face') }}",
            absensiStatus: "{{ url('/absensi/status') }}",
            absenMasuk: "{{ url('/absensi/masuk') }}",
            absenPulang: "{{ url('/absensi/pulang') }}"
        };


        function loadRiwayatRealtime() {
            $.ajax({
                url: "{{ route('absensi.riwayat.json') }}",
                method: "GET",
                cache: false, // penting biar gak ambil cache lama
                success: function(data) {

                    let html = '';

                    if (!data || data.length === 0) {
                        html = `<div class="text-center text-gray-500 text-sm">
                            Belum ada riwayat absensi
                        </div>`;
                    } else {

                        data.forEach(a => {

                            const date = new Date(a.tanggal);
                            const day = date.toLocaleDateString('id-ID', {
                                weekday: 'short'
                            });
                            const dayNumber = String(date.getDate()).padStart(2, '0');

                            const jamMasuk = a.jam_masuk ? a.jam_masuk : '-';
                            const jamKeluar = a.jam_keluar ? a.jam_keluar : '-';

                            html += `
                    <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-4 animate-fadeIn">
                        <a href="/absensi/riwayat"
                           class="flex items-center gap-4 p-4 hover:bg-gray-50 transition w-full">

                            <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-xs text-blue-600 font-medium">${day}</div>
                                    <div class="text-xl font-bold text-blue-700">${dayNumber}</div>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">${a.status}</h3>
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
    </script>

    {{-- <script>
        function openAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script> --}}

    {{-- <script>
        const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';

        async function startCamera(videoElement) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                videoElement.srcObject = stream;
            } catch (err) {
                console.error("Gagal akses kamera:", err);
            }
        }


        async function detectFace(videoElement) {
            const canvas = faceapi.createCanvasFromMedia(videoElement);
            videoElement.parentElement.appendChild(canvas);
            const displaySize = {
                width: videoElement.clientWidth,
                height: videoElement.clientHeight
            };
            faceapi.matchDimensions(canvas, displaySize);

            const interval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(
                    videoElement,
                    new faceapi.TinyFaceDetectorOptions()
                ).withFaceLandmarks().withFaceDescriptors();

                // Tampilkan bounding box
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                faceapi.draw.drawDetections(canvas, resizedDetections);

                if (detections.length > 0) {
                    const faceEmbedding = detections[0].descriptor;

                    // Kirim otomatis ke backend
                    fetch('/absensi/masuk', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                face_embedding: JSON.stringify(faceEmbedding),
                                latitude: null, // nanti bisa pakai geolocation
                                longitude: null
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data);
                            if (data.absensi) {
                                alert("Absen berhasil: " + data.message);
                                videoElement.srcObject.getTracks().forEach(track => track.stop());
                                clearInterval(interval);
                            }
                        })
                        .catch(err => console.error(err));
                }
            }, 1000);
        }

        function openAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const video = document.getElementById('videoStream');
            startCamera(video);
        }

        function closeAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            const video = document.getElementById('videoStream');
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        }
    </script> --}}

</body>

</html>
