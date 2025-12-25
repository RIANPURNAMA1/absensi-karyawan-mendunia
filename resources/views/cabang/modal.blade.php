<div class="modal fade" id="modalTambahCabang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header  text-white">
                <h5 class="modal-title"><i class="ph ph-map-pin-plus me-2"></i>Tambah Cabang Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahCabang" action="{{ route('cabang.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Cabang</label>
                        <input type="text" name="nama_cabang" class="form-control" placeholder="Contoh: Kantor Pusat" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" name="latitude" id="add_lat" class="form-control" placeholder="-6.12345" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" name="longitude" id="add_lng" class="form-control" placeholder="106.12345" required>
                        </div>
                    </div>

                    <div class="mt-2 mb-3">
                        <button type="button" class="btn btn-sm btn-light-primary w-100" onclick="getCurrentLocation('add')">
                            <i class="ph ph-navigation-arrow me-1"></i> Gunakan Lokasi Saya Saat Ini
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Radius Absensi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="radius" class="form-control border-danger text-danger fw-bold" value="100" required>
                            <span class="input-group-text bg-danger text-white">Meter</span>
                        </div>
                        <small class="text-muted">Jarak maksimal karyawan boleh absen dari titik koordinat.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Raya Utama No. 123..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Cabang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditCabang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header  text-white">
                <h5 class="modal-title"><i class="ph ph-pencil-line me-2"></i>Edit Data Cabang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditCabang"  action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Cabang</label>
                        <input type="text" name="nama_cabang" id="edit_nama_cabang" class="form-control" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" name="latitude" id="edit_latitude" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" name="longitude" id="edit_longitude" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-2 mb-3">
                        <button type="button" class="btn btn-sm btn-light-warning w-100" onclick="getCurrentLocation('edit')">
                            <i class="ph ph-navigation-arrow me-1"></i> Update ke Lokasi Saya Sekarang
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Radius Absensi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="radius" id="edit_radius" class="form-control fw-bold" required>
                            <span class="input-group-text">Meter</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Update Cabang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // FUNGSI AMBIL LOKASI GPS HP/LAPTOP
    function getCurrentLocation(type) {
        if (navigator.geolocation) {
            Swal.fire({
                title: 'Mencari Lokasi...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    Swal.close();
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (type === 'add') {
                        $('#add_lat').val(lat);
                        $('#add_lng').val(lng);
                    } else {
                        $('#edit_latitude').val(lat);
                        $('#edit_longitude').val(lng);
                    }
                    
                    Swal.fire({ icon: 'success', title: 'Lokasi Ditemukan!', timer: 1000, showConfirmButton: false });
                },
                (error) => {
                    Swal.close();
                    Swal.fire('Gagal', 'Pastikan GPS aktif dan izinkan akses lokasi.', 'error');
                },
                { enableHighAccuracy: true }
            );
        } else {
            Swal.fire('Error', 'Browser Anda tidak mendukung Geolocation.', 'error');
        }
    }

    // Logic untuk mengarahkan Action Form Edit secara dinamis
    $('#modalEditCabang').on('show.bs.modal', function () {
        var id = $('#edit_id').val();
        $('#formEditCabang').attr('action', '/cabang/' + id);
    });


    $(document).ready(function() {
    $('#formTambahCabang').on('submit', function(e) {
        e.preventDefault(); // Mencegah form reload halaman

        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize(); // Mengambil semua data input

        // Menampilkan Loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang memproses data cabang',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(response) {
                Swal.close();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Cabang baru telah ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                });

                // Tutup modal
                $('#modalTambahCabang').modal('hide');
                
                // Reset form
                form[0].reset();

                // Reload halaman otomatis untuk memperbarui tabel
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                Swal.close();
                
                // Ambil pesan error dari Laravel jika ada (validasi gagal)
                let errorMsg = "Terjadi kesalahan saat menyimpan data.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMsg
                });
            }
        });
    });


$('#formEditCabang').on('submit', function(e) {
    e.preventDefault();

    let form = $(this);
    let id = $('#edit_id').val();
    let url = "/cabang/" + id;
    
    // Gunakan FormData agar lebih akurat mengirim method spoofing
    let formData = new FormData(this);
    // Tambahkan _method PUT secara manual untuk memastikan
    formData.append('_method', 'PUT');

    $.ajax({
        url: url,
        type: "POST", // Browser mengirim sebagai POST
        data: formData,
        processData: false, // Wajib jika menggunakan FormData
        contentType: false, // Wajib jika menggunakan FormData
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data cabang telah diperbarui',
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalEditCabang').modal('hide');
            setTimeout(() => { location.reload(); }, 1500);
        },
        error: function(xhr) {
            // Jika error 405 (Method Not Allowed) muncul lagi, cek route:list
            console.log(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Method tidak didukung atau terjadi kesalahan server.' });
        }
    });
});
});
</script>