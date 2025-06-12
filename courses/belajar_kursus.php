<?php
session_start();
require '../komponen/koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../log in or register/login.php");
    exit();
}

$kursus = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET["id"];
    // Di sini Anda bisa menambahkan validasi apakah siswa ini benar-benar sudah membeli kursus tsb
    
    $stmt = mysqli_prepare($conn, "SELECT judul, deskripsi FROM kursus WHERE id_kursus = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $kursus = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
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
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h1 class="mb-0">Materi Kursus: <?php echo htmlspecialchars($kursus['judul']); ?></h1>
                            <a href="../dashboard/dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-grid-fill me-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                        <hr>
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Deskripsi Awal</h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($kursus['deskripsi'])); ?></p>
                                
                                <hr>
                                <h5 class="card-title mt-4">Konten Pembelajaran</h5>
                                <p><i>
                                    <!-- (Di sini Anda bisa menambahkan video, teks materi, kuis, dll. sesuai struktur database materi Anda nantinya.) -->
                                </i></p>
                                <div class="alert alert-info">
                                    <!-- Konten materi kursus akan ditampilkan di area ini. -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <h4>Materi Kursus Tidak Ditemukan!</h4>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="mt-5"><?php require '../komponen/footer.php'; ?></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>