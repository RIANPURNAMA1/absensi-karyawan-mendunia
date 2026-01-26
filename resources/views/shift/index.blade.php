@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <!-- PAGE HEADER + BREADCRUMB -->
    <div class="page-header mb-3">
        <div class="page-block">
            <div class="row align-items-center">

                <!-- TITLE -->
                <div class="col-md-6">
                    <div class="page-header-title">

                    </div>
                </div>

                <!-- BREADCRUMB + BUTTON -->
                <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">

                    <ul class="breadcrumb mb-0 me-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="ph ph-house"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">Manajemen Karyawan</li>
                        <li class="breadcrumb-item active">Shift Kerja</li>
                    </ul>

                    <!-- BUTTON TAMBAH -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahShift"
                        style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); 
           border: none; 
           padding: 8px 16px; 
           font-weight: 500; 
           box-shadow: 0 2px 6px rgba(30, 60, 114, 0.3);
           transition: all 0.3s ease;">
                        <i class="ph ph-plus-circle"></i> Tambah Shift
                    </button>

                </div>

            </div>
        </div>
    </div>

    <!-- CARD TABLE -->
    <div class="card table-card">
        <div class="card-header">
            <h5>Daftar Shift Kerja</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive p-5">
                <table class="table align-middle mb-0" id="shiftTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Shift</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Total Jam</th>
                            <th>Toleransi Keterlambatan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($shifts as $index => $shift)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);">
                                                <i class="ph ph-timer text-white" style="font-size: 20px;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $shift->nama_shift }}</h6>
                                            <small class="text-muted">{{ $shift->kode_shift ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge"
                                        style="background: linear-gradient(135deg, #71b280 0%, #134e5e 100%); padding: 6px 12px;">
                                        <i class="ph ph-clock-clockwise"></i>
                                        {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge"
                                        style="background: linear-gradient(135deg, #94716b 0%, #7a5d58 100%); padding: 6px 12px;">
                                        <i class="ph ph-clock-counter-clockwise"></i>
                                        {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $shift->total_jam ?? '8' }} Jam</strong>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <i class="ph ph-clock"></i> {{ $shift->toleransi ?? '15' }} Menit
                                    </span>
                                </td>
                                <td>
                                    @if ($shift->status === 'AKTIF')
                                        <span class="badge bg-success">
                                            <i class="ph ph-check-circle"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="ph ph-x-circle"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info" onclick="detailShift({{ $shift->id }})">
                                            <i class="ph ph-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="editShift(
                                            {{ $shift->id }},
                                            '{{ $shift->nama_shift }}',
                                            '{{ $shift->kode_shift }}',
                                            '{{ $shift->jam_masuk }}',
                                            '{{ $shift->jam_pulang }}',
                                            '{{ $shift->total_jam }}',
                                            '{{ $shift->toleransi }}',
                                            '{{ $shift->status }}',
                                            '{{ $shift->keterangan ?? '' }}'
                                        )">
                                            <i class="ph ph-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteShift({{ $shift->id }})">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- modal tambah & edit data --}}
    @include('shift.modal')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Detail Shift Function
        function detailShift(id) {
            // Implementasi detail shift
            Swal.fire({
                title: 'Detail Shift',
                html: '<p>Loading detail shift...</p>',
                icon: 'info'
            });
        }

        // Edit Shift Function
        function editShift(id, nama_shift, kode_shift, jam_masuk, jam_pulang, total_jam, toleransi, status, keterangan) {
            $('#edit_id').val(id);
            $('#edit_nama_shift').val(nama_shift);
            $('#edit_kode_shift').val(kode_shift);
            $('#edit_jam_masuk').val(jam_masuk);
            $('#edit_jam_pulang').val(jam_pulang);
            $('#edit_total_jam').val(total_jam);
            $('#edit_toleransi').val(toleransi);
            $('#edit_status').val(status);
            $('#edit_keterangan').val(keterangan);

            $('#modalEditShift').modal('show');
        }

        // DataTable Initialization
        $(document).ready(function() {
            $('#shiftTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 7]
                }],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak tersedia",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    }
                }
            });
        });

        // Delete Shift Function
        function deleteShift(id) {
            Swal.fire({
                title: 'Hapus Shift?',
                text: 'Data shift yang dihapus tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/shift/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    });
                }
            });
        }
    </script>

    <style>
        /* Custom Button Styles */
        .btn-group .btn {
            margin: 0 2px;
        }

        /* Table Row Hover */
        #shiftTable tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        /* Badge Custom */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }
    </style>
@endsection
