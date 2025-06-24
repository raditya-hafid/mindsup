<?php
session_start();
require '../komponen/koneksi.php';

// Pastikan hanya siswa yang login yang bisa akses
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    die("Akses ditolak. Anda harus login sebagai siswa untuk memberikan ulasan.");
}

// Validasi ID detail pembelian dari URL
if (!isset($_GET['id_detail']) || !is_numeric($_GET['id_detail'])) {
    die("Error: ID Detail Pembelian tidak valid.");
}
$id_detail = (int)$_GET['id_detail'];

// Ambil data detail pembelian untuk memastikan kepemilikan dan menampilkan judul kursus
$id_siswa = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT dp.*, k.judul, k.id_kursus FROM detail_pembelian dp JOIN pembelian p ON dp.id_pembelian = p.id_pembelian JOIN kursus k ON dp.id_kursus = k.id_kursus WHERE dp.id_detail = ? AND p.id_siswa = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_detail, $id_siswa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) === 0) {
    die("Anda tidak memiliki akses untuk memberi ulasan pada kursus ini atau data tidak ditemukan.");
}
$data_ulasan = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php require '../head/head.php'; ?>
    <title>Beri Ulasan untuk Kursus: <?php echo htmlspecialchars($data_ulasan['judul']); ?></title>
</head>
<body>
    <?php require '../komponen/nav.php'; ?>

    <main class="container mt-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3>Tulis Ulasan Anda</h3>
                        <p class="mb-0">Untuk kursus: <strong><?php echo htmlspecialchars($data_ulasan['judul']); ?></strong></p>
                    </div>
                    <div class="card-body">
                        <form action="proses_ulasan.php" method="POST">
                            <input type="hidden" name="id_detail" value="<?php echo $id_detail; ?>">
                            
                            <div class="mb-3">Beri Rating:</div>
                            <div class="rating d-flex justify-content-start gap-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" name="rating" id="b<?= $i ?>" value="<?= $i ?>" class="btn-check" required>
                                    <label class="btn btn-outline-warning" for="b<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </div>

                            <br>
                            
                            <div class="mb-3">
                                <label for="ulasan" class="form-label">Komentar / Ulasan Anda</label>
                                <textarea name="ulasan" id="ulasan" class="form-control" rows="5" required placeholder="Tuliskan pengalaman belajar Anda di sini..."><?php echo htmlspecialchars($data_ulasan['ulasan'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                            <a href="detail_kursus.php?id=<?php echo $data_ulasan['id_kursus']; ?>" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require '../komponen/footer.php'; ?>
</body>
</html>