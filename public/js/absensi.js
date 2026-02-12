$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});

lucide.createIcons();
const MODEL_URL =
    "https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights";
let stream = null;
let isEngineReady = false;
let detectionInterval = null;

// Variabel untuk deteksi otomatis
let faceDetectedCount = 0;
const DETECTION_THRESHOLD = 3; // Deteksi 3x berturut-turut untuk konfirmasi
let absensiProcessing = false;

window.openAbsen = async function () {
    const modal = document.getElementById("modalAbsenManual");
    modal.classList.remove("hidden");
    modal.classList.add("flex");

    await loadModels();
    startCamera();
};

function closeAbsenManual() {
    const modal = document.getElementById("modalAbsenManual");
    modal.classList.add("hidden");
    modal.classList.remove("flex");

    if (stream) stream.getTracks().forEach((track) => track.stop());
    if (detectionInterval) clearInterval(detectionInterval);

    // reset state
    faceDetectedCount = 0;
    absensiProcessing = false;
    document.getElementById("instructionTextAbsen").textContent =
        "Posisikan wajah Anda di depan kamera...";
}

async function loadModels() {
    if (isEngineReady) return;

    document.getElementById("instructionTextAbsen").textContent =
        "Memuat model deteksi wajah...";

    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
    ]);
    isEngineReady = true;

    document.getElementById("instructionTextAbsen").textContent =
        "Posisikan wajah Anda di depan kamera...";
}

async function startCamera() {
    const video = document.getElementById("videoStream");
    const canvas = document.getElementById("canvasStream");

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user",
                width: { ideal: 640 },
                height: { ideal: 480 }
            },
        });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            document.getElementById("instructionTextAbsen").textContent =
                "Mencari wajah...";
            startRealtimeDetection(video, canvas);
        };
    } catch (err) {
        console.error("Tidak bisa akses kamera:", err);
        Swal.fire(
            "Kamera Error",
            "Izinkan akses kamera untuk absensi.",
            "error",
        );
    }
}

function startRealtimeDetection(video, canvas) {
    const displaySize = {
        width: video.clientWidth,
        height: video.clientHeight,
    };
    faceapi.matchDimensions(canvas, displaySize);

    detectionInterval = setInterval(async () => {
        // Skip jika sedang memproses absensi
        if (!video.videoWidth || !isEngineReady || absensiProcessing) return;

        const detection = await faceapi
            .detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions({
                    inputSize: 224,
                    scoreThreshold: 0.5,
                }),
            )
            .withFaceLandmarks()
            .withFaceDescriptor();

        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (detection) {
            const resized = faceapi.resizeResults(detection, displaySize);
            const box = resized.detection.box;

            // Increment counter jika wajah terdeteksi
            faceDetectedCount++;

            // Gambar kotak hijau di sekitar wajah yang terdeteksi
            ctx.strokeStyle = "#10b981"; // Green color
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Tambahkan indikator visual
            ctx.fillStyle = "#10b981";
            ctx.font = "16px Arial";
            ctx.fillText("Wajah Terdeteksi", box.x, box.y - 10);

            // Update instruksi
            if (faceDetectedCount >= DETECTION_THRESHOLD) {
                document.getElementById("instructionTextAbsen").textContent =
                    "Wajah terdeteksi! Memproses absensi...";
            } else {
                document.getElementById("instructionTextAbsen").textContent =
                    `Wajah terdeteksi (${faceDetectedCount}/${DETECTION_THRESHOLD})...`;
            }

            // Jika sudah terdeteksi beberapa kali berturut-turut, proses absensi
            if (faceDetectedCount >= DETECTION_THRESHOLD && !absensiProcessing) {
                absensiProcessing = true;

                // Hentikan interval deteksi
                clearInterval(detectionInterval);

                Swal.fire({
                    title: "Memproses Absensi...",
                    text: "Mohon tunggu sebentar",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                // Ambil lokasi GPS
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        prosesAbsensiWajah(
                            resized.descriptor,
                            pos.coords.latitude,
                            pos.coords.longitude,
                        );
                    },
                    (err) => {
                        absensiProcessing = false;
                        faceDetectedCount = 0;

                        Swal.fire({
                            icon: "error",
                            title: "Error Lokasi",
                            text: "Gagal mengambil koordinat GPS. Pastikan GPS aktif.",
                            confirmButtonText: "Coba Lagi"
                        }).then(() => {
                            // Restart deteksi
                            if ($("#modalAbsenManual").is(":visible")) {
                                startRealtimeDetection(video, canvas);
                            }
                        });
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }
        } else {
            // Reset counter jika wajah tidak terdeteksi
            faceDetectedCount = 0;
            document.getElementById("instructionTextAbsen").textContent =
                "Posisikan wajah Anda di depan kamera...";
        }
    }, 300); // Check setiap 300ms untuk performa lebih baik
}

function prosesAbsensiWajah(faceEmbedding, latitude, longitude) {
    const payload = {
        face_embedding: JSON.stringify(Array.from(faceEmbedding)),
        latitude: latitude,
        longitude: longitude,
    };

    $.ajax({
        url: window.routes.absensiStatus,
        method: "POST",
        data: payload,
        success: function (res) {
            let targetUrl = "";
            let titleText = "";

            if (res.status === "BELUM_MASUK") {
                targetUrl = window.routes.absenMasuk;
                titleText = "Absensi Masuk";
            } else if (res.status === "SUDAH_MASUK") {
                targetUrl = window.routes.absenPulang;
                titleText = "Absensi Pulang";
            } else {
                // Kasus sudah absen semua
                Swal.fire({
                    icon: "info",
                    title: "Sudah Absen",
                    text: "Anda sudah melakukan absensi masuk dan pulang hari ini.",
                    confirmButtonText: "OK"
                }).then(() => {
                    closeAbsenManual();
                });
                return;
            }

            // Submit absensi
            $.ajax({
                url: targetUrl,
                method: "POST",
                data: payload,
                success: function (r) {
                    Swal.fire({
                        icon: "success",
                        title: titleText + " Berhasil!",
                        text: r.message,
                        timer: 2500,
                        showConfirmButton: false,
                    }).then(() => {
                        closeAbsenManual();
                        // Reload data jika ada fungsi loadRiwayatRealtime
                        if (typeof loadRiwayatRealtime === 'function') {
                            loadRiwayatRealtime();
                        }
                    });
                },
                error: function (xhr) {
                    handleError(xhr);
                },
            });
        },
        error: function (xhr) {
            handleError(xhr);
        },
    });
}

function handleError(xhr) {
    absensiProcessing = false;
    faceDetectedCount = 0;

    // Stop deteksi dan kamera
    if (detectionInterval) {
        clearInterval(detectionInterval);
    }

    const pesanError =
        xhr.responseJSON?.message ??
        "Terjadi kesalahan pada sistem. Silakan coba lagi.";

    Swal.fire({
        icon: "error",
        title: "Absensi Gagal",
        text: pesanError,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Coba Lagi",
    }).then((result) => {
        if (result.isConfirmed) {
            // Restart deteksi jika user ingin coba lagi
            const video = document.getElementById("videoStream");
            const canvas = document.getElementById("canvasStream");

            if ($("#modalAbsenManual").is(":visible") && video.srcObject) {
                startRealtimeDetection(video, canvas);
            } else {
                closeAbsenManual();
            }
        } else {
            closeAbsenManual();
        }
    });
}

// beda fungsi
lucide.createIcons();

async function initFaceEngine() {
    try {
        console.log("🚀 Memuat AI Engine...");
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);

        isEngineReady = true;
        $("#loaderFace").addClass("hidden");
        $("#mainContentReg").removeClass("hidden").addClass("flex");

        if (window.lucide) lucide.createIcons();
    } catch (err) {
        console.error("Gagal memuat model:", err);
        document.getElementById("loaderFace").innerHTML =
            `<p class="text-red-500 text-sm">Gagal memuat AI. Periksa koneksi internet.</p>`;
    }
}

async function showModalRegistrasi() {
    $("#modalRegistrasiWajah").removeClass("hidden").addClass("flex");
    if (isEngineReady) await startCameraReg();
}

async function startCameraReg() {
    try {
        streamReg = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user",
                width: 640,
                height: 480,
            },
        });
        const video = document.getElementById("videoReg");
        video.srcObject = streamReg;
        video.onloadedmetadata = () => {
            video.play();
            $("#instructionText")
                .text("Mencari wajah...")
                .removeClass("text-blue-600 text-green-600 text-red-500");
            startRealtimeDetectionReg();
        };
    } catch (err) {
        Swal.fire(
            "Kamera Error",
            "Mohon izinkan akses kamera untuk verifikasi wajah.",
            "error",
        );
    }
}

let isProcessingReg = false;
let holdStartTime = null;
const HOLD_DURATION = 5000; // 3 detik
const TILT_TOLERANCE = 0.15; // toleransi kemiringan kepala (radians)

async function startRealtimeDetectionReg() {
    const video = document.getElementById("videoReg");
    const canvas = document.getElementById("canvasReg");
    if (!video || !canvas) return;

    const displaySize = { width: 640, height: 480 };
    faceapi.matchDimensions(canvas, displaySize);

    detectionIntervalReg = setInterval(async () => {
        if (!video.videoWidth || !isEngineReady || isProcessingReg) return;

        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                inputSize: 224,
                scoreThreshold: 0.5,
            }))
            .withFaceLandmarks();

        if (detection) {
            const resized = faceapi.resizeResults(detection, displaySize);
            const box = resized.detection.box;
            const landmarks = resized.landmarks;

            // gambar kotak wajah
            ctx.strokeStyle = "#10b981";
            ctx.lineWidth = 2;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // 🔹 CEK APAKAH KEPALA LURUS
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();

            const eyeSlope = (rightEye[0].y - leftEye[0].y) / (rightEye[0].x - leftEye[0].x);

            const headIsStraight = Math.abs(eyeSlope) < TILT_TOLERANCE;

            if (headIsStraight) {
                if (!holdStartTime) holdStartTime = Date.now();
                const holdTime = Date.now() - holdStartTime;
                const remainingTime = Math.max(0, Math.ceil((HOLD_DURATION - holdTime) / 1000));

                // progres instruksi
                if (holdTime < HOLD_DURATION) {
                    $("#instructionText")
                        .text(`Tahan kepala lurus... ${remainingTime} detik`)
                        .removeClass("text-red-500 text-green-600")
                        .addClass("text-blue-600");
                } else {
                    $("#instructionText")
                        .text("Kepala stabil! Memproses...")
                        .removeClass("text-blue-600 text-red-500")
                        .addClass("text-green-600");

                    isProcessingReg = true;
                    clearInterval(detectionIntervalReg);
                    prosesRegistrasiWajah();
                }
            } else {
                holdStartTime = null;
                $("#instructionText")
                    .text("Kepala miring, luruskan kepala")
                    .removeClass("text-green-600 text-blue-600")
                    .addClass("text-red-500");
            }

        } else {
            holdStartTime = null;
            $("#instructionText")
                .text("Wajah tidak terdeteksi")
                .removeClass("text-blue-600 text-green-600")
                .addClass("text-red-500");
        }

    }, 150); // lebih responsif
}



async function prosesRegistrasiWajah() {
    const video = document.getElementById("videoReg");

    // Tampilkan loading agar user tahu proses sedang berjalan
    Swal.fire({
        title: "Memproses Verifikasi...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const detection = await faceapi
        .detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions({ inputSize: 416 }),
        )
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        isProcessingReg = false;
        faceDetectedCountReg = 0;

        Swal.fire({
            icon: "error",
            title: "Gagal Deteksi",
            text: "Wajah tidak terdeteksi dengan jelas. Silakan coba lagi.",
            confirmButtonText: "Coba Lagi"
        }).then(() => {
            $("#instructionText")
                .text("Posisikan wajah Anda di depan kamera...")
                .removeClass("text-green-600 text-blue-600")
                .addClass("text-red-500");

            $("#btnCaptureWajah")
                .removeClass("bg-green-600")
                .addClass("bg-gray-400")
                .text("Menunggu Wajah...");

            startRealtimeDetectionReg();
        });
        return;
    }

    $.ajax({
        url: window.routes.updateFace,
        method: "POST",
        data: {
            face_embedding: JSON.stringify(Array.from(detection.descriptor)),
        },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "Verifikasi Berhasil!",
                text: "Data wajah Anda berhasil tersimpan.",
                timer: 2500,
                showConfirmButton: false,
            }).then(() => {
                // Stop kamera sebelum reload
                if (streamReg) {
                    streamReg.getTracks().forEach(track => track.stop());
                }
                location.reload();
            });
        },
        error: function (xhr) {
            console.error(xhr.responseText);

            // Buka kunci agar user bisa mencoba lagi
            isProcessingReg = false;
            faceDetectedCountReg = 0;

            let msg = xhr.responseJSON?.message ?? "Terjadi kesalahan sistem.";

            Swal.fire({
                icon: "warning",
                title: "Verifikasi Gagal",
                text: msg,
                confirmButtonColor: "#ef4444",
                confirmButtonText: "Coba Lagi",
            }).then(() => {
                $("#instructionText")
                    .text("Posisikan wajah Anda kembali...")
                    .removeClass("text-green-600 text-blue-600")
                    .addClass("text-red-500");

                $("#btnCaptureWajah")
                    .removeClass("bg-green-600")
                    .addClass("bg-gray-400")
                    .text("Menunggu Wajah...");

                startRealtimeDetectionReg();
            });
        },
    });
}

// Haversine formula: hitung jarak dalam meter
function hitungJarak(lat1, lon1, lat2, lon2) {
    const R = 6371000; // radius bumi dalam meter
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Submit absensi
//       function submitAbsen(type) {

//     Swal.fire({
//         title: 'Memverifikasi Lokasi...',
//         text: 'Harap tunggu, kami sedang memastikan posisi Anda tepat di radius cabang.',
//         allowOutsideClick: false,
//         showConfirmButton: false,
//         didOpen: () => Swal.showLoading()
//     });

//     if (!navigator.geolocation) {
//         Swal.fire('Error', 'Perangkat/Browser Anda tidak mendukung GPS', 'error');
//         return;
//     }

//     navigator.geolocation.getCurrentPosition(

//         (position) => {

//             const userLat = position.coords.latitude;
//             const userLong = position.coords.longitude;

//             const jarak = hitungJarak(userLat, userLong, CABANG.lat, CABANG.long);
//             console.log("Jarak ke cabang:", jarak, "meter");

//             if (jarak > CABANG.radius) {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Di Luar Area Cabang',
//                     html: `Anda berada <b>${Math.round(jarak)} meter</b> dari cabang <b>${CABANG.nama}</b>.<br>Radius absensi hanya <b>${CABANG.radius} meter</b>.`,
//                     confirmButtonColor: '#ef4444'
//                 });
//                 return;
//             }

//             sendToServer(type, userLat, userLong);
//         },

//         (error) => {
//             let msg = "Gagal mengambil lokasi.";
//             if (error.code === 1) msg = "Izin lokasi ditolak.";
//             if (error.code === 2) msg = "Sinyal GPS tidak stabil.";
//             if (error.code === 3) msg = "Waktu pencarian lokasi habis.";

//             Swal.fire({
//                 icon: 'error',
//                 title: 'Lokasi Gagal diakses',
//                 text: msg,
//                 confirmButtonColor: '#ef4444'
//             });
//         },

//         {
//             enableHighAccuracy: true,
//             timeout: 7000,
//             maximumAge: 0
//         }
//     );
// }

//         // AJAX ke server
// function sendToServer(type, lat, long) {

//     const url = type === 'masuk'
//         ? window.routes.absenMasuk
//         : window.routes.absenPulang;

//     $.ajax({
//         url: url,
//         method: 'POST',
//         data: {
//             latitude: lat,
//             longitude: long
//         },

//         success(res) {
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Presensi Berhasil',
//                 text: res.message,
//                 confirmButtonColor: '#059669'
//             }).then(() => location.reload());
//         },

//         error(xhr) {
//             console.error(xhr.responseText);

//             const errorMsg = xhr.responseJSON?.message ?? 'Terjadi kesalahan sistem';

//             Swal.fire({
//                 icon: 'warning',
//                 title: 'Akses Ditolak',
//                 text: errorMsg,
//                 confirmButtonColor: '#f97316'
//             });
//         }
//     });
// }


// jadwaal

function toggleModalJadwal(show) {
    const modal = document.getElementById('modalJadwal');
    const content = document.getElementById('modalContent');

    if (show) {
        // 1. Tampilkan container utama (menghilangkan class hidden)
        modal.classList.remove('hidden');

        // 2. Beri sedikit jeda agar transisi slide-up terlihat
        setTimeout(() => {
            content.classList.remove('translate-y-full');
            content.classList.add('translate-y-0');
        }, 10);

        // 3. Kunci scroll body
        document.body.style.overflow = 'hidden';
    } else {
        // 1. Jalankan animasi slide-down terlebih dahulu
        content.classList.remove('translate-y-0');
        content.classList.add('translate-y-full');

        // 2. Tunggu animasi selesai (300ms sesuai duration-300) baru sembunyikan container
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);

        // 3. Kembalikan scroll body
        document.body.style.overflow = 'auto';
    }
}

// Pastikan Lucide Icons ter-render jika data dimuat dinamis
document.addEventListener("DOMContentLoaded", function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});



// ============================================
// ABSENSI FOTO - FIXED VERSION
// ============================================

let mediaStreamAbsen = null;

/**
 * Mulai kamera untuk absensi
 */
async function mulaiAbsenFoto() {
    const elModal = document.getElementById('modalKameraAbsen');
    const elVideo = document.getElementById('videoPreviewAbsen');

    // Tampilkan modal
    elModal.classList.remove('hidden');
    elModal.classList.add('flex');

    try {
        // ⭐ PERBAIKAN: Constraint kamera yang lebih baik
        const constraints = {
            video: {
                facingMode: "user", // Kamera depan
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 }
            },
            audio: false
        };

        // Request akses kamera
        mediaStreamAbsen = await navigator.mediaDevices.getUserMedia(constraints);

        // ⭐ PERBAIKAN: Set stream ke video element
        elVideo.srcObject = mediaStreamAbsen;

        // ⭐ PERBAIKAN: Tunggu video ready sebelum play
        await new Promise((resolve) => {
            elVideo.onloadedmetadata = () => {
                elVideo.play()
                    .then(resolve)
                    .catch(err => console.error("Error playing video:", err));
            };
        });

        console.log("✅ Kamera berhasil diaktifkan");

    } catch (err) {
        console.error("❌ Error mengakses kamera:", err);

        let errorMessage = "Kamera tidak dapat diakses.";

        if (err.name === 'NotAllowedError') {
            errorMessage = "Akses kamera ditolak. Mohon izinkan akses kamera di browser settings.";
        } else if (err.name === 'NotFoundError') {
            errorMessage = "Kamera tidak ditemukan. Pastikan device memiliki kamera.";
        } else if (err.name === 'NotReadableError') {
            errorMessage = "Kamera sedang digunakan aplikasi lain.";
        }

        alert(errorMessage);
        hentikanKameraAbsen();
    }
}

/**
 * Hentikan kamera dan tutup modal
 */
function hentikanKameraAbsen() {
    const elModal = document.getElementById('modalKameraAbsen');
    const elVideo = document.getElementById('videoPreviewAbsen');

    // Hentikan semua track kamera
    if (mediaStreamAbsen) {
        mediaStreamAbsen.getTracks().forEach(track => {
            track.stop();
            console.log("🛑 Track stopped:", track.kind);
        });
        mediaStreamAbsen = null;
    }

    // Reset video element
    elVideo.srcObject = null;
    elVideo.pause();

    // Tutup modal
    elModal.classList.add('hidden');
    elModal.classList.remove('flex');

    console.log("✅ Kamera dihentikan");
}

/**
 * Ambil foto dari video stream
 */
function eksekusiAmbilFoto() {
    const elVideo = document.getElementById('videoPreviewAbsen');
    const elCanvas = document.getElementById('canvasSimpanFoto');
    const btnShutter = document.getElementById('btnShutterAbsen');

    // Validasi video sedang aktif
    if (!elVideo.srcObject || elVideo.paused) {
        alert("Kamera belum siap. Mohon tunggu sebentar.");
        return;
    }

    // ⭐ PERBAIKAN: Pastikan video sudah ada dimensi
    if (elVideo.videoWidth === 0 || elVideo.videoHeight === 0) {
        alert("Video belum dimuat dengan benar. Coba tutup dan buka kamera lagi.");
        return;
    }

    const konteks = elCanvas.getContext('2d');

    // --- STEP 1: OPTIMASI DIMENSI (RESIZING) ---
    const MAX_WIDTH = 800;
    const videoWidth = elVideo.videoWidth;
    const videoHeight = elVideo.videoHeight;
    const scale = Math.min(1, MAX_WIDTH / videoWidth);

    elCanvas.width = videoWidth * scale;
    elCanvas.height = videoHeight * scale;

    console.log(`📐 Canvas size: ${elCanvas.width}x${elCanvas.height}`);

    // --- STEP 2: LOGIKA MIRRORING (Flip horizontal) ---
    konteks.save();
    konteks.translate(elCanvas.width, 0);
    konteks.scale(-1, 1); // Mirror horizontal

    // Gambar video ke canvas
    konteks.drawImage(elVideo, 0, 0, elCanvas.width, elCanvas.height);

    konteks.restore();

    // --- STEP 3: OPTIMASI SIZE (COMPRESSION) ---
    const gambarBase64 = elCanvas.toDataURL('image/jpeg', 0.7); // Quality 70%

    console.log(`📷 Foto captured, size: ${(gambarBase64.length / 1024).toFixed(2)} KB`);

    // Disable tombol agar tidak klik ganda
    if (btnShutter) {
        btnShutter.disabled = true;
        btnShutter.classList.add('opacity-50', 'cursor-not-allowed');
        btnShutter.innerHTML = '<div class="w-full h-full rounded-full border-[3px] border-gray-400 flex items-center justify-center"><div class="w-5 h-5 border-2 border-t-transparent border-gray-600 rounded-full animate-spin"></div></div>';
    }

    // --- STEP 4: AMBIL GPS & KIRIM DATA ---
    if (!navigator.geolocation) {
        alert("Geolocation tidak didukung oleh browser Anda.");
        resetShutterButton(btnShutter);
        return;
    }

    console.log("📍 Meminta lokasi GPS...");

    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log(`✅ GPS berhasil: ${position.coords.latitude}, ${position.coords.longitude}`);

            const data = {
                photo: gambarBase64,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                _token: document.querySelector('meta[name="csrf-token"]')?.content || ""
            };

            console.log("📤 Mengirim data ke server...");

            fetch("/absensi/foto/proses", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(async response => {
                    const res = await response.json();

                    if (!response.ok) {
                        throw new Error(res.message || `Server error: ${response.status}`);
                    }

                    return res;
                })
                .then(res => {
                    console.log("✅ Absensi berhasil:", res);
                    Swal.fire({
                        title: 'Absensi Berhasil!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'Mantap!',
                        confirmButtonColor: '#10b981', // Warna hijau emerald (sesuai Tailwind)
                        timer: 3000, // Akan tertutup otomatis dalam 3 detik
                        timerProgressBar: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        }
                    }).then((result) => {
                        // Jika Anda ingin halaman reload otomatis setelah user klik OK
                        if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                            location.reload();
                        }
                    });
                    hentikanKameraAbsen();

                    // Reload halaman setelah delay
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                })
                .catch(err => {
                    console.error("❌ Error kirim absensi:", err);
                    alert("❌ Gagal mengirim absensi:\n\n" + err.message);
                    resetShutterButton(btnShutter);
                });
        },
        function (error) {
            console.error("❌ GPS Error:", error);

            let pesanError = "Gagal mendapatkan lokasi.";

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    pesanError = "❌ Akses lokasi ditolak.\n\nMohon izinkan akses lokasi di browser settings.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    pesanError = "❌ Informasi lokasi tidak tersedia.\n\nPastikan GPS aktif dan sinyal baik.";
                    break;
                case error.TIMEOUT:
                    pesanError = "❌ Waktu permintaan lokasi habis.\n\nCoba lagi atau periksa koneksi GPS.";
                    break;
            }

            alert(pesanError);
            resetShutterButton(btnShutter);
        },
        {
            enableHighAccuracy: true,
            timeout: 15000, // 15 detik
            maximumAge: 0
        }
    );
}

/**
 * Reset shutter button ke kondisi semula
 */
function resetShutterButton(btnShutter) {
    if (btnShutter) {
        btnShutter.disabled = false;
        btnShutter.classList.remove('opacity-50', 'cursor-not-allowed');
        btnShutter.innerHTML = '<div class="w-full h-full rounded-full border-[3px] border-gray-800 group-active:border-gray-600 transition-colors"></div>';
    }
}

// ============================================
// DEBUG HELPER
// ============================================

// Log info kamera yang tersedia (untuk debugging)
if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
    navigator.mediaDevices.enumerateDevices()
        .then(devices => {
            const cameras = devices.filter(d => d.kind === 'videoinput');
            console.log(`📹 Kamera tersedia: ${cameras.length}`);
            cameras.forEach((cam, i) => {
                console.log(`  ${i + 1}. ${cam.label || 'Camera ' + (i + 1)}`);
            });
        })
        .catch(err => console.error("Error enumerating devices:", err));
}