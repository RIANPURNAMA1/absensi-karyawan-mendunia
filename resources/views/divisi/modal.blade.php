<div class="modal fade" id="modalTambahDivisi" tabindex="-1" >
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTambahDivisi" action="{{ route('divisi.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white"  style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);">
                    <h5 class="modal-title text-white"><i class="ph ph-plus-circle me-2"></i>Tambah Divisi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Divisi</label>
                        <input type="text" name="kode_divisi" class="form-control" placeholder="Contoh: IT, HRD, MKT" required>
                        <small class="text-muted">Gunakan singkatan unik maksimal 10 karakter.</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Nama Divisi</label>
                        <input type="text" name="nama_divisi" class="form-control" placeholder="Contoh: Information Technology" required>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSimpanDivisi">Simpan Divisi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditDivisi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditDivisi" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id">
            
            <div class="modal-content border-0 shadow-lg" >
                <div class="modal-header  text-white"  style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);">
                    <h5 class="modal-title text-white"><i class="ph ph-pencil-line me-2"></i>Edit Divisi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Divisi</label>
                        <input type="text" name="kode_divisi" id="edit_kode_divisi" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Nama Divisi</label>
                        <input type="text" name="nama_divisi" id="edit_nama_divisi" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold text-white" id="btnUpdateDivisi">Update Divisi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Logic Pengisian Data ke Modal Edit
     * Dipanggil dari tombol edit di index.blade.php
     */
    function editDivisi(id, kode, nama) {
        $('#edit_id').val(id);
        $('#edit_kode_divisi').val(kode);
        $('#edit_nama_divisi').val(nama);
        $('#formEditDivisi').attr('action', `/divisi/${id}`);
        $('#modalEditDivisi').modal('show');
    }

    $(document).ready(function() {
        /* ========== PROSES TAMBAH (AJAX) ========== */
        $('#formTambahDivisi').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    $('#btnSimpanDivisi').prop('disabled', true);
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    $('#modalTambahDivisi').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Divisi baru telah ditambahkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    $('#btnSimpanDivisi').prop('disabled', false);
                    let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan server';
                    
                    // Jika ada error validasi spesifik dari Laravel
                    if (xhr.responseJSON?.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).map(v => v[0]).join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        html: errorMsg
                    });
                }
            });
        });

        /* ========== PROSES UPDATE (AJAX) ========== */
        $('#formEditDivisi').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    $('#btnUpdateDivisi').prop('disabled', true);
                    Swal.fire({
                        title: 'Memperbarui...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    $('#modalEditDivisi').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data divisi telah diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    $('#btnUpdateDivisi').prop('disabled', false);
                    let errorMsg = xhr.responseJSON?.message || 'Gagal memperbarui data';
                    
                    if (xhr.responseJSON?.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).map(v => v[0]).join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update',
                        html: errorMsg
                    });
                }
            });
        });
    });
</script>