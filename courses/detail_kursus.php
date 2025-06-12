<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../log in or register/login.php"); //
    exit();
}

require '../head/head.php';
require '../komponen/koneksi.php';

$kursus = null;
$source = $_GET['source'] ?? 'halaman'; // Default source adalah 'halaman' (daftar kursus biasa)

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET["id"];
    $stmt = mysqli_prepare($conn, "SELECT k.*, m.username AS nama_mentor FROM kursus k JOIN mentor m ON k.id_mentor = m.id_mentor WHERE k.id_kursus = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $kursus = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
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
    <main class="mt-5 pt-4">
        <div class="container">
            <?php if ($kursus): ?>
                <div class="row">
                    <div class="col-lg-5 mb-4">
                        <?php
                        $final_image_src = '../asset/placeholder_image.png';
                        $path_dari_db = $kursus['gambar'];
                        if (!empty($path_dari_db)) {
                            $path_bersih = str_replace('../','',$path_dari_db);
                            $path_untuk_html = '../' . $path_bersih;
                            $path_untuk_cek_server = realpath(__DIR__ . '/..') . '/' . $path_bersih;
                            if (file_exists($path_untuk_cek_server)) {
                                $final_image_src = $path_untuk_html;
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($final_image_src); ?>" alt="Course Thumbnail" class="img-fluid rounded shadow-sm w-100">
                    </div>
                    <div class="col-lg-7">
                        <h2><?php echo htmlspecialchars($kursus['judul']); ?></h2>
                        <p class="lead"><?php echo nl2br(htmlspecialchars($kursus['deskripsi'])); ?></p>
                        <hr>
                        <table class="table table-bordered">
                            <tr><td width="200"><b>Kategori</b></td><td><?php echo htmlspecialchars($kursus['kategori']); ?></td></tr>
                            <tr><td><b>Jenis Kursus</b></td><td><?php echo htmlspecialchars($kursus['jenis_kursus']); ?></td></tr>
                            <tr><td><b>Harga</b></td><td>Rp <?php echo number_format($kursus['harga'], 0, ',', '.'); ?></td></tr>
                            <tr><td><b>Penerbit/Mentor</b></td><td><?php echo htmlspecialchars($kursus['nama_mentor']); ?></td></tr>
                        </table>

                        <div class="mt-4 d-flex align-items-center">
                            <?php
                            // Tentukan tujuan tombol kembali berdasarkan 'source'
                            $kembali_link = ($source === 'dashboard') ? '../dashboard/dashboard.php' : 'Halaman.php';
                            ?>
                            <a href="<?php echo $kembali_link; ?>" class="btn btn-secondary me-3"><i class="bi bi-arrow-left"></i> Kembali</a>

                            <?php if (($source === 'dashboard' || $_SESSION['role'] === "siswa") && in_array($kursus['id_kursus'], $list_kursus_dibeli ?? [])): // Jika datang dari dashboard, tampilkan tombol "Pelajari" ?>
                                <a href="belajar_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-play-circle-fill me-2"></i> Pelajari Course
                                </a>
                            <?php else: // Jika dari halaman lain, tampilkan tombol "Gabung Course" untuk siswa ?>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'siswa'): ?>
                                    <form action="../keranjang_aksi.php" method="POST" class="m-0">
                                        <input type="hidden" name="action" value="tambah">
                                        <input type="hidden" name="id_kursus" value="<?php echo $kursus['id_kursus']; ?>">
                                        <input type="hidden" name="nama_kursus" value="<?php echo htmlspecialchars($kursus['judul']); ?>">
                                        <input type="hidden" name="harga_kursus" value="<?php echo floatval($kursus['harga']); ?>">
                                        <input type="hidden" name="gambar_kursus" value="<?php echo htmlspecialchars($kursus['gambar']); ?>">
                                        <input type="hidden" name="dari_keranjang" value="true">
                                        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-cart-plus-fill me-2"></i> Gabung Course</button>
                                    </form>
                                <?php elseif (!isset($_SESSION['role'])): ?>
                                    <a href="../log in or register/login.php" class="btn btn-success btn-lg"><i class="bi bi-cart-plus-fill me-2"></i> Gabung Course</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger text-center"><h4>Kursus Tidak Ditemukan!</h4></div>
            <?php endif; ?>
        </div>
    </main>
    <div class="mt-5"><?php require '../komponen/footer.php'; ?></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>