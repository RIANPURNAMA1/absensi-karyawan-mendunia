@extends('app')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0" style="font-weight: 700; font-size: 16px;">Penilaian Siswa</h5>
            <small class="text-muted">Kelola data penilaian dan evaluasi siswa</small>
        </div>
        <div>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenilaian">
                <i class="ph ph-plus-circle me-1"></i> Tambah Penilaian
            </button>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="ph ph-users text-primary fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted">Total Siswa Dinilai</small>
                            <h5 class="mb-0 fw-bold">{{ $penilaians->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="ph ph-trophy text-success fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted">Rata-rata Nilai</small>
                            <h5 class="mb-0 fw-bold">{{ $penilaians->avg('nilai') ? number_format($penilaians->avg('nilai'), 1) : '-' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="ph ph-book-open text-warning fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted">Mata Pelajaran</small>
                            <h5 class="mb-0 fw-bold">{{ $penilaians->pluck('mata_pelajaran')->unique()->filter()->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="ph ph-graduation-cap text-info fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted">Kelas</small>
                            <h5 class="mb-0 fw-bold">{{ $penilaians->pluck('kelas')->unique()->filter()->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-3">
        <div class="p-3 border-bottom" style="border-bottom-color: #f0f0f0 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-semibold" style="font-size: 13px;">Data Penilaian</span>
                <span class="text-muted" style="font-size: 11px;">{{ $penilaians->count() }} data</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Nilai</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penilaians as $penilaian)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-medium" style="font-size: 13px;">{{ $penilaian->nama_siswa }}</span>
                            </td>
                            <td>{{ $penilaian->kelas ?? '-' }}</td>
                            <td>{{ $penilaian->mata_pelajaran ?? '-' }}</td>
                            <td>
                                @if($penilaian->nilai)
                                    @php
                                        $nilai = $penilaian->nilai;
                                        if ($nilai >= 90) $badge = 'success';
                                        elseif ($nilai >= 75) $badge = 'primary';
                                        elseif ($nilai >= 60) $badge = 'warning';
                                        else $badge = 'danger';
                                    @endphp
                                    <span class="badge bg-{{ $badge }} fw-normal px-2 py-1">{{ number_format($nilai, 0) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">{{ $penilaian->tanggal_penilaian->format('d/m/Y') }}</td>
                            <td>
                                <span style="font-size: 12px; max-width: 150px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $penilaian->keterangan ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary border-0" title="Edit"
                                        onclick="editPenilaian({{ $penilaian->id }}, '{{ $penilaian->nama_siswa }}', '{{ $penilaian->kelas }}', '{{ $penilaian->mata_pelajaran }}', '{{ $penilaian->nilai }}', '{{ $penilaian->keterangan }}', '{{ $penilaian->tanggal_penilaian->format('Y-m-d') }}')">
                                        <i class="ph ph-note-pencil"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary border-0" title="Hapus"
                                        onclick="deletePenilaian({{ $penilaian->id }})">
                                        <i class="ph ph-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($penilaians->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="ph ph-notebook d-block fs-2 mb-2"></i>
                                Belum ada data penilaian. Klik "Tambah Penilaian" untuk memulai.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Penilaian --}}
<div class="modal fade" id="modalTambahPenilaian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('penilaian.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tambah Penilaian</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama_siswa" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Mis: XII IPA 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" name="mata_pelajaran" class="form-control" placeholder="Mis: Matematika">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai" class="form-control" min="0" max="100" step="0.01" placeholder="0-100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Penilaian --}}
<div class="modal fade" id="modalEditPenilaian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditPenilaian" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Edit Penilaian</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama_siswa" id="edit_nama_siswa" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" id="edit_kelas" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" name="mata_pelajaran" id="edit_mata_pelajaran" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai" id="edit_nilai" class="form-control" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penilaian" id="edit_tanggal_penilaian" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editPenilaian(id, namaSiswa, kelas, mapel, nilai, keterangan, tanggal) {
    $('#edit_nama_siswa').val(namaSiswa);
    $('#edit_kelas').val(kelas === '-' ? '' : kelas);
    $('#edit_mata_pelajaran').val(mapel === '-' ? '' : mapel);
    $('#edit_nilai').val(nilai);
    $('#edit_keterangan').val(keterangan === '-' ? '' : keterangan);
    $('#edit_tanggal_penilaian').val(tanggal);

    let url = "{{ route('penilaian.update', ':id') }}";
    url = url.replace(':id', id);
    $('#formEditPenilaian').attr('action', url);

    $('#modalEditPenilaian').modal('show');
}

function deletePenilaian(id) {
    Swal.fire({
        title: 'Hapus penilaian?',
        text: 'Data penilaian akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/penilaian/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function() {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Penilaian berhasil dihapus', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Penilaian gagal dihapus' });
                }
            });
        }
    });
}
</script>
@endsection
