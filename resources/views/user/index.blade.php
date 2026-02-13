@extends('app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    
    <div class="container-fluid">
        <div class="page-header mb-3">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h5>Data User Karyawan</h5>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
                        <button id="downloadPdf" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                            <i class="ph ph-file-pdf"></i> Download PDF
                        </button>

                        <ul class="breadcrumb mb-0 ms-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="ph ph-house"></i> Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Data Karyawan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Daftar Akun Login Karyawan</h5>
                <small class="text-muted">Hanya menampilkan data dengan role karyawan</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle table-hover mb-0" id="userTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Nama Karyawan</th>
                                <th style="width: 30%">Email Login</th>
                                <th style="width: 25%">Password (Default)</th>
                                <th class="no-export text-center" style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                {{-- Filter: Hanya tampilkan role karyawan --}}
                                @if($user->role == 'KARYAWAN')
                                <tr id="row-{{ $user->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary px-3 py-2 fs-6">
                                            <code>12345678</code>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-icon btn-link-danger btn-sm deleteUser" 
                                                data-id="{{ $user->id }}" 
                                                title="Hapus User">
                                            <i class="ph ph-trash fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.0/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi DataTable
            const table = $('#userTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [2, 3] } // Password & Aksi tidak bisa disortir
                ],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Karyawan tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ Karyawan",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // 2. Logika Download PDF (Client-side)
            $('#downloadPdf').click(function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                // Judul PDF
                doc.setFontSize(18);
                doc.setTextColor(40);
                doc.text("Laporan Akun Karyawan", 14, 20);
                
                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text("Daftar email login dan password default karyawan.", 14, 27);
                doc.text("Dicetak pada: " + new Date().toLocaleString('id-ID'), 14, 33);

                // Export tabel ke PDF
                doc.autoTable({
                    html: '#userTable',
                    startY: 40,
                    // Filter kolom: Hanya ambil Nama, Email, dan Password (index 0, 1, 2)
                    columns: [
                        { header: 'Nama Karyawan', dataKey: 0 },
                        { header: 'Email Login', dataKey: 1 },
                        { header: 'Password Default', dataKey: 2 },
                    ],
                    headStyles: {
                        fillColor: [46, 204, 113], // Hijau Emerald
                        textColor: 255,
                        fontStyle: 'bold'
                    },
                    bodyStyles: { textColor: 50 },
                    alternateRowStyles: { fillColor: [245, 245, 245] },
                    margin: { top: 40 },
                });

                doc.save('Data_Akun_Karyawan_' + new Date().getTime() + '.pdf');
            });

            // 3. Logika Delete User
            $(document).on('click', '.deleteUser', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Hapus Akses Karyawan?',
                    text: "User ini tidak akan bisa login ke sistem lagi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Akun',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/daftar-user/delete/' + id, // Sesuaikan route Anda
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                row.fadeOut(400, function() {
                                    table.row(row).remove().draw();
                                });
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Akun karyawan telah dihapus.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection