@extends('app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <style>
        body { color: #333; }

        .filter-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        #rekapTable {
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e0e0e0;
            font-size: 0.85rem;
        }

        #rekapTable thead th {
            background-color: #f8f9fa !important;
            color: #334155 !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 12px 8px;
            border-bottom: 2px solid #edeff2 !important;
            vertical-align: middle;
        }

        #rekapTable tbody tr:hover { background-color: #f8fafc !important; }

        .col-highlight-blue { background-color: #f0f7ff !important; color: #0284c7 !important; }
        .col-highlight-dark { background-color: #f1f5f9 !important; font-weight: 700; color: #1e293b; }
        .col-grand-total    { background-color: #1e293b !important; color: #ffffff !important; }
        .col-libur          { background-color: #fef9f0 !important; color: #b45309 !important; }

        .badge-outline {
            border: 1px solid #e2e8f0;
            color: #64748b;
            background: #ffffff;
            padding: 4px 8px;
            font-weight: 500;
        }
        .text-xs { font-size: 0.75rem; }
    </style>

    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="mb-4 border-bottom pb-3">
            <h4 class="fw-bold text-uppercase mb-1" style="letter-spacing:1px">
                Laporan Rekapitulasi Absensi
            </h4>
            <p class="text-muted small mb-0">
                Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
                s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
            </p>
        </div>

        {{-- Filter --}}
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="filter-label">Mulai Tanggal</label>
                            <input type="date" name="start_date"
                                   class="form-control form-control-sm"
                                   value="{{ $start_date }}">
                        </div>
                        <div class="col-md-2">
                            <label class="filter-label">Sampai Tanggal</label>
                            <input type="date" name="end_date"
                                   class="form-control form-control-sm"
                                   value="{{ $end_date }}">
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">Cabang</label>
                            <select name="cabang_id" class="form-select form-select-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($list_cabang as $c)
                                    <option value="{{ $c->id }}"
                                        {{ request('cabang_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">Divisi</label>
                            <select name="divisi_id" class="form-select form-select-sm">
                                <option value="">Semua Divisi</option>
                                @foreach ($list_divisi as $d)
                                    <option value="{{ $d->id }}"
                                        {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-search me-1"></i> Cari
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="rekapTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="40">No</th>
                                <th class="text-start">Karyawan</th>
                                <th class="text-center">Cabang</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Alpa</th>
                                <th class="text-center">P.Awal</th>
                                <th class="text-center col-libur">Libur</th>
                                <th class="text-center col-highlight-blue">Lembur (X)</th>
                                <th class="text-center col-highlight-blue">Jam Lembur</th>
                                <th class="text-center" style="background-color:#f0fdf4 !important; color:#15803d !important;">Kehadiran Sensei</th>
                                <th class="text-center" style="background-color:#faf5ff !important; color:#7c3aed !important;">Total Agenda</th>
                                <th class="text-center col-highlight-dark">Jam Kerja</th>
                                <th class="text-center col-grand-total">Grand Total</th>
                                <th class="text-center" style="background-color:#fff7ed !important; color:#c2410c !important;">Total Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekap as $index => $r)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $r->nama }}</div>
                                        <div class="text-xs text-muted text-uppercase">{{ $r->jabatan }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-outline rounded-pill">{{ $r->cabang }}</span>
                                    </td>
                                    <td class="text-center">{{ $r->hadir }}</td>
                                    <td class="text-center {{ $r->terlambat > 0 ? 'text-danger fw-bold' : '' }}">
                                        {{ $r->terlambat }}
                                    </td>
                                    <td class="text-center">{{ $r->izin }}</td>
                                    <td class="text-center {{ $r->alpa > 0 ? 'text-danger fw-bold' : '' }}">
                                        {{ $r->alpa }}
                                    </td>
                                    <td class="text-center">{{ $r->pulang_awal }}</td>
                                    <td class="text-center col-libur fw-bold">
                                        {{ $r->libur }}
                                        <span class="text-xs" style="color:#b45309">hari</span>
                                    </td>
                                    <td class="text-center col-highlight-blue fw-bold">
                                        {{ $r->jumlah_lembur }}<span class="text-xs">x</span>
                                    </td>
                                    <td class="text-center col-highlight-blue">{{ $r->total_jam_lembur }}</td>
                                    <td class="text-center" style="background-color:#f0fdf4 !important; color:#15803d !important; font-weight:600;">
                                        {{ $r->sensei_kehadiran }}<span class="text-xs">x</span>
                                    </td>
                                    <td class="text-center" style="background-color:#faf5ff !important; color:#7c3aed !important; font-weight:600;">
                                        {{ $r->total_agenda }}
                                    </td>
                                    <td class="text-center col-highlight-dark">{{ $r->total_jam_kerja }}</td>
                                    <td class="text-center col-grand-total">{{ $r->grand_total_jam }}</td>
                                    <td class="text-center" style="background-color:#fff7ed !important; color:#c2410c !important; font-weight:700;">
                                        {{ $r->total_kehadiran }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            var table = $('#rekapTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center border-bottom p-3"Bf>t<"d-flex justify-content-between align-items-center p-3"ip>',
                buttons: [
                    {
                        extend:'excel',
                        className:'btn btn-sm btn-outline-success',
                        text:'<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                        title: 'Rekapitulasi Absensi Karyawan',
                        messageTop: 'Periode: {{ \Carbon\Carbon::parse($start_date)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($end_date)->format("d/m/Y") }}',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend:'pdf',
                        className:'btn btn-sm btn-outline-danger',
                        text:'<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                        title: 'Rekapitulasi Absensi Karyawan',
                        messageTop: 'Periode: {{ \Carbon\Carbon::parse($start_date)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($end_date)->format("d/m/Y") }}',
                        orientation: 'landscape',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend:'print',
                        className:'btn btn-sm btn-outline-secondary',
                        text:'<i class="bi bi-printer me-1"></i> Cetak',
                        title: 'Rekapitulasi Absensi Karyawan',
                        messageTop: 'Periode: {{ \Carbon\Carbon::parse($start_date)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($end_date)->format("d/m/Y") }}',
                        exportOptions: { columns: ':visible' }
                    },
                ],
                pageLength: 25,
                language: {
                    search: "Cari:",
                    info: "Data _START_–_END_ dari _TOTAL_",
                    paginate: { next:"Lanjut", previous:"Kembali" }
                }
            });
        });
    </script>
@endsection