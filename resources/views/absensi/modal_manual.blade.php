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


</body>

</html>
