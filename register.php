<?php
require_once 'config/database.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'dinas') {
        header("Location: dinas/dashboard.php");
        exit();
    } else {
        header("Location: masyarakat/dashboard.php");
        exit();
    }
}

$pesan = "";
$tipe_pesan = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $role = 'masyarakat'; 

    if (!empty($username) && !empty($password) && !empty($nama_lengkap)) {
        $query_cek = "SELECT username FROM users WHERE username = '$username'";
        $result_cek = mysqli_query($conn, $query_cek);

        if (mysqli_num_rows($result_cek) > 0) {
            $pesan = "Username sudah terdaftar! Silakan pilih username lain.";
            $tipe_pesan = "danger";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            $query_insert = "INSERT INTO users (username, password, nama_lengkap, role) 
                             VALUES ('$username', '$password_hashed', '$nama_lengkap', '$role')";
            
            if (mysqli_query($conn, $query_insert)) {
                $pesan = "Akun warga berhasil dibuat! Silakan login.";
                $tipe_pesan = "success";
            } else {
                $pesan = "Gagal mendaftarkan akun, coba lagi.";
                $tipe_pesan = "danger";
            }
        }
    } else {
        $pesan = "Seluruh kolom data wajib diisi dengan benar!";
        $tipe_pesan = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Warga - Jejak Berkas</title>
    <link href="https:
    <style>
        body { background-color: #f4f6f9; font-family: sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .card-header { background-color: #198754; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
        .btn-success { background-color: #198754; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card mt-4">
                    <div class="card-header text-center py-3">
                        <h5 class="mb-0">Pendaftaran Akun Layanan Mandiri Warga</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan; ?>" role="alert">
                                <?= $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username Pilihan</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap Anda (Sesuai KTP/KK)</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required placeholder="Contoh: Java Maulana">
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" name="register" class="btn btn-success py-2">Daftar Akun</button>
                            </div>
                        </form>
                        <hr>
                        <p class="text-center mb-0">Sudah punya akun mandiri? <a href="login.php" class="text-success text-decoration-none fw-bold">Login di sini</a></p>
                        <p class="text-center mt-2 mb-0"><a href="index.php" class="text-muted text-decoration-none">&larr; Beranda Utama</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>