<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login | Sistem Absensi Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png" style="width: 40px">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .input-glow:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-badge {
            transition: all 0.3s ease;
        }

        .feature-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .logo-glow {
            filter: drop-shadow(0 4px 20px rgba(37, 99, 235, 0.3));
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">



    <!-- Background Decorative Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl">
        </div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md relative z-10 fade-in">
        <!-- Success Message -->
        @if (session('status'))
            <div
                class="alert-success bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-2xl mb-6 overflow-hidden shadow-lg">
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-900 text-base mb-1">Password Berhasil Diubah!</h3>
                            <p class="text-sm text-green-700 font-medium">{{ session('status') }}</p>
                            <p class="text-xs text-green-600 mt-2">Silakan gunakan password baru Anda untuk login.</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            class="flex-shrink-0 text-green-600 hover:text-green-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="h-1 bg-green-200">
                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 animate-progress"
                        style="width: 0%; animation: progress 5s linear forwards;"></div>
                </div>
            </div>

            <style>
                @keyframes progress {
                    from {
                        width: 100%;
                    }

                    to {
                        width: 0%;
                    }
                }
            </style>
        @endif
        <!-- Login Card -->
        <div class="glass-effect rounded-3xl shadow-2xl p-8 relative overflow-hidden">
            <!-- Decorative Gradient Bar -->
            <div class="absolute top-0 left-0 right-0 h-1.5 gradient-bg"></div>

            <!-- Logo & Header -->
            <div class="text-center mb-8 mt-2">
                <div class="inline-block mb-6 floating">
                    <img src="{{ asset('assets/images/logo/logo.png') }}" class="mx-auto w-32 logo-glow drop-shadow-xl">
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang Kembali</h1>
                <p class="text-gray-500 text-sm font-medium">Masuk untuk melanjutkan ke dashboard absensi</p>
            </div>

            <!-- Login Form -->
            <form id="login" action="/login/process" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide ml-1">
                        Alamat Email
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail"
                                class="w-5 h-5 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="email" name="email" required
                            class="w-full h-14 pl-12 pr-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-800 placeholder-gray-400 input-glow"
                            placeholder="nama@perusahaan.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Kata Sandi
                        </label>
                        <a href="/forgot-password"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                            Lupa Password?
                        </a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock"
                                class="w-5 h-5 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full h-14 pl-12 pr-12 bg-gray-50 border-2 border-gray-200 rounded-xl focus:bg-white focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-800 placeholder-gray-400 input-glow"
                            placeholder="••••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-600 focus:ring-offset-0 transition-all cursor-pointer">
                        <span
                            class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
                            Ingat saya selama 30 hari
                        </span>
                    </label>
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button type="submit" id="btnLogin"
                        class="w-full h-14 rounded-xl font-bold text-white btn-gradient hover:shadow-2xl active:scale-[0.98] transition-all text-sm uppercase tracking-wider relative overflow-hidden">
                        <span class="relative z-10">Masuk ke Dashboard</span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t-2 border-gray-100"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="px-4 text-gray-400 bg-white font-semibold tracking-wider">Fitur Keamanan</span>
                </div>
            </div>

            <!-- Security Features -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="feature-badge bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl p-4 text-center border border-blue-200/50">
                    <div
                        class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 rounded-lg mb-2 shadow-lg">
                        <i data-lucide="scan-face" class="w-5 h-5 text-white"></i>
                    </div>
                    <p class="text-xs font-bold text-blue-900">Face Recognition</p>
                    <p class="text-[10px] text-blue-600 font-medium mt-0.5">Verifikasi Wajah</p>
                </div>

                <div
                    class="feature-badge bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl p-4 text-center border border-blue-200/50">
                    <div
                        class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 rounded-lg mb-2 shadow-lg">
                        <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                    </div>
                    <p class="text-xs font-bold text-blue-900">GPS Tracking</p>
                    <p class="text-[10px] text-blue-700 font-medium mt-0.5">Lokasi Terverifikasi</p>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500 font-medium">
                    Belum punya akun?
                    <a href="#"
                        class="text-blue-600 font-semibold hover:text-blue-700 transition-colors">Hubungi Admin</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-white/80 text-xs font-medium">
                &copy; 2026 Mendunia Absensi System. All rights reserved.
            </p>
            <div class="flex items-center justify-center gap-4 mt-3">
                <a href="#"
                    class="text-white/60 hover:text-white text-xs font-medium transition-colors">Kebijakan Privasi</a>
                <span class="text-white/40">•</span>
                <a href="#" class="text-white/60 hover:text-white text-xs font-medium transition-colors">Syarat
                    & Ketentuan</a>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Setup CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // AJAX LOGIN
        $('#login').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = $('#btnLogin');
            let originalText = btn.html();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                beforeSend() {
                    btn.prop('disabled', true).html(`
                        <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    `);
                },
                success(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil',
                        text: res.message ?? 'Selamat datang di dashboard',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl',
                            title: 'text-xl font-bold',
                            htmlContainer: 'text-sm'
                        }
                    });

                    setTimeout(() => {
                        window.location.href = res.redirect ?? '/dashboard';
                    }, 1500);
                },
                error(xhr) {
                    let msg = 'Email atau password salah';
                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;

                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: msg,
                        customClass: {
                            popup: 'rounded-2xl',
                            title: 'text-xl font-bold',
                            htmlContainer: 'text-sm'
                        },
                        confirmButtonColor: '#2563eb'
                    });

                    btn.prop('disabled', false).html(originalText);
                },
                complete() {
                    // Re-initialize icons after button content changes
                    lucide.createIcons();
                }
            });
        });

        // TOGGLE PASSWORD
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }

            lucide.createIcons();
        }

        // Add subtle animations on input focus
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-[1.02]');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-[1.02]');
            });
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

</body>

</html>
