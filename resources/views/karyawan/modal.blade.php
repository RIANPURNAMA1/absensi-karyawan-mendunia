   <!-- MODAL TAMBAH KARYAWAN -->
   <div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
       <div class="modal-dialog modal-lg modal-dialog-centered">
           <div class="modal-content">

               <div class="modal-header">
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
                               <input type="text" name="nip" class="form-control" required>
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

                           <!-- DEPARTEMEN -->
                           <div class="col-md-6">
                               <label class="form-label">Divisi</label>
                               <select name="divisi_id" class="form-select" required>
                                   <option value="">-- Pilih Divisi --</option>
                                   @foreach ($divisi as $d)
                                       <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                   @endforeach
                               </select>
                           </div>


                           <!-- NO HP -->
                           <div class="col-md-6">
                               <label class="form-label">No HP</label>
                               <input type="text" name="no_hp" class="form-control" required>
                           </div>
                           <!-- email -->
                           <div class="col-md-6">
                               <label class="form-label">Email</label>
                               <input type="text" name="email" class="form-control" required>
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

                           <!-- FOTO -->
                           <div class="col-md-6">
                               <label class="form-label">Foto Profil</label>
                               <input type="file" name="foto_profil" class="form-control">
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

{{-- edit modal --}}
<div class="modal fade" id="modalEditKaryawan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditKaryawan" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>NIP</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Nama</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Divisi</label>
                            <select name="divisi_id" id="edit_divisi" class="form-select" required>
                                @foreach ($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="text" name="email" id="edit_email" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Status Kerja</label>
                            <select name="status_kerja" id="edit_status_kerja" class="form-select" required>
                                <option value="TETAP">Tetap</option>
                                <option value="KONTRAK">Kontrak</option>
                                <option value="MAGANG">Magang</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Foto Profil (Opsional)</label>
                            <input type="file" name="foto_profil" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label>Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="btnUpdateKaryawan">
                        Update
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
