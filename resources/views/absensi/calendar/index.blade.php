<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>

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

    <div class="px-5 mt-4">
        <div class="bg-white rounded-3xl shadow-sm p-5 border border-gray-100">
            <div id="monthDisplay" class="text-lg font-bold text-gray-900 mb-4 text-center"></div>
            <div id="calendarGrid" class="grid grid-cols-7 gap-y-2 text-center">
            </div>
        </div>

        <div id="detailSection" class="mt-6 hidden">
            <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                <h3 id="txtTanggal" class="font-bold text-indigo-900 mb-3 text-sm italic"></h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-3 rounded-xl">
                        <p class="text-[10px] text-gray-400">Jam Masuk</p>
                        <p id="txtMasuk" class="text-sm font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-white p-3 rounded-xl">
                        <p class="text-[10px] text-gray-400">Jam Keluar</p>
                        <p id="txtKeluar" class="text-sm font-bold text-gray-800"></p>
                    </div>
                </div>
                <div id="lblStatus" class="mt-3 inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-50 flex flex-wrap justify-center gap-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span class="text-[10px] font-medium text-gray-500">Hadir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                    <span class="text-[10px] font-medium text-gray-500">Terlambat</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                    <span class="text-[10px] font-medium text-gray-500">Izin</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-[10px] font-medium text-gray-500">Alpa</span>
                </div>
            </div>
        </div>
    </div>
    @include('components.bottom_Nav')

<script>
    // 1. Definisikan route secara manual jika tidak menggunakan Laravel Blade global
    window.routes = {
        riwayatKalender: "/absensi/riwayat-kalender" // Sesuaikan dengan URL route Anda
    };

    let riwayatAbsensi = []; 

    async function renderCalendar() {
        lucide.createIcons();
        const grid = document.getElementById('calendarGrid');
        const monthDisplay = document.getElementById('monthDisplay');
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth(); // 0 - 11

        // Tampilkan Bulan & Tahun (Bahasa Indonesia)
        monthDisplay.innerText = now.toLocaleString('id-ID', {
            month: 'long',
            year: 'numeric'
        });

        try {
            // Ambil data dari Laravel
            const response = await fetch(`${window.routes.riwayatKalender}?bulan=${month + 1}&tahun=${year}`);
            riwayatAbsensi = await response.json();

            grid.innerHTML = '';

            // --- BAGIAN PENTING: LOGIKA KALENDER ---
            const firstDayOfMonth = new Date(year, month, 1).getDay(); // Mendapatkan hari (0-6)
            const daysInMonth = new Date(year, month + 1, 0).getDate(); // Jumlah hari dalam bulan ini

            // 1. Tambahkan slot kosong untuk hari sebelum tanggal 1
            for (let i = 0; i < firstDayOfMonth; i++) {
                grid.innerHTML += `<div></div>`;
            }

            // 2. Render Tanggal 1 sampai Selesai
            for (let d = 1; d <= daysInMonth; d++) {
                // Pencarian data yang lebih aman (handle format ISO T17:00 atau YYYY-MM-DD)
                const dataHariIni = riwayatAbsensi.find(item => {
                    const tglString = item.tanggal.includes('T') ? item.tanggal.split('T')[0] : item.tanggal;
                    return parseInt(tglString.split('-')[2]) === d;
                });

                let dotColor = 'bg-transparent';
                if (dataHariIni) {
                    if (dataHariIni.status === 'HADIR') dotColor = 'bg-green-500';
                    else if (dataHariIni.status === 'TERLAMBAT') dotColor = 'bg-orange-500';
                    else if (dataHariIni.status === 'IZIN') dotColor = 'bg-blue-400';
                }

                const isToday = new Date().getDate() === d && new Date().getMonth() === month;

                grid.innerHTML += `
                    <div onclick="tampilkanDetail(${d})" class="flex flex-col items-center py-2 cursor-pointer hover:bg-gray-100 rounded-2xl transition-all">
                        <span class="text-sm ${isToday ? 'bg-indigo-600 text-white w-7 h-7 flex items-center justify-center rounded-full font-bold' : 'text-gray-700 font-medium'}">
                            ${d}
                        </span>
                        <div class="w-1.5 h-1.5 rounded-full ${dotColor} mt-1"></div>
                    </div>
                `;
            }
        } catch (error) {
            console.error("Gagal memproses UI:", error);
            grid.innerHTML = `<div class="col-span-7 text-red-500 text-xs text-center">Gagal memuat data.</div>`;
        }
    }

    function tampilkanDetail(tgl) {
        // Logika pencarian data yang sama dengan di atas
        const data = riwayatAbsensi.find(item => {
            const tglString = item.tanggal.includes('T') ? item.tanggal.split('T')[0] : item.tanggal;
            return parseInt(tglString.split('-')[2]) === tgl;
        });

        const section = document.getElementById('detailSection');

        if (data) {
            section.classList.remove('hidden');
            section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            document.getElementById('txtTanggal').innerText = `Detail Kehadiran: Tanggal ${tgl}`;
            document.getElementById('txtMasuk').innerText = data.jam_masuk ?? '--:--';
            document.getElementById('txtKeluar').innerText = data.jam_keluar ?? '--:--';

            const lbl = document.getElementById('lblStatus');
            lbl.innerText = data.status;
            
            // Pewarnaan Badge Status
            const colors = {
                'HADIR': 'bg-green-100 text-green-600',
                'TERLAMBAT': 'bg-orange-100 text-orange-600',
                'IZIN': 'bg-blue-100 text-blue-600',
                'ALPA': 'bg-red-100 text-red-600'
            };
            lbl.className = `mt-3 inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase ${colors[data.status] || 'bg-gray-100'}`;
        } else {
            section.classList.add('hidden');
        }
    }

    function goBack() { window.history.back(); }

    document.addEventListener('DOMContentLoaded', renderCalendar);
</script>
</body>

</html>
