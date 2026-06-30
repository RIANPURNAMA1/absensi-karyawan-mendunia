@extends('app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0" style="font-weight: 700; font-size: 16px;">Data Siswa</h5>
            <small class="text-muted">Master data seluruh siswa</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                <i class="ph ph-plus-circle me-1"></i> Tambah
            </button>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-auto">
                <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                    <option value="NONAKTIF" {{ request('status') == 'NONAKTIF' ? 'selected' : '' }}>NONAKTIF</option>
                </select>
            </div>
            <div class="col-auto">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-dark"><i class="ph ph-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ph ph-arrow-counter-clockwise me-1"></i>Reset</a>
            </div>
        </div>
    </form>

    <div class="rounded-3">
        <div class="p-3 border-bottom" style="border-bottom-color: #f0f0f0 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-semibold" style="font-size: 13px;"></span>
                <span class="text-muted" style="font-size: 11px;">{{ $siswa->count() }} data</span>
            </div>
        </div>
        <div class="table-responsive">
            <table id="siswaTable" class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th scope="col">Siswa</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Shift</th>
                        <th scope="col" class="text-center">L/P</th>
                        <th scope="col">No. HP</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Akun</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswa as $s)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $s->foto && file_exists(public_path('uploads/siswa/' . $s->foto))
                                        ? asset('uploads/siswa/' . $s->foto)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($s->nama) . '&background=e5e7eb&color=6b7280&size=32' }}"
                                        class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                    <span class="fw-medium" style="font-size: 13px;">{{ $s->nama }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-info">{{ $s->kelasRelasi->nama_kelas ?? $s->kelas }}</span></td>
                            <td>
                                @if($s->shift)
                                <span class="badge bg-primary">{{ $s->shift->nama_shift }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="{{ $s->jenis_kelamin == 'L' ? 'text-primary' : 'text-danger' }} fw-semibold">{{ $s->jenis_kelamin ?? '-' }}</span>
                            </td>
                            <td class="text-muted">{{ $s->no_hp ?? '-' }}</td>
                            <td class="text-center">
                                @if ($s->status === 'AKTIF')
                                    <span class="badge bg-success">AKTIF</span>
                                @else
                                    <span class="badge bg-secondary">NONAKTIF</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($s->user_id)
                                    <span class="badge bg-success"><i class="ph ph-check"></i> Ada</span>
                                @else
                                    <button class="btn btn-sm btn-outline-primary buatkan-akun" data-id="{{ $s->id }}" data-nama="{{ $s->nama }}">
                                        <i class="ph ph-user-plus"></i>
                                    </button>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-sm btn-outline-warning edit-siswa"
                                        data-id="{{ $s->id }}"
                                        data-nama="{{ $s->nama }}"
                                        data-kelas-id="{{ $s->kelas_id }}"
                                        data-shift-id="{{ $s->shift_id }}"
                                        data-jk="{{ $s->jenis_kelamin }}"
                                        data-tempat-lahir="{{ $s->tempat_lahir }}"
                                        data-tgl-lahir="{{ $s->tanggal_lahir ? $s->tanggal_lahir->format('Y-m-d') : '' }}"
                                        data-agama="{{ $s->agama }}"
                                        data-alamat="{{ $s->alamat }}"
                                        data-no-hp="{{ $s->no_hp }}">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger hapus-siswa" data-id="{{ $s->id }}" data-nama="{{ $s->nama }}">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary toggle-status-siswa" data-id="{{ $s->id }}" data-nama="{{ $s->nama }}" data-status="{{ $s->status }}">
                                        <i class="ph ph-toggle"></i>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahSiswa">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Siswa</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select form-select-sm" required>
                                <option value="">- Pilih Kelas -</option>
                                @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select name="shift_id" class="form-select form-select-sm">
                                <option value="">- Pilih Shift -</option>
                                @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->nama_shift }} ({{ $shift->jam_masuk->format('H:i') }} - {{ $shift->jam_pulang->format('H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select form-select-sm">
                                <option value="">- Pilih -</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select form-select-sm">
                                <option value="">- Pilih -</option>
                                <option value="ISLAM">Islam</option>
                                <option value="KRISTEN">Kristen</option>
                                <option value="KATOLIK">Katolik</option>
                                <option value="HINDU">Hindu</option>
                                <option value="BUDDHA">Buddha</option>
                                <option value="KONGHUCU">Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditSiswa">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Siswa</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-select form-select-sm" required>
                                <option value="">- Pilih Kelas -</option>
                                @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select name="shift_id" id="edit_shift_id" class="form-select form-select-sm">
                                <option value="">- Pilih Shift -</option>
                                @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->nama_shift }} ({{ $shift->jam_masuk->format('H:i') }} - {{ $shift->jam_pulang->format('H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jk" class="form-select form-select-sm">
                                <option value="">- Pilih -</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tgl_lahir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Agama</label>
                            <select name="agama" id="edit_agama" class="form-select form-select-sm">
                                <option value="">- Pilih -</option>
                                <option value="ISLAM">Islam</option>
                                <option value="KRISTEN">Kristen</option>
                                <option value="KATOLIK">Katolik</option>
                                <option value="HINDU">Hindu</option>
                                <option value="BUDDHA">Buddha</option>
                                <option value="KONGHUCU">Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Buat Akun -->
<div class="modal fade" id="modalBuatAkun" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formBuatAkun">
                @csrf
                <input type="hidden" name="id" id="akun_siswa_id">
                <div class="modal-header">
                    <h6 class="modal-title">Buat Akun Login untuk <span id="akun_siswa_nama"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="text" name="password" class="form-control form-control-sm" value="siswa123" required>
                        <small class="text-muted">Default: siswa123</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#siswaTable').DataTable({
        paging: true,
        pageLength: 25,
        ordering: true,
        info: false,
        searching: false,
        lengthChange: false,
        columnDefs: [{ orderable: false, targets: [6, 7] }]
    });

    $('#formTambahSiswa').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("siswa.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                $('#modalTambahSiswa').modal('hide');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Gagal menyimpan data';
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    });

    $(document).on('click', '.edit-siswa', function() {
        var btn = $(this);
        $('#edit_id').val(btn.data('id'));
        $('#edit_nama').val(btn.data('nama'));
        $('#edit_kelas_id').val(btn.data('kelas-id'));
        $('#edit_shift_id').val(btn.data('shift-id'));
        $('#edit_jk').val(btn.data('jk'));
        $('#edit_tempat_lahir').val(btn.data('tempat-lahir'));
        $('#edit_tgl_lahir').val(btn.data('tgl-lahir'));
        $('#edit_agama').val(btn.data('agama'));
        $('#edit_alamat').val(btn.data('alamat'));
        $('#edit_no_hp').val(btn.data('no-hp'));
        $('#modalEditSiswa').modal('show');
    });

    $('#formEditSiswa').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            url: '/siswa/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                $('#modalEditSiswa').modal('hide');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Gagal mengupdate data';
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    });

    $(document).on('click', '.hapus-siswa', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus ' + nama + '?',
            text: 'Data siswa akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/siswa/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1500);
                    }
                });
            }
        });
    });

    $(document).on('click', '.toggle-status-siswa', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var status = $(this).data('status');
        var newStatus = status === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
        Swal.fire({
            title: 'Ubah status ' + nama + '?',
            text: 'Status akan menjadi ' + newStatus,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/siswa/' + id + '/toggle-status',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1500);
                    }
                });
            }
        });
    });

    $(document).on('click', '.buatkan-akun', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        $('#akun_siswa_id').val(id);
        $('#akun_siswa_nama').text(nama);
        $('#modalBuatAkun').modal('show');
    });

    $('#formBuatAkun').on('submit', function(e) {
        e.preventDefault();
        var id = $('#akun_siswa_id').val();
        var data = $(this).serialize();
        $.ajax({
            url: '/siswa/' + id + '/buatkan-akun',
            type: 'POST',
            data: data + '&_token={{ csrf_token() }}',
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Akun berhasil dibuat. Email: ' + res.data.email, timer: 3000, showConfirmButton: true });
                $('#modalBuatAkun').modal('hide');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Gagal membuat akun';
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    });
});
</script>
@endpush
