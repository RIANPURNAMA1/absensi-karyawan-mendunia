<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absen Manual</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CDN (DEV ONLY) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">


    <!-- FORM ABSEN (UNTUK CSRF) -->
    <form id="formAbsenMasuk" action="{{ route('absen.masuk') }}" method="POST">
        @csrf
    </form>

    <form id="formAbsenPulang" action="{{ route('absen.pulang') }}" method="POST">
        @csrf
    </form>

    <!-- MODAL ABSEN MANUAL -->
    <div id="modalAbsenManual" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

        <div class="bg-white w-[90%] max-w-sm rounded-2xl p-5">
            <h3 class="text-lg font-bold mb-1 text-gray-800">Absen Manual</h3>
            <p class="text-xs text-gray-500 mb-4">
                Absensi akan tercatat sesuai tanggal hari ini
            </p>

            <div class="space-y-3">
                <button onclick="openCamera('masuk')"
                    class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition">
                    Absen Masuk
                </button>

                <button onclick="openCamera('pulang')"
                    class="w-full py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold transition">
                    Absen Pulang
                </button>


                <button onclick="closeAbsenManual()"
                    class="w-full py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KAMERA -->
    <div id="modalCamera" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

        <div class="bg-white w-[90%] max-w-sm rounded-2xl p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Ambil Foto Absensi</h3>

            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-black">
                <video id="video" autoplay playsinline class="w-full rounded-lg"></video>
                <canvas id="canvas" class="hidden"></canvas>
            </div>

            <div class="mt-4 flex gap-2">
                <button onclick="capturePhoto()" class="flex-1 py-2 rounded-xl bg-blue-600 text-white font-semibold">
                    Ambil Foto
                </button>

                <button onclick="closeCamera()" class="flex-1 py-2 rounded-xl bg-gray-300 text-gray-700 font-medium">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let absenType = null;
        let capturedImage = null;

        function openCamera(type) {
            absenType = type;

            closeAbsenManual();

            const modal = document.getElementById('modalCamera');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(s => {
                    stream = s;
                    const video = document.getElementById('video');
                    video.srcObject = stream;
                    video.play(); // 🔥 PENTING
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Kamera tidak dapat diakses', 'error');
                    closeCamera();
                });
        }

        function closeCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            document.getElementById('modalCamera').classList.add('hidden');
        }

        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            canvas.getContext('2d').drawImage(video, 0, 0);

            capturedImage = canvas.toDataURL('image/jpeg');

            closeCamera();
            submitAbsenWithPhoto();
        }
    </script>




</body>

</html>
