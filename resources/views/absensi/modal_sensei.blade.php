<div id="modalSensei" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModalSensei()"></div>

    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-[40px] p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex flex-col items-center">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mb-6"></div>

            <div class="flex justify-between items-center w-full mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-violet-100 rounded-2xl flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-gray-900">Absen Guru</h2>
                        <p class="text-[10px] text-gray-400">Isi data kelas yang diajarkan</p>
                    </div>
                </div>
                <button onclick="closeModalSensei()" class="p-2 bg-gray-100 rounded-full text-gray-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="formSensei" class="w-full space-y-4">
                <!-- Nama Kelas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="nama_kelas" placeholder="Contoh: Kelas React Basic" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                        required>
                </div>

                <!-- Batch -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Batch</label>
                    <select name="batch_id" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                        required>
                        <option value="">Pilih Batch</option>
                        @foreach ($batchList as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_batch }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level yang Diajarkan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Level yang Diajarkan</label>
                    <select name="level" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                        required>
                        <option value="">Pilih Level</option>
                        @foreach ($levels as $lv)
                        <option value="{{ $lv }}">Level {{ $lv }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                        required>
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                        required>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..." 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModalSensei()" 
                        class="flex-1 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm active:scale-95 transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="flex-1 py-4 bg-blue-600 text-white rounded-xl font-bold text-sm active:scale-95 transition shadow-lg shadow-violet-200">
                        Simpan & Absen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModalSensei() {
        document.getElementById('modalSensei').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="tanggal_mulai"]').value = today;
    }

    function closeModalSensei() {
        document.getElementById('modalSensei').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formSensei').reset();
    }

    document.getElementById('formSensei').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="animate-pulse">Menyimpan...</span>';

        fetch('/absensi/sensei/store-kelas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kelas berhasil dibuat. Silakan absen masuk.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    closeModalSensei();
                    if (typeof loadKelasAktif === 'function') {
                        loadKelasAktif();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan & Absen';
        });
    });
</script>
