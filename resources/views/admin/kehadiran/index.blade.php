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
                        <input type="date" name="start_date" value="{{ $start_date }}" class="form-control">

                        <input type="date" name="end_date" value="{{ $end_date }}" class="form-control">
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
                        <thead class="bg-blue-700" style=" color: white;">
                            <tr>
                                <th class="text-white">No</th>
                                <th class="text-white">Karyawan</th>
                                <th class="text-white">Shift</th>
                                <th class="text-center text-white">Foto Masuk</th>
                                <th class="text-center text-white">Foto Pulang</th>
                                <th class="text-white">Jam Masuk</th>
                                <th class="text-white">Jam Pulang</th>
                                <th class="text-white">Lokasi Cabang</th>
                                <th class="text-white">Status</th>
                                <th width="10%" class="text-center text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensis as $a)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $a->user->foto_profil && file_exists(public_path('uploads/foto_profil/' . $a->user->foto_profil))
                                                ? asset('uploads/foto_profil/' . $a->user->foto_profil)
                                                : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png' }}"
                                                class="rounded-full object-cover border border-gray-100 shadow-sm"
                                                alt="{{ $a->user->name }}"
                                                style="width: 40px; height: 40px; border-radius: 100%; flex-shrink: 0;">
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

                                    <td class="text-center">
                                        @if ($a->foto_masuk)
                                            <img src="{{ asset('storage/' . $a->foto_masuk) }}"
                                                class="rounded border shadow-sm"
                                                style="width: 60px; height: 60px; object-fit: cover;"
                                                onclick="window.open(this.src)" role="button">
                                        @elseif($a->jam_masuk)
                                            <small class="text-primary fw-bold" style="font-size: 0.75rem;">
                                                <i class="ph ph-scan text-primary"></i> Face Recognition
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($a->foto_keluar)
                                            {{-- Ganti ke foto_pulang jika nama kolom di DB berbeda --}}
                                            <img src="{{ asset('storage/' . $a->foto_keluar) }}"
                                                class="rounded border shadow-sm"
                                                style="width: 60px; height: 60px; object-fit: cover;"
                                                onclick="window.open(this.src)" role="button">
                                        @elseif($a->jam_keluar)
                                            <small class="text-primary fw-bold" style="font-size: 0.75rem;">
                                                <i class="ph ph-scan text-primary"></i> Face Recognition
                                            </small>
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
                            @endforeach
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
                            <option value="TIDAK ABSEN PULANG">TIDAK ABSEN PULANG</option>
                            <option value="LIBUR">LIBUR</option>
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
