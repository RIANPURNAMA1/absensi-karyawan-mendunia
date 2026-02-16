 <div class="navbar-content">
     <ul class="pc-navbar">

         <!-- DASHBOARD -->
         <li class="pc-item pc-caption">
             <label>Dashboard</label>
         </li>
         <li class="pc-item">
             <a href="/" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-gauge"></i></span>
                 <span class="pc-mtext">Dashboard</span>
             </a>
         </li>

         <li class="pc-item pc-caption">
             <label>Manajemen Karyawan</label>
         </li>
         <li class="pc-item pc-hasmenu">
             <a href="#!" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-users-three"></i></span>
                 <span class="pc-mtext">Manajemen Karyawan</span>
                 <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
             </a>
             <ul class="pc-submenu">
                 <li class="pc-item">
                     <a href="/karyawan" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-users"></i></span>
                         <span class="pc-mtext">Data Karyawan</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/divisi" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-buildings"></i></span>
                         <span class="pc-mtext">Divisi</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/cabang" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-map-pin"></i></span>
                         <span class="pc-mtext">Cabang / Lokasi</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/shift" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-timer"></i></span>
                         <span class="pc-mtext">Shift Kerja</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/daftar-user" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-list"></i></span>
                         <span class="pc-mtext">Daftar User Karyawan</span>
                     </a>
                 </li>
             </ul>
         </li>

         <!-- ABSENSI -->
         <li class="pc-item pc-caption">
             <label>Absensi</label>
         </li>
         <li class="pc-item pc-hasmenu">
             <a href="#!" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-calendar-check"></i></span>
                 <span class="pc-mtext">Sistem Absensi</span>
                 <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
             </a>
             <ul class="pc-submenu">
                 <li class="pc-item">
                     <a href="/data-kehadiran" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-clipboard-text"></i></span>
                         <span class="pc-mtext">Data Kehadiran</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/izin-cuti" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-file-text"></i></span>
                         <span class="pc-mtext">Izin & Cuti</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/approval-lembur" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-clock-countdown"></i></span>
                         <span class="pc-mtext">Approval Lembur</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/hari-libur" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-file-text"></i></span>
                         <span class="pc-mtext">Hari Libur </span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/rekap-absensi" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-chart-bar"></i></span>
                         <span class="pc-mtext">Rekap Absensi</span>
                     </a>
                 </li>
                 <li class="pc-item">
                     <a href="/monitoring-lokasi" class="pc-link">
                         <span class="pc-micon"><i class="ph ph-map-pin"></i></span>
                         <span class="pc-mtext">Monitoring Lokasi</span>
                     </a>
                 </li>
             </ul>
         </li>


         @php
             $roleUser = auth()->user()->role; // ambil role user yang login
         @endphp


         @if ($roleUser !== 'HR')
             <li class="pc-item pc-caption">
                 <label>Monitoring & Project</label>
             </li>
             <li class="pc-item pc-hasmenu">
                 <a href="#!" class="pc-link">
                     <span class="pc-micon"><i class="ph ph-kanban"></i></span>
                     <span class="pc-mtext">Project Management</span>
                     <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                 </a>
                 <ul class="pc-submenu">
                     <li class="pc-item">
                         <a href="/project/dashboard" class="pc-link">
                             <span class="pc-micon"><i class="ph ph-chart-pie"></i></span>
                             <span class="pc-mtext">Dashboard Project</span>
                         </a>
                     </li>
                     <li class="pc-item">
                         <a href="/project/board" class="pc-link">
                             <span class="pc-micon"><i class="ph ph-columns"></i></span>
                             <span class="pc-mtext">Board Project</span>
                         </a>
                     </li>
                     <li class="pc-item">
                         <a href="/project/task" class="pc-link">
                             <span class="pc-micon"><i class="ph ph-check-square"></i></span>
                             <span class="pc-mtext">Daftar Task</span>
                         </a>
                     </li>
                     <li class="pc-item">
                         <a href="/project/progress" class="pc-link">
                             <span class="pc-micon"><i class="ph ph-trend-up"></i></span>
                             <span class="pc-mtext">Progress Pekerjaan</span>
                         </a>
                     </li>
                     <li class="pc-item">
                         <a href="/project/timeline" class="pc-link">
                             <span class="pc-micon"><i class="ph ph-chart-line"></i></span>
                             <span class="pc-mtext">Timeline Project</span>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif


         {{-- <!-- LAPORAN -->
         <li class="pc-item pc-caption">
             <label>Laporan</label>
         </li>
         <li class="pc-item">
             <a href="/laporan/harian" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-file-text"></i></span>
                 <span class="pc-mtext">Laporan Harian</span>
             </a>
         </li>
         <li class="pc-item">
             <a href="/laporan/bulanan" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-calendar"></i></span>
                 <span class="pc-mtext">Laporan Bulanan</span>
             </a>
         </li> --}}

         @if ($roleUser !== 'HR')
             <!-- BLOG WEBSITE -->
             <li class="pc-item pc-caption">
                 <label>Konten Website</label>
             </li>
             <li class="pc-item pc-hasmenu">
                 <a href="#!" class="pc-link">
                     <span class="pc-micon"><i class="ph ph-article"></i></span>
                     <span class="pc-mtext">Blog Website</span>
                     <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                 </a>
                 <ul class="pc-submenu">
                     <li class="pc-item">
                         <a href="/blog/artikel" class="pc-link">Daftar Artikel</a>
                     </li>
                     <li class="pc-item">
                         <a href="/blog/kategori" class="pc-link">Kategori</a>
                     </li>
                     <li class="pc-item">
                         <a href="/blog/tambah" class="pc-link">Buat Artikel Baru</a>
                     </li>
                     <li class="pc-item">
                         <a href="/blog/komentar" class="pc-link">Kelola Komentar</a>
                     </li>
                 </ul>
             </li>
         @endif

         <!-- PENGATURAN -->
         <li class="pc-item pc-caption">
             <label>Pengaturan</label>
         </li>

         <li class="pc-item">
             <a href="/pengaturan" class="pc-link">
                 <span class="pc-micon"><i class="ph ph-user-gear"></i></span>
                 <span class="pc-mtext">Manajemen Akun</span>
             </a>
         </li>

         <!-- LOGOUT -->
         <li class="pc-item pc-caption">
             <label>Akun</label>
         </li>
         <li class="pc-item">
             <a href="#" id="btnLogout" class="pc-link text-danger">
                 <span class="pc-micon"><i class="ph ph-sign-out"></i></span>
                 <span class="pc-mtext">Logout</span>
             </a>
         </li>
     </ul>
 </div>
 <!-- jQuery -->
 <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 <!-- SweetAlert2 -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script>
     $(document).ready(function() {

         $('#btnLogout').on('click', function(e) {
             e.preventDefault();

             Swal.fire({
                 title: 'Apakah Anda yakin?',
                 text: "Anda akan keluar dari sistem.",
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#3085d6',
                 cancelButtonColor: '#d33',
                 confirmButtonText: 'Ya, logout!',
                 cancelButtonText: 'Batal'
             }).then((result) => {
                 if (result.isConfirmed) {

                     $.ajax({
                         url: "{{ route('logout') }}",
                         type: "POST",
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         success: function(res) {
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Berhasil',
                                 text: res.message,
                                 timer: 1500,
                                 showConfirmButton: false
                             });

                             setTimeout(() => {
                                 window.location.href = res.redirect;
                             }, 1500);
                         },
                         error: function() {
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Gagal',
                                 text: 'Logout gagal'
                             });
                         }
                     });

                 }
             });
         });

     });
 </script>
