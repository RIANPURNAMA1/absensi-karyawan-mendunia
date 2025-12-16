<!doctype html>
<html lang="id">

<head>
    <title>Login | Sistem Absensi Karyawan</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #00c0ff, #0096cc);
        }

        .login-card {
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .25);
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: #00c0ff;
            box-shadow: 0 0 0 .2rem rgba(0, 192, 255, .25);
        }

        .btn-login {
            height: 48px;
            border-radius: 14px;
            background-color: #fea901;
            border: none;
        }

        .btn-login:hover {
            background-color: #ffae19;
        }

        .input-group-text {
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">

                <div class="card login-card border-0">
                    <div class="card-body p-4 p-md-5">

                        <!-- LOGO -->
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/logo/logo.png') }}" width="240">
                        </div>

                        <form id="login" action="/login/process" method="POST">
                            @csrf

                            <!-- EMAIL -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-envelope-fill text-warning"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="email@perusahaan.com" required>
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-lock-fill text-warning"></i>
                                    </span>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="••••••••" required>
                                    <span class="input-group-text bg-white" style="cursor:pointer"
                                        onclick="togglePassword()">
                                        <i class="bi bi-eye-fill" id="eyeIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- REMEMBER -->
                            <div class="d-flex justify-content-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Ingat saya</label>
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login fw-semibold" id="btnLogin">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                                </button>
                            </div>
                        </form>


                        <!-- FOOTER -->
                        <p class="text-center text-muted mt-4 mb-0" style="font-size: 13px;">
                            <i class="bi bi-person-bounding-box me-1"></i> Face Recognition
                            &nbsp;•&nbsp;
                            <i class="bi bi-geo-alt-fill me-1"></i> Geolokasi
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        $(document).ready(function() {

            $('#login').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let btn = $('#btnLogin');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),

                    beforeSend: function() {
                        btn.prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm"></span> Memproses...'
                            );
                    },

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil',
                            text: response.message ?? 'Selamat datang!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.href = response.redirect ?? '/dashboard';
                        }, 1500);
                    },

                    error: function(xhr) {
                        let message = 'Email atau password salah';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors)[0][0];
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: message
                        });
                    },

                    complete: function() {
                        btn.prop('disabled', false)
                            .html('<i class="bi bi-box-arrow-in-right me-1"></i> Masuk');
                    }
                });
            });

        });


        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye-fill');
                eyeIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash-fill');
                eyeIcon.classList.add('bi-eye-fill');
            }
        }
    </script>


</body>

</html>
