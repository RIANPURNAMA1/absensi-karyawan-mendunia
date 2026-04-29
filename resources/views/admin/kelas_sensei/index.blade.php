@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">

                    <!-- Header -->
                    <div class="col-12 col-md-4">
                        <h4 class="mb-1 fw-bold text-dark">Kelas Sensei</h4>
                        <p class="text-muted small mb-0">
                            Daftar kelas yang dibuat oleh Sensei
                        </p>
                    </div>

                    <!-- Filter -->
                    <div class="col-12 col-md-8">
                        <form method="GET" action="">
                            <div class="row g-2 justify-content-md-end">

                                <div class="col-12 col-sm-6 col-md-4">
                                    <select name="user_id" class="form-select shadow-sm">
                                        <option value="">Semua Sensei</option>
                                        @foreach ($list_sensei as $sensei)
                                            <option value="{{ $sensei->id }}"
                                                {{ $user_id_selected == $sensei->id ? 'selected' : '' }}>
                                                {{ $sensei->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-sm-4 col-md-3">
                                    <select name="status" class="form-select shadow-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ $status_selected == 'aktif' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="selesai" {{ $status_selected == 'selesai' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                        <option value="dibatalkan" {{ $status_selected == 'dibatalkan' ? 'selected' : '' }}>
                                            Dibatalkan
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-2 col-md-2">
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                        <i class="ph ph-magnifying-glass me-1"></i> Filter
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>

        @if($kelas->count() > 0)
        <div class="card border rounded-3 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama Kelas</th>
                                <th>Level</th>
                                <th>Nama Sensei</th>
                                <th>Tanggal Mulai</th>
                                 <th>Tanggal Selesai</th>
                                 <th class="text-center">Total Pertemuan</th>
                                 <th class="text-center">Absen Terisi</th>
                                 <th>Status</th>
                                 <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($kelas as $kelasItem)
                                <tr>
                                 <td class="text-center">{{ $no++ }}</td>
                                     <td class="fw-bold text-primary">{{ $kelasItem->nama_kelas }}</td>
                                     <td><span class="badge bg-secondary">{{ $kelasItem->level }}</span></td>
                                     <td>{{ $kelasItem->user->name ?? '-' }}</td>
                                     <td>{{ \Carbon\Carbon::parse($kelasItem->tanggal_mulai)->format('d M Y') }}</td>
                                     <td>{{ \Carbon\Carbon::parse($kelasItem->tanggal_selesai)->format('d M Y') }}</td>
                                     <td class="text-center">{{ $kelasItem->total_pertemuan }}</td>
                                     <td class="text-center">{{ $kelasItem->jumlah_absen }}</td>
                                     <td>
                                         @php
                                             $badgeClass = [
                                                 'aktif' => 'success',
                                                 'selesai' => 'primary',
                                                 'dibatalkan' => 'danger',
                                             ];
                                         @endphp
                                         <span class="badge bg-{{ $badgeClass[$kelasItem->status] ?? 'secondary' }}">
                                             {{ ucfirst($kelasItem->status) }}
                                         </span>
                                     </td>
                                      <td class="text-center">
                                          <form method="POST" action="{{ route('kelas-sensei.destroy', $kelasItem->id) }}" class="delete-form-{{ $kelasItem->id }}">
                                              @csrf
                                              @method('DELETE')
                                              <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $kelasItem->id }}">
                                                  <i class="ph ph-trash"></i>
                                              </button>
                                          </form>
                                      </td>
                                 </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

         </div>
@endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete buttons with SweetAlert
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const classId = this.getAttribute('data-id');
                    const form = document.querySelector(`.delete-form-${classId}`);
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Kelas ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
