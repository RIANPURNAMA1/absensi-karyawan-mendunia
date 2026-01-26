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

    <div id="loaderFace">
        <div class="relative">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-3 mx-auto"></div>
            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-ping"></div>
            </div>
        </div>
        <p class="text-sm text-gray-700 font-medium mb-1">Memuat AI Engine...</p>
        <p class="text-xs text-gray-400">Tunggu 2-5 detik</p>

        <!-- Progress Bar -->
        <div class="w-48 h-1 bg-gray-200 rounded-full mt-3 overflow-hidden">
            <div class="h-full bg-blue-600 animate-pulse" style="width: 60%"></div>
        </div>
    </div>


    <!-- BOTTOM NAV -->
    @include('components.bottom_nav')
    {{-- modal absensi manual --}}
    @include('absensi.modal_manual')


    {{-- MODAL VERIFIKASI WAJAH --}}
    {{-- MODAL REGISTRASI WAJAH (Otomatis muncul jika face_embedding kosong) --}}
    <div id="modalRegistrasiWajah" class="hidden fixed inset-0 bg-black/80 items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-3xl p-6 relative w-full max-w-sm flex flex-col items-center shadow-2xl">

            <div id="loaderFace">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-2"></div>
                <p class="text-xs text-gray-500">Memuat AI Engine...</p>
            </div>

            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="scan-face" class="w-8 h-8 text-blue-600"></i>
            </div>

            <h3 class="font-bold text-xl text-gray-900 mb-1">Daftarkan Wajah</h3>
            <p class="text-gray-500 text-sm text-center mb-5">Wajah Anda diperlukan sebagai kunci akses absensi
                digital.</p>

            <div
                class="relative w-full aspect-video bg-gray-200 rounded-2xl overflow-hidden border-2 border-blue-100 shadow-inner">
                <video id="videoReg" autoplay muted playsinline class="w-full h-full object-cover shadow-lg"></video>
                <canvas id="canvasReg" class="absolute inset-0 w-full h-full"></canvas>
            </div>

            <div class="mt-6 w-full flex flex-col gap-2">
                <button id="btnCaptureWajah" onclick="prosesRegistrasiWajah()" disabled
                    class="w-full py-3 bg-gray-400 text-white rounded-xl font-bold transition shadow-lg shadow-blue-200">
                    Ambil Data Wajah
                </button>
                <p class="text-[10px] text-center text-gray-400 italic">*Pastikan pencahayaan cukup dan wajah terlihat
                    jelas</p>
            </div>
        </div>
    </div>



    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let streamReg = null;
        let isEngineReady = false;
        let detectionIntervalReg = null;

        // MENGGUNAKAN TINY FACE DETECTOR - PALING RINGAN & CEPAT
        const MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';

        $(document).ready(async function() {
            // Tunggu Face-API.js dimuat dari CDN
            if (typeof faceapi === 'undefined') {
                console.log('⏳ Menunggu Face-API.js dimuat...');
                await waitForFaceAPI();
            }

            // Cek status registrasi wajah dari Laravel
            const hasFace = @json(auth()->user()->face_embedding != null);

            // PENTING: Muat engine segera setelah halaman siap
            initFaceEngine().then(() => {
                if (!hasFace) {
                    showModalRegistrasi();
                }
            });
        });

        // Fungsi untuk menunggu Face-API.js dimuat
        function waitForFaceAPI() {
            return new Promise((resolve) => {
                const checkInterval = setInterval(() => {
                    if (typeof faceapi !== 'undefined') {
                        clearInterval(checkInterval);
                        console.log('✅ Face-API.js berhasil dimuat');
                        resolve();
                    }
                }, 100);

                // Timeout setelah 10 detik
                setTimeout(() => {
                    clearInterval(checkInterval);
                    if (typeof faceapi === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Library Error',
                            text: 'Face-API.js tidak dapat dimuat. Refresh halaman.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }, 10000);
            });
        }

        async function initFaceEngine() {
            try {
                const loader = document.getElementById('loaderFace');

                // Update UI: Memulai loading
                if (loader) {
                    loader.classList.remove('hidden');
                    loader.innerHTML = `
                    <div class="relative mb-3">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    </div>
                    <p class="text-sm text-gray-700 font-medium mb-1">Memuat AI Engine...</p>
                    <p class="text-xs text-gray-400">Loading model (1.5MB)</p>
                    <div class="w-48 h-1 bg-gray-200 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-blue-600 animate-pulse" style="width: 30%; transition: width 2s;"></div>
                    </div>
                `;
                }

                console.log("🚀 Memuat AI Engine (Tiny Model - Super Cepat)...");

                const loadStart = Date.now();

                // HANYA LOAD MODEL YANG DIPERLUKAN (TINY = PALING RINGAN)
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL), // ~400KB
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL), // ~80KB  
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL) // ~1MB
                ]);

                const loadTime = ((Date.now() - loadStart) / 1000).toFixed(2);
                console.log(`✅ AI Engine Ready! (${loadTime} detik)`);

                isEngineReady = true;

                // Update UI: Berhasil dimuat
                if (loader) {
                    loader.innerHTML = `
                    <div class="mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-green-600 font-medium">AI Engine Ready!</p>
                    <p class="text-xs text-gray-400">Loaded in ${loadTime}s</p>
                `;

                    // Sembunyikan loader setelah 500ms
                    setTimeout(() => {
                        loader.classList.add('hidden');
                    }, 500);
                }

            } catch (err) {
                console.error("❌ Error loading models:", err);

                isEngineReady = false;

                // Tampilkan error yang lebih informatif
                const loader = document.getElementById('loaderFace');
                if (loader) {
                    loader.innerHTML = `
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-red-600 font-medium mb-1">Gagal Memuat AI</p>
                        <p class="text-xs text-gray-500 mb-3">Cek koneksi internet Anda</p>
                        <button onclick="location.reload()" 
                                class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                            Coba Lagi
                        </button>
                    </div>
                `;
                }
            }
        }

        async function showModalRegistrasi() {
            const modal = document.getElementById('modalRegistrasiWajah');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const loader = document.getElementById('loaderFace');

            // Jika engine sudah siap di background, langsung buka kamera
            if (isEngineReady) {
                if (loader) loader.classList.add('hidden');
                await startCameraReg();
            } else {
                // Tampilkan loader sambil menunggu engine ready
                if (loader) {
                    loader.classList.remove('hidden');
                    loader.innerHTML = `
                    <div class="relative">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-3 mx-auto"></div>
                    </div>
                    <p class="text-sm text-gray-700 font-medium mb-1">Memuat AI Engine...</p>
                    <p class="text-xs text-gray-400">Mohon tunggu sebentar</p>
                `;
                }

                // Tunggu sampai engine ready
                await waitForEngine();

                if (loader) loader.classList.add('hidden');
                await startCameraReg();
            }
        }

        // Fungsi untuk menunggu engine ready
        function waitForEngine() {
            return new Promise((resolve) => {
                const checkInterval = setInterval(() => {
                    if (isEngineReady) {
                        clearInterval(checkInterval);
                        resolve();
                    }
                }, 100);

                // Timeout 30 detik
                setTimeout(() => {
                    clearInterval(checkInterval);
                    resolve();
                }, 30000);
            });
        }

        async function startCameraReg() {
            try {
                streamReg = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 640
                        },
                        height: {
                            ideal: 480
                        }
                    }
                });

                const video = document.getElementById('videoReg');
                video.srcObject = streamReg;

                // Tunggu video siap, BARU start deteksi
                video.onloadedmetadata = () => {
                    video.play();

                    // Pastikan engine benar-benar ready sebelum deteksi
                    if (isEngineReady) {
                        console.log('🎥 Kamera aktif, memulai deteksi wajah...');
                        startRealtimeDetectionReg();
                    } else {
                        console.log('⏳ Menunggu AI Engine...');
                        // Tunggu engine ready baru start deteksi
                        const waitDetection = setInterval(() => {
                            if (isEngineReady) {
                                clearInterval(waitDetection);
                                console.log('🎥 AI Ready, memulai deteksi...');
                                startRealtimeDetectionReg();
                            }
                        }, 100);
                    }
                };

                // Enable tombol capture
                const btn = document.getElementById('btnCaptureWajah');
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('bg-gray-400');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }

            } catch (err) {
                console.error("Kamera error:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Error',
                    text: 'Mohon izinkan akses kamera di browser Anda.',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // DETEKSI WAJAH REALTIME - MENAMPILKAN KOTAK DI WAJAH
        async function startRealtimeDetectionReg() {
            const video = document.getElementById('videoReg');
            const canvas = document.getElementById('canvasReg');

            if (!video || !canvas) return;

            const displaySize = {
                width: video.videoWidth || 640,
                height: video.videoHeight || 480
            };

            faceapi.matchDimensions(canvas, displaySize);

            // OPTIMASI: Deteksi lebih jarang untuk performa lebih baik
            detectionIntervalReg = setInterval(async () => {
                if (!video.videoWidth) return;

                // MENGGUNAKAN TINY LANDMARK - LEBIH CEPAT
                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 224, // Lebih kecil = lebih cepat (default 416)
                        scoreThreshold: 0.5 // Threshold deteksi
                    }))
                    .withFaceLandmarks(true); // true = tiny landmarks (lebih cepat)

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detection) {
                    const resizedDetection = faceapi.resizeResults(detection, displaySize);

                    // Gambar kotak wajah dengan warna hijau
                    const box = resizedDetection.detection.box;
                    ctx.strokeStyle = '#10b981';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);

                    // Gambar landmark (titik-titik wajah)
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetection);

                    // Tampilkan confidence score
                    const confidence = Math.round(detection.detection.score * 100);
                    ctx.fillStyle = '#10b981';
                    ctx.font = 'bold 16px Arial';
                    ctx.fillText(`${confidence}%`, box.x, box.y - 10);
                }

            }, 200); // Update setiap 200ms (lebih jarang = lebih cepat)
        }

        async function prosesRegistrasiWajah() {
            if (!isEngineReady) {
                Swal.fire('Tunggu', 'AI Engine belum siap. Tunggu beberapa saat...', 'warning');
                return;
            }

            const video = document.getElementById('videoReg');

            Swal.fire({
                title: 'Menganalisa Wajah...',
                html: '<div class="animate-pulse">Sedang memproses data wajah Anda</div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            // Deteksi menggunakan opsi Tiny (Super Cepat) dengan optimasi
            const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 224, // Ukuran input kecil = lebih cepat
                        scoreThreshold: 0.5 // Threshold deteksi
                    })
                ).withFaceLandmarks(true) // true = tiny landmarks (lebih cepat)
                .withFaceDescriptor();

            if (!detection) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajah Tidak Terdeteksi',
                    html: `
                    <p class="text-gray-600 mb-2">Pastikan:</p>
                    <ul class="text-sm text-gray-500 text-left">
                        <li>✓ Wajah menghadap kamera</li>
                        <li>✓ Pencahayaan cukup terang</li>
                        <li>✓ Tidak ada penghalang (masker, kacamata hitam)</li>
                    </ul>
                `,
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            // Cek confidence score
            const confidence = Math.round(detection.detection.score * 100);
            if (confidence < 70) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kualitas Deteksi Rendah',
                    text: `Akurasi: ${confidence}%. Coba posisi yang lebih baik (minimal 70%).`,
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            // Konversi descriptor ke array
            const faceDescriptor = Array.from(detection.descriptor);

            // Kirim ke server
            $.ajax({
                url: "{{ route('user.update-face') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    face_embedding: JSON.stringify(faceDescriptor)
                },
                success(res) {
                    // Stop detection interval
                    if (detectionIntervalReg) {
                        clearInterval(detectionIntervalReg);
                        detectionIntervalReg = null;
                    }

                    // Stop camera
                    if (streamReg) {
                        streamReg.getTracks().forEach(t => t.stop());
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        html: `
                        <p class="text-gray-600 mb-2">Wajah Anda telah terdaftar di sistem</p>
                        <p class="text-sm text-gray-400">Akurasi: ${confidence}%</p>
                    `,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        location.reload();
                    });
                },
                error(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Gagal menyimpan data ke database.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: errorMsg,
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        }

        // Cleanup saat halaman ditutup
        window.addEventListener('beforeunload', () => {
            if (detectionIntervalReg) clearInterval(detectionIntervalReg);
            if (streamReg) streamReg.getTracks().forEach(t => t.stop());
        });
    </script>
    <script>
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
    </script>




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
        let stream = null;
        let jenisAbsensi = null;

        // Init icon
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', loadRiwayat);

        // ===============================
        // OPEN CAMERA
        // ===============================
        async function openCamera(type) {
            jenisAbsensi = type;

            document.getElementById('modalTitle').textContent =
                `Absen ${type === 'masuk' ? 'Masuk' : 'Pulang'}`;

            const modal = document.getElementById('modalKamera');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    },
                    audio: false
                });

                const video = document.getElementById('video');
                video.srcObject = stream;
                video.play();

            } catch (err) {
                Swal.fire('Error', 'Kamera tidak dapat diakses', 'error');
                stopCamera();
            }

            lucide.createIcons();
        }

        // ===============================
        // STOP CAMERA
        // ===============================
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }

            const modal = document.getElementById('modalKamera');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ===============================
        // CAPTURE & AJAX SUBMIT
        // ===============================
        function capture() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            const photoBase64 = canvas.toDataURL('image/jpeg', 0.9);

            if (!photoBase64) {
                Swal.fire('Error', 'Foto gagal diambil', 'error');
                return;
            }

            stopCamera();
            submitAbsen(photoBase64);
        }

        // ===============================
        // AJAX ABSEN
        // ===============================
        // function submitAbsen(photo) {

        //     const url = jenisAbsensi === 'masuk' ?
        //         "{{ route('absen.masuk') }}" :
        //         "{{ route('absen.pulang') }}";

        //     $.ajax({
        //         url: url,
        //         method: 'POST',
        //         data: {
        //             _token: document.querySelector('meta[name="csrf-token"]').content,
        //             photo: photo
        //         },
        //         beforeSend() {
        //             Swal.fire({
        //                 title: 'Menyimpan...',
        //                 allowOutsideClick: false,
        //                 didOpen: () => Swal.showLoading()
        //             });
        //         },
        //         success(res) {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Berhasil',
        //                 text: res.message,
        //                 timer: 1500,
        //                 showConfirmButton: false
        //             });

        //             loadRiwayat();
        //         },
        //         error(xhr) {
        //             Swal.fire(
        //                 'Gagal',
        //                 xhr.responseJSON?.message ?? 'Terjadi kesalahan',
        //                 'error'
        //             );
        //         }
        //     });
        // }
    </script>


    {{-- <script>
        // Fungsi untuk MENUTUP Modal
        function closeAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Menutup modal jika pengguna mengklik area hitam di luar modal (Overlay)
        window.onclick = function(event) {
            const modal = document.getElementById('modalAbsenManual');
            if (event.target == modal) {
                closeAbsenManual();
            }
        }

        function submitAbsen(type) {
            // 1. Alert awal agar user tahu proses sedang berjalan
            Swal.fire({
                title: 'Memverifikasi Lokasi...',
                text: 'Harap tunggu, kami sedang memastikan posisi Anda tepat di radius cabang.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 2. Cek fitur Geolocation di Browser/HP
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const long = position.coords.longitude;

                        // Kirim data koordinat ke server
                        sendToServer(type, lat, long);
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
                        enableHighAccuracy: true, // Akurasi tinggi (GPS)
                        timeout: 10000, // Maksimal 10 detik pencarian
                        maximumAge: 0 // Jangan gunakan cache lokasi lama
                    }
                );
            } else {
                Swal.fire('Error', 'Perangkat/Browser Anda tidak mendukung GPS', 'error');
            }
        }

        function sendToServer(type, lat, long) {
            // URL disesuaikan dengan route Laravel Anda
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
                    }).then(() => {
                        location.reload(); // Refresh halaman untuk update status
                    });
                },
                error(xhr) {
                    // Menangkap error radius dari controller (422/403)
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
    </script> --}}


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

</body>

</html>
