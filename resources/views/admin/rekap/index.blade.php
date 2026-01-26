@extends('app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">


    <div class="container-fluid">

        <div class="page-header mb-3 d-flex justify-content-between">
            <h4 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>
                Rekapitulasi Absensi Bulanan
            </h4>


            <form method="GET" class="d-flex gap-2">
                <select name="bulan" class="form-control">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                    @endfor
                </select>

                <select name="tahun" class="form-control">
                    @for ($y = 2024; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endfor
                </select>

                <button class="btn btn-primary">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
            </form>
        </div>


        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rekapTable" class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Cabang</th>
                                <th>Hadir</th>
                                <th>Terlambat</th>
                                <th>Izin</th>
                                <th>Alpa</th>
                                <th>Pulang Lebih Awal</th>
                                <th>Total Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekap as $index => $r)
                                <tr class="text-center">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start">{{ $r->nama }}</td>
                                    <td>{{ $r->cabang}}</td>
                                    <td class="text-success fw-semibold">{{ $r->hadir }}</td>
                                    <td class="text-warning fw-semibold">{{ $r->terlambat }}</td>
                                    <td class="text-info fw-semibold">{{ $r->izin }}</td>
                                    <td class="text-danger fw-semibold">{{ $r->alpa }}</td>
                                    <td class="text-primary fw-semibold">{{ $r->pulang_awal }}</td>
                                    <td class="fw-bold">{{ $r->total_hadir }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>



    <script>
        $(document).ready(function() {
            $('#rekapTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm'
                    }
                ],
                pageLength: 10,
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "→",
                        previous: "←"
                    }
                }
            });
        });
    </script>
@endsection
