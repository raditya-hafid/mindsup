<?php
session_start(); // Mulai session

require '../komponen/koneksi.php';

// Periksa apakah pengguna sudah login
// Jika tidak ada session username, redirect ke halaman login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: ../log in or register/login.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT k.id_kursus, k.judul, k.deskripsi, k.gambar FROM siswa s JOIN pembelian p ON s.id_siswa = p.id_siswa JOIN detail_pembelian dp ON p.id_pembelian = dp.id_pembelian JOIN kursus k ON dp.id_kursus = k.id_kursus WHERE s.id_siswa = ?;");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$courses = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
}

// Jika sudah login, tampilkan halaman dashboard
?>
<!DOCTYPE html>
<html lang="en">
<?php
    // Sertakan file head.php untuk bagian head HTML
    require '../head/head.php'; //
?>
<style>
    /* CSS Kustom untuk Halaman Dashboard */
    .dashboard-hero {
        background: linear-gradient(to right, #007bff, #0056b3); /* Warna biru yang lebih soft */
        color: white;
        padding: 30px 20px; /* Padding sedikit dikurangi */
        margin-bottom: 30px;
        border-radius: 8px;
    }
    .dashboard-hero h1 {
        font-size: 2.2rem; /* Ukuran font judul disesuaikan */
        font-weight: bold;
    }
    .dashboard-hero p {
        font-size: 1rem; /* Ukuran font paragraf disesuaikan */
    }
    .course-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e9ecef; /* Border lebih halus */
        border-radius: .50rem; /* Radius border lebih besar */
        overflow: hidden; /* Agar gambar tidak keluar dari card */
        background-color: #fff;
        cursor: pointer;
    }
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .course-card img {
        width: 100%;
        height: 180px; /* Tinggi gambar disamakan */
        object-fit: cover; /* Agar gambar terpotong dengan baik */
    }
    .course-card .card-body {
        padding: 1.25rem;
    }
    .course-card .card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .course-card .card-text {
        font-size: 0.9rem;
        color: #6c757d; /* Warna teks deskripsi */
        min-height: 60px; /* Agar tinggi deskripsi konsisten */
    }
    .progress {
        height: 10px; /* Tinggi progress bar dikecilkan */
        margin-bottom: 1rem;
        border-radius: .25rem;
    }
    .btn-course {
        font-size: 0.9rem;
    }
    .section-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #343a40;
    }
</style>
<body>
    
    <?php
    // Sertakan file sidebar.php untuk tampilan sidebar
    require '../komponen/sidebar.php'; //
    ?>
        
    </div>

    <?php
     // Sertakan file nav.php untuk tampilan navigasi
     require '../komponen/nav.php'; //

    ?>
    <main class="mt-3 pt-2">
        <div class="container-fluid">
            
            <div class="dashboard-hero">
                <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Semoga aktivitas belajarmu menyenangkan.</p>
            </div>

            <h2 class="section-title">Kursus Saya</h2>
            
            <div class="row">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100">
                                <img src="<?php echo htmlspecialchars($course['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['judul']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['judul']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($course['deskripsi']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                         <div class="col-12 text-center">
                            <h4>Anda Belum Memiliki Kursus</h4>
                            <p class="text-muted">Sepertinya Anda belum mendaftar di kursus manapun. Jangan khawatir!</p>
                            <a href="../landing page/pertama.php#courses" class="btn btn-lg btn-success">
                                <i class="bi bi-search me-2"></i>Jelajahi Kursus Sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php
        // Sertakan file footer.php untuk tampilan footer
        require '../komponen/footer.php'; //
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>