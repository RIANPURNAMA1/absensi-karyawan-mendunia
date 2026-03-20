@extends('app')

@section('content')
    <div class="container-fluid">

        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h4 class="m-b-10">Manajemen Hari Libur</h4>
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
                            <li class="breadcrumb-item active">Hari Libur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <i class="ph ph-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Form Tambah Hari Libur --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Tambah Libur Nasional</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hari-libur.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Dari Tanggal</label>
                                <input type="date" name="tgl_mulai"
                                    class="form-control @error('tgl_mulai') is-invalid @enderror" required>
                                @error('tgl_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sampai Tanggal</label>
                                <input type="date" name="tgl_selesai"
                                    class="form-control @error('tgl_selesai') is-invalid @enderror" required>
                                @error('tgl_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Contoh: Libur Lebaran" required>
                            </div>
                            <div class="alert alert-info py-2 border-0 mb-3" style="font-size:0.85rem">
                                <i class="ph ph-info me-1"></i>
                                Tanggal yang sudah terdaftar akan dilewati otomatis.
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ph ph-plus-circle me-1"></i> Simpan Rentang Tanggal
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Hari Libur --}}
            <div class="col-md-8">
                <div class="card table-card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Daftar Tanggal Merah</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-4">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hariLiburs as $libur)
                                        <tr>
                                            <td>{{ $hariLiburs->firstItem() + $loop->index }}</td>
                                            <td>
                                                <span class="fw-bold">
                                                    {{ \Carbon\Carbon::parse($libur->tanggal)->format('d F Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light-danger text-danger border border-danger">
                                                    {{ \Carbon\Carbon::parse($libur->tanggal)->isoFormat('dddd') }}
                                                </span>
                                            </td>
                                            <td>{{ $libur->keterangan }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('hari-libur.destroy', $libur->id) }}" method="POST"
                                                    class="form-delete d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-danger shadow-none btn-delete">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="ph ph-calendar-blank d-block fs-1 mb-2"></i>
                                                Belum ada libur nasional yang ditambahkan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($hariLiburs->hasPages())
                            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                                <small class="text-muted">
                                    Menampilkan {{ $hariLiburs->firstItem() }}–{{ $hariLiburs->lastItem() }}
                                    dari {{ $hariLiburs->total() }} data
                                </small>
                                {{ $hariLiburs->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Tangkap semua tombol dengan class btn-delete
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.form-delete'); // Ambil form terdekat

                Swal.fire({
                    title: 'Hapus Tanggal Libur?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6e7881',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true, // Agar tombol 'Batal' di kiri
                    borderRadius: '15px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Jalankan submit form jika klik 'Ya'
                    }
                });
            });
        });
    </script>
@endsection
