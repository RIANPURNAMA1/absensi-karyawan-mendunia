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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div id="modalAbsenManual"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-6 pb-4 text-center">
                <div
                    class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Presensi Lokasi</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Silakan lakukan absensi sesuai radius cabang yang ditentukan.
                </p>
            </div>

            <div class="p-6 pt-0 space-y-3">
                <button onclick="submitAbsen('masuk')"
                    class="w-full py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Absen Masuk Sekarang</span>
                </button>

                <button onclick="submitAbsen('pulang')"
                    class="w-full py-4 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-bold shadow-lg shadow-orange-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Absen Pulang</span>
                </button>

                <button onclick="closeAbsenManual()"
                    class="w-full py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold transition-colors">
                    Batal
                </button>

                <button onclick="window.location.href='/absensi'"
                    class="w-full py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold transition-colors">
                    Kembali ke Dashboard
                </button>
            </div>

            <div
                class="bg-slate-50 p-4 border-t border-slate-100 text-center text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                Mendunia Presence System v2.0
            </div>
        </div>
    </div>

</body>

</html>
