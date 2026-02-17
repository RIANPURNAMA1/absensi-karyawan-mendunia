@extends('app')

@section('content')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <div class="container-fluid">
        {{-- HEADER --}}
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h4 class="m-0 text-dark fw-bold">Data Kehadiran Karyawan</h4>
                    <p class="text-muted small mb-0">Kelola dan pantau absensi harian seluruh staf</p>
                </div>

                <div class="col-md-9">
                    <form method="GET" action="">
                        <div class="row g-2 justify-content-md-end">
                            {{-- Filter Cabang --}}
                            <div class="col-6 col-md-2">
                                <select name="cabang_id" class="form-select form-select-sm shadow-sm">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($list_cabang as $c)
                                        <option value="{{ $c->id }}"
                                            {{ request('cabang_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Divisi --}}
                            <div class="col-6 col-md-2">
                                <select name="divisi_id" class="form-select form-select-sm shadow-sm">
                                    <option value="">Semua Divisi</option>
                                    @foreach ($list_divisi as $d)
                                        <option value="{{ $d->id }}"
                                            {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Status (Disesuaikan dengan ENUM Database) --}}
                            <div class="col-6 col-md-2">
                                <select name="status" class="form-select form-select-sm shadow-sm">
                                    <option value="">Semua Status</option>
                                    @php
                                        $statuses = [
                                            'HADIR',
                                            'TERLAMBAT',
                                            'IZIN',
                                            'ALPA',
                                            'PULANG LEBIH AWAL',
                                            'TIDAK ABSEN PULANG',
                                            'LIBUR',
                                        ];
                                    @endphp
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Tanggal --}}
                            <div class="col-6 col-md-2">
                                <input type="date" name="start_date" value="{{ $start_date }}"
                                    class="form-control form-control-sm shadow-sm" title="Tanggal Mulai">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="date" name="end_date" value="{{ $end_date }}"
                                    class="form-control form-control-sm shadow-sm" title="Tanggal Akhir">
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="col-12 col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                                    <i class="ph ph-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
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
                        <thead class="bg-blue-700">
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

                                    {{-- Karyawan --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $a->user->foto_profil && file_exists(public_path('uploads/foto_profil/' . $a->user->foto_profil))
                                                ? asset('uploads/foto_profil/' . $a->user->foto_profil)
                                                : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png' }}"
                                                class="rounded-circle border border-gray-100" alt="{{ $a->user->name }}"
                                                style="width: 35px; height: 35px; object-fit: cover; flex-shrink: 0;">
                                            <div>
                                                <div class="fw-bold mb-0" style="font-size: 0.9rem;">{{ $a->user->name }}
                                                </div>
                                                <small class="text-muted">{{ $a->user->nip }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Shift --}}
                                    <td>
                                        <span class="text-dark small">{{ $a->shift->nama_shift ?? '-' }}</span>
                                    </td>

                                    {{-- Foto Masuk --}}
                                    <td class="text-center">
                                        @if ($a->foto_masuk)
                                            <img src="{{ asset('storage/' . $a->foto_masuk) }}"
                                                class="rounded border shadow-sm"
                                                style="width: 45px; height: 45px; object-fit: cover;"
                                                onclick="window.open(this.src)" role="button">
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Foto Pulang --}}
                                    <td class="text-center">
                                        @php $fotoPulang = $a->foto_pulang ?? $a->foto_keluar; @endphp
                                        @if ($fotoPulang)
                                            <img src="{{ asset('storage/' . $fotoPulang) }}"
                                                class="rounded border shadow-sm"
                                                style="width: 45px; height: 45px; object-fit: cover;"
                                                onclick="window.open(this.src)" role="button">
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Jam --}}
                                    <td class="small">{{ $a->jam_masuk ? date('H:i', strtotime($a->jam_masuk)) : '-' }}
                                    </td>
                                    <td class="small">{{ $a->jam_keluar ? date('H:i', strtotime($a->jam_keluar)) : '-' }}
                                    </td>

                                    {{-- Cabang --}}
                                    <td class="small text-muted">
                                        {{ $a->cabang->nama_cabang ?? '-' }}
                                    </td>

                                    {{-- Status (Teks Berwarna Tanpa Badge) --}}
                                    <td class="fw-bold small">
                                        @php
                                            $color = 'text-danger';
                                            if ($a->status == 'HADIR') {
                                                $color = 'text-success';
                                            } elseif ($a->status == 'TERLAMBAT') {
                                                $color = 'text-warning';
                                            } elseif ($a->status == 'IZIN') {
                                                $color = 'text-info';
                                            } elseif ($a->status == 'LIBUR') {
                                                $color = 'text-secondary';
                                            }
                                        @endphp
                                        <span class="{{ $color }}">{{ $a->status }}</span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning border-0"
                                            onclick="ubahStatus('{{ $a->id }}','{{ $a->status }}')">
                                            <i class="ph ph-pencil-simple fs-5"></i>
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
