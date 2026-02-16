@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <div class="container-fluid">

        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h4 class="m-b-10">Approval Pengajuan Izin</h4>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                        <ul class="breadcrumb mb-0 me-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">
                                    <i class="ph ph-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">Manajemen Absensi</li>
                            <li class="breadcrumb-item active">Approval Izin</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar Pengajuan Izin Karyawan</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="izinTable">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold  text-white">No</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Nama</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Jenis Izin</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Periode</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Alasan</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Lampiran</th>
                                <th class="px-4 py-3 text-left font-semibold  text-white">Status</th>
                                <th class="px-4 py-3 text-center font-semibold  text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($izins as $izin)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <span class="fw-bold text-dark">{{ $izin->user->name }}</span><br>
                                        <small class="text-muted">{{ $izin->user->email }}</small>
                                    </td>

                                    <td>
                                        <span class="badge bg-light-primary text-primary border border-primary">
                                            {{ $izin->jenis_izin }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $mulai = \Carbon\Carbon::parse($izin->tgl_mulai);
                                            $selesai = \Carbon\Carbon::parse($izin->tgl_selesai);
                                            $durasi = $mulai->diffInDays($selesai) + 1; // +1 biar hari pertama dihitung
                                        @endphp

                                        {{ $mulai->format('d M Y') }} <br>
                                        <i class="ph ph-arrow-down"></i><br>
                                        {{ $selesai->format('d M Y') }}

                                        <div class="mt-1">
                                            <span class="badge bg-info text-dark">
                                                {{ $durasi }} hari
                                            </span>
                                        </div>
                                    </td>


                                    <td>{{ Str::limit($izin->alasan, 40) }}</td>

                                    <td>
                                        <a href="{{ asset('uploads/izin/' . $izin->lampiran) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="ph ph-paperclip"></i> Lihat
                                        </a>

                                    </td>


                                    <td>
                                        <span
                                            class="badge 
                                    @if ($izin->status == 'PENDING') bg-warning
                                    @elseif($izin->status == 'APPROVED') bg-success
                                    @else bg-danger @endif">
                                            {{ $izin->status }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if ($izin->status == 'PENDING')
                                            <form action="{{ route('izin.approve', $izin->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    <i class="ph ph-check"></i>
                                                </button>
                                            </form>

                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $izin->id }}">
                                                <i class="ph ph-x"></i>
                                            </button>
                                        @else
                                            <small class="text-muted">Diproses</small>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Reject --}}
                                <div class="modal fade" id="rejectModal{{ $izin->id }}">
                                    <div class="modal-dialog">
                                        <form action="{{ route('izin.reject', $izin->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea name="catatan" class="form-control" required placeholder="Tulis alasan penolakan..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-danger">Tolak Izin</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                           

                        </tbody>
                    </table>
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
        $(document).ready(function() {
            $('#izinTable').DataTable({
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
