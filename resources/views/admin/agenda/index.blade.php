@extends('app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Data Agenda Karyawan</h4>
                <p class="text-muted small mb-0">Riwayat agenda harian seluruh staf</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <select name="cabang_id" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" {{ $cabang_id == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="divisi_id" class="form-select">
                            <option value="">Semua Divisi</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}" {{ $divisi_id == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.agenda.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover" id="agendaTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Cabang</th>
                                <th>Divisi</th>
                                <th>Keterangan</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Jam Absen</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $index => $agenda)
                            <tr>
                                <td class="text-center">{{ $index + 1 + ($agendas->currentPage() - 1) * $agendas->perPage() }}</td>
                                <td>{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $agenda->user->name ?? '-' }}</div>
                                </td>
                                <td>
                                    @forelse($agenda->user->cabang as $cabang)
                                        <span class="badge bg-info me-1">{{ $cabang->nama_cabang }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>{{ $agenda->user->divisi->nama_divisi ?? '-' }}</td>
                                <td>{{ $agenda->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    @if($agenda->foto)
                                    <img src="{{ asset('uploads/agenda/'.$agenda->foto) }}" 
                                        class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($agenda->jam_absen_masuk)
                                    <span class="text-success">{{ \Carbon\Carbon::parse($agenda->jam_absen_masuk)->format('H:i') }}</span>
                                    @endif
                                    @if($agenda->jam_absen_keluar)
                                    <span class="text-danger">- {{ \Carbon\Carbon::parse($agenda->jam_absen_keluar)->format('H:i') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($agenda->status_absen == 'hadir' && $agenda->jam_absen_keluar)
                                    <span class="badge bg-success">Selesai</span>
                                    @elseif($agenda->status_absen == 'hadir')
                                    <span class="badge bg-primary">Hadir</span>
                                    @else
                                    <span class="badge bg-secondary">Terjadwal</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Tidak ada data agenda</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $agendas->appends(['cabang_id' => $cabang_id, 'divisi_id' => $divisi_id, 'start_date' => $start_date, 'end_date' => $end_date])->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#agendaTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                ordering: false,
                dom: 't'
            });
        });
    </script>
@endsection