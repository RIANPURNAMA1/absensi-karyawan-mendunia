@extends('app')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4">
        <h3 class="fw-bold">Absensi Karyawan</h3>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Card Absensi -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body text-center">
            <h5 class="card-title mb-3">Absensi Hari Ini</h5>
            <p class="text-muted mb-3" id="jamSekarang"></p>

            <div class="d-flex justify-content-center gap-3">
                <button id="btnMasuk" class="btn btn-success btn-lg">
                    <i class="bi bi-door-open-fill"></i> Absen Masuk
                </button>
                <button id="btnPulang" class="btn btn-warning btn-lg">
                    <i class="bi bi-door-closed-fill"></i> Absen Pulang
                </button>
            </div>

            <div class="mt-3" id="statusAbsensi"></div>
        </div>
    </div>

    <!-- Riwayat Absensi -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Riwayat Absensi</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="riwayatAbsensi">
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

</div>


  <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Tampilkan jam sekarang
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('jamSekarang').innerText = 'Waktu Sekarang: ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Load riwayat absensi
    function loadHistory() {
        $.get("{{ route('absensi.history') }}", function(res) {
            let html = '';
            res.forEach(item => {
                html += `
                    <tr>
                        <td>${item.tanggal}</td>
                        <td>${item.jam_masuk ?? '-'}</td>
                        <td>${item.jam_keluar ?? '-'}</td>
                        <td>${item.status ?? '-'}</td>
                        <td>${item.keterangan ?? '-'}</td>
                    </tr>
                `;
            });
            $('#riwayatAbsensi').html(html);
        });
    }

    loadHistory();

    // Tombol Absen Masuk
    $('#btnMasuk').click(function() {
        $.post("{{ route('absensi.masuk') }}", {_token: "{{ csrf_token() }}"}, function(res) {
            Swal.fire('Berhasil', res.message, 'success');
            loadHistory();
        }).fail(function(xhr){
            Swal.fire('Gagal', xhr.responseJSON.message, 'error');
        });
    });

    // Tombol Absen Pulang
    $('#btnPulang').click(function() {
        $.post("{{ route('absensi.pulang') }}", {_token: "{{ csrf_token() }}"}, function(res) {
            Swal.fire('Berhasil', res.message, 'success');
            loadHistory();
        }).fail(function(xhr){
            Swal.fire('Gagal', xhr.responseJSON.message, 'error');
        });
    });
</script>
@endsection
