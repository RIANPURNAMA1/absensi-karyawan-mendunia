@extends('app')

@section('content')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="page-header mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="m-b-10">Data Kehadiran Karyawan</h4>
                </div>

                <div class="col-md-6 d-flex justify-content-md-end gap-2">
                    <form method="GET" class="d-flex gap-2">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
                        <button class="btn btn-primary">
                            <i class="ph ph-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card table-card">
            <div class="card-header">
                <h5>Rekap Absensi Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="absensiTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Karyawan</th>
                                <th>Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Lokasi Cabang</th>
                                <th>Status</th>
                                <th width="10%" class="text-center">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($absensis as $a)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $a->user->foto_profil ? asset('storage/foto-karyawan/' . $a->user->foto_profil) : asset('assets/images/avatar/avatar-1.jpg') }}"
                                                class="rounded-circle" width="40" style="height: 40px; object-fit:cover">
                                            <div>
                                                <span class="fw-bold">{{ $a->user->name }}</span><br>
                                                <small class="text-muted">{{ $a->user->nip }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($a->shift)
                                            <span class="badge bg-light-primary text-primary border border-primary">
                                                {{ $a->shift->nama_shift }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}
                                    </td>

                                    <td>
                                        {{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i') : '-' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-light-info text-info border border-info">
                                            {{ $a->cabang->nama_cabang ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="badge
                                    @if ($a->status == 'HADIR') bg-success
                                    @elseif($a->status == 'TERLAMBAT') bg-warning
                                    @elseif($a->status == 'IZIN') bg-info
                                    @else bg-danger @endif">
                                            {{ $a->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="ubahStatus('{{ $a->id }}','{{ $a->status }}')">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ph ph-calendar-x d-block fs-2 mb-2"></i>
                                        Tidak ada data kehadiran
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- modal revisi --}}
    <div class="modal fade" id="modalStatus">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.absensi.updateStatus') }}">
                    @csrf
                    <input type="hidden" name="id" id="status_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Status Absensi</h5>
                    </div>

                    <div class="modal-body">

                        <label class="fw-semibold mb-2">Status Baru</label>
                        <select name="status" id="status_value" class="form-control" required>
                            <option value="HADIR">HADIR</option>
                            <option value="TERLAMBAT">TERLAMBAT</option>
                            <option value="IZIN">IZIN</option>
                            <option value="ALPA">ALPA</option>
                            <option value="PULANG LEBIH AWAL">PULANG LEBIH AWAL</option>
                        </select>

                        <div class="alert alert-warning mt-3 small">
                            Perubahan status akan tercatat sebagai revisi admin.
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        // revisi
        function ubahStatus(id, status) {
            document.getElementById('status_id').value = id;
            document.getElementById('status_value').value = status;

            var modal = new bootstrap.Modal(document.getElementById('modalStatus'));
            modal.show();
        }
        $(document).ready(function() {


            $('#absensiTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: 'Export PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary btn-sm'
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "→",
                        previous: "←"
                    },
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });
    </script>
@endsection
