@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <div class="container-fluid">

        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h4 class="m-b-10">Approval Lembur</h4>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                        <ul class="breadcrumb mb-0 me-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">
                                    <i class="ph ph-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">Sistem Absensi</li>
                            <li class="breadcrumb-item active">Approval Lembur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card table-card">
            <div class="card-header">
                <h5>Daftar Pengajuan Lembur Karyawan</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="lemburTable">
                        <thead class="bg-blue-700">
                            <tr class="text-white">
                                <th class="text-white text-center" width="5%">NO</th>
                                <th class="text-white">KARYAWAN</th>
                                <th class="text-white">TANGGAL</th>
                                <th class="text-white" width="25%">KETERANGAN</th>
                                <th class="text-white text-center">BUKTI</th>
                                <th class="text-white">STATUS</th>
                                <th class="text-white text-center" width="15%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lembur as $l)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $l->user->name }}</h6>
                                                <small class="text-muted">{{ $l->user->divisi->nama_divisi ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $l->created_at->format('d M Y') }}</span><br>
                                        <small>{{ $l->created_at->format('H:i') }} WIB</small>
                                    </td>
                                    <td><small class="text-wrap">{{ $l->keterangan }}</small></td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)"
                                            onclick="viewImage('{{ asset('uploads/lembur/' . $l->foto) }}')">
                                            <img src="{{ asset('uploads/lembur/' . $l->foto) }}" class="rounded shadow-sm"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    </td>
                                    <td>
                                        @if ($l->status == 'PENDING')
                                            <span class="badge bg-light-warning text-warning">WAITING</span>
                                        @elseif($l->status == 'APPROVED')
                                            <span class="badge bg-light-success text-success">APPROVED</span>
                                        @else
                                            <span class="badge bg-light-danger text-danger">REJECTED</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($l->status == 'PENDING')
                                            <button class="btn btn-sm btn-success me-1" title="Setujui"
                                                onclick="updateStatus('{{ $l->id }}', 'APPROVED')">
                                                <i class="ph ph-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Tolak"
                                                onclick="updateStatus('{{ $l->id }}', 'REJECTED')">
                                                <i class="ph ph-x"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-secondary disabled"><i
                                                    class="ph ph-lock"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <img id="imgFull" src="" class="w-100 rounded">
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Fungsi preview foto
        function viewImage(url) {
            $('#imgFull').attr('src', url);
            $('#modalPreview').modal('show');
        }

        // Fungsi Update Status (Setujui/Tolak)
        function updateStatus(id, status) {
            let color = status === 'APPROVED' ? '#28a745' : '#dc3545';
            let title = status === 'APPROVED' ? 'Setujui Lembur?' : 'Tolak Lembur?';

            Swal.fire({
                title: title,
                text: "Status pengajuan akan segera diperbarui",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: color,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/approval-lembur/' + id + '/status',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Status lembur berhasil diperbarui',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        }


        $(document).ready(function() {
            $('#lemburTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [1, 'asc']
                ], // urutkan berdasarkan NIP
              
               
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
    </script>
@endsection
