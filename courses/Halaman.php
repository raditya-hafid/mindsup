<?php
session_start();
require '../komponen/koneksi.php';

// Logika untuk filter kategori
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$sql = "SELECT k.id_kursus, k.judul, k.deskripsi, k.gambar, k.kategori, k.harga, m.username AS mentor FROM kursus k JOIN mentor m ON k.id_mentor = m.id_mentor";
if ($kategori) {
    $sql .= " WHERE k.kategori = ?";
}
$sql .= " ORDER BY k.id_kursus DESC";

$stmt = $conn->prepare($sql);
if ($kategori) {
    $stmt->bind_param("s", $kategori);
}
$stmt->execute();
$result = $stmt->get_result();
$list_kursus = [];
while ($row = $result->fetch_assoc()) {
    $list_kursus[] = $row;
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
    <?php require '../komponen/nav.php'; ?>

    <main class="mt-2 pt-1">
        
        <div class="container-fluid featured-courses" id="courses">
            <div style="text-align: center;" class="row justify-content-md-center">
                <div class="col-md-9">
                    <h1 style="margin-top: 1em;">SEMUA KURSUS KAMI</h1>
                    <div class="d-flex align-items-center justify-content-center">
                      <div style="height: 3px; width: 40%; background-color: #007bff; text-align: center; margin-top: -7px;"></div>
                    </div>
                    <p class="my-2">
                        Temukan beragam kursus menarik yang telah kami siapkan untuk meningkatkan pengetahuan dan keahlian Anda!
                    </p>
                </div>
              </div>
            <div class="container mt-4 my-4" style="margin-bottom: 25px;">
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
            <div class="row">
                <?php if (!empty($list_kursus)) : ?>
                    <?php foreach ($list_kursus as $kursus) : ?>
                        <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
                            <div class="card panel-course w-100 shadow-sm">
                                <?php
                                $final_image_src = '../asset/placeholder_image.png';
                                $path_dari_db = $kursus['gambar'];
                                if (!empty($path_dari_db)) {
                                    $path_bersih = str_replace('../', '', $path_dari_db);
                                    $path_untuk_html = '../' . $path_bersih;
                                    $path_untuk_cek = realpath(__DIR__ . '/..') . '/' . $path_bersih;
                                    if (file_exists($path_untuk_cek)) {
                                        $final_image_src = $path_untuk_html;
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($final_image_src); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($kursus['judul']); ?>" style="height: 180px; object-fit: cover;">
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title fw-bold"><?php echo htmlspecialchars($kursus['judul']); ?></h6>
                                    <p class="small text-muted flex-grow-1">
                                        <?php
                                        $deskripsi = htmlspecialchars($kursus['deskripsi']);
                                        echo strlen($deskripsi) > 80 ? substr($deskripsi, 0, 77) . '...' : $deskripsi;
                                        ?>
                                    </p>
                                    <p class="small mb-2">Mentor: <strong><?php echo htmlspecialchars($kursus['mentor']); ?></strong></p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="detail_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-sm btn-outline-primary">
                                            Pelajari <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'siswa' && !in_array($kursus['id_kursus'], $list_kursus_dibeli ?? [])): ?>
                                            <form action="../keranjang_aksi.php" method="POST" class="m-0">
                                                <input type="hidden" name="id_kursus" value="<?php echo $kursus['id_kursus']; ?>">
                                                <input type="hidden" name="nama_kursus" value="<?php echo htmlspecialchars($kursus['judul']); ?>">
                                                <input type="hidden" name="harga_kursus" value="<?php echo floatval($kursus['harga']); ?>">
                                                <input type="hidden" name="gambar_kursus" value="<?php echo htmlspecialchars($kursus['gambar']); ?>">
                                                <input type="hidden" name="action" value="tambah">
                                                <button type="submit" class="btn btn-sm btn-success" title="Tambah ke Keranjang">
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
                        <div class="alert alert-info text-center">Tidak ada kursus yang ditemukan untuk kriteria ini.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php require '../komponen/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>