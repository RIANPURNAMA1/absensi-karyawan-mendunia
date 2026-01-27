<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absensi Geofencing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        video {
            width: 100%;
            max-width: 320px;
            height: auto;
            border-radius: 0.75rem;
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div id="modalAbsenManual"
        class="hidden fixed inset-0 bg-black/80 items-center justify-center z-[60] p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 relative w-full max-w-sm flex flex-col items-center shadow-2xl">

            <!-- Ikon status -->
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="user-check" id="statusIconAbsen" class="w-8 h-8 text-blue-600"></i>
            </div>

            <!-- Judul & instruksi -->
            <h3 class="font-bold text-xl text-gray-900 mb-1 text-center">Presensi Wajah</h3>
            <p id="instructionTextAbsen" class="text-gray-500 text-sm text-center mb-5">
                Posisikan wajah di tengah lingkaran dan diam sebentar...
            </p>

            <!-- Video & Canvas deteksi -->
            <div
                class="relative w-full aspect-square max-w-[280px] bg-gray-900 rounded-full overflow-hidden border-4 border-blue-100 shadow-xl mx-auto">
                <video id="videoStream" autoplay muted playsinline
                    class="w-full h-full object-cover scale-x-[-1]"></video>
                <canvas id="canvasStream" class="absolute inset-0 w-full h-full"></canvas>

                <!-- Lingkaran indikator progres -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none -rotate-90">
                    <circle cx="140" cy="140" r="135" stroke="currentColor" stroke-width="8"
                        fill="transparent" class="text-blue-600/30" />
                    <circle id="progressCircleAbsen" cx="140" cy="140" r="135" stroke="currentColor"
                        stroke-width="8" fill="transparent" stroke-dasharray="848" stroke-dashoffset="848"
                        class="text-blue-600 transition-all duration-200 ease-linear" />
                </svg>
            </div>

            <!-- Timer & instruksi tambahan -->
            <div class="mt-6 w-full">
                <p id="timerTextAbsen" class="text-center font-bold text-blue-600 text-lg h-7"></p>
                <p class="text-[10px] text-center text-gray-400 italic">
                    Sistem akan mengambil foto otomatis saat posisi stabil
                </p>
            </div>

            <!-- Tombol batal & kembali -->
            <div class="mt-6 w-full space-y-3">
                <button onclick="closeAbsenManual()"
                    class="w-full py-3 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold transition-colors">
                    Batal
                </button>

                <button onclick="window.location.href='/absensi'"
                    class="w-full py-3 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold transition-colors">
                    Kembali ke Dashboard
                </button>
            </div>

        </div>
    </div>
    {{-- <script>
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
                    video: {
                        facingMode: 'user'
                    }
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
            const displaySize = {
                width: video.clientWidth,
                height: video.clientHeight
            };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                if (!video.videoWidth || !isEngineReady || isCapturing) return;

                const detection = await faceapi.detectSingleFace(video,
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 224,
                        scoreThreshold: 0.5
                    })
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
                                prosesAbsensiWajah(resized.descriptor, pos.coords.latitude, pos.coords
                                    .longitude);
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
    </script> --}}
</body>

</html>
