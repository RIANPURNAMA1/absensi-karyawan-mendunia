@extends('app')

@section('content')
    <div class="container-fluid">

        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h4 class="m-b-10">Master Cabang</h4>
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
                            <li class="breadcrumb-item active">Data Cabang</li>
                        </ul>

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCabang">
                            <i class="ph ph-plus-circle me-1"></i> Tambah Cabang
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

        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar Cabang / Lokasi Kantor</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle mb-0" id="cabangTable">
                        <thead class="bg-blue-700">
                            <tr>
                                <th class="text-white" width="5%">NO</th>
                                <th class="text-white" width="10%">KODE</th>
                                <th class="text-white"> NAMA</th>
                                <th class="text-white">PUSAT/CABANG</th>
                                <th class="text-white">LOKASI (LAT, LONG)</th>
                                <th class="text-white">RADIUS</th>
                                <th class="text-white" width="15%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cabangs as $c)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-light-secondary text-dark fw-bold">{{ $c->kode_cabang }}</span></td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $c->nama_cabang }}</span><br>
                                        <small class="text-muted">{{ Str::limit($c->alamat, 40) }}</small>
                                    </td>
                                    <td>
                                        @if($c->status_pusat == 'PUSAT')
                                            <span class="badge bg-primary">PUSAT</span>
                                        @else
                                            <span class="badge bg-info">CABANG</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $c->latitude }}, {{ $c->longitude }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light-info text-info border border-info">
                                            <i class="ph ph-arrows-out-line me-1"></i> {{ $c->radius }} Meter
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="editCabang('{{ $c->id }}', '{{ $c->kode_cabang }}', '{{ $c->nama_cabang }}', '{{ $c->status_pusat }}', '{{ $c->latitude }}', '{{ $c->longitude }}', '{{ $c->radius }}', '{{ $c->alamat }}')">
                                            <i class="ph ph-pencil"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" onclick="deleteCabang({{ $c->id }})">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($cabangs->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ph ph-map-pin-slash d-block fs-2 mb-2"></i>
                                        Data cabang belum tersedia
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('cabang.modal')

    <script>
        // Fungsi Edit (Menyesuaikan parameter baru: kode dan status)
        function editCabang(id, kode, nama, status, lat, long, radius, alamat) {
            $('#edit_id').val(id);
            $('#edit_kode_cabang').val(kode); // Pastikan ID input ini ada di modal
            $('#edit_nama_cabang').val(nama);
            $('#edit_status_pusat').val(status); // Pastikan ID select/input ini ada di modal
            $('#edit_latitude').val(lat);
            $('#edit_longitude').val(long);
            $('#edit_radius').val(radius);
            $('#edit_alamat').val(alamat);
            $('#modalEditCabang').modal('show');
        }

        // Fungsi Delete dengan SweetAlert2
        function deleteCabang(id) {
            Swal.fire({
                title: 'Hapus cabang?',
                text: 'Data cabang dan jangkauan lokasinya akan dihapus',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/cabang/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Cabang berhasil dihapus',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => { location.reload(); }, 1500);
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Cabang gagal dihapus' });
                        }
                    });
                }
            });
        }
    </script>
@endsection