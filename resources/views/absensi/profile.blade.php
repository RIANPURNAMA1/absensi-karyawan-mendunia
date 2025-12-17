<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Karyawan</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
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
            <button onclick="goBack()" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-700"></i>
            </button>
            <h1 class="text-lg font-bold text-gray-900">Profil Saya</h1>
            <button class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                <i data-lucide="settings" class="w-5 h-5 text-gray-700"></i>
            </button>
        </div>
    </div>

    <!-- PROFILE CARD -->
    <div class="px-5 py-5">
        <!-- Profile Header -->
        <div class="bg-gradient-to-br from-[#00c0ff] to-blue-700 rounded-3xl p-6 text-white shadow-lg mb-5">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-white/30">
                    <img src="{{ auth()->user() && auth()->user()->foto_profil
                        ? asset('storage/foto-karyawan/' . auth()->user()->foto_profil)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=fff&color=2874c9&size=200' }}"
                        class="w-full h-full object-cover" alt="Profile">
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-bold mb-1">
                        {{ auth()->user()->name ?? auth()->user()->name }}
                    </h2>

                    <p class="text-blue-100 text-sm">
                        {{ auth()->user()->divisi->nama_divisi ?? 'Divisi belum diatur' }}
                    </p>

                    <div class="flex items-center gap-2 mt-2">
                        <div class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-medium">
                            {{ auth()->user()->status_kerja ?? 'Status belum diatur' }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-white/20">
                <div class="text-center">
                    <div class="text-2xl font-bold">12</div>
                    <div class="text-xs text-blue-100">Hari Hadir</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">1</div>
                    <div class="text-xs text-blue-100">Izin</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">0</div>
                    <div class="text-xs text-blue-100">Terlambat</div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Informasi Kontak</h3>

            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-medium text-gray-900">rianpurnama@email.com</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="phone" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">No. HP</p>
                        <p class="font-medium text-gray-900">0812-3456-7890</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="map-pin" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="font-medium text-gray-900">Bandung, Jawa Barat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Info -->
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Informasi Kepegawaian</h3>

            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="briefcase" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Divisi</p>
                        <p class="font-medium text-gray-900">Marketing</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5 text-teal-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Tanggal Bergabung</p>
                        <p class="font-medium text-gray-900">12 Januari 2024</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="id-card" class="w-5 h-5 text-pink-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">ID Karyawan</p>
                        <p class="font-medium text-gray-900">EMP-2024-001</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- alert --}}

        <!-- CUSTOM ALERT MODAL -->
        <div id="customAlert"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-5">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full transform transition-all scale-95"
                id="alertBox">
                <!-- Alert Icon -->
                <div class="pt-8 pb-4 flex justify-center">
                    <div id="alertIcon" class="w-20 h-20 rounded-full flex items-center justify-center">
                        <!-- Icon will be inserted here -->
                    </div>
                </div>

                <!-- Alert Content -->
                <div class="px-6 pb-6 text-center">
                    <h3 id="alertTitle" class="text-xl font-bold text-gray-900 mb-2"></h3>
                    <p id="alertText" class="text-gray-600 text-sm"></p>
                </div>

                <!-- Alert Buttons -->
                <div id="alertButtons" class="px-6 pb-6 space-y-3">
                    <!-- Buttons will be inserted here -->
                </div>
            </div>
        </div>

        <!-- LOADING OVERLAY -->
        <div id="loadingOverlay"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
            <div class="bg-white rounded-3xl p-8 flex flex-col items-center gap-4">
                <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-700 font-medium">Memproses...</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 mb-20">
            <button
                class="w-full bg-gradient-to-r from-[#00c0ff] to-blue-700 text-white py-4 rounded-2xl font-semibold text-base shadow-lg active:scale-95 transition flex items-center justify-center gap-2">
                <i data-lucide="edit" class="w-5 h-5"></i>
                Edit Profil
            </button>

            <button
                class="w-full bg-white text-gray-700 py-4 rounded-2xl font-semibold text-base shadow-sm border border-gray-200 active:scale-95 transition flex items-center justify-center gap-2">
                <i data-lucide="lock" class="w-5 h-5"></i>
                Ubah Password
            </button>

            <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="button" id="btnLogout"
                    class="w-full bg-white text-red-600 py-4 rounded-2xl font-semibold text-base shadow-sm border border-gray-200 active:scale-95 transition flex items-center justify-center gap-2">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Logout
                </button>
            </form>


        </div>
    </div>

    <!-- BOTTOM NAV -->
    @include('components.bottom_nav')

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Update status time
        function updateStatusTime() {
            const now = new Date();
            document.getElementById('statusTime').textContent =
                now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }
        setInterval(updateStatusTime, 1000);
        updateStatusTime();

        // Navigation functions
        function goBack() {
            window.history.back();
        }

        function goToHome() {
            window.location.href = '/';
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

        $('#btnLogout').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan keluar dari sistem',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#00c0ff', // MERAH (logout)
                cancelButtonColor: '#6b7280' // ABU-ABU

        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: $('#logoutForm').attr('action'),
                    type: 'POST',
                    data: $('#logoutForm').serialize(), // <-- CSRF ikut terkirim
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Logout berhasil',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.href =
                                "{{ route('login') }}";
                        }, 1200);
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Gagal',
                            xhr.responseJSON?.message || 'Logout gagal',
                            'error'
                        );
                    }
                });

            }
        });
        });

        });
    </script>

</body>

</html>
