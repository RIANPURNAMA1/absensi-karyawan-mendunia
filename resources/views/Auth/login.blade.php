<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login | Sistem Absensi Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

    <!-- LOGIN CARD -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl px-6 py-8">

        <!-- LOGO -->
        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo/logo.png') }}" class="mx-auto w-52">
        </div>

        <!-- FORM -->
        <form id="login" action="/login/process" method="POST" class="space-y-5">
            @csrf

            <!-- EMAIL -->
            <div>
                <label class="text-sm font-medium text-gray-700">Email</label>
                <div class="relative mt-1">
                    <input type="email" name="email" required
                        class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary outline-none"
                        placeholder="email@perusahaan.com">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary">
                        ✉️
                    </span>
                </div>
            </div>

            <!-- PASSWORD -->
            <div>
                <label class="text-sm font-medium text-gray-700">Password</label>
                <div class="relative mt-1">
                    <input type="password" name="password" id="password" required
                        class="w-full h-12 pl-11 pr-11 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary outline-none"
                        placeholder="••••••••">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary">
                        🔒
                    </span>
                    <span onclick="togglePassword()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-gray-500"
                        id="eyeIcon">
                        👁️
                    </span>
                </div>
            </div>

            <!-- REMEMBER -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-primary focus:ring-primary">
                    Ingat saya
                </label>
            </div>

            <!-- BUTTON -->
            <button type="submit" id="btnLogin"
                class="w-full h-12 rounded-xl font-semibold text-white
                       bg-gradient-to-r from-[#00c0ff] to-blue-600
                       hover:opacity-90 transition active:scale-95 shadow-lg">
                Masuk
            </button>
        </form>

        <!-- FOOTER -->
        <p class="text-center text-gray-400 text-xs mt-6">
            Face Recognition • Geolokasi
        </p>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT -->
    <script>
        // AJAX LOGIN
        $('#login').on('submit', function (e) {
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
