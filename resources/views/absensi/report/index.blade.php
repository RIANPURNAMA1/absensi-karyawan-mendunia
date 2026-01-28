<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran</title>
    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .safe-area-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .card-stats { transition: transform 0.2s ease; }
        .card-stats:active { transform: scale(0.95); }
    </style>
</head>

<body class="bg-gray-50 pb-24">
 <!-- STATUS BAR -->
    <div class="bg-white px-4 pt-3 pb-2">
        <div class="flex items-center justify-between text-xs text-gray-600">
            <span id="statusTime">--:--</span>
            <div class="flex gap-1">
                <div class="w-4 h-3 border border-gray-400 rounded-sm relative">
                    <div class="absolute inset-0.5 bg-gray-800 rounded-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();

            let hours = now.getHours().toString().padStart(2, '0');
            let minutes = now.getMinutes().toString().padStart(2, '0');

            document.getElementById("statusTime").textContent = `${hours}:${minutes}`;
        }

        // Jalankan pertama kali
        updateTime();

        // Update tiap 1 detik
        setInterval(updateTime, 1000);
    </script>

    <div class="bg-white px-5 pt-4 pb-6 shadow-sm border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <button onclick="window.location='/absensi'" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center">
                <i data-lucide="chevron-left" class="w-6 h-6 text-gray-700"></i>
            </button>
            <h1 class="text-lg font-bold text-gray-800">Laporan Bulanan</h1>
            <div class="w-10"></div> </div>
        <p class="text-center text-gray-400 text-xs font-bold uppercase tracking-widest mt-2" id="currentMonthYear">Januari 2026</p>
    </div>

    <div class="px-5 mt-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm card-stats">
                <div class="w-10 h-10 bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Total Hadir</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">22 <span class="text-xs font-medium text-gray-400">Hari</span></h3>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm card-stats">
                <div class="w-10 h-10 bg-amber-100 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Terlambat</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">3 <span class="text-xs font-medium text-gray-400">Kali</span></h3>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm card-stats">
                <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="file-text" class="w-5 h-5 text-slate-600"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Izin / Sakit</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">1 <span class="text-xs font-medium text-gray-400">Hari</span></h3>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm card-stats">
                <div class="w-10 h-10 bg-rose-50 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="user-x" class="w-5 h-5 text-rose-500"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Tanpa Kabar</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1 text-rose-600">0</h3>
            </div>
        </div>
    </div>

    <div class="px-5 mt-6">
        <div class="bg-blue-600 rounded-3xl p-6 shadow-lg shadow-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-white font-bold text-sm italic">Skor Disiplin</h4>
                <span class="bg-white/20 text-white text-[10px] px-2 py-1 rounded-lg">98% Sangat Baik</span>
            </div>
            <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                <div class="bg-white w-[98%] h-full rounded-full"></div>
            </div>
            <p class="text-slate-300 text-[10px] mt-4 leading-relaxed">
                Anda memiliki tingkat kehadiran yang sangat baik bulan ini. Pertahankan ketepatan waktu Anda.
            </p>
        </div>
    </div>

    <div class="px-5 mt-8 mb-10">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-gray-800">Aktivitas Terakhir</h4>
            <a href="/calendar" class="text-xs font-bold text-slate-500 uppercase">Lihat Kalender</a>
        </div>
        
        <div class="space-y-3">
            <div class="bg-white p-4 rounded-2xl border border-gray-50 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xs">
                        28
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Hadir Tepat Waktu</p>
                        <p class="text-[10px] text-gray-400 font-medium">Jan 2026 • 07:55 WIB</p>
                    </div>
                </div>
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-50 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold text-xs">
                        27
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Terlambat</p>
                        <p class="text-[10px] text-gray-400 font-medium">Jan 2026 • 08:15 WIB</p>
                    </div>
                </div>
                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-500"></i>
            </div>
        </div>
    </div>

   @include('components.bottom_Nav')

    <script>
        lucide.createIcons();
        
        // Update Time
        function updateTime() {
            const now = new Date();
            document.getElementById("statusTime").textContent = 
                now.getHours().toString().padStart(2, '0') + ":" + 
                now.getMinutes().toString().padStart(2, '0');
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Update Month Label
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const d = new Date();
        document.getElementById("currentMonthYear").innerText = monthNames[d.getMonth()] + " " + d.getFullYear();
    </script>
</body>
</html>