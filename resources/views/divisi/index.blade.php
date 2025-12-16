@extends('app')

@section('content')
    <div class="container-fluid">

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
                            <li class="breadcrumb-item">Master Data</li>
                            <li class="breadcrumb-item active">Data Karyawan</li>
                        </ul>

                        <!-- BUTTON TAMBAH -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            + Tambah Divisi
                        </button>


                    </div>

                </div>
            </div>
        </div>


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- CARD TABLE -->
        <div class="card table-card">
            <div class="card-header">
                <h5>Daftar Divisi</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="divisiTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Divisi</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($divisi as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->nama_divisi }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="editDivisi({{ $d->id }}, '{{ $d->nama_divisi }}')">
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
                                    <td colspan="3" class="text-center text-muted">
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


    {{-- delete rquest --}}
    <script>
        function deleteDivisi(id) {

            Swal.fire({
                title: 'Hapus divisi?',
                text: 'Data divisi akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
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
                        success: function() {

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
                                text: 'Divisi gagal dihapus'
                            });
                        }
                    });

                }
            });
        }
    </script>
@endsection
