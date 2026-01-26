@extends('app')

@section('content')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <div class="container-fluid">

        <div class="page-header mb-3 d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-map-marker-alt"></i> Monitoring Lokasi Absensi</h4>


            <form method="GET">
                <input type="date" name="tanggal" value="{{ $tanggal ?? now()->format('Y-m-d') }}" class="form-control"
                    onchange="this.form.submit()">
            </form>
        </div>

        <!-- MAP -->
        <div id="map" style="height: 500px; border-radius: 12px;"></div>



        <!-- TABEL ABSENSI -->
        <div class="card mt-3">

            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle" id="monitorTable">
                        <thead class="table-light">
                            <tr>
                                <th>Karyawan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Cabang</th>
                                <th>Lokasi Terekam</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensis as $a)
                                <tr>
                                    <td>{{ $a->user->name }}</td>
                                    <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</td>
                                    <td>{{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i') : '-' }}
                                    </td>
                                    <td>{{ $a->cabang->nama_cabang ?? '-' }}</td>
                                    <td>
                                        @if ($a->lat_masuk)
                                            <span class="badge bg-success">Lokasi Masuk</span>
                                        @endif
                                        @if ($a->lat_pulang)
                                            <span class="badge bg-primary">Lokasi Pulang</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($a->foto_masuk)
                                            <img src="{{ asset('storage/' . $a->foto_masuk) }}" width="40"
                                                class="rounded mb-1"><br>
                                        @endif
                                        @if ($a->foto_pulang)
                                            <img src="{{ asset('storage/' . $a->foto_pulang) }}" width="40"
                                                class="rounded">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // ===== DataTables =====
        $(document).ready(function() {

            $('#monitorTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "→",
                        previous: "←"
                    },
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });

        // ===== Leaflet Map =====
        var map = L.map('map').setView([-6.2, 106.8], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        var masukIcon = new L.Icon({
            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            iconSize: [32, 32]
        });
        var pulangIcon = new L.Icon({
            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            iconSize: [32, 32]
        });

        @foreach ($absensis as $a)
            @if ($a->lat_masuk && $a->long_masuk)
                L.marker([{{ $a->lat_masuk }}, {{ $a->long_masuk }}], {
                        icon: masukIcon
                    })
                    .addTo(map)
                    .bindPopup(`<b>{{ $a->user->name }}</b><br>Absen Masuk<br>Jam: {{ $a->jam_masuk ?? '-' }}`);
            @endif
            @if ($a->lat_pulang && $a->long_pulang)
                L.marker([{{ $a->lat_pulang }}, {{ $a->long_pulang }}], {
                        icon: pulangIcon
                    })
                    .addTo(map)
                    .bindPopup(`<b>{{ $a->user->name }}</b><br>Absen Pulang<br>Jam: {{ $a->jam_keluar ?? '-' }}`);
            @endif
        @endforeach
    </script>
@endsection
