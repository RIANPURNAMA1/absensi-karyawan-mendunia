<div class="modal fade" id="modalTambahShift" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);">
                <h5 class="modal-title text-white">
                    <i class="ph ph-plus-circle"></i> Tambah Shift Kerja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formTambahShift">
                @csrf

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-tag"></i> Nama Shift <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_shift" class="form-control"
                                placeholder="Contoh: Shift Pagi" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-hash"></i> Kode Shift
                            </label>
                            <input type="text" name="kode_shift" class="form-control" placeholder="Contoh: PAGI"
                                style="text-transform: uppercase;">
                            <small class="text-muted">Opsional - Kode unik untuk shift</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-clock-clockwise"></i> Jam Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-clock-counter-clockwise"></i> Jam Pulang <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_pulang" id="jam_pulang" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-timer"></i> Toleransi Keterlambatan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="toleransi" class="form-control" min="0"
                                    max="60" value="15" required>
                                <span class="input-group-text">Menit</span>
                            </div>
                            <small class="text-muted">Maksimal 60 menit</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-toggle-right"></i> Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="AKTIF" selected>Aktif</option>
                                <option value="NONAKTIF">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="ph ph-note"></i> Keterangan
                            </label>
                            <textarea name="keterangan" class="form-control" rows="3"
                                placeholder="Tambahkan catatan untuk shift ini (opsional)"></textarea>
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="ph ph-info"></i> Total jam kerja akan dihitung secara otomatis oleh
                                    sistem.
                                </small>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph ph-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph ph-check"></i> Simpan Shift
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditShift" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background: linear-gradient(135deg, #94716b 0%, #7a5d58 100%);">
                <h5 class="modal-title text-white">
                    <i class="ph ph-pencil"></i> Edit Shift Kerja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditShift">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id" name="id">

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-tag"></i> Nama Shift <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_shift" id="edit_nama_shift" class="form-control"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-hash"></i> Kode Shift
                            </label>
                            <input type="text" name="kode_shift" id="edit_kode_shift" class="form-control"
                                style="text-transform: uppercase;">
                            <small class="text-muted">Contoh: PAGI, MALAM, dsb.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-clock-clockwise"></i> Jam Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_masuk" id="edit_jam_masuk" class="form-control"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-clock-counter-clockwise"></i> Jam Pulang <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_pulang" id="edit_jam_pulang" class="form-control"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-timer"></i> Toleransi Keterlambatan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="toleransi" id="edit_toleransi" class="form-control"
                                    min="0" max="60" required>
                                <span class="input-group-text">Menit</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="ph ph-toggle-right"></i> Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="AKTIF">Aktif</option>
                                <option value="NONAKTIF">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="ph ph-note"></i> Keterangan
                            </label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"
                                placeholder="Tambahkan catatan..."></textarea>

                            <div class="mt-2 alert alert-sm alert-soft-info py-2 mb-0">
                                <small>
                                    <i class="ph ph-info"></i> <strong>Sistem Otomatis:</strong> Total jam kerja akan
                                    diperbarui secara otomatis jika Anda mengubah Jam Masuk atau Jam Pulang.
                                </small>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph ph-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="btnUpdateShift">
                        <i class="ph ph-note-pencil"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // FORM TAMBAH SHIFT
    $('#formTambahShift').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('shift.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.btn-primary').prop('disabled', true).html(
                    '<i class="ph ph-spinner spinner-border spinner-border-sm"></i> Menyimpan...'
                    );
            },
            success: function(res) {
                $('#modalTambahShift').modal('hide');
                $('#formTambahShift')[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Shift berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                $('.btn-primary').prop('disabled', false).html(
                    '<i class="ph ph-check"></i> Simpan Shift');

                let errors = xhr.responseJSON?.errors;
                let msg = '';

                if (errors) {
                    $.each(errors, function(key, value) {
                        msg += value[0] + '<br>';
                    });
                } else {
                    msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: msg
                });
            },
            complete: function() {
                $('.btn-primary').prop('disabled', false).html(
                    '<i class="ph ph-check"></i> Simpan Shift');
            }
        });
    });

    // FORM EDIT SHIFT
    $('#formEditShift').on('submit', function(e) {
        e.preventDefault();

        let id = $('#edit_id').val();
        let url = `/shift/${id}`;
        let formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btnUpdateShift').prop('disabled', true).html(
                    '<i class="ph ph-spinner spinner-border spinner-border-sm"></i> Menyimpan...'
                    );
            },
            success: function(res) {
                $('#modalEditShift').modal('hide');
                $('#formEditShift')[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Shift berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan';

                if (xhr.responseJSON?.errors) {
                    msg = '';
                    $.each(xhr.responseJSON.errors, function(key, val) {
                        msg += val[0] + '<br>';
                    });
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: msg
                });
            },
            complete: function() {
                $('#btnUpdateShift').prop('disabled', false).html(
                    '<i class="ph ph-check"></i> Update Shift');
            }
        });
    });

    // Auto uppercase untuk kode shift
    $('input[name="kode_shift"]').on('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>

<style>
    /* Modal Header Gradient */
    .modal-header {
        border-bottom: none;
        border-radius: 8px 8px 0 0;
    }

    /* Form Label dengan Icon */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-label i {
        margin-right: 6px;
        color: #2a5298;
    }

    /* Input Focus Style */
    .form-control:focus,
    .form-select:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
    }

    /* Required Star */
    .text-danger {
        font-weight: bold;
    }

    /* Small Text */
    small.text-muted {
        font-size: 0.8rem;
        display: block;
        margin-top: 4px;
    }

    /* Modal Footer */
    .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }

    /* Spinner Animation */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>
