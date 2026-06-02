<?php
require_once 'config/database.php';

$reg = "";
$layanan = null;
$logs = array();
$error_lacak = "";

if (isset($_GET['reg'])) {
    $reg = mysqli_real_escape_string($conn, trim($_GET['reg']));
    
    if (!empty($reg)) {
        // Mengambil data utama antrean berdasarkan nomor registrasi loket yang diinput warga
        $query_layanan = "SELECT * FROM layanan_ktp WHERE nomor_registrasi = '$reg'";
        $result_layanan = mysqli_query($conn, $query_layanan);

        if (mysqli_num_rows($result_layanan) === 1) {
            $layanan = mysqli_fetch_assoc($result_layanan);
            $id_layanan = $layanan['id_layanan'];

            $query_log = "SELECT tl.*, u.nama_lengkap AS nama_petugas FROM tracking_log tl 
                          JOIN users u ON tl.id_petugas = u.id_user 
                          WHERE tl.id_layanan = '$id_layanan' 
                          ORDER BY tl.waktu_log DESC";
            $result_log = mysqli_query($conn, $query_log);
            
            while ($row = mysqli_fetch_assoc($result_log)) {
                $logs[] = $row;
            }
        } else {
            $error_lacak = "Nomor Registrasi tidak terdaftar. Silakan periksa kembali lembar tanda terima Anda.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pelayanan KTP-el - Dinas Dukcapil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .navbar { background-color: #198754 !important; }
        .jumbotron { background-color: #ffffff; padding: 40px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .timeline { position: relative; border-left: 3px solid #198754; padding-left: 20px; margin-left: 10px; list-style: none; }
        .timeline-item { position: relative; margin-bottom: 25px; }
        .timeline-badge { position: absolute; left: -29px; top: 4px; background: #198754; border-radius: 50%; width: 15px; height: 15px; }
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
            <a class="navbar-brand fw-bold" href="index.php">Transparansi Layanan Dukcapil</a>
            <div class="ms-auto">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'dinas'): ?>
                    <a href="dinas/dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard Petugas</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light btn-sm fw-bold text-success">Portal Petugas</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="jumbotron text-center mb-5">
            <h1 class="display-6 fw-bold text-success mb-3">Lacak Progress Pembuatan KTP Baru</h1>
            <p class="col-md-8 mx-auto text-muted">Pantau tahapan pembuatan KTP-el Anda secara real-time. Masukkan Nomor Registrasi resmi yang Anda dapatkan dari petugas loket di bawah ini.</p>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-7">
                    <form action="index.php" method="GET" class="d-flex gap-2">
                        <input type="text" class="form-control form-control-lg text-center" name="reg" value="<?= htmlspecialchars($reg); ?>" placeholder="Contioh: REG-20260524-123" required>
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold">Lacak Status</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (!empty($error_lacak)): ?>
            <div class="alert alert-danger text-center shadow-sm" role="alert">
                ⚠️ <?= $error_lacak; ?>
            </div>
        <?php endif; ?>

        <?php if ($layanan !== null): ?>
            <div class="row">
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="card-title mb-0 fs-6">Data Pendaftaran Pemohon</h5>
                        </div>
                        <div class="card-body p-4">
                            <table class="table table-striped table-sm mb-0">
                                <tr>
                                    <th width="40%">No. Registrasi</th>
                                    <td>: <strong class="text-success"><?= $layanan['nomor_registrasi']; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Nama Warga</th>
                                    <td>: <?= htmlspecialchars($layanan['nama_warga']); ?></td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>: <?= htmlspecialchars($layanan['nik_warga']); ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat Rumah</th>
                                    <td>: <?= htmlspecialchars($layanan['alamat_warga']); ?></td>
                                </tr>
                                <tr>
                                    <th>Langkah Sekarang</th>
                                    <td>: 
                                        <?php
                                        $status = $layanan['status_sekarang'];
                                        $badge = "badge-success";
                                        if ($status === 'Loket Pendaftaran') $badge = "badge-warning";
                                        if ($status === 'Verifikasi Berkas') $badge = "badge-info";
                                        if ($status === 'Perekaman Biometrik') $badge = "badge-primary";
                                        if ($status === 'Pencetakan KTP') $badge = "badge-dark";
                                        ?>
                                        <span class="badge <?= $badge; ?> p-2 fs-7"><?= $status; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Waktu Registrasi</th>
                                    <td>: <?= date('d-m-Y H:i', strtotime($layanan['created_at'])); ?> WIB</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="card-title mb-0 fs-6">Alur Riwayat Tahapan Layanan (Sederhana & Transparan)</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (count($logs) > 0): ?>
                                <ul class="timeline mb-0">
                                    <?php foreach ($logs as $log): ?>
                                        <li class="timeline-item">
                                            <div class="timeline-badge"></div>
                                            <div class="ps-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <?php
                                                    $st_log = $log['status'];
                                                    $bg_log = "badge-success";
                                                    if ($st_log === 'Loket Pendaftaran') $bg_log = "badge-warning";
                                                    if ($st_log === 'Verifikasi Berkas') $bg_log = "badge-info";
                                                    if ($st_log === 'Perekaman Biometrik') $bg_log = "badge-primary";
                                                    if ($st_log === 'Pencetakan KTP') $bg_log = "badge-dark";
                                                    ?>
                                                    <span class="badge <?= $bg_log; ?> fw-bold"><?= $st_log; ?></span>
                                                    <small class="text-muted"><?= date('d M Y H:i', strtotime($log['waktu_log'])); ?> WIB</small>
                                                </div>
                                                <p class="mb-1 text-dark" style="font-size: 14px;"><strong>Update Keterangan Lapangan:</strong> <?= htmlspecialchars($log['keterangan']); ?></p>
                                                <small class="text-muted d-block" style="font-size: 11px;">Oleh Petugas: <?= htmlspecialchars($log['nama_petugas']); ?></small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-center text-muted mb-0">Belum ada perkembangan tahapan operasional.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>