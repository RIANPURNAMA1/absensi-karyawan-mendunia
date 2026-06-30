<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png" style="width: 40px">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .safe-area-bottom { padding-bottom: env(safe-area-bottom); }
    </style>
</head>

<body class="bg-gray-50 pb-24">

    <div class="bg-white px-4 pt-3 pb-2">
        <div class="flex items-center justify-between text-xs text-gray-600">
            <span id="statusTime">--:--</span>
        </div>
    </div>

    <!-- HEADER -->
    <div class="px-5 pt-4 pb-2 flex items-center gap-3">
        <button id="btnBack" onclick="backToBatch()"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </button>
        <div>
            <h1 id="pageTitle" class="text-lg font-bold text-gray-900">Data Siswa</h1>
            <p id="pageSubtitle" class="text-xs text-gray-500">{{ $kelasList->count() }} kelas tersedia</p>
        </div>
    </div>

    <!-- KELAS LIST VIEW -->
    <div id="batchView" class="px-5 space-y-3">
        @forelse ($kelasList as $k)
        <button onclick="pilihBatch('{{ $k->batch_id }}', '{{ $k->nama_kelas }}', {{ $k->level }})"
            class="w-full bg-white rounded-2xl p-4 shadow-sm border border-gray-100 active:scale-95 transition text-left">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="book-open" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">{{ $k->nama_kelas }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $k->batchRelasi->nama_batch ?? '-' }} &middot; Level {{ $k->level }} &middot; {{ $k->user->name ?? $k->user->nama ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $k->tanggal_mulai->format('d/m') }} - {{ $k->tanggal_selesai->format('d/m') }} &middot; {{ $k->siswa_count }} siswa
                    </p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400"></i>
            </div>
        </button>
        @empty
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="layers" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada kelas</p>
        </div>
        @endforelse
    </div>

    <!-- SISWA ATTENDANCE VIEW (hidden by default) -->
    <div id="siswaView" class="hidden">
        <div class="px-5 pb-3 overflow-x-auto">
            <div class="flex gap-1 text-xs text-gray-500 mb-2">
                <span class="inline-flex items-center gap-1 mr-3"><span class="w-3 h-3 rounded bg-blue-500"></span> H = Hadir</span>
                <span class="inline-flex items-center gap-1 mr-3"><span class="w-3 h-3 rounded bg-amber-500"></span> I = Izin</span>
                <span class="inline-flex items-center gap-1 mr-3"><span class="w-3 h-3 rounded bg-red-500"></span> A = Alpa</span>
                <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500"></span> S = Sakit</span>
            </div>
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr>
                        <th class="text-left py-2 pr-3 sticky left-0 bg-gray-50 z-10 w-28 text-gray-700 font-semibold">Nama</th>
                        <th class="text-center py-2 px-2 text-gray-700 font-semibold w-10">Level</th>
                        @foreach ($days as $d)
                        <th class="text-center py-2 px-1 text-gray-700 font-semibold">
                            {{ \Carbon\Carbon::parse($d)->translatedFormat('D') }}<br>
                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($d)->format('d/m') }}</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="siswaTableBody">
                </tbody>
            </table>
        </div>
        <div id="siswaEmpty" class="px-5 text-center py-12 text-gray-400 hidden">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Tidak ada siswa di batch ini</p>
        </div>
    </div>

    <!-- DATA SISWA + ABSENSI JSON (hidden) -->
    <div id="siswaData" class="hidden">
        @foreach ($siswa as $s)
        <div class="siswa-item"
            data-batch="{{ $s->batch_id }}"
            data-nama="{{ $s->nama }}"
            data-foto="{{ $s->foto && file_exists(public_path('uploads/siswa/' . $s->foto)) ? asset('uploads/siswa/' . $s->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($s->nama) . '&background=00c0ff&color=fff&size=32' }}"
            data-level="{{ $s->level ?? '' }}"
            data-days="@foreach ($days as $d){{ ($absensiSiswa[$s->id.'_'.$d] ?? null) ? $absensiSiswa[$s->id.'_'.$d]->status : '-' }}@if(!$loop->last),@endif @endforeach">
        </div>
        @endforeach
    </div>

    @include('components.bottom_Nav')

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('statusTime').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateTime();
        setInterval(updateTime, 1000);
        lucide.createIcons();

        let isDrillDown = false;

        function pilihBatch(batchId, batchNama, kelasLevel) {
            isDrillDown = true;
            document.getElementById('batchView').classList.add('hidden');
            document.getElementById('siswaView').classList.remove('hidden');
            document.getElementById('pageTitle').textContent = batchNama;

            const items = document.querySelectorAll('.siswa-item');
            const tbody = document.getElementById('siswaTableBody');
            const empty = document.getElementById('siswaEmpty');
            let rows = '';
            let count = 0;

            items.forEach(item => {
                if (item.dataset.batch == batchId) {
                    count++;
                    const days = item.dataset.days.split(',');
                    let cells = '';
                    days.forEach(status => {
                        const s = status.trim();
                        const cell = getStatusCell(s);
                        cells += `<td class="text-center py-2 px-1">${cell}</td>`;
                    });
                    rows += `
                        <tr class="border-b border-gray-100">
                            <td class="py-2 pr-3 sticky left-0 bg-white">
                                <div class="flex items-center gap-2">
                                    <img src="${item.dataset.foto}" class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                                    <span class="text-xs font-medium text-gray-900 truncate">${item.dataset.nama}</span>
                                </div>
                            </td>
                            <td class="text-center py-2 px-2 text-xs text-gray-600">${kelasLevel}</td>
                            ${cells}
                        </tr>
                    `;
                }
            });

            document.getElementById('pageSubtitle').textContent = count + ' siswa';
            tbody.innerHTML = rows;

            if (count === 0) {
                empty.classList.remove('hidden');
            } else {
                empty.classList.add('hidden');
            }
        }

        function getStatusCell(status) {
            const map = {
                'HADIR': 'H',
                'TERLAMBAT': 'H',
                'IZIN': 'I',
                'SAKIT': 'S',
                'ALPA': 'A',
                'LIBUR': 'L',
            };
            const colorMap = {
                'HADIR': 'bg-blue-500 text-white',
                'TERLAMBAT': 'bg-orange-400 text-white',
                'IZIN': 'bg-amber-500 text-white',
                'SAKIT': 'bg-emerald-500 text-white',
                'ALPA': 'bg-red-500 text-white',
                'LIBUR': 'bg-gray-400 text-white',
            };
            const label = map[status] || '-';
            if (label === '-') return '<span class="text-gray-300">-</span>';
            const color = colorMap[status] || 'bg-gray-100 text-gray-500';
            return `<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold ${color}">${label}</span>`;
        }

        function backToBatch() {
            if (isDrillDown) {
                isDrillDown = false;
                document.getElementById('batchView').classList.remove('hidden');
                document.getElementById('siswaView').classList.add('hidden');
                document.getElementById('pageTitle').textContent = 'Data Siswa';
                document.getElementById('pageSubtitle').textContent = '{{ $kelasList->count() }} kelas tersedia';
            } else {
                window.location.href = '/absensi';
            }
        }
        function toggleModalJadwal(v) {}
        function mulaiAbsenFoto() { window.location.href = '/absensi'; }
    </script>
</body>
</html>
