<?php
session_start(); // Mulai session jika belum
require '../komponen/koneksi.php'; // Path untuk koneksi database

// Ambil semua data kursus dari database
$list_kursus = [];
// Query untuk mengambil data kursus termasuk kategori
// Menggunakan nama kolom: id_kursus, judul, deskripsi, gambar, kategori
$stmt = $conn->prepare("SELECT id_kursus, judul, deskripsi, gambar, kategori FROM kursus ORDER BY id_kursus DESC");
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
<?php require '../head/head.php'; // Path untuk head HTML ?>
<body>
    <?php require '../komponen/sidebar.php'; // Path untuk sidebar ?>
    <?php require '../komponen/nav.php'; // Path untuk navigasi ?>

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
                                    // Ambil path gambar dari database (misal: 'uploads/thumbnails/namafile.jpg')
                                    $db_thumbnail_path = htmlspecialchars($kursus['gambar'] ?? '');

                                    // Path relatif untuk tag <img> dari lokasi courses/index.php
                                    $html_image_src = '../' . $db_thumbnail_path;

                                    // Path absolut di server untuk pemeriksaan file_exists()
                                    // __DIR__ adalah direktori dari file courses/index.php (C:\...\mindsup\courses)
                                    // Jadi, __DIR__ . '/../' akan mengarah ke C:\...\mindsup\
                                    $server_image_path = realpath(__DIR__ . '/../' . $db_thumbnail_path);
                                    
                                    $final_image_src = '../asset/placeholder_image.png'; // Default ke placeholder

                                    if (!empty($kursus['gambar']) && $server_image_path && file_exists($server_image_path)) {
                                        $final_image_src = $html_image_src;
                                    }
                                    ?>
                                    <img src="<?php echo $final_image_src; ?>" alt="<?php echo htmlspecialchars($kursus['judul'] ?? 'Judul Kursus'); ?>" class="img-fluid rounded-top course-thumbnail">
                                    
                                    <div class="course-image-overlay">
                                        <i class="bi bi-eye-fill overlay-icon"></i>
                                        <span class="overlay-text">Lihat Detail</span>
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
                                    <a href="../detail_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-outline-primary mt-auto course-button">
                                        Pelajari Lebih Lanjut <i class="bi bi-arrow-right-short"></i>
                                    </a>
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

    <?php require '../komponen/footer.php'; // Path untuk footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>