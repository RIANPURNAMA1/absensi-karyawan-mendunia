   <!-- MODAL TAMBAH KARYAWAN -->
   <div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
       <div class="modal-dialog modal-lg modal-dialog-centered">
           <div class="modal-content">

               <div class="modal-header" style="">
                   <h5 class="modal-title">Tambah Karyawan</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <form id="formTambahKaryawan" enctype="multipart/form-data">
                   @csrf

                   <div class="modal-body">
                       <div class="row g-3">

                           <!-- NIP -->
                           <div class="col-md-6">
                               <label class="form-label">NIP</label>
                               <input type="text" name="nip" class="form-control"
                                   placeholder="Otomatis akan terisi setelah simpan">
                           </div>

                           <!-- NAMA -->
                           <div class="col-md-6">
                               <label class="form-label">Nama Lengkap</label>
                               <input type="text" name="name" class="form-control" required>
                           </div>

                           <!-- JABATAN -->
                           <div class="col-md-6">
                               <label class="form-label">Jabatan</label>
                               <input type="text" name="jabatan" class="form-control" required>
                           </div>

                           <!-- DIVISI -->
                           <div class="col-md-6">
                               <label class="form-label">Divisi</label>
                               <select name="divisi_id" class="form-select" required>
                                   <option value="">-- Pilih Divisi --</option>
                                   @foreach ($divisi as $d)
                                       <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- CABANG -->
                           <div class="col-md-6">
                               <label class="form-label">Cabang</label>
                               <select name="cabang_id" class="form-select" required>
                                   <option value="">-- Pilih Cabang --</option>
                                   @foreach ($cabang as $c)
                                       <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- SHIFT -->
                           <div class="col-md-6">
                               <label class="form-label">Shift Kerja</label>
                               <select name="shift_id" class="form-select" required>
                                   @foreach ($shifts as $s)
                                       <option value="{{ $s->id }}">{{ $s->nama_shift }}</option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- NO HP -->
                           <div class="col-md-6">
                               <label class="form-label">No HP</label>
                               <input type="text" name="no_hp" class="form-control" required>
                           </div>

                           <!-- EMAIL -->
                           <div class="col-md-6">
                               <label class="form-label">Email</label>
                               <input type="email" name="email" class="form-control" required>
                           </div>

                           <!-- TANGGAL MASUK -->
                           <div class="col-md-6">
                               <label class="form-label">Tanggal Masuk</label>
                               <input type="date" name="tanggal_masuk" class="form-control" required>
                           </div>

                           <!-- STATUS KERJA -->
                           <div class="col-md-6">
                               <label class="form-label">Status Kerja</label>
                               <select name="status_kerja" class="form-select" required>
                                   <option value="">-- Pilih Status --</option>
                                   <option value="TETAP">Tetap</option>
                                   <option value="KONTRAK">Kontrak</option>
                                   <option value="MAGANG">Magang</option>
                               </select>
                           </div>

                           <!-- TEMPAT LAHIR -->
                           <div class="col-md-6">
                               <label class="form-label">Tempat Lahir</label>
                               <input type="text" name="tempat_lahir" class="form-control">
                           </div>

                           <!-- TANGGAL LAHIR -->
                           <div class="col-md-6">
                               <label class="form-label">Tanggal Lahir</label>
                               <input type="date" name="tanggal_lahir" class="form-control">
                           </div>

                           <!-- JENIS KELAMIN -->
                           <div class="col-md-6">
                               <label class="form-label">Jenis Kelamin</label>
                               <select name="jenis_kelamin" class="form-select">
                                   <option value="">-- Pilih Jenis Kelamin --</option>
                                   <option value="L">Laki-laki</option>
                                   <option value="P">Perempuan</option>
                               </select>
                           </div>

                           <!-- AGAMA -->
                           <div class="col-md-6">
                               <label class="form-label">Agama</label>
                               <select name="agama" class="form-select">
                                   <option value="">-- Pilih Agama --</option>
                                   <option value="ISLAM">Islam</option>
                                   <option value="KRISTEN">Kristen</option>
                                   <option value="KATOLIK">Katolik</option>
                                   <option value="HINDU">Hindu</option>
                                   <option value="BUDDHA">Buddha</option>
                                   <option value="KONGHUCU">Konghucu</option>
                               </select>
                           </div>

                           <!-- STATUS PERNIKAHAN -->
                           <div class="col-md-6">
                               <label class="form-label">Status Pernikahan</label>
                               <select name="status_pernikahan" class="form-select">
                                   <option value="">-- Pilih Status --</option>
                                   <option value="BELUM_MENIKAH">Belum Menikah</option>
                                   <option value="MENIKAH">Menikah</option>
                                   <option value="CERAI">Cerai</option>
                               </select>
                           </div>

                           <!-- FOTO PROFIL -->
                           <div class="col-md-6">
                               <label class="form-label">Foto Profil</label>
                               <input type="file" name="foto_profil" class="form-control">
                           </div>

                           <!-- FILE TAMBAHAN -->
                           <div class="col-md-6">
                               <label class="form-label">Foto KTP</label>
                               <input type="file" name="foto_ktp" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label">Foto Ijazah</label>
                               <input type="file" name="foto_ijazah" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label">Foto KK</label>
                               <input type="file" name="foto_kk" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label">CV</label>
                               <input type="file" name="cv_file" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label">Sertifikat</label>
                               <input type="file" name="sertifikat_file" class="form-control">
                           </div>

                           <!-- ALAMAT -->
                           <div class="col-md-12">
                               <label class="form-label">Alamat</label>
                               <textarea name="alamat" class="form-control" rows="3"></textarea>
                           </div>

                       </div>
                   </div>

                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                           Batal
                       </button>
                       <button type="submit" class="btn btn-primary">
                           <i class="ph ph-check"></i> Simpan
                       </button>
                   </div>
               </form>



           </div>
       </div>
   </div>
   <div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-hidden="true">
       <div class="modal-dialog modal-lg modal-dialog-centered">
           <div class="modal-content border-0 shadow-lg">

               <div class="modal-header text-white border-0">
                   <h5 class="modal-title fw-bold">
                       <i class="ph ph-user-circle-plus me-2"></i>Edit Data Karyawan
                   </h5>
                   <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                       aria-label="Close"></button>
               </div>

               <form id="formEditKaryawan" enctype="multipart/form-data">
                   @csrf
                   @method('PUT')

                   <input type="hidden" id="edit_id" name="id">

                   <div class="modal-body p-4">
                       <div class="row g-3">

                           <!-- NIP -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                               <input type="text" name="nip" id="edit_nip" class="form-control bg-light"
                                   required>
                           </div>

                           <!-- NAMA -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Nama Lengkap <span
                                       class="text-danger">*</span></label>
                               <input type="text" name="name" id="edit_name" class="form-control" required>
                           </div>

                           <!-- JABATAN -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Jabatan <span
                                       class="text-danger">*</span></label>
                               <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                           </div>

                           <!-- DIVISI -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Divisi <span class="text-danger">*</span></label>
                               <select name="divisi_id" id="edit_divisi" class="form-select" required>
                                   <option value="">-- Pilih Divisi --</option>
                                   @foreach ($divisi as $d)
                                       <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- CABANG -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Cabang <span class="text-danger">*</span></label>
                               <select name="cabang_id" id="edit_cabang" class="form-select" required>
                                   <option value="">-- Pilih Cabang --</option>
                                   @foreach ($cabang as $c)
                                       <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- SHIFT -->
                           <div class="col-md-6">
                               <label class="form-label fw-bold text-primary">Shift Kerja <span
                                       class="text-danger">*</span></label>
                               <select name="shift_id" id="edit_shift_id" class="form-select border-primary"
                                   required>
                                   <option value="">-- Pilih Shift --</option>
                                   @foreach ($shifts as $s)
                                       <option value="{{ $s->id }}">
                                           {{ $s->nama_shift }}
                                           ({{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} -
                                           {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }})
                                       </option>
                                   @endforeach
                               </select>
                           </div>

                           <!-- STATUS KERJA -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Status Kerja <span
                                       class="text-danger">*</span></label>
                               <select name="status_kerja" id="edit_status_kerja" class="form-select" required>
                                   <option value="TETAP">Tetap</option>
                                   <option value="KONTRAK">Kontrak</option>
                                   <option value="MAGANG">Magang</option>
                               </select>
                           </div>

                           <!-- NO HP -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                               <input type="text" name="no_hp" id="edit_no_hp" class="form-control">
                           </div>

                           <!-- EMAIL -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Email Perusahaan</label>
                               <input type="email" name="email" id="edit_email" class="form-control">
                           </div>

                           <!-- TANGGAL MASUK -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Tanggal Masuk</label>
                               <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk"
                                   class="form-control">
                           </div>

                           <!-- TEMPAT LAHIR -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Tempat Lahir</label>
                               <input type="text" name="tempat_lahir" id="edit_tempat_lahir"
                                   class="form-control">
                           </div>

                           <!-- TANGGAL LAHIR -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Tanggal Lahir</label>
                               <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir"
                                   class="form-control">
                           </div>

                           <!-- JENIS KELAMIN -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Jenis Kelamin</label>
                               <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select">
                                   <option value="">-- Pilih Jenis Kelamin --</option>
                                   <option value="L">Laki-laki</option>
                                   <option value="P">Perempuan</option>
                               </select>
                           </div>

                           <!-- AGAMA -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Agama</label>
                               <select name="agama" id="edit_agama" class="form-select">
                                   <option value="">-- Pilih Agama --</option>
                                   <option value="ISLAM">Islam</option>
                                   <option value="KRISTEN">Kristen</option>
                                   <option value="KATOLIK">Katolik</option>
                                   <option value="HINDU">Hindu</option>
                                   <option value="BUDDHA">Buddha</option>
                                   <option value="KONGHUCU">Konghucu</option>
                               </select>
                           </div>

                           <!-- STATUS PERNIKAHAN -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Status Pernikahan</label>
                               <select name="status_pernikahan" id="edit_status_pernikahan" class="form-select">
                                   <option value="">-- Pilih Status --</option>
                                   <option value="BELUM_MENIKAH">Belum Menikah</option>
                                   <option value="MENIKAH">Menikah</option>
                                   <option value="CERAI">Cerai</option>
                               </select>
                           </div>

                           <!-- FOTO PROFIL -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Foto Profil (Opsional)</label>
                               <input type="file" name="foto_profil" accept="image/*" class="form-control">
                               <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                           </div>

                           <!-- FILE TAMBAHAN -->
                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Foto KTP</label>
                               <input type="file" name="foto_ktp" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Foto Ijazah</label>
                               <input type="file" name="foto_ijazah" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Foto KK</label>
                               <input type="file" name="foto_kk" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label fw-semibold">CV</label>
                               <input type="file" name="cv_file" class="form-control">
                           </div>

                           <div class="col-md-6">
                               <label class="form-label fw-semibold">Sertifikat</label>
                               <input type="file" name="sertifikat_file" class="form-control">
                           </div>

                           <!-- ALAMAT -->
                           <div class="col-md-12">
                               <label class="form-label fw-semibold">Alamat Lengkap</label>
                               <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
                           </div>

                       </div>
                   </div>

                   <div class="modal-footer bg-light border-0">
                       <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                           <i class="ph ph-x me-1"></i> Batal
                       </button>
                       <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                           <i class="ph ph-floppy-disk me-1"></i> Simpan Perubahan
                       </button>
                   </div>
               </form>

           </div>
       </div>
   </div>

   <script>
       $('#formTambahKaryawan').submit(function(e) {
           e.preventDefault();

           let formData = new FormData(this);

           $.ajax({
               url: "{{ route('karyawan.store') }}",
               type: "POST",
               data: formData,
               contentType: false,
               processData: false,
               beforeSend: function() {
                   $('.btn-primary').prop('disabled', true);
               },
               success: function(res) {
                   $('#modalTambahKaryawan').modal('hide');

                   Swal.fire({
                       icon: 'success',
                       title: 'Berhasil',
                       text: 'Karyawan berhasil ditambahkan',
                       timer: 1500,
                       showConfirmButton: false
                   });

                   setTimeout(() => location.reload(), 1500);
               },
               error: function(xhr) {
                   $('.btn-primary').prop('disabled', false);

                   let errors = xhr.responseJSON.errors;
                   let msg = '';

                   $.each(errors, function(key, value) {
                       msg += value[0] + '<br>';
                   });

                   Swal.fire({
                       icon: 'error',
                       title: 'Gagal',
                       html: msg
                   });
               }
           });
       });


       $('#formEditKaryawan').on('submit', function(e) {
           e.preventDefault();

           let id = $('#edit_id').val();
           let url = `/karyawan/${id}`;
           let form = document.getElementById('formEditKaryawan');
           let data = new FormData(form);

           $.ajax({
               url: url,
               type: 'POST',
               data: data,
               processData: false,
               contentType: false,
               beforeSend: function() {
                   $('#btnUpdateKaryawan').prop('disabled', true);
               },
               success: function(res) {

                   $('#btnUpdateKaryawan').prop('disabled', false);
                   $('#modalEditKaryawan').modal('hide');

                   Swal.fire({
                       icon: 'success',
                       title: 'Berhasil',
                       text: res.message,
                       timer: 1500,
                       showConfirmButton: false
                   });

                   setTimeout(() => {
                       location.reload();
                   }, 1500);
               },
               error: function(xhr) {

                   $('#btnUpdateKaryawan').prop('disabled', false);

                   let msg = 'Terjadi kesalahan';
                   if (xhr.responseJSON?.errors) {
                       msg = '';
                       $.each(xhr.responseJSON.errors, function(key, val) {
                           msg += val[0] + '<br>';
                       });
                   }

                   Swal.fire({
                       icon: 'error',
                       title: 'Gagal',
                       html: msg
                   });
               }
           });
       });
   </script>
