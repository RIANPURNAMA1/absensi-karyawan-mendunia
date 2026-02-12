<script src="https://unpkg.com/html5-qrcode"></script>

<div class="modal fade" id="modalScanQR" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopScanner()"></button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%; border-radius: 10px; overflow: hidden;"></div>
                
                <div class="mt-3 text-center">
                    <p class="text-sm text-muted">Arahkan kamera ke QR Code di lokasi cabang</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let html5QrcodeScanner = null;

    // Jalankan scanner saat modal dibuka
    const modalScanQR = document.getElementById('modalScanQR');
    modalScanQR.addEventListener('shown.bs.modal', function () {
        startScanner();
    });

    // Matikan scanner saat modal ditutup
    modalScanQR.addEventListener('hidden.bs.modal', function () {
        stopScanner();
    });

    function startScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        });
        html5QrcodeScanner.render(onScanSuccess);
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(error => console.error("Gagal stop scanner", error));
        }
    }

    function onScanSuccess(decodedText) {
        // Berhenti scan agar tidak duplikat
        stopScanner();
        
        // Ambil Lokasi (Penting karena QR Statis selamanya)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                sendData(decodedText, position.coords.latitude, position.coords.longitude);
            }, function() {
                Swal.fire('Gagal', 'Izin lokasi (GPS) wajib aktif untuk absen!', 'error');
            });
        }
    }

    function sendData(qrData, lat, lng) {
        fetch('/absensi/proses-scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_code: qrData,
                latitude: lat,
                longitude: lng
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Masalah koneksi ke server', 'error'));
    }
</script>