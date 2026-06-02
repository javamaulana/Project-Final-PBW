<?php
require_once 'config/database.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'dinas') {
    header("Location: dinas/dashboard.php");
    exit();
}

$pesan = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $password_md5 = md5($password);

        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password_md5' AND role = 'dinas'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            header("Location: dinas/dashboard.php");
            exit();
        } else {
            $pesan = "Kombinasi Username dan Password Petugas salah!";
        }
    } else {
        $pesan = "Seluruh kolom isian login wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Disdukcapil Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .card-header { background-color: #212529; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
        .btn-dark { background-color: #212529; border: none; }
        .btn-dark:hover { background-color: #000000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 mt-5">
                <div class="card mt-4">
                    <div class="card-header text-center py-3">
                        <h5 class="mb-0">Portal Internal Petugas Dukcapil</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username Petugas</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" name="login" class="btn btn-dark py-2 fw-bold">Masuk Sistem</button>
                            </div>
                        </form>
                        <hr>
                        <p class="text-center mb-0"><a href="index.php" class="text-success text-decoration-none fw-bold">&larr; Kembali ke Pelacakan Warga</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>