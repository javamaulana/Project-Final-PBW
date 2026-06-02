<?php
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dinas') {
    header("Location: ../login.php");
    exit();
}

$pesan = "";
$tipe_pesan = "";

if (isset($_POST['submit'])) {
    $id_petugas = $_SESSION['id_user'];
    $nama_warga = mysqli_real_escape_string($conn, trim($_POST['nama_warga']));
    $nik_warga = mysqli_real_escape_string($conn, trim($_POST['nik_warga']));
    $alamat_warga = mysqli_real_escape_string($conn, trim($_POST['alamat_warga']));
    
    $nomor_registrasi = "REG-" . date('Ymd') . "-" . rand(100, 999);

    if (!empty($nama_warga) && !empty($nik_warga) && !empty($alamat_warga)) {
        if (strlen($nik_warga) === 16) {
            
            $query_insert = "INSERT INTO layanan_ktp (nomor_registrasi, nik_warga, nama_warga, alamat_warga, status_sekarang) 
                             VALUES ('$nomor_registrasi', '$nik_warga', '$nama_warga', '$alamat_warga', 'Loket Pendaftaran')";
            
            if (mysqli_query($conn, $query_insert)) {
                $id_layanan_baru = mysqli_insert_id($conn);
                $keterangan_awal = "Berkas persyaratan fisik telah dicek manual oleh staff loket dan dinyatakan LENGKAP.";

                $query_log = "INSERT INTO tracking_log (id_layanan, status, keterangan, id_petugas) 
                              VALUES ('$id_layanan_baru', 'Loket Pendaftaran', '$keterangan_awal', '$id_petugas')";
                
                mysqli_query($conn, $query_log);

                $_SESSION['sukses_reg'] = $nomor_registrasi;
                header("Location: dashboard.php");
                exit();
            } else {
                $pesan = "Gagal memproses query registrasi ke database.";
                $tipe_pesan = "danger";
            }
        } else {
            $pesan = "Nomor NIK tidak valid! Panjang karakter harus tepat 16 digit angka.";
            $tipe_pesan = "danger";
        }
    } else {
        $pesan = "Seluruh kolom data warga wajib diisi!";
        $tipe_pesan = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berkas Warga - Disdukcapil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: sans-serif; }
        .navbar { background-color: #212529 !important; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">🏢 Konsol Internal Petugas Dukcapil</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Batal</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0 fs-6 fw-bold">Form Registrasi Kunjungan & Verifikasi Manual Berkas</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan; ?> text-center" role="alert">
                                <?= $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <form action="tambah_warga.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Lengkap Warga (Sesuai KK) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_warga" required placeholder="Masukkan nama lengkap warga...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nomor Induk Kependudukan (NIK) <span class="text-danger">* (16 Digit)</span></label>
                                <input type="number" class="form-control" name="nik_warga" required placeholder="Contoh: 1371XXXXXXXXXXXX">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat Rumah Tinggal Sesuai KK <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alamat_warga" rows="3" required placeholder="Masukkan alamat lengkap rumah warga..."></textarea>
                            </div>
                            <hr class="mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="dashboard.php" class="btn btn-secondary fw-bold">Kembali</a>
                                <button type="submit" name="submit" class="btn btn-success px-4 fw-bold">Verifikasi & Terbitkan Nomor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>