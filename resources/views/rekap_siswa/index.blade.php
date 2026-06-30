@extends('app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    #rekapSiswaTable thead th {
        background-color: #fff !important;
        color: #212529 !important;
        font-weight: 700;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
    #rekapSiswaTable tbody td {
        border: 1px solid #dee2e6;
        padding: 8px;
        vertical-align: middle;
    }
    #rekapSiswaTable { border-collapse: collapse; border: 1px solid #dee2e6; font-size: 0.85rem; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0" style="font-weight: 700; font-size: 16px;">Rekapitulasi Siswa</h5>
            <small class="text-muted">
                Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
                s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
            </small>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size: 12px;">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $start_date }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size: 12px;">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $end_date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1" style="font-size: 12px;">Kelas</label>
                <select name="kelas_id" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
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

    <div class="table-responsive">
        <table id="rekapSiswaTable" class="table table-hover">
            <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                    <th class="text-center">HADIR</th>
                    <th class="text-center">TERLAMBAT</th>
                    <th class="text-center">IZIN</th>
                    <th class="text-center">SAKIT</th>
                    <th class="text-center">ALPA</th>
                    <th class="text-center">LIBUR</th>
                    <th class="text-center">Total Hadir</th>
                    <th class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $r)
                    <tr>
                        <td class="fw-medium">{{ $r->nama }}</td>
                        <td><span class="badge bg-info">{{ $r->kelas }}</span></td>
                        <td class="text-center">{{ $r->hadir }}</td>
                        <td class="text-center">{{ $r->terlambat }}</td>
                        <td class="text-center">{{ $r->izin }}</td>
                        <td class="text-center">{{ $r->sakit }}</td>
                        <td class="text-center">{{ $r->alpa }}</td>
                        <td class="text-center">{{ $r->libur }}</td>
                        <td class="text-center fw-bold">{{ $r->total_hadir }}</td>
                        <td class="text-center">{{ $r->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada data rekap</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#rekapSiswaTable').DataTable({
        paging: true,
        pageLength: 25,
        ordering: true,
        info: false,
        searching: true,
        lengthChange: false,
    });
});
</script>
@endpush
