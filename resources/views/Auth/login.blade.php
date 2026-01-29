<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login | Sistem Absensi Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link rel="icon" href="{{ asset('assets/compiled/png/LOGO/logo4.png') }}" type="image/x-icon"> --}}
    <link rel="icon" href="{{ asset('assets/images/logo/logo-sm.png') }}" type="image/png" style="width: 40px">
    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#00c0ff'
                    }
                }
            }
        }
    </script>
</head>

<body class="font-poppins bg-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md h-screen mx-auto flex flex-col justify-between px-6 py-6 overflow-hidden">
        <div class="flex-1 flex flex-col justify-center">

            <div class="relative w-full">
                <div class="absolute -top-6 -left-6 w-32 h-32 bg-blue-100/50 rounded-full blur-3xl -z-10"></div>
                <div class="absolute top-10 -right-6 w-24 h-24 bg-indigo-100/40 rounded-full blur-2xl -z-10"></div>

                <div class="text-center mb-8">
                    <img src="{{ asset('assets/images/logo/logo.png') }}" class="mx-auto w-32 mb-4 drop-shadow-sm">
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-500 text-xs mt-1">Silakan masuk untuk akses absensi.</p>
                </div>

                <form id="login" action="/login/process" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-500 uppercase ml-1 tracking-wider">Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="mail"
                                    class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input type="email" name="email" required
                                class="w-full h-12 pl-11 pr-4 bg-gray-50/50 border border-gray-100 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm font-semibold text-gray-700 shadow-sm"
                                placeholder="nama@perusahaan.com">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Password</label>
                            <a href="#" class="text-[10px] font-bold text-blue-600 uppercase">Lupa?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="lock"
                                    class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full h-12 pl-11 pr-11 bg-gray-50/50 border border-gray-100 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm font-semibold text-gray-700 shadow-sm"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 active:scale-90 transition-transform">
                                <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center px-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-0 transition-all">
                            <span class="ml-2 text-[11px] font-bold text-gray-500 uppercase tracking-tight">Ingat
                                Saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="btnLogin"
                            class="w-full h-[50px] rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.97] shadow-lg shadow-blue-200 transition-all text-sm uppercase tracking-widest">
                            Masuk Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-8 flex items-center justify-center gap-4 border-t border-gray-50 pt-6">
                    <div class="flex items-center gap-1.5 opacity-60">
                        <i data-lucide="scan-face" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span class="text-[9px] font-bold uppercase text-gray-600">Face ID</span>
                    </div>
                    <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                    <div class="flex items-center gap-1.5 opacity-60">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span class="text-[9px] font-bold uppercase text-gray-600">GPS Verified</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-400 text-[9px] font-medium py-2 uppercase tracking-[0.2em]">
            &copy; 2026 Mendunia Absensi System
        </p>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT -->
    <script>
        // AJAX LOGIN
        $('#login').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = $('#btnLogin');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                beforeSend() {
                    btn.prop('disabled', true).html('Memproses...');
                },
                success(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil',
                        text: res.message ?? 'Selamat datang',
                        timer: 1500,
                        showConfirmButton: false
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
                        text: msg
                    });
                },
                complete() {
                    btn.prop('disabled', false).html('Masuk');
                }
            });
        });

        // TOGGLE PASSWORD
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>

</html>
