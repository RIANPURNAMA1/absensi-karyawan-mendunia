<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form id="formTambah" action="{{ route('divisi.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Divisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="mb-1">Nama Divisi</label>
                    <input type="text" name="nama_divisi" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanDivisi">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Divisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="mb-1">Nama Divisi</label>
                    <input type="text" name="nama_divisi" id="editNamaDivisi"
                        class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="btnUpdateDivisi">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editDivisi(id, nama) {
        $('#editNamaDivisi').val(nama);
        $('#formEdit').attr('action', `/divisi/${id}`);
        $('#modalEdit').modal('show');
    }

    /* ========== TAMBAH ========== */
    $('#formTambah').on('submit', function(e) {
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

            success: function() {
                $('#btnSimpanDivisi').prop('disabled', false);
                $('#modalTambah').modal('hide');
                form.trigger('reset');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Divisi berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => location.reload(), 1500);
            },

            error: function(xhr) {
                $('#btnSimpanDivisi').prop('disabled', false);

                let msg = 'Terjadi kesalahan';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors)
                        .map(v => v[0]).join('<br>');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: msg
                });
            }
        });
    });

    /* ========== EDIT ========== */
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST', // spoof PUT
            data: form.serialize(),

            beforeSend: function() {
                $('#btnUpdateDivisi').prop('disabled', true);
                Swal.fire({
                    title: 'Memperbarui...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },

            success: function() {
                $('#btnUpdateDivisi').prop('disabled', false);
                $('#modalEdit').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Divisi berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => location.reload(), 1500);
            },

            error: function(xhr) {
                $('#btnUpdateDivisi').prop('disabled', false);

                let msg = 'Terjadi kesalahan';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors)
                        .map(v => v[0]).join('<br>');
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
