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

<body class="font-poppins bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">
        <div
            class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] px-8 py-10 border border-gray-50 relative overflow-hidden">

            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50"></div>

            <div class="text-center mb-10 relative">
                <img src="{{ asset('assets/images/logo/logo.png') }}" class="mx-auto w-44 drop-shadow-sm">
                <h2 class="mt-4 text-xl font-bold text-gray-800 tracking-tight">Selamat Datang</h2>
                <p class="text-gray-400 text-xs mt-1">Silakan masuk untuk akses absensi</p>
            </div>

            <form id="login" action="/login/process" method="POST" class="space-y-6 relative">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-500 uppercase ml-1 tracking-wider">Email
                        Perusahaan</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input type="email" name="email" required
                            class="w-full h-14 pl-12 pr-4 rounded-2xl bg-gray-50 border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-sm font-medium"
                            placeholder="email@perusahaan.com">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-[10px] font-bold text-blue-600 hover:underline">Lupa?</a>
                    </div>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full h-14 pl-12 pr-12 rounded-2xl bg-gray-50 border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-sm font-medium"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-all">
                        <span
                            class="ml-2 text-xs font-medium text-gray-500 group-hover:text-gray-700 transition-colors">Ingat
                            saya di perangkat ini</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" id="btnLogin"
                        class="w-full h-14 rounded-2xl font-bold text-white
                               bg-blue-600 shadow-[0_10px_20px_rgba(37,99,235,0.2)]
                               hover:bg-blue-700 hover:shadow-none active:scale-95 transition-all duration-200">
                        Masuk Ke Akun
                    </button>
                </div>
            </form>

            <div class="mt-10 flex items-center justify-center gap-4">
                <div class="flex items-center gap-1.5 opacity-40">
                    <i data-lucide="scan-face" class="w-4 h-4"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">Face ID</span>
                </div>
                <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                <div class="flex items-center gap-1.5 opacity-40">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">GPS Verified</span>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-400 text-[10px] font-medium mt-8 uppercase tracking-[0.2em]">
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
