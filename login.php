<?php
require 'config.php';
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK Handphone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/animated-bg.css">
    <style>
        .login-card {
            max-width: 450px;
            margin: 100px auto;
            border-radius: 25px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 45px rgba(0,0,0,0.2);
        }
        .login-card .card-header {
            background: linear-gradient(135deg, #f8bbd0, #bbdefb);
            border-radius: 25px 25px 0 0;
            padding: 25px;
            text-align: center;
        }
        .login-card .card-header i {
            font-size: 3rem;
            color: #d81b60;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 30px;
            transition: transform 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-3px);
        }
        .input-group-text {
            background: linear-gradient(135deg, #f8bbd0, #bbdefb);
            border: none;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ec407a;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card">
        <div class="card-header">
            <i class="bi bi-phone-fill"></i>
            <h3 class="mt-2" style="color:#6a1b2a;">Ratu Handphone</h3>
            <p class="text-muted">Sistem Pendukung Keputusan - TOPSIS</p>
        </div>
        <div class="card-body p-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-login w-100 text-white">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
            <hr>
            <p class="text-center text-muted small">Demo: username: admin, password: admin</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>