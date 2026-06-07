<?php
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dinas') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM layanan_ktp ORDER BY id_layanan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Dinas - Dukcapil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: sans-serif; }
        .navbar { background-color: #212529 !important; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .table thead { background-color: #343a40; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-info { background-color: #0dcaf0; color: #fff; }
        .badge-primary { background-color: #0d6efd; color: #fff; }
        .badge-dark { background-color: #212529; color: #fff; }
        .badge-success { background-color: #198754; color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Konsol Internal Petugas Dukcapil</a>
            <div class="ms-auto">
                <span class="navbar-text text-white me-3">Petugas Aktif: <strong><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong></span>
                <a href="../logout.php" class="btn btn-danger btn-sm fw-bold">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if (isset($_SESSION['sukses_reg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm text-center py-3" role="alert">
                <h5 class="fw-bold">Pendaftaran Berhasil!</h5>
                <p class="mb-0 fs-5">Berikan Nomor Registrasi ini ke warga untuk pelacakan: <strong class="text-danger font-monospace"><?= $_SESSION['sukses_reg']; ?></strong></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['sukses_reg']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Antrean & Tahapan Pelayanan KTP-el Warga</h2>
            <a href="tambah_warga.php" class="btn btn-success fw-bold">+ Registrasi Kedatangan Warga</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center py-3">No</th>
                                <th width="20%">No. Registrasi</th>
                                <th width="25%">Nama Lengkap Warga</th>
                                <th width="20%">NIK</th>
                                <th width="15%" class="text-center">Tahapan Saat Ini</th>
                                <th width="15%" class="text-center">Aksi Layanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if (mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><span class="badge bg-secondary font-monospace fs-6 p-2"><?= $row['nomor_registrasi']; ?></span></td>
                                        <td><strong><?= htmlspecialchars($row['nama_warga']); ?></strong></td>
                                        <td><?= htmlspecialchars($row['nik_warga']); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $st = $row['status_sekarang'];
                                            $bg_badge = "badge-success";
                                            if ($st === 'Loket Pendaftaran') $bg_badge = "badge-warning";
                                            if ($st === 'Verifikasi Berkas') $bg_badge = "badge-info";
                                            if ($st === 'Perekaman Biometrik') $bg_badge = "badge-primary";
                                            if ($st === 'Pencetakan KTP') $bg_badge = "badge-dark";
                                            ?>
                                            <span class="badge <?= $bg_badge; ?> px-2 py-2"><?= $st; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="update_status.php?id=<?= $row['id_layanan']; ?>" class="btn btn-sm btn-primary fw-bold">⚡ Pindahkan Tahap</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">Belum ada data antrean kunjungan warga hari ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>