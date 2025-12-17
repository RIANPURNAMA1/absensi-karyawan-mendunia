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
    <div id="modalAbsenManual"
        class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

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

    <!-- SCRIPT -->
    <script>
        function openAbsenManual() {
            const modal = document.getElementById('modalAbsenManual');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAbsenManual() {
            document.getElementById('modalAbsenManual').classList.add('hidden');
        }

        function submitAbsen(type) {

            const form = type === 'masuk'
                ? $('#formAbsenMasuk')
                : $('#formAbsenPulang');

            const confirmText = type === 'masuk'
                ? 'Yakin ingin absen masuk hari ini?'
                : 'Yakin ingin absen pulang hari ini?';

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Absen',
                cancelButtonText: 'Batal',
                confirmButtonColor: type === 'masuk' ? '#16a34a' : '#ea580c',
                cancelButtonColor: '#6b7280'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),

                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },

                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        closeAbsenManual();
                    },

                    error: function(xhr) {
                        let message = xhr.responseJSON?.message ?? 'Terjadi kesalahan';

                        if (xhr.status === 400 || xhr.status === 409) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Informasi',
                                text: message,
                                confirmButtonColor: '#3b82f6'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: message
                            });
                        }
                    }
                });
            });
        }
    </script>

</body>
</html>
