@extends('app')
@section('content')
    <div class="row">
        <!-- Total Karyawan -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body">
                    <h6 class="text-white">Total Karyawan</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-users float-start"></i>
                        <span>{{ $totalKaryawan ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Aktif<span class="float-end">{{ $karyawanAktif ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #134e5e 0%, #71b280 100%);">
                <div class="card-body">
                    <h6 class="text-white">Hadir Hari Ini</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-check-circle float-start"></i>
                        <span>{{ $hadirHariIni ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">On Time<span class="float-end">{{ $tepatWaktu ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #b79891 0%, #94716b 100%);">
                <div class="card-body">
                    <h6 class="text-white">Terlambat</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-clock float-start"></i>
                        <span>{{ $terlambat ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Tidak Hadir -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);">
                <div class="card-body">
                    <h6 class="text-white">Tidak Hadir</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-x-circle float-start"></i>
                        <span>{{ $tidakHadir ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Project Aktif -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);">
                <div class="card-body">
                    <h6 class="text-white">Project Aktif</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-kanban float-start"></i>
                        <span>{{ $projectAktif ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Selesai<span class="float-end">{{ $projectSelesai ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Total Task -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #403b4a 0%, #5c6670 100%);">
                <div class="card-body">
                    <h6 class="text-white">Total Task</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-check-square float-start"></i>
                        <span>{{ $totalTask ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Progress<span class="float-end">{{ $taskProgress ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Izin & Cuti -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #36454f 0%, #556b7a 100%);">
                <div class="card-body">
                    <h6 class="text-white">Izin & Cuti</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-file-text float-start"></i>
                        <span>{{ $izinCuti ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Pending<span class="float-end">{{ $izinPending ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Artikel Blog -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #1a4068 0%, #2e7d9a 100%);">
                <div class="card-body">
                    <h6 class="text-white">Artikel Blog</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-article float-start"></i>
                        <span>{{ $totalArtikel ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Published<span class="float-end">{{ $artikelPublished ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Divisi Aktif -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #3d5a80 0%, #627e94 100%);">
                <div class="card-body">
                    <h6 class="text-white">Divisi Aktif</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-buildings float-start"></i>
                        <span>{{ $totalDivisi ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Total Divisi</p>
                </div>
            </div>
        </div>

        <!-- Shift Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);">
                <div class="card-body">
                    <h6 class="text-white">Shift Hari Ini</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-timer float-start"></i>
                        <span>{{ $shiftAktif ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Karyawan Shift</p>
                </div>
            </div>
        </div>

        <!-- Lembur Bulan Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #485563 0%, #29323c 100%);">
                <div class="card-body">
                    <h6 class="text-white">Lembur Bulan Ini</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-clock-afternoon float-start"></i>
                        <span>{{ $totalLembur ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Jam Lembur<span class="float-end">{{ $jamLembur ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <!-- Cabang & Lokasi -->
        <div class="col-md-6 col-xl-3">
            <div class="card order-card" style="background: linear-gradient(135deg, #2b5876 0%, #4e8098 100%);">
                <div class="card-body">
                    <h6 class="text-white">Cabang & Lokasi</h6>
                    <h2 class="text-end text-white">
                        <i class="ph ph-map-pin-line float-start"></i>
                        <span>{{ $totalCabang ?? 0 }}</span>
                    </h2>
                    <p class="m-b-0 text-white">Total Cabang</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-7">
            <div class="card">
                <div class="card-header">
                    <h5>Peta Lokasi Absensi</h5>
                </div>
                <div class="card-body">
                    <div id="world-map-markers" class="set-map" style="height: 365px"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-5">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5>Statistik Kehadiran Karyawan</h5>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown">
                            <i class="material-icons-two-tone f-18">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Detail</a>
                            <a class="dropdown-item" href="#">Export</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="avtar avtar-s bg-light-primary flex-shrink-0">
                            <i class="ph ph-user-check f-20"></i>
                        </div>
                        <div class="media-body ms-3">
                            <p class="mb-0 text-muted">Total Kehadiran Bulan Ini</p>
                            <h5 class="mb-0">128 Kehadiran</h5>
                        </div>
                    </div>
                    <div class="mt-4 text-center text-muted">
                        <div class="mt-4">
                            <canvas id="attendanceMonthlyChart" height="80"></canvas>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Ringkasan Kehadiran -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="media align-items-center">
                                <div class="avtar avtar-s flex-shrink-0" style="background: rgba(40, 167, 69, 0.7);">
                                    <i class="ph ph-check-circle f-20 text-white"></i>
                                </div>
                                <div class="media-body ms-2">
                                    <p class="mb-0 text-muted">Hadir</p>
                                    <h6 class="mb-0">96</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="media align-items-center">
                                <div class="avtar avtar-s flex-shrink-0" style="background: rgba(255, 193, 7, 0.7);">
                                    <i class="ph ph-clock f-20 text-white"></i>
                                </div>
                                <div class="media-body ms-2">
                                    <p class="mb-0 text-muted">Terlambat</p>
                                    <h6 class="mb-0">32</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="media align-items-center">
                                <div class="avtar avtar-s flex-shrink-0" style="background: rgba(220, 53, 69, 0.7);">
                                    <i class="ph ph-x-circle f-20 text-white"></i>
                                </div>
                                <div class="media-body ms-2">
                                    <p class="mb-0 text-muted">Tidak Hadir</p>
                                    <h6 class="mb-0">12</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- table riwayat --}}
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Riwayat Absensi Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Foto Wajah</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam keluar</th>
                                    <th>Status</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi as $a)
                                    <tr>
                                        <td>
                                            <img src="{{ $a->foto_profil ? asset('storage/foto-karyawan/' . $a->foto_profil) : '../assets/images/avatar/avatar-1.jpg' }}"
                                                class="rounded-circle" width="40" height="40" alt="foto">
                                        </td>
                                        <td>{{ $a->nip }}</td>
                                        <td>{{ $a->name }}</td>
                                        <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('Y-m-d H:i') : '-' }}
                                        <td>{{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_masuk)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'HADIR' => 'bg-success',
                                                    'TERLAMBAT' => 'bg-warning',
                                                    'IZIN' => 'bg-primary',
                                                    'ALPHA' => 'bg-danger',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClass[$a->status] ?? 'bg-secondary' }}">
                                                {{ $a->status ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $a->lokasi ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada absensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Recent Orders end -->
    </div>
@endsection
