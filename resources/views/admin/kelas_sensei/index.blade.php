@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="m-0 text-dark fw-bold">Kelas Sensei</h4>
                    <p class="text-muted small mb-0">Daftar kelas yang dibuat oleh Sensei</p>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="row g-2 justify-content-md-end">
                            <div class="col-6 col-md-3">
                                <select name="user_id" class="form-select form-select-sm shadow-sm">
                                    <option value="">Semua Sensei</option>
                                    @foreach ($list_sensei as $sensei)
                                        <option value="{{ $sensei->id }}" {{ $user_id_selected == $sensei->id ? 'selected' : '' }}>
                                            {{ $sensei->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="status" class="form-select form-select-sm shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ $status_selected == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ $status_selected == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ $status_selected == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                                    <i class="ph ph-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card border rounded-3">
            <div class="card-body text-center py-5 text-muted">
                <i class="ph ph-books fs-1 opacity-50"></i>
                <p class="mb-0 mt-2">Belum ada data kelas sensei</p>
            </div>
        </div>
        @endif
    </div>
@endsection
