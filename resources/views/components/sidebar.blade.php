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

           <!-- ABSENSI -->
           <li class="pc-item pc-caption">
               <label>Absensi</label>
           </li>
           <li class="pc-item">
               <a href="/absensi/masuk" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-camera"></i></span>
                   <span class="pc-mtext">Absen Masuk</span>
               </a>
           </li>
           <li class="pc-item">
               <a href="/absensi/pulang" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-sign-out"></i></span>
                   <span class="pc-mtext">Absen Pulang</span>
               </a>
           </li>
           <li class="pc-item">
               <a href="/absensi/riwayat" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-clock-counter-clockwise"></i></span>
                   <span class="pc-mtext">Riwayat Absensi</span>
               </a>
           </li>

           <!-- DATA KARYAWAN -->
           <li class="pc-item pc-caption">
               <label>Manajemen Karyawan</label>
           </li>
           <li class="pc-item">
               <a href="/karyawan" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-users"></i></span>
                   <span class="pc-mtext">Data Karyawan</span>
               </a>
           </li>
           <li class="pc-item">
               <a href="/shift" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-timer"></i></span>
                   <span class="pc-mtext">Shift Kerja</span>
               </a>
           </li>

           <!-- MONITORING -->
           <li class="pc-item pc-caption">
               <label>Monitoring</label>
           </li>
           <li class="pc-item">
               <a href="/monitoring/kehadiran" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-map-pin"></i></span>
                   <span class="pc-mtext">Monitoring Lokasi</span>
               </a>
           </li>
           <li class="pc-item">
               <a href="/monitoring/wajah" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-face-mask"></i></span>
                   <span class="pc-mtext">Verifikasi Wajah</span>
               </a>
           </li>

           <!-- LAPORAN -->
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
           </li>

           <!-- PENGATURAN -->
           <li class="pc-item pc-caption">
               <label>Pengaturan</label>
           </li>
           <li class="pc-item">
               <a href="/pengaturan/cabang" class="pc-link">
                   <span class="pc-micon"><i class="ph ph-buildings"></i></span>
                   <span class="pc-mtext">Cabang & Lokasi</span>
               </a>
           </li>
           <li class="pc-item">
               <a href="/pengaturan/akun" class="pc-link">
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
