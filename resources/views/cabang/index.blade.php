@extends('app')

@section('content')
<div class="container-fluid px-4 py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0" style="font-weight: 700; font-size: 16px;">Data Cabang</h5>
            <small class="text-muted">Master data seluruh cabang dan lokasi kantor</small>
        </div>
        <div>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahCabang">
                <i class="ph ph-plus-circle me-1"></i> Tambah Cabang
            </button>
        </div>
    </div>

    <div class="rounded-3">
        <div class="p-3 border-bottom" style="border-bottom-color: #f0f0f0 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-semibold" style="font-size: 13px;"></span>
                <span class="text-muted" style="font-size: 11px;">{{ $cabangs->count() }} data</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th scope="col" width="5%">No</th>
                        <th scope="col" width="10%">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Pusat/Cabang</th>
                        <th scope="col">Lokasi (Lat, Long)</th>
                        <th scope="col">Radius</th>
                        <th scope="col" width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabangs as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light-primary text-primary fw-normal px-2 py-1">{{ $c->kode_cabang }}</span></td>
                            <td>
                                <span class="fw-medium" style="font-size: 13px;">{{ $c->nama_cabang }}</span><br>
                                <small class="text-muted">{{ Str::limit($c->alamat, 40) }}</small>
                            </td>
                            <td>
                                @if($c->status_pusat == 'PUSAT')
                                    <span class="badge bg-dark fw-normal px-2 py-1">PUSAT</span>
                                @else
                                    <span class="badge bg-light-secondary text-dark fw-normal px-2 py-1">CABANG</span>
                                @endif
                            </td>
                            <td><code class="text-muted" style="font-size: 11px;">{{ $c->latitude }}, {{ $c->longitude }}</code></td>
                            <td><span class="text-muted" style="font-size: 12px;"><i class="ph ph-arrows-out-line me-1"></i>{{ $c->radius }} Meter</span></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary border-0" title="Edit"
                                        onclick="editCabang('{{ $c->id }}', '{{ $c->kode_cabang }}', '{{ $c->nama_cabang }}', '{{ $c->status_pusat }}', '{{ $c->latitude }}', '{{ $c->longitude }}', '{{ $c->radius }}', '{{ $c->alamat }}')">
                                        <i class="ph ph-note-pencil"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary border-0" title="Hapus"
                                        onclick="deleteCabang({{ $c->id }})">
                                        <i class="ph ph-trash"></i>
                                    </a>
                                </div>
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

@include('cabang.modal')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editCabang(id, kode, nama, status, lat, long, radius, alamat) {
    $('#edit_id').val(id);
    $('#edit_kode_cabang').val(kode);
    $('#edit_nama_cabang').val(nama);
    $('#edit_status_pusat').val(status);
    $('#edit_latitude').val(lat);
    $('#edit_longitude').val(long);
    $('#edit_radius').val(radius);
    $('#edit_alamat').val(alamat);
    $('#formEditCabang').attr('action', '/cabang/' + id);
    $('#modalEditCabang').modal('show');
}

function deleteCabang(id) {
    Swal.fire({
        title: 'Hapus cabang?',
        text: 'Data cabang dan jangkauan lokasinya akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/cabang/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function() {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Cabang berhasil dihapus', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
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
