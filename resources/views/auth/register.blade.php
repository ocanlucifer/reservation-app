<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #ffffff;
        }
        .register-card {
            margin-top: 100px;
            padding: 2rem;
            border: none;
            border-radius: 12px;
            background: #1e1e1e;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .register-card h3 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .form-control {
            background-color: #2e2e2e;
            color: #ffffff;
            border: 1px solid #444;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #4CAF50;
            box-shadow: none;
        }
        .form-label {
            font-weight: 500;
            color: #aaaaaa;
        }
        .alert {
            font-size: 0.875rem;
            margin-bottom: 1rem;
            background-color: #d9534f;
            border: none;
            color: #ffffff;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            color: #45a049;
            text-decoration: underline;
        }
        /* Placeholder styling */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.65); /* Placeholder transparan */
        }
        .form-control:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: rgba(255, 255, 255, 0.65);
        }
        .form-control::-ms-input-placeholder { /* Microsoft Edge */
            color: rgba(255, 255, 255, 0.65);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="register-card">
                    <h3 class="text-center">Reservasi Register</h3>

                    <!-- Menampilkan error jika ada -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan email" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Ulangi password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
