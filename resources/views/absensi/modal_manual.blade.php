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
                <button onclick="submitAbsen('masuk')"
                    class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition">
                    Absen Masuk
                </button>

                <button onclick="submitAbsen('pulang')"
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
    <div id="modalKamera" class="fixed inset-0 bg-black z-50 hidden flex-col">
        <div class="bg-black/80 backdrop-blur p-4 flex items-center justify-between">
            <h2 class="text-white font-semibold text-lg" id="modalTitle">Absen Masuk</h2>
            <button onclick="stopCamera()" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                ✕
            </button>
        </div>

        <div class="flex-1 relative">
            <video id="video" autoplay playsinline muted class="w-full h-full object-cover bg-black"></video>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-64 h-64 border-4 border-white/50 rounded-full"></div>
            </div>
        </div>

        <canvas id="canvas" class="hidden"></canvas>

        <div class="p-6 bg-black/80 backdrop-blur">
            <button onclick="capture()"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 rounded-2xl font-semibold text-lg">
                Ambil Foto & Absen
            </button>
        </div>
    </div>


    {{-- 
    <script>
        let stream = null;
        let absenType = null;
        let capturedImage = null;

        function submitAbsen(type) {
            absenType = type;
            closeAbsenManual();
            openCamera(type);
        }

        async function openCamera(type) {
            document.getElementById('modalTitle').innerText =
                type === 'masuk' ? 'Absen Masuk' : 'Absen Pulang';

            const modal = document.getElementById('modalKamera');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
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
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            document.getElementById('modalKamera').classList.add('hidden');
        }

        function capture() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            capturedImage = canvas.toDataURL('image/jpeg', 0.9);

            stopCamera();
            submitAbsenWithPhoto();
        }

        function submitAbsenWithPhoto() {
            const url = absenType === 'masuk' ?
                "{{ route('absen.masuk') }}" :
                "{{ route('absen.pulang') }}";

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: document
                        .querySelector('meta[name="csrf-token"]').content,
                    photo: capturedImage
                },
                beforeSend() {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error(xhr) {
                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message ?? 'Terjadi kesalahan',
                        'error'
                    );
                }
            });
        }
    </script> --}}




</body>

</html>
