<?php
session_start(); // Mulai session jika belum
require '../komponen/koneksi.php';

$list_kursus = [];
// Ambil juga kolom 'harga' dari tabel kursus
$stmt = $conn->prepare("SELECT id_kursus, judul, deskripsi, gambar, kategori, harga FROM kursus ORDER BY id_kursus DESC"); // Tambahkan 'harga'
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $list_kursus[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; ?>
<body>
    <?php require '../komponen/sidebar.php'; ?>
    <?php require '../komponen/nav.php'; // Pastikan nav.php memulai session jika belum ?>

    <main class="mt-5 pt-4">
        <div class="container-fluid featured-courses" id="courses">
            <div style="text-align: center;" class="row justify-content-md-center">
                <div class="col-md-9">
                    <h1>SEMUA KURSUS KAMI</h1>
                    <hr>
                    <p>
                        Temukan beragam kursus menarik yang telah kami siapkan untuk meningkatkan pengetahuan dan keahlian Anda!
                    </p>
                </div>
            </div>

            <div class="row justify-content-md-center">
                <?php if (!empty($list_kursus)) : ?>
                    <?php foreach ($list_kursus as $kursus) : ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="rounded panel-course h-100">
                                <div class="course-image-container">
                                    <?php
                                    $db_thumbnail_path = $kursus['gambar'] ?? '';
                                    $judul_kursus_alt = $kursus['judul'] ?? 'Judul Kursus';
                                    $html_image_src_candidate = '../' . ltrim(htmlspecialchars($db_thumbnail_path), '/');
                                    $server_path_to_check_candidate = realpath(__DIR__ . '/../' . ltrim($db_thumbnail_path, '/'));
                                    $final_image_src = '../asset/placeholder_image.png';
                                    $alt_text = $judul_kursus_alt . " (Placeholder)";

                                    if (!empty($kursus['gambar']) && file_exists($kursus['gambar'])) {
                                        $final_image_src = $kursus['gambar'];
                                        $alt_text = $judul_kursus_alt;
                                    }
                                    ?>
                                    <img src="<?php echo $final_image_src; ?>" alt="<?php echo htmlspecialchars($alt_text); ?>" class="img-fluid rounded-top course-thumbnail">
                                    <div class="course-image-overlay">
                                        <a href="../detail_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="text-white text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                                            <i class="bi bi-eye-fill overlay-icon"></i>
                                            <span class="overlay-text">Lihat Detail</span>
                                        </a>
                                    </div>
                                    <?php if (!empty($kursus['kategori'])) : ?>
                                        <span class="course-category-badge"><?php echo htmlspecialchars($kursus['kategori']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-3 d-flex flex-column course-content">
                                    <h5 class="fw-semibold course-title"><?php echo htmlspecialchars($kursus['judul'] ?? 'Judul Kursus'); ?></h5>
                                    <p class="text-muted course-description" style="word-break: break-all;">
                                        <?php
                                        $deskripsi_text = htmlspecialchars($kursus['deskripsi'] ?? 'Deskripsi tidak tersedia.');
                                        if (strlen($deskripsi_text) > 120) {
                                            echo substr($deskripsi_text, 0, 117) . '...';
                                        } else {
                                            echo $deskripsi_text;
                                        }
                                        ?>
                                    </p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="../detail_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-sm btn-outline-primary course-button-detail">
                                            Pelajari <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                        <form action="../keranjang_aksi.php" method="POST" style="margin-bottom: 0;">
                                            <input type="hidden" name="id_kursus" value="<?php echo $kursus['id_kursus']; ?>">
                                            <input type="hidden" name="nama_kursus" value="<?php echo htmlspecialchars($kursus['judul'] ?? 'Nama Kursus'); ?>">
                                            <input type="hidden" name="harga_kursus" value="<?php echo floatval($kursus['harga'] ?? 0); ?>">
                                            <input type="hidden" name="gambar_kursus" value="<?php echo htmlspecialchars($db_thumbnail_path); ?>">
                                            <input type="hidden" name="action" value="tambah">
                                            <button type="submit" class="btn btn-sm btn-success course-button-keranjang" title="Tambah ke Keranjang">
                                                <i class="bi bi-cart-plus-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">Belum ada kursus yang tersedia saat ini. Silakan cek kembali nanti.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php require '../komponen/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>