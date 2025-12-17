@extends('app')

@section('content')
    <div class="">
        <!-- PAGE HEADER + BREADCRUMB -->
        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h5>Data User Karyawan</h5>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                        <ul class="breadcrumb mb-0 me-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="ph ph-house"></i> Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">Master Data</li>
                            <li class="breadcrumb-item active">Data User</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="card table-card">
            <div class="card-header">
                <h5>Daftar User Karyawan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-5">
                    <table class="table align-middle mb-0" id="karyawanTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Email User</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr id="row-{{ $user->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger deleteUser" data-id="{{ $user->id }}">
                                            <i class="ph ph-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data user karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pastikan SweetAlert2 sudah di-include -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            $('.deleteUser').click(function() {
                let id = $(this).data('id');

                // SweetAlert konfirmasi
                Swal.fire({
                    title: 'Yakin ingin menghapus user ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX delete
                        $.ajax({
                            url: 'daftar-user/delete/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                // Hapus baris dari tabel
                                $('#row-' + id).fadeOut(500, function() {
                                    $(this).remove();
                                });

                                // Notifikasi sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                let res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: res && res.message ? res.message :
                                        'Terjadi kesalahan saat menghapus user.'
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
