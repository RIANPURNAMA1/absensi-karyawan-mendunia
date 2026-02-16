@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <div class="page-header mb-3">
        <div class="page-block">
            <div class="row align-items-center">

                <div class="col-md-6">
                    <div class="page-header-title">
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
                        <li class="breadcrumb-item active">Data Karyawan</li>
                    </ul>

                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan"
                        style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); 
           border: none; 
           padding: 8px 16px; 
           font-weight: 500; 
           box-shadow: 0 2px 6px rgba(30, 60, 114, 0.3);
           transition: all 0.3s ease;">
                        <i class="ph ph-plus-circle"></i> Tambah Karyawan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('karyawan.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Cabang</label>
                    <select name="cabang_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Semua Cabang --</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Divisi</label>
                    <select name="divisi_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Semua Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ph ph-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-light border w-100">
                        <i class="ph ph-arrow-counter-clockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h5>Daftar Karyawan</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive p-5">
                <table class="table align-middle mb-0" id="karyawanTable">
                    <thead class="bg-blue-700">
                        <tr>
                            <th class="text-center text-white" width="50">NO</th>
                            <th class="text-center text-white">FOTO</th>
                            <th class="text-center text-white">KARYAWAN</th>
                            <th class="text-center text-white">CABANG</th>
                            <th class="text-center text-white">DEPARTEMEN</th>
                            <th class="text-center text-white">JABATAN</th>
                            <th class="text-center text-white">L/P</th>
                            <th class="text-center text-white">STATUS</th>
                            <th class="text-center text-white">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawan as $index => $k)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ $k->foto_profil && file_exists(public_path('uploads/foto_profil/' . $k->foto_profil))
                                        ? asset('uploads/foto_profil/' . $k->foto_profil)
                                        : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png' }}"
                                        class="rounded-circle shadow-sm" width="40" height="40"
                                        style="object-fit: cover" alt="Foto {{ $k->name }}">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $k->name }}</div>
                                    <small class="text-muted">{{ $k->nip }}</small>
                                </td>
                                <td>
                                    @if ($k->cabang && $k->cabang->count() > 0)
                                        @foreach ($k->cabang as $c)
                                            <span class="badge bg-light text-dark border mb-1">
                                                {{ $c->nama_cabang }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $k->divisi?->nama_divisi ?? '-' }}</td>
                                <td>{{ $k->jabatan }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $k->jenis_kelamin == 'L' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' }}">
                                        {{ $k->jenis_kelamin }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($k->status_kerja === 'TETAP')
                                        <span class="badge bg-success">Tetap</span>
                                    @elseif ($k->status_kerja === 'KONTRAK')
                                        <span class="badge bg-warning text-dark">Kontrak</span>
                                    @else
                                        <span class="badge bg-info">Magang</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('karyawan.show', $k->id) }}" class="btn btn-sm btn-info"
                                            title="Detail">
                                            <i class="ph ph-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                            onclick="editKaryawan(
                                '{{ $k->id }}',
                                '{{ $k->nik }}',
                                '{{ $k->nip }}',
                                '{{ $k->name }}',
                                '{{ $k->jabatan }}',
                                '{{ $k->pendidikan_terakhir }}',
                                '{{ $k->divisi_id }}',
                                '{{ $k->cabang_id }}',
                                '{{ $k->shift_id }}',
                                '{{ $k->status_kerja }}',
                                '{{ $k->no_hp }}',
                                '{{ $k->email }}',
                                '{{ $k->tanggal_masuk }}',
                                '{{ $k->tempat_lahir }}',
                                '{{ $k->tanggal_lahir }}',
                                '{{ $k->jenis_kelamin }}',
                                '{{ $k->agama }}',
                                '{{ $k->status_pernikahan }}',
                                '{{ $k->alamat }}'
                            )">
                                            <i class="ph ph-note-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="deleteKaryawan({{ $k->id }})">
                                            <i class="ph ph-trash"></i>
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

    {{-- modal tambah data --}}
    @include('karyawan.modal')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // Pastikan urutan parameter sama dengan tombol di Blade
        function editKaryawan(
            id, nik, nip, name, jabatan, pendidikan_terakhir, divisi_id, cabang_id, shift_id, status_kerja,
            no_hp, email, tanggal_masuk,
            tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_pernikahan,
            alamat
        ) {
            // Masukkan data ke input modal
            $('#edit_id').val(id);
            $('#edit_nik').val(nik); 
            $('#edit_nip').val(nip);
            $('#edit_name').val(name);
            $('#edit_jabatan').val(jabatan);
            $('#edit_pendidikan_terakhir').val(pendidikan_terakhir); 

            // Dropdown Relasi
            $('#edit_divisi').val(divisi_id);
            $('#edit_cabang').val(cabang_id);
            $('#edit_shift_id').val(shift_id);

            // Data Kepegawaian & Kontak
            $('#edit_status_kerja').val(status_kerja);
            $('#edit_no_hp').val(no_hp);
            $('#edit_email').val(email);
            $('#edit_tanggal_masuk').val(tanggal_masuk);

            // Field tambahan (Personal)
            $('#edit_tempat_lahir').val(tempat_lahir);
            $('#edit_tanggal_lahir').val(tanggal_lahir);
            $('#edit_jenis_kelamin').val(jenis_kelamin);
            $('#edit_agama').val(agama);
            $('#edit_status_pernikahan').val(status_pernikahan);

            $('#edit_alamat').val(alamat);

            // Ubah Action Form Update
            $('#formEditKaryawan').attr('action', '/karyawan/' + id);

            // Tampilkan modal
            $('#modalEditKaryawan').modal('show');
        }

        $(document).ready(function() {
            $('#karyawanTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="ph ph-file-xls me-1"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7] }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="ph ph-file-pdf me-1"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7] }
                    },
                    {
                        extend: 'print',
                        text: '<i class="ph ph-printer me-1"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7] }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: [0, 1, 8]
                    }
                ],
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

        // delete function
        function deleteKaryawan(id) {
            Swal.fire({
                title: 'Hapus karyawan?',
                text: 'Data yang dihapus tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/karyawan/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
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
                                text: 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection