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
            <div class="card-header d-flex justify-content-between align-items-center py-4">
                <div>
                    <h5 class="mb-0 fw-bold">Daftar Pengajuan Izin Karyawan</h5>
                    <small class="text-muted">Total: {{ $izins->count() }} pengajuan</small>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="izinTable">
                        <thead>
                            <tr class="bg-blue-700 text-white">
                                <th class="px-4 py-3 text-white" style="width:40px">No</th>
                                <th class="px-4 py-3 text-white">Nama</th>
                                <th class="px-4 py-3 text-white">Jenis Izin</th>
                                <th class="px-4 py-3 text-white">Periode</th>
                                <th class="px-4 py-3 text-white">Alasan</th>
                                <th class="px-4 py-3 text-white text-center">Lampiran</th>
                                <th class="px-4 py-3 text-white text-center">Status</th>
                                <th class="px-4 py-3 text-white text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($izins as $izin)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>

                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-dark">{{ $izin->user->name }}</div>
                                        <small class="text-muted">{{ $izin->user->email }}</small>
                                    </td>

                                    <td class="px-4 py-3">
                                        @php
                                            $jenisColor = match($izin->jenis_izin) {
                                                'SAKIT' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                                'CUTI'  => 'bg-blue-50 text-blue-700 border border-blue-200',
                                                default => 'bg-violet-50 text-violet-700 border border-violet-200',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $jenisColor }}">
                                            {{ $izin->jenis_izin }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        @php
                                            $mulai   = \Carbon\Carbon::parse($izin->tgl_mulai);
                                            $selesai = \Carbon\Carbon::parse($izin->tgl_selesai);
                                            $durasi  = $mulai->diffInDays($selesai) + 1;
                                        @endphp
                                        <div class="text-sm">
                                            <div class="fw-semibold">{{ $mulai->format('d M Y') }}</div>
                                            <div class="text-muted" style="font-size:11px">s/d</div>
                                            <div class="fw-semibold">{{ $selesai->format('d M Y') }}</div>
                                            <span class="badge bg-info text-dark mt-1">{{ $durasi }} hari</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3" style="max-width:260px; min-width:180px">
                                        <div class="text-sm text-gray-700"
                                             style="white-space:pre-wrap; word-break:break-word; line-height:1.6">
                                            {{ $izin->alasan }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if($izin->lampiran)
                                            <a href="{{ asset('uploads/izin/' . $izin->lampiran) }}" target="_blank"
                                               class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1">
                                                <i class="ph ph-paperclip"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusClass = match($izin->status) {
                                                'PENDING'  => 'bg-warning text-dark',
                                                'APPROVED' => 'bg-success text-white',
                                                default    => 'bg-danger text-white',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} px-2 py-1">
                                            {{ $izin->status }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($izin->status == 'PENDING')
                                            <div class="d-flex gap-1 justify-content-center">
                                                <form action="{{ route('izin.approve', $izin->id) }}" method="POST">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                                        <i class="ph ph-check"></i> Setuju
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $izin->id }}">
                                                    <i class="ph ph-x"></i> Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted text-xs">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Reject --}}
                                <div class="modal fade" id="rejectModal{{ $izin->id }}">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('izin.reject', $izin->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <div>
                                                        <h5 class="modal-title fw-bold">Tolak Pengajuan Izin</h5>
                                                        <small class="text-muted">
                                                            {{ $izin->user->name }} — {{ $izin->jenis_izin }}
                                                        </small>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label fw-semibold text-sm">
                                                        Alasan Penolakan <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="catatan" class="form-control" rows="4" required
                                                              placeholder="Tulis alasan penolakan secara jelas..."></textarea>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-danger d-inline-flex align-items-center gap-1">
                                                        <i class="ph ph-x-circle"></i> Tolak Izin
                                                    </button>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#izinTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[1, 'asc']],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak tersedia",
                    paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
                }
            });
        });
    </script>
@endsection
