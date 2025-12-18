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

    <!-- UPCOMING SHIFT CARD -->
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

        <div class="bg-gradient-to-br from-[#00c0ff] to-blue-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base">Shift Pagi - Regular</h3>
                        <p class="text-blue-100 text-sm">Senin - Jumat</p>
                    </div>
                </div>
                <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <i data-lucide="camera" class="w-5 h-5 text-blue-600"></i>
                </button>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-200"></i>
                    <span class="text-sm">Senin, 20 Jan</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-blue-200"></i>
                    <span class="text-sm">08:00 - 17:00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="px-5 pb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-gray-900">Quick Actions</h2>
            <button class="text-blue-600 text-sm font-semibold">See All</button>
        </div>

        <div class="grid grid-cols-5 gap-2">
            <!-- CHECK IN -->
            <button onclick="openCamera('masuk')"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check" class="w-5 h-5 text-green-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Masuk</span>
            </button>

            <!-- CHECK OUT -->
            <button onclick="openCamera('pulang')"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5 text-orange-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Pulang</span>
            </button>

            <!-- OVERTIME -->
            <button
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Overtime</span>
            </button>

            <!-- REQUEST -->
            <button
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Request</span>
            </button>

            <!-- ABSEN MANUAL -->
            <button onclick="openAbsenManual()"
                class="flex flex-col items-center gap-1 bg-white rounded-xl p-3 shadow-sm active:scale-95 transition border border-dashed border-blue-400">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="edit" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-blue-600">Manual</span>
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


    <!-- BOTTOM NAV -->
    @include('components.bottom_nav')
    {{-- modal absensi manual --}}
    @include('absensi.modal_manual')

    <!-- MODAL KAMERA -->
    <div id="modalKamera" class="fixed inset-0 bg-black z-50 hidden flex-col">
        <div class="bg-black/80 backdrop-blur p-4 flex items-center justify-between">
            <h2 class="text-white font-semibold text-lg" id="modalTitle">Absen Masuk</h2>
            <button onclick="stopCamera()"
                class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <i data-lucide="x" class="w-6 h-6 text-white"></i>
            </button>
        </div>

        <div class="flex-1 relative">
            <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-64 h-64 border-4 border-white/50 rounded-full"></div>
            </div>
        </div>

        <canvas id="canvas" class="hidden"></canvas>

        <div class="p-6 bg-black/80 backdrop-blur">
            <button onclick="capture()"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 rounded-2xl font-semibold text-lg shadow-lg active:scale-95 transition flex items-center justify-center gap-2">
                <i data-lucide="camera" class="w-6 h-6"></i>
                Ambil Foto & Absen
            </button>
        </div>
    </div>
    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        let absenType = null;
        let stream = null;
        let capturedPhoto = null;

        /* =========================
           STEP 1: KLIK ABSEN → BUKA KAMERA
        ========================== */
        function submitAbsen(type) {
            absenType = type;
            closeAbsenManual();
            openCamera();
        }

        function openCamera() {
            $('#modalCamera').removeClass('hidden').addClass('flex');

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user'
                }
            }).then(s => {
                stream = s;
                document.getElementById('video').srcObject = stream;
            }).catch(() => {
                Swal.fire('Error', 'Kamera tidak dapat diakses', 'error');
                closeCamera();
            });
        }

        function closeCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            $('#modalCamera').addClass('hidden').removeClass('flex');
        }

        /* =========================
           STEP 2: AMBIL FOTO
        ========================== */
        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            canvas.getContext('2d').drawImage(video, 0, 0);
            capturedPhoto = canvas.toDataURL('image/jpeg');

            closeCamera();
            submitAbsenWithPhoto();
        }

        /* =========================
           STEP 3: SUBMIT AJAX + FOTO
        ========================== */
        function submitAbsenWithPhoto() {

            const form = absenType === 'masuk' ?
                $('#formAbsenMasuk') :
                $('#formAbsenPulang');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    photo: capturedPhoto
                },

                beforeSend() {
                    Swal.fire({
                        title: 'Menyimpan absensi...',
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

                    loadRiwayatRealtime(); // realtime update
                },

                error(xhr) {
                    let message = xhr.responseJSON?.message ?? 'Terjadi kesalahan';

                    Swal.fire({
                        icon: xhr.status === 400 || xhr.status === 409 ? 'info' : 'error',
                        title: 'Gagal',
                        text: message
                    });
                }
            });
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

                            <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
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
        // Initialize Lucide Icons
        lucide.createIcons();



        document.addEventListener('DOMContentLoaded', loadRiwayat);

        // Open Camera
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
                        facingMode: "user"
                    },
                    audio: false
                });
                document.getElementById('video').srcObject = stream;
            } catch (err) {
                alert('Kamera tidak tersedia');
                stopCamera();
            }

            lucide.createIcons();
        }

        // Stop Camera
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            const modal = document.getElementById('modalKamera');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Capture
        function capture() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            const image = canvas.toDataURL('image/jpeg');
            console.log('Send to backend:', {
                jenis: jenisAbsensi,
                image
            });

            alert(`Absen ${jenisAbsensi.toUpperCase()} berhasil`);
            stopCamera();
        }

        // Initialize
        loadRiwayat();
    </script>
</body>

</html>
