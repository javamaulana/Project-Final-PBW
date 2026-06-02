<?php
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dinas') {
    header("Location: ../login.php");
    exit();
}

$pesan = "";
$tipe_pesan = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id_layanan = (int)$_GET['id'];
$id_petugas = $_SESSION['id_user'];

$query_view = "SELECT * FROM layanan_ktp WHERE id_layanan = '$id_layanan'";
$result_view = mysqli_query($conn, $query_view);

if (mysqli_num_rows($result_view) !== 1) {
    header("Location: dashboard.php");
    exit();
}

$layanan = mysqli_fetch_assoc($result_view);

if (isset($_POST['update'])) {
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_sekarang']);
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan']));

    if (!empty($status_baru) && !empty($keterangan)) {
        
        $query_update = "UPDATE layanan_ktp SET status_sekarang = '$status_baru' WHERE id_layanan = '$id_layanan'";
        
        if (mysqli_query($conn, $query_update)) {
            $query_log = "INSERT INTO tracking_log (id_layanan, status, keterangan, id_petugas) 
                          VALUES ('$id_layanan', '$status_baru', '$keterangan', '$id_petugas')";
            
            if (mysqli_query($conn, $query_log)) {
                header("Location: dashboard.php");
                exit();
            } else {
                $pesan = "Langkah terupdate, namun pencatatan riwayat log mutasi gagal.";
                $tipe_pesan = "danger";
            }
        } else {
            $pesan = "Gagal memperbarui tahapan operasional.";
            $tipe_pesan = "danger";
        }
    } else {
        $pesan = "Pilihan tahapan baru dan catatan update lapangan wajib diisi!";
        $tipe_pesan = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Progress Layanan - Disdukcapil</title>
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
            <a class="navbar-brand" href="dashboard.php">Konsol Internal Petugas Dukcapil</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Batal</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0 fs-6 fw-bold">Pembaruan Tahapan Alur Pembuatan KTP Baru (Real-Time)</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan; ?> text-center" role="alert">
                                <?= $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <table class="table table-bordered bg-white mb-4">
                            <tr>
                                <th width="35%" class="table-light">No. Registrasi Warga</th>
                                <td><span class="font-monospace fw-bold text-success"><?= $layanan['nomor_registrasi']; ?></span></td>
                            </tr>
                            <tr>
                                <th class="table-light">Nama Warga Pemohon</th>
                                <td><?= htmlspecialchars($layanan['nama_warga']); ?></td>
                            </tr>
                            <tr>
                                <th class="table-light">Tahapan Saat Ini</th>
                                <td><span class="badge bg-secondary p-2 fs-7"><?= $layanan['status_sekarang']; ?></span></td>
                            </tr>
                        </table>

                        <form action="update_status.php?id=<?= $id_layanan; ?>" method="POST">
                            <div class="mb-4">
                                <label class="form-label d-block fw-bold mb-3">Pindahkan ke Tahap Alur Selanjutnya <span class="text-danger">*</span></label>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status_sekarang" id="step2" value="Verifikasi Berkas" <?= ($layanan['status_sekarang'] === 'Verifikasi Berkas') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label text-info fw-bold" for="step2">1. Verifikasi Berkas & Validasi Data Sistem</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status_sekarang" id="step3" value="Perekaman Biometrik" <?= ($layanan['status_sekarang'] === 'Perekaman Biometrik') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label text-primary fw-bold" for="step3">2. Perekaman Data Biometrik (Foto, Sidik Jari, Iris Mata)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status_sekarang" id="step4" value="Pencetakan KTP" <?= ($layanan['status_sekarang'] === 'Pencetakan KTP') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label text-dark fw-bold" for="step4">3. Proses Pencetakan Fisik KTP-el</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status_sekarang" id="step5" value="Siap Diambil" <?= ($layanan['status_sekarang'] === 'Siap Diambil') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label text-success fw-bold" for="step5">4. Selesai & Fisik KTP-el Siap Diambil di Loket</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-bold">Catatan Perkembangan Petugas Lapangan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Berkas persyaratan valid, warga silakan mengantre di ruang foto biometrik." required></textarea>
                            </div>
                            
                            <hr class="mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="dashboard.php" class="btn btn-secondary fw-bold">Kembali</a>
                                <button type="submit" name="update" class="btn btn-primary px-4 fw-bold">Simpan Progres</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>