<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>

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
            <span id="statusTime">9:41</span>
            <div class="flex gap-1">
                <div class="w-4 h-3 border border-gray-400 rounded-sm relative">
                    <div class="absolute inset-0.5 bg-gray-800 rounded-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="bg-white px-5 pt-4 pb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <!-- PROFILE INFO (CLICKABLE) -->
            <a href="/absensi/profile" class="flex items-center gap-3 hover:opacity-80 transition">
                <!-- FOTO PROFIL (DINAMIS DARI BACKEND) -->
                <div class="w-10 h-10 rounded-full overflow-hidden border border-blue-500">
                    <img src="{{ auth()->user() && auth()->user()->foto_profil
                        ? asset('storage/foto-karyawan/' . auth()->user()->foto_profil)
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
                    <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md">
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

        <div class="grid grid-cols-5 gap-2">
            <button onclick="location.href='/izin'"
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

            <button onclick="location.href='/schedule'"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Jadwal</span>
            </button>

            <button onclick="openAbsenManual()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition border border-dashed border-blue-400">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-blue-600">Manual Testing </span>
            </button>
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
                    <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-4">
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

            <div class="relative w-72 h-72 border-2 border-white/30 rounded-full flex items-center justify-center">
                <div class="absolute inset-0 border-4 border-blue-500 rounded-full animate-pulse"></div>
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
            <h3 id="status-title" class="font-bold text-xl text-gray-800 mb-1">Mencocokkan Wajah...</h3>
            <p id="status-desc" class="text-sm text-gray-500 mb-8 px-10">Posisikan wajah Anda tepat di dalam lingkaran
                biru.</p>

            <button type="button" onclick="closeCamera()"
                class="w-full py-4 bg-gray-100 text-gray-600 rounded-2xl font-bold transition-all active:scale-95">
                Batalkan Absensi
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
    @include('components.bottom_nav')
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


    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
lucide.createIcons();

const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
let stream = null;
let isEngineReady = false;
let detectionInterval = null;

// Variabel untuk stabilitas wajah
let stabilityScore = 0;
const STABILITY_REQUIRED = 15; // ~1,5 detik wajah stabil
let lastX = 0;
let lastY = 0;
let isCapturing = false;

async function openAbsenManual() {
    const modal = document.getElementById('modalAbsenManual');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    await loadModels();
    startCamera();
}

function closeAbsenManual() {
    const modal = document.getElementById('modalAbsenManual');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    if (stream) stream.getTracks().forEach(track => track.stop());
    if (detectionInterval) clearInterval(detectionInterval);

    // reset state
    stabilityScore = 0;
    isCapturing = false;
    lastX = 0;
    lastY = 0;
    document.getElementById('instructionTextAbsen').textContent =
        'Posisikan wajah di tengah lingkaran dan diam sebentar...';
}

async function loadModels() {
    if (isEngineReady) return;
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
    ]);
    isEngineReady = true;
}

async function startCamera() {
    const video = document.getElementById('videoStream');
    const canvas = document.getElementById('canvasStream');

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user' }
        });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            startRealtimeDetection(video, canvas);
        };
    } catch (err) {
        console.error("Tidak bisa akses kamera:", err);
        Swal.fire('Kamera Error', 'Izinkan akses kamera untuk absensi.', 'error');
    }
}

function startRealtimeDetection(video, canvas) {
    const displaySize = { width: video.clientWidth, height: video.clientHeight };
    faceapi.matchDimensions(canvas, displaySize);

    detectionInterval = setInterval(async () => {
        if (!video.videoWidth || !isEngineReady || isCapturing) return;

        const detection = await faceapi.detectSingleFace(video,
            new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
        ).withFaceLandmarks().withFaceDescriptor();

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (detection) {
            const resized = faceapi.resizeResults(detection, displaySize);
            const box = resized.detection.box;

            // Logika stabilitas
            const movement = Math.abs(box.x - lastX) + Math.abs(box.y - lastY);
            stabilityScore = (movement < 7) ? stabilityScore + 1 : 0;

            lastX = box.x;
            lastY = box.y;

            // Feedback visual
            ctx.lineWidth = 4;
            if (stabilityScore > 5) {
                ctx.strokeStyle = '#3b82f6';
                document.getElementById('instructionTextAbsen').textContent =
                    'Tahan posisi, sedang memproses...';
            } else {
                ctx.strokeStyle = '#f87171';
                document.getElementById('instructionTextAbsen').textContent =
                    'Posisikan wajah dengan tenang...';
            }
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Jika stabil cukup lama -> kirim absensi
            if (stabilityScore >= STABILITY_REQUIRED) {
                isCapturing = true;
                clearInterval(detectionInterval);
                document.getElementById('instructionTextAbsen').textContent =
                    'Wajah Terdeteksi! Mengirim absensi...';

                // Ambil geolokasi sebelum kirim
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        prosesAbsensiWajah(resized.descriptor, pos.coords.latitude, pos.coords.longitude);
                    },
                    err => {
                        console.warn('Gagal mengambil lokasi, tetap lanjut tanpa koordinat');
                        prosesAbsensiWajah(resized.descriptor, null, null);
                    }
                );
            }
        } else {
            stabilityScore = 0;
            document.getElementById('instructionTextAbsen').textContent = 'Wajah tidak terlihat...';
        }
    }, 100);
}

function prosesAbsensiWajah(faceEmbedding, latitude, longitude) {
    $.ajax({
        url: '/absensi/status', // endpoint baru untuk cek status otomatis
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            face_embedding: JSON.stringify(Array.from(faceEmbedding)),
            latitude: latitude,
            longitude: longitude
        },
        success: function(res) {
            if (res.status === 'BELUM_MASUK') {
                // Absen masuk otomatis
                $.ajax({
                    url: '/absensi/masuk',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        face_embedding: JSON.stringify(Array.from(faceEmbedding)),
                        latitude: latitude,
                        longitude: longitude
                    },
                    success: function(r) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Absensi Masuk Berhasil',
                            text: r.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => closeAbsenManual());
                    }
                });
            } else if (res.status === 'SUDAH_MASUK') {
                // Absen pulang otomatis
                $.ajax({
                    url: '/absensi/pulang',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        face_embedding: JSON.stringify(Array.from(faceEmbedding)),
                        latitude: latitude,
                        longitude: longitude
                    },
                    success: function(r) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Absensi Pulang Berhasil',
                            text: r.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => closeAbsenManual());
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Absen',
                    text: 'Anda sudah melakukan absensi hari ini.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => closeAbsenManual());
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Absensi',
                text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
            }).then(() => {
                stabilityScore = 0;
                isCapturing = false;
                startCamera();
            });
        }
    });
}
</script>




    <script>
        let streamReg = null;
        let isEngineReady = false;
        let detectionIntervalReg = null;

        // Variabel untuk logika stabilitas
        let stabilityScore = 0;
        const STABILITY_REQUIRED = 15; // Butuh ~1.5 detik posisi diam
        let lastX = 0;
        let lastY = 0;
        let isCapturing = false;

        // CDN URL untuk model
        const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';

        $(document).ready(async function() {
            // Ambil status face_embedding dari Laravel
            const hasFace = @json(auth()->user()->face_embedding != null);

            initFaceEngine().then(() => {
                if (!hasFace) {
                    showModalRegistrasi();
                }
            });
        });

        async function initFaceEngine() {
            try {
                console.log("🚀 Memuat AI Engine...");
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);

                isEngineReady = true;
                $('#loaderFace').addClass('hidden');
                $('#mainContentReg').removeClass('hidden').addClass('flex');

                if (window.lucide) lucide.createIcons();
            } catch (err) {
                console.error("Gagal memuat model:", err);
                document.getElementById('loaderFace').innerHTML =
                    `<p class="text-red-500 text-sm">Gagal memuat AI. Periksa koneksi internet.</p>`;
            }
        }

        async function showModalRegistrasi() {
            $('#modalRegistrasiWajah').removeClass('hidden').addClass('flex');
            if (isEngineReady) await startCameraReg();
        }

        async function startCameraReg() {
            try {
                streamReg = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: 640,
                        height: 480
                    }
                });
                const video = document.getElementById('videoReg');
                video.srcObject = streamReg;
                video.onloadedmetadata = () => {
                    video.play();
                    startRealtimeDetectionReg();
                };
            } catch (err) {
                Swal.fire('Kamera Error', 'Mohon izinkan akses kamera untuk verifikasi wajah.', 'error');
            }
        }

        async function startRealtimeDetectionReg() {
            const video = document.getElementById('videoReg');
            const canvas = document.getElementById('canvasReg');
            if (!video || !canvas) return;

            const displaySize = {
                width: 640,
                height: 480
            };
            faceapi.matchDimensions(canvas, displaySize);

            detectionIntervalReg = setInterval(async () => {
                if (!video.videoWidth || !isEngineReady || isCapturing) return;

                const detection = await faceapi.detectSingleFace(video,
                        new faceapi.TinyFaceDetectorOptions({
                            inputSize: 224,
                            scoreThreshold: 0.5
                        }))
                    .withFaceLandmarks();

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detection) {
                    const resized = faceapi.resizeResults(detection, displaySize);
                    const box = resized.detection.box;

                    // Logika Stabilitas: Cek pergeseran wajah
                    const movement = Math.abs(box.x - lastX) + Math.abs(box.y - lastY);

                    if (movement < 7) {
                        stabilityScore++;
                    } else {
                        stabilityScore = 0;
                    }

                    lastX = box.x;
                    lastY = box.y;

                    // Feedback Visual
                    ctx.lineWidth = 4;
                    if (stabilityScore > 5) {
                        ctx.strokeStyle = '#3b82f6'; // Biru (Proses diam)
                        $('#instructionText').text('Tahan posisi, sedang memproses...').addClass(
                            'text-blue-600');
                    } else {
                        ctx.strokeStyle = '#f87171'; // Merah (Bergerak/Cari wajah)
                        $('#instructionText').text('Posisikan wajah dengan tenang...').removeClass(
                            'text-blue-600 text-green-600');
                    }
                    ctx.strokeRect(box.x, box.y, box.width, box.height);

                    // Jika sudah stabil cukup lama, lakukan capture
                    if (stabilityScore >= STABILITY_REQUIRED) {
                        isCapturing = true;
                        clearInterval(detectionIntervalReg);

                        $('#instructionText').text('Wajah Terdeteksi! Verifikasi...').addClass(
                            'text-green-600');
                        $('#btnCaptureWajah').removeClass('bg-gray-400').addClass('bg-green-600').text(
                            'Memproses...');

                        prosesRegistrasiWajah();
                    }
                } else {
                    stabilityScore = 0;
                    $('#instructionText').text('Wajah tidak terlihat...').removeClass('text-blue-600');
                }
            }, 100);
        }

        async function prosesRegistrasiWajah() {
            const video = document.getElementById('videoReg');

            // Ambil data wajah dengan akurasi tinggi
            const detection = await faceapi.detectSingleFace(video,
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416
                    }))
                .withFaceLandmarks().withFaceDescriptor();

            if (!detection) {
                isCapturing = false;
                stabilityScore = 0;
                $('#instructionText').text('Gagal mengambil data, ulangi posisi diam...').addClass('text-red-500');
                startRealtimeDetectionReg();
                return;
            }

            $.ajax({
                url: "{{ route('user.update-face') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    face_embedding: JSON.stringify(Array.from(detection.descriptor))
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verifikasi Berhasil',
                        text: 'Data wajah Anda sudah tersimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    isCapturing = false;
                    stabilityScore = 0;

                    let msg = 'Terjadi kesalahan sistem.';
                    if (xhr.status === 422) {
                        msg = xhr.responseJSON.message; // Pesan: "Wajah sudah terdaftar di akun lain"
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Terverifikasi',
                        text: msg,
                        confirmButtonText: 'Coba Lagi'
                    }).then(() => {
                        $('#instructionText').text('Posisikan wajah kembali...').removeClass(
                            'text-green-600');
                        startRealtimeDetectionReg();
                    });
                }
            });
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




    <script>
        function loadRiwayatRealtime() {
            $.get("{{ route('absensi.riwayat.json') }}", function(data) {

                let html = '';

                if (data.length === 0) {
                    html = `<div class="text-center text-gray-500 text-sm">
                            Belum ada riwayat absensi
                        </div>`;
                } else {
                    data.forEach(a => {
                        const date = new Date(a.tanggal);
                        const day = date.toLocaleDateString('id-ID', {
                            weekday: 'short'
                        });
                        const dayNumber = date.getDate();

                        html += `
                    <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-4">
                        <a href="/absensi/riwayat"
                           class="flex items-center gap-4 p-4 hover:bg-gray-50 transition w-full">

                            <div class="w-16 h-16 bg-gradient-to-br fromp-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-xs text-blue-600 font-medium">${day}</div>
                                    <div class="text-xl font-bold text-blue-700">${dayNumber}</div>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">${a.status}</h3>
                                <div class="flex gap-4 text-xs text-gray-500">
                                    <span>In: ${a.jam_masuk ?? '-'}</span>
                                    <span>Out: ${a.jam_keluar ?? '-'}</span>
                                </div>
                            </div>

                            <i class="w-5 h-5 text-gray-400" data-lucide="chevron-right"></i>
                        </a>
                    </div>`;
                    });
                }

                $('#riwayatContainer').html(html);
                lucide.createIcons();
            });
        }
    </script>

    <script>
        // 🔥 DATA CABANG DARI SERVER
        const CABANG = {
            nama: "{{ $namaCabang }}",
            lat: {{ $cabangLat }},
            long: {{ $cabangLong }},
            radius: {{ $radiusMeter }}
        };

        // Haversine formula: hitung jarak dalam meter
        function hitungJarak(lat1, lon1, lat2, lon2) {
            const R = 6371000; // radius bumi dalam meter
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) *
                Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) ** 2;

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Submit absensi
        function submitAbsen(type) {
            Swal.fire({
                title: 'Memverifikasi Lokasi...',
                text: 'Harap tunggu, kami sedang memastikan posisi Anda tepat di radius cabang.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            if (!navigator.geolocation) {
                Swal.fire('Error', 'Perangkat/Browser Anda tidak mendukung GPS', 'error');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLong = position.coords.longitude;

                    const jarak = hitungJarak(userLat, userLong, CABANG.lat, CABANG.long);
                    console.log("Jarak ke cabang:", jarak, "meter");

                    if (jarak > CABANG.radius) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Di Luar Area Cabang',
                            html: `Anda berada <b>${Math.round(jarak)} meter</b> dari cabang <b>${CABANG.nama}</b>.<br>Radius absensi hanya <b>${CABANG.radius} meter</b>.`,
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }

                    // Kirim ke server
                    sendToServer(type, userLat, userLong);
                },
                (error) => {
                    let msg = "Gagal mengambil lokasi.";
                    if (error.code == 1) msg =
                        "Izin lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser Anda.";
                    if (error.code == 2) msg = "Sinyal GPS tidak stabil.";
                    if (error.code == 3) msg = "Waktu pencarian lokasi habis.";

                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Gagal diakses',
                        text: msg,
                        confirmButtonColor: '#ef4444'
                    });
                }, {
                    enableHighAccuracy: false, // cukup akurat, lebih cepat
                    timeout: 5000, // maksimal 5 detik
                    maximumAge: 0
                }
            );
        }

        // AJAX ke server
        function sendToServer(type, lat, long) {
            const url = type === 'masuk' ? "{{ route('absen.masuk') }}" : "{{ route('absen.pulang') }}";

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    latitude: lat,
                    longitude: long
                },
                success(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Presensi Berhasil',
                        text: res.message,
                        confirmButtonColor: '#059669'
                    }).then(() => location.reload());
                },
                error(xhr) {
                    const errorMsg = xhr.responseJSON?.message ?? 'Terjadi kesalahan sistem';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Ditolak',
                        text: errorMsg,
                        confirmButtonColor: '#f97316'
                    });
                }
            });
        }
    </script>

    <script>
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
    </script>

</body>

</html>
