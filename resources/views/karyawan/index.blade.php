@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

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

    <!-- CARD TABLE -->
    <div class="card table-card">
        <div class="card-header">
            <h5>Daftar Karyawan</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive p-5">
                <table class="table align-middle mb-0" id="karyawanTable">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Cabang</th>
                            <th>Shift</th>
                            <th>No HP</th>
                            <th>Email</th>
                            <th>Status Kerja</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawan as $k)
                            <tr>
                                <td>
                                    <img src="{{ $k->foto_profil && file_exists(public_path('storage/foto-karyawan/' . $k->foto_profil))
                                        ? asset('storage/foto-karyawan/' . $k->foto_profil)
                                        : asset('assets/images/avatar/avatar-1.jpg') }}"
                                        class="rounded-circle" width="40" height="40" style="object-fit: cover">
                                </td>

                                <td>{{ $k->nip }}</td>
                                <td>{{ $k->name }}</td>
                                <td>{{ $k->jabatan }}</td>
                                <td>{{ $k->divisi->nama_divisi }}</td>
                                <td>{{ $k->cabang->nama_cabang }}</td>

                                <td class="text-center">
                                    @if ($k->shift)
                                        <span class="badge bg-light text-primary border border-primary px-2 py-1 mb-1">
                                            <i class="ph ph-clock me-1"></i> {{ $k->shift->nama_shift }}
                                        </span>
                                        <br>
                                        <small class="text-muted fw-bold">
                                            {{ \Carbon\Carbon::parse($k->shift->jam_masuk)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($k->shift->jam_pulang)->format('H:i') }}
                                        </small>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">
                                            <i class="ph ph-warning-circle me-1"></i> Belum Set
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $k->no_hp }}</td>
                                <td>{{ $k->email }}</td>

                                <td>
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
                                        <button type="button" class="btn btn-sm btn-warning"
                                            onclick="editKaryawan(
                                                '{{ $k->id }}', 
                                                '{{ $k->nip }}', 
                                                '{{ $k->name }}', 
                                                '{{ $k->jabatan }}', 
                                                '{{ $k->divisi_id }}', 
                                                '{{ $k->cabang_id }}', 
                                                '{{ $k->no_hp }}', 
                                                '{{ $k->email }}', 
                                                '{{ $k->tanggal_masuk }}', 
                                                '{{ $k->status_kerja }}', 
                                                '{{ $k->alamat }}',
                                                '{{ $k->shift_id }}'
                                            )">
                                            <i class="ph ph-note-pencil"></i>
                                        </button>

                                        <a href="{{ route('karyawan.show', $k->id) }}" class="btn btn-sm btn-info">
                                            <i class="ph ph-eye"></i>
                                        </a>

                                        <button class="btn btn-sm btn-danger"
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // Pastikan urutan parameter sesuai dengan tombol di Blade
        function editKaryawan(id, nip, name, jabatan, divisi_id,cabang_id, no_hp, email, tanggal_masuk, status_kerja, alamat,
            shift_id) {

            // Reset form dan masukkan data ke input modal
            $('#edit_id').val(id);
            $('#edit_nip').val(nip);
            $('#edit_name').val(name);
            $('#edit_jabatan').val(jabatan);
            $('#edit_divisi').val(divisi_id);
            $('#edit_cabang').val(cabang_id);
            $('#edit_no_hp').val(no_hp);
            $('#edit_email').val(email);
            $('#edit_tanggal_masuk').val(tanggal_masuk);
            $('#edit_status_kerja').val(status_kerja);
            $('#edit_alamat').val(alamat);

            // PERBAIKAN: Gunakan variabel shift_id langsung dari parameter fungsi
            $('#edit_shift_id').val(shift_id);

            // Tampilkan modal
            $('#modalEditKaryawan').modal('show');
        }
        $(document).ready(function() {
            $('#karyawanTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [1, 'asc']
                ], // urutkan berdasarkan NIP
                columnDefs: [{
                        orderable: false,
                        targets: [0, 7]
                    } // kolom Foto & Aksi
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
