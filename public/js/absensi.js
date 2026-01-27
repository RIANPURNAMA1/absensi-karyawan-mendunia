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

// Variabel untuk stabilitas wajah
let stabilityScore = 0;
const STABILITY_REQUIRED = 15; // ~1,5 detik wajah stabil
let lastX = 0;
let lastY = 0;
let isCapturing = false;
let absensiProcessing = false; // 🔥 kunci request server
let detectionPaused = false; // 🔥 pause deteksi kamera

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
    stabilityScore = 0;
    isCapturing = false;
    lastX = 0;
    lastY = 0;
    document.getElementById("instructionTextAbsen").textContent =
        "Posisikan wajah di tengah lingkaran dan diam sebentar...";
}

async function loadModels() {
    if (isEngineReady) return;
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
    ]);
    isEngineReady = true;
}

async function startCamera() {
    const video = document.getElementById("videoStream");
    const canvas = document.getElementById("canvasStream");

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user",
            },
        });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
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
        if (!video.videoWidth || !isEngineReady || isCapturing) return;

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

            // Logika stabilitas
            const movement = Math.abs(box.x - lastX) + Math.abs(box.y - lastY);
            stabilityScore = movement < 7 ? stabilityScore + 1 : 0;

            lastX = box.x;
            lastY = box.y;

            // Feedback visual
            ctx.lineWidth = 4;
            if (stabilityScore > 5) {
                ctx.strokeStyle = "#3b82f6";
                document.getElementById("instructionTextAbsen").textContent =
                    "Tahan posisi, sedang memproses...";
            } else {
                ctx.strokeStyle = "#f87171";
                document.getElementById("instructionTextAbsen").textContent =
                    "Posisikan wajah dengan tenang...";
            }
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Jika stabil cukup lama -> kirim absensi
            // ... di dalam setInterval startRealtimeDetection ...
            if (stabilityScore >= STABILITY_REQUIRED && !absensiProcessing) {
                absensiProcessing = true; // Kunci agar tidak masuk ke sini lagi (stop spam)
                isCapturing = true;

                // Tampilkan loading agar user tahu proses sedang berjalan
                Swal.fire({
                    title: "Sedang Memproses...",
                    text: "Mohon tunggu sebentar",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        prosesAbsensiWajah(
                            resized.descriptor,
                            pos.coords.latitude,
                            pos.coords.longitude,
                        );
                    },
                    (err) => {
                        absensiProcessing = false; // Buka kunci jika error GPS
                        isCapturing = false;
                        Swal.fire(
                            "Error Lokasi",
                            "Gagal mengambil koordinat GPS.",
                            "error",
                        );
                    },
                );
            }
        } else {
            stabilityScore = 0;
            document.getElementById("instructionTextAbsen").textContent =
                "Wajah tidak terlihat...";
        }
    }, 100);
}

function prosesAbsensiWajah(faceEmbedding, latitude, longitude) {
    // 1. Kunci proses di awal agar tidak dipanggil berulang kali oleh interval
    if (window.absensiProcessing) return;
    window.absensiProcessing = true;

    const payload = {
        face_embedding: JSON.stringify(Array.from(faceEmbedding)),
        latitude: latitude,
        longitude: longitude,
    };

    // Tampilkan loading agar user tahu proses sedang berjalan
    Swal.fire({
        title: "Memproses Absensi...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

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
                window.absensiProcessing = false; // Buka kunci
                Swal.fire({
                    icon: "info",
                    title: "Sudah Absen",
                    text: "Anda sudah melakukan absensi hari ini.",
                    timer: 2000,
                    showConfirmButton: false,
                }).then(() => closeAbsenManual());
                return;
            }

            // Jalankan AJAX kedua untuk submit data
            $.ajax({
                url: targetUrl,
                method: "POST",
                data: payload,
                success: function (r) {
                    // Berhasil: Tidak perlu buka kunci karena biasanya reload/tutup
                    Swal.fire({
                        icon: "success",
                        title: titleText + " Berhasil",
                        text: r.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        window.absensiProcessing = false; // Reset sebelum pindah
                        closeAbsenManual();
                        loadRiwayatRealtime();
                    });
                },
                error: function (xhr) {
                    window.absensiProcessing = false; // PENTING: Buka kunci jika gagal
                    isCapturing = false;
                    stabilityScore = 0;

                    let pesanError =
                        xhr.responseJSON?.message ??
                        "Terjadi kesalahan pada sistem.";

                    Swal.fire({
                        icon: "error",
                        title: "Absensi Gagal",
                        text: pesanError,
                        confirmButtonColor: "#d33",
                        confirmButtonText: "Coba Lagi",
                    }).then(() => {
                        // Jalankan deteksi lagi hanya jika user menekan "Coba Lagi"
                        if ($("#modalAbsenManual").is(":visible")) {
                            startRealtimeDetection(
                                document.getElementById("videoStream"),
                                document.getElementById("canvasStream"),
                            );
                        }
                    });
                },
            });
        },
        error: function (xhr) {
            window.absensiProcessing = false; // Buka kunci jika pengecekan status gagal
            isCapturing = false;
            stabilityScore = 0;

            Swal.fire({
                icon: "error",
                title: "Gagal Koneksi",
                text: xhr.responseJSON?.message ?? "Terjadi kesalahan sistem.",
            });
        },
    });
}

function handleError(xhr) {
    absensiProcessing = false;
    isCapturing = false;
    stabilityScore = 0;

    // 🔴 STOP semua proses deteksi
    detectionPaused = true;
    clearInterval(detectionInterval);

    const video = document.getElementById("videoAbsen");
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }

    // TAMPILKAN PESAN ERROR SAJA
    Swal.fire({
        icon: "error",
        title: "Absensi Gagal",
        text: xhr.responseJSON?.message ?? "Lokasi Anda di luar area absensi.",
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

async function startRealtimeDetectionReg() {
    const video = document.getElementById("videoReg");
    const canvas = document.getElementById("canvasReg");
    if (!video || !canvas) return;

    const displaySize = {
        width: 640,
        height: 480,
    };
    faceapi.matchDimensions(canvas, displaySize);

    detectionIntervalReg = setInterval(async () => {
        if (!video.videoWidth || !isEngineReady || isCapturing) return;

        const detection = await faceapi
            .detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions({
                    inputSize: 224,
                    scoreThreshold: 0.5,
                }),
            )
            .withFaceLandmarks();

        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (detection) {
            const resized = faceapi.resizeResults(detection, displaySize);
            const box = resized.detection.box;

            // Logika Stabilitas: Cek pergeseran wajah
            const movement = Math.abs(box.x - lastX) + Math.abs(box.y - lastY);

            if (movement < 7) {
                stabilityScore++;
            } else {
                stabilityScore = 0;
            }

            lastX = box.x;
            lastY = box.y;

            // Feedback Visual
            ctx.lineWidth = 4;
            if (stabilityScore > 5) {
                ctx.strokeStyle = "#3b82f6"; // Biru (Proses diam)
                $("#instructionText")
                    .text("Tahan posisi, sedang memproses...")
                    .addClass("text-blue-600");
            } else {
                ctx.strokeStyle = "#f87171"; // Merah (Bergerak/Cari wajah)
                $("#instructionText")
                    .text("Posisikan wajah dengan tenang...")
                    .removeClass("text-blue-600 text-green-600");
            }
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Jika sudah stabil cukup lama, lakukan capture
            if (stabilityScore >= STABILITY_REQUIRED) {
                isCapturing = true;
                clearInterval(detectionIntervalReg);

                $("#instructionText")
                    .text("Wajah Terdeteksi! Verifikasi...")
                    .addClass("text-green-600");
                $("#btnCaptureWajah")
                    .removeClass("bg-gray-400")
                    .addClass("bg-green-600")
                    .text("Memproses...");

                prosesRegistrasiWajah();
            }
        } else {
            stabilityScore = 0;
            $("#instructionText")
                .text("Wajah tidak terlihat...")
                .removeClass("text-blue-600");
        }
    }, 100);
}

// 1. Tambahkan variabel pengunci di luar fungsi (global scope di file js Anda)
let isProcessingReg = false;

async function prosesRegistrasiWajah() {
    // 2. CEK: Jika sedang memproses, jangan jalankan fungsi ini lagi (anti-spam)
    if (isProcessingReg) return;
    isProcessingReg = true; // Kunci proses

    const video = document.getElementById("videoReg");

    // Tampilkan loading agar user tahu proses sedang berjalan
    Swal.fire({
        title: "Sedang Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
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
        isProcessingReg = false; // Buka kunci karena gagal deteksi
        isCapturing = false;
        stabilityScore = 0;
        Swal.close();
        $("#instructionText")
            .text("Gagal mengambil data, ulangi posisi diam...")
            .addClass("text-red-500");
        startRealtimeDetectionReg();
        return;
    }

    $.ajax({
        url: window.routes.updateFace,
        method: "POST",
        data: {
            face_embedding: JSON.stringify(Array.from(detection.descriptor)),
        },
        success: function (response) {
            // Jika berhasil, tidak perlu buka kunci karena halaman akan reload
            Swal.fire({
                icon: "success",
                title: "Verifikasi Berhasil",
                text: "Data wajah Anda sudah tersimpan.",
                timer: 2000,
                showConfirmButton: false,
            }).then(() => location.reload());
        },
        error: function (xhr) {
            console.error(xhr.responseText);

            // 3. PENTING: Buka kunci kembali agar user bisa mencoba lagi setelah klik "Coba Lagi"
            isProcessingReg = false;
            isCapturing = false;
            stabilityScore = 0;

            let msg = xhr.responseJSON?.message ?? "Terjadi kesalahan sistem.";

            Swal.fire({
                icon: "warning",
                title: "Gagal Terverifikasi",
                text: msg,
                confirmButtonText: "Coba Lagi",
            }).then(() => {
                $("#instructionText")
                    .text("Posisikan wajah kembali...")
                    .removeClass("text-green-600");
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
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });