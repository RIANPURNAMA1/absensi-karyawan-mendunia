@extends('app')

@section('content')
    <div class="container-fluid">

        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h4 class="m-b-10">Master Divisi</h4>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                        <ul class="breadcrumb mb-0 me-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">
                                    <i class="ph ph-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">Master Data</li>
                            <li class="breadcrumb-item active">Data Divisi</li>
                        </ul>

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDivisi">
                            <i class="ph ph-plus-circle me-1"></i> Tambah Divisi
                        </button>
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card table-card">
            <div class="card-header">
                <h5>Daftar Divisi Kerja</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0 " id="divisiTable">
                        <thead class="bg-blue-700">
                            <tr class="text-white">
                                <th class="text-white" width="5%">NO</th>
                                <th class="text-white" width="15%">KODE</th>
                                <th class="text-white">NAMA DIVISI</th>
                                <th class="text-white" width="15%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($divisi as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-light-primary text-primary fw-bold">{{ $d->kode_divisi }}</span></td>
                                    <td><span class="fw-bold text-dark">{{ $d->nama_divisi }}</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="editDivisi('{{ $d->id }}', '{{ $d->kode_divisi }}', '{{ $d->nama_divisi }}')">
                                            <i class="ph ph-pencil"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" onclick="deleteDivisi({{ $d->id }})">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($divisi->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="ph ph-folder-not-found d-block fs-2 mb-2"></i>
                                        Data divisi belum tersedia
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('divisi.modal')

    <script>
        // Fungsi untuk mengisi data ke Modal Edit
        function editDivisi(id, kode, nama) {
            $('#edit_id').val(id);
            $('#edit_kode_divisi').val(kode);
            $('#edit_nama_divisi').val(nama);
            $('#modalEditDivisi').modal('show');
        }

        // Fungsi delete dengan SweetAlert2
        function deleteDivisi(id) {
            Swal.fire({
                title: 'Hapus divisi?',
                text: 'Data divisi akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/divisi/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Divisi berhasil dihapus',
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
                                text: 'Divisi gagal dihapus atau masih terikat dengan data karyawan'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection