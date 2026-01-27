@extends('app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-3">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="page-header-title">
                        <h4 class="m-b-10">Manajemen Akun Admin</h4>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
                        <i class="ph ph-user-plus me-1"></i> Tambah Akun
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-header">
            <h5>Daftar HR & Manager</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama & Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $admin->name }}</span><br>
                                <small class="text-muted">{{ $admin->email }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $admin->role == 'MANAGER' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $admin->role }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $admin->status == 'AKTIF' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $admin->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" onclick="editAdmin({{ $admin }})">
                                    <i class="ph ph-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAdmin({{ $admin->id }})">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('pengaturan.user.modal')
@include('pengaturan.user.edit')

<script>
   function editAdmin(data) {
    // 1. Isi data ke dalam input modal
    $('#edit_id').val(data.id);
    $('#edit_name').val(data.name);
    $('#edit_email').val(data.email);
    $('#edit_role').val(data.role);
    $('#edit_status').val(data.status);

    // 2. Buat URL route secara dinamis
    // Kita ambil template URL dari Laravel, lalu ganti placeholder :id dengan ID asli
    let url = "{{ route('pengaturan.update', ':id') }}";
    url = url.replace(':id', data.id);

    // 3. Update atribut action pada form
    $('#formEditAdmin').attr('action', url);

    // 4. Tampilkan modal
    $('#modalEditAdmin').modal('show');
}

    function deleteAdmin(id) {
        Swal.fire({
            title: 'Hapus akun?',
            text: 'Akses login orang ini akan dicabut sepenuhnya',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pengaturan/users/' + id,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function() { location.reload(); }
                });
            }
        });
    }
</script>
@endsection