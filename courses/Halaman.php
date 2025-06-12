<?php
session_start();
require '../komponen/koneksi.php';

$list_kursus = [];

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Siapkan query berdasarkan filter
if ($kategori) {
    $stmt = $conn->prepare("SELECT k.id_kursus, k.judul, k.deskripsi, k.gambar, k.kategori, k.harga, m.username AS mentor FROM kursus k JOIN mentor m ON k.id_mentor = m.id_mentor WHERE kategori = ? ORDER BY id_kursus DESC");
    $stmt->bind_param("s", $kategori);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("SELECT k.id_kursus, k.judul, k.deskripsi, k.gambar, k.kategori, k.harga, m.username AS mentor FROM kursus k JOIN mentor m ON k.id_mentor = m.id_mentor ORDER BY id_kursus DESC"); // Tambahkan 'harga'
    $stmt->execute();
}

if ($stmt) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $list_kursus[] = $row;
    }
    $stmt->close();
}

$id_siswa = $_SESSION['user_id'];
$list_kursus_dibeli = [];
$stmt2 = $conn->prepare("SELECT dp.id_kursus FROM pembelian p JOIN detail_pembelian dp ON p.id_pembelian = dp.id_pembelian WHERE p.id_siswa = ?");
if ($stmt2) {
    $stmt2->bind_param("i", $id_siswa);
    $stmt2->execute();
    $result = $stmt2->get_result();
    while($row = $result->fetch_assoc()){
        $list_kursus_dibeli[] = $row['id_kursus'];
    }
}

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

            <div class="container mt-4">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="kategori" class="form-label">Filter Kategori Kursus</label>
                        <select class="form-select" id="kategori" name="kategori">
                            <option value="">-- Semua Kategori --</option>
                            <option value="Matematika" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Matematika') ? 'selected' : '' ?>>Matematika</option>
                            <option value="IPA" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'IPA') ? 'selected' : '' ?>>IPA</option>
                            <option value="IPS" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'IPS') ? 'selected' : '' ?>>IPS</option>
                            <option value="Bahasa Indonesia" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Bahasa Indonesia') ? 'selected' : '' ?>>Bahasa Indonesia</option>
                            <option value="Bahasa Inggris" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Bahasa Inggris') ? 'selected' : '' ?>>Bahasa Inggris</option>
                            <option value="Pemrograman" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Pemrograman') ? 'selected' : '' ?>>Pemrograman</option>
                            <option value="Desain" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Desain') ? 'selected' : '' ?>>Desain</option>
                        </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </form>
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
                                    
                                    <div class="course-image-overlay">
                                        <a <?php if (isset($_SESSION['username'])) {echo '';} else {echo 'href="../log in or register/login.php"';} ?> class="text-white text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                                            <img src="<?php echo $final_image_src; ?>" alt="<?php echo htmlspecialchars($alt_text); ?>" class="img-fluid rounded-top course-thumbnail">    
                                            <!-- <i class="bi bi-eye-fill overlay-icon"></i> -->
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
                                    <p>Mentor : <?php echo $kursus['mentor'] ?></p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="<?php if (isset($_SESSION['username'])) {echo 'detail_kursus.php?id=' . $kursus['id_kursus'];} else {echo '../log in or register/login.php';} ?>" class="btn btn-sm btn-outline-primary course-button-detail">
                                            Pelajari <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                        
                                        <?php if (!in_array($kursus['id_kursus'], $list_kursus_dibeli) && (isset($_SESSION['role'])) ? $_SESSION['role'] === 'siswa' : false): ?>
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
                                        <?php else: ?>
                                            <form style="margin-bottom: 0;">
                                            <button class="btn btn-sm btn-secondary course-button-keranjang" title="Tambah ke Keranjang">
                                                <i class="bi bi-cart-plus-fill"></i>
                                            </button>
                                            </form>
                                        <?php endif; ?>
                                        
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