<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../log in or register/login.php");
    exit();
}

require '../head/head.php';
require '../komponen/koneksi.php';

$kursus = null;
$ulasan_list = [];
$id_detail_pembelian = null; // Variabel untuk menyimpan id_detail
$sudah_beli = false;
$source = $_GET['source'] ?? 'halaman';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_kursus = (int)$_GET["id"];
    
    // 1. Ambil detail kursus
    $stmt_kursus = mysqli_prepare($conn, "SELECT k.*, m.username AS nama_mentor FROM kursus k JOIN mentor m ON k.id_mentor = m.id_mentor WHERE k.id_kursus = ?");
    mysqli_stmt_bind_param($stmt_kursus, "i", $id_kursus);
    mysqli_stmt_execute($stmt_kursus);
    $result_kursus = mysqli_stmt_get_result($stmt_kursus);
    if ($result_kursus && mysqli_num_rows($result_kursus) > 0) {
        $kursus = mysqli_fetch_assoc($result_kursus);
    }
    mysqli_stmt_close($stmt_kursus);

    // 2. Cek apakah siswa sudah membeli kursus ini
    $id_siswa = $_SESSION['user_id'];
    $stmt_cek = $conn->prepare("SELECT dp.id_detail FROM pembelian p JOIN detail_pembelian dp ON p.id_pembelian = dp.id_pembelian WHERE p.id_siswa = ? AND dp.id_kursus = ?");
    if ($stmt_cek) {
        $stmt_cek->bind_param("ii", $id_siswa, $id_kursus);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        if($row_cek = $result_cek->fetch_assoc()){
            $sudah_beli = true;
            $id_detail_pembelian = $row_cek['id_detail']; // Simpan id_detail
        }
        $stmt_cek->close();
    }
    
    // 3. Ambil semua ulasan untuk kursus ini
    $stmt_ulasan = mysqli_prepare($conn, "SELECT dp.ulasan, dp.Rating, s.username FROM detail_pembelian dp JOIN pembelian p ON dp.id_pembelian = p.id_pembelian JOIN siswa s ON p.id_siswa = s.id_siswa WHERE dp.id_kursus = ? AND dp.ulasan IS NOT NULL AND dp.ulasan != ''");
    mysqli_stmt_bind_param($stmt_ulasan, "i", $id_kursus);
    mysqli_stmt_execute($stmt_ulasan);
    $result_ulasan = mysqli_stmt_get_result($stmt_ulasan);
    while($row_ulasan = mysqli_fetch_assoc($result_ulasan)){
        $ulasan_list[] = $row_ulasan;
    }
    mysqli_stmt_close($stmt_ulasan);

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
                            // Logika path gambar Anda yang sudah baik
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
                            <a href="<?php echo ($source === 'dashboard') ? '../dashboard/dashboard.php' : 'Halaman.php'; ?>" class="btn btn-secondary me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                            
                            <?php if ($sudah_beli): ?>
                                <a href="belajar_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-primary me-2"><i class="bi bi-play-circle-fill"></i> Pelajari</a>
                                <a href="beri_ulasan.php?id_detail=<?php echo $id_detail_pembelian; ?>" class="btn btn-outline-success"><i class="bi bi-chat-square-text-fill"></i> Beri Ulasan</a>
                            <?php else: ?>
                                <form action="../keranjang_aksi.php" method="POST" class="m-0">
                                    </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <h3>Ulasan dari Siswa</h3>
                    <hr>
                    <?php if (!empty($ulasan_list)): ?>
                        <?php foreach($ulasan_list as $ulasan): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($ulasan['username']); ?></h5>
                                    <p class="card-text"><?php for($i = 0; $i < $ulasan['Rating']; $i++) {echo "★";} ?></p>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($ulasan['ulasan'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">Belum ada ulasan untuk kursus ini.</div>
                    <?php endif; ?>
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