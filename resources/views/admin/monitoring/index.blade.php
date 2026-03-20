@extends('app')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        .label-monitor {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 3px 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            font-family: inherit;
            line-height: 1.5;
            white-space: nowrap;
        }
        .label-monitor::before { display: none; }
    </style>

    <div class="container-fluid">

        {{-- Header --}}
        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="m-b-10">
                            <i class="ph ph-map-pin me-1 text-blue-600"></i>
                            Monitoring Lokasi Absensi
                        </h4>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end">
                        <ul class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="ph ph-house"></i> Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Monitoring</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-sm mb-1">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai"
                               value="{{ $tglMulai }}"
                               class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-sm mb-1">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai"
                               value="{{ $tglSelesai }}"
                               class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-sm mb-1">Cabang</label>
                        <select name="cabang_id" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cab)
                                <option value="{{ $cab->id }}"
                                    {{ $cabangId == $cab->id ? 'selected' : '' }}>
                                    {{ $cab->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ph ph-magnifying-glass me-1"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-light w-100">
                            <i class="ph ph-arrow-counter-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary badge --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="badge bg-success px-3 py-2" style="font-size:12px">
                <i class="ph ph-circle me-1"></i> Masuk
            </span>
            <span class="badge bg-primary px-3 py-2" style="font-size:12px">
                <i class="ph ph-circle me-1"></i> Pulang
            </span>
            <span class="text-muted" style="font-size:13px">
                Total: <strong>{{ $absensis->count() }}</strong> titik lokasi
                | Periode: <strong>{{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }}</strong>
                s/d <strong>{{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}</strong>
            </span>
        </div>

        {{-- MAP --}}
        <div class="card shadow-sm mb-4 overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="ph ph-map-trifold me-1 text-blue-600"></i>
                    Sebaran Lokasi Absensi
                </h5>
                <span class="flex items-center gap-1 text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 d-inline-block me-1"
                          style="animation:pulse 1.8s infinite"></span>
                    Live
                </span>
            </div>
            <div id="map" style="height:520px;"></div>
        </div>

        {{-- TABEL --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">Detail Absensi</h5>
                <span class="badge bg-blue-700 text-white px-3 py-2">
                    {{ $absensis->count() }} data
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="monitorTable">
                        <thead>
                            <tr class="bg-blue-700 text-white">
                                <th class="px-4 py-3 text-white">Karyawan</th>
                                <th class="px-4 py-3 text-white">Tanggal</th>
                                <th class="px-4 py-3 text-white text-center">Jam Masuk</th>
                                <th class="px-4 py-3 text-white text-center">Jam Pulang</th>
                                <th class="px-4 py-3 text-white">Cabang</th>
                                <th class="px-4 py-3 text-white text-center">Status</th>
                                <th class="px-4 py-3 text-white text-center">Lokasi</th>
                                <th class="px-4 py-3 text-white text-center">Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($absensis as $a)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold">{{ $a->user->name }}</div>
                                        <small class="text-muted">{{ $a->user->nip ?? '' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($a->tanggal)->isoFormat('dddd') }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3 text-center font-monospace">
                                        {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-monospace">
                                        {{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="px-4 py-3">{{ $a->cabang->nama_cabang ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $sc = [
                                                'HADIR'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'TERLAMBAT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'IZIN'      => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'ALPA'      => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'LIBUR'     => 'bg-slate-50 text-slate-600 border-slate-200',
                                            ];
                                            $cls = $sc[$a->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold border {{ $cls }}">
                                            {{ $a->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($a->lat_masuk)
                                            <span class="badge bg-success mb-1 d-block">
                                                <i class="ph ph-map-pin me-1"></i>Masuk
                                            </span>
                                        @endif
                                        @if($a->lat_pulang)
                                            <span class="badge bg-primary d-block">
                                                <i class="ph ph-map-pin me-1"></i>Pulang
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($a->foto_masuk)
                                                <img src="{{ asset('storage/'.$a->foto_masuk) }}"
                                                     class="rounded shadow-sm"
                                                     style="width:40px;height:40px;object-fit:cover;cursor:pointer"
                                                     onclick="viewImg('{{ asset('storage/'.$a->foto_masuk) }}', 'Masuk')">
                                            @endif
                                            @if($a->foto_pulang)
                                                <img src="{{ asset('storage/'.$a->foto_pulang) }}"
                                                     class="rounded shadow-sm"
                                                     style="width:40px;height:40px;object-fit:cover;cursor:pointer"
                                                     onclick="viewImg('{{ asset('storage/'.$a->foto_pulang) }}', 'Pulang')">
                                            @endif
                                            @if(!$a->foto_masuk && !$a->foto_pulang)
                                                <span class="text-muted text-xs">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="ph ph-map-pin d-block fs-1 mb-2 text-gray-300"></i>
                                        Tidak ada data lokasi pada periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview Foto --}}
    <div class="modal fade" id="modalFoto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" id="modalFotoLabel">Foto Absensi</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="imgPreview" src="" class="w-100 rounded shadow-sm">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // ── Preview foto ───────────────────────────────────────────────
        function viewImg(url, tipe) {
            $('#imgPreview').attr('src', url);
            $('#modalFotoLabel').text('Foto Absensi ' + tipe);
            $('#modalFoto').modal('show');
        }

        // ── DataTable ──────────────────────────────────────────────────
        $(function () {
            $('#monitorTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[1, 'desc']],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: { first:"Awal", last:"Akhir", next:"›", previous:"‹" }
                }
            });
        });

        // ── Leaflet Map ────────────────────────────────────────────────
        var bounds = [];

        var masukIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34], shadowSize:[41,41]
        });
        var pulangIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34], shadowSize:[41,41]
        });

        var map = L.map('map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
        }).addTo(map);

        @foreach ($absensis as $a)
            @if ($a->lat_masuk && $a->long_masuk)
                (function(){
                    var ll = [{{ $a->lat_masuk }}, {{ $a->long_masuk }}];
                    bounds.push(ll);
                    L.marker(ll, { icon: masukIcon })
                        .addTo(map)
                        .bindTooltip(
                            '<b style="font-size:11px">{{ addslashes($a->user->name) }}</b><br>' +
                            '<span style="font-size:10px;color:#6b7280">🟢 Masuk · {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format("H:i") : "--" }} · {{ \Carbon\Carbon::parse($a->tanggal)->format("d M") }}</span>',
                            { permanent: true, direction:'top', offset:[0,-38], className:'label-monitor' }
                        )
                        .bindPopup(
                            '<div style="min-width:160px">' +
                            '<p style="font-weight:700;font-size:13px;margin-bottom:6px">{{ addslashes($a->user->name) }}</p>' +
                            '<p style="font-size:12px">🟢 <b>Absen Masuk</b></p>' +
                            '<p style="font-size:12px;color:#6b7280">⏰ {{ $a->jam_masuk ?? "--" }}</p>' +
                            '<p style="font-size:12px;color:#6b7280">📅 {{ \Carbon\Carbon::parse($a->tanggal)->format("d M Y") }}</p>' +
                            '<p style="font-size:12px;color:#6b7280">🏢 {{ addslashes($a->cabang->nama_cabang ?? "--") }}</p>' +
                            '</div>'
                        );
                })();
            @endif
            @if ($a->lat_pulang && $a->long_pulang)
                (function(){
                    var ll = [{{ $a->lat_pulang }}, {{ $a->long_pulang }}];
                    bounds.push(ll);
                    L.marker(ll, { icon: pulangIcon })
                        .addTo(map)
                        .bindTooltip(
                            '<b style="font-size:11px">{{ addslashes($a->user->name) }}</b><br>' +
                            '<span style="font-size:10px;color:#6b7280">🔵 Pulang · {{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format("H:i") : "--" }} · {{ \Carbon\Carbon::parse($a->tanggal)->format("d M") }}</span>',
                            { permanent: true, direction:'top', offset:[0,-38], className:'label-monitor' }
                        )
                        .bindPopup(
                            '<div style="min-width:160px">' +
                            '<p style="font-weight:700;font-size:13px;margin-bottom:6px">{{ addslashes($a->user->name) }}</p>' +
                            '<p style="font-size:12px">🔵 <b>Absen Pulang</b></p>' +
                            '<p style="font-size:12px;color:#6b7280">⏰ {{ $a->jam_keluar ?? "--" }}</p>' +
                            '<p style="font-size:12px;color:#6b7280">📅 {{ \Carbon\Carbon::parse($a->tanggal)->format("d M Y") }}</p>' +
                            '<p style="font-size:12px;color:#6b7280">🏢 {{ addslashes($a->cabang->nama_cabang ?? "--") }}</p>' +
                            '</div>'
                        );
                })();
            @endif
        @endforeach

        // Auto-fit bounds
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding:[60,60], maxZoom:16 });
        } else {
            map.setView([-6.2, 106.8], 12);
        }
    </script>
@endsection