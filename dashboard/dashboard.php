<?php
session_start(); // Mulai session

// Periksa apakah pengguna sudah login
// Jika tidak ada session username, redirect ke halaman login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: ../log in or register/login.php");
    exit();
}

// Data kursus (contoh, nantinya bisa diambil dari database)
// Semua progress diatur ke 0
$courses = [
    [
        'id' => 1,
        'title' => 'Matematika Dasar',
        'description' => 'Pelajari konsep dasar matematika dengan mudah dan menyenangkan. Cocok untuk pemula.',
        'image' => '../asset/matematika.png', // Ganti dengan path gambar yang sesuai
        'progress' => 0, // Progress diatur ke 0
        'link' => '#' // Link ke halaman detail kursus
    ],
    [
        'id' => 2,
        'title' => 'Pengantar Sains (IPA)',
        'description' => 'Eksplorasi dunia sains melalui materi-materi seru dan interaktif.',
        'image' => '../asset/ipaa.png', // Ganti dengan path gambar yang sesuai
        'progress' => 25, // Progress diatur ke 0
        'link' => '#'
    ],
//    [
//         'id' => 3,
//         'title' => 'Ilmu Pengetahuan Sosial (IPS)',
//         'description' => 'Kenali lingkungan sosial dan budaya di sekitar kita dengan cara yang menarik.',
//         'image' => '../asset/ips.png', // Ganti dengan path gambar yang sesuai
//         'progress' => 0, // Progress diatur ke 0
//         'link' => '#'
//     ] 
    // Tambahkan kursus lain di sini jika perlu, dengan progress 0
];

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
                                <img src="<?php echo htmlspecialchars($course['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                                    
                                    <?php if (isset($course['progress'])): ?>
                                    <div class="mt-auto">
                                        <p class="mb-1"><small>Progres: <?php echo $course['progress']; ?>%</small></p>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $course['progress']; ?>%;" aria-valuenow="<?php echo $course['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <a href="<?php echo htmlspecialchars($course['link']); ?>" class="btn btn-primary btn-course w-100 <?php echo isset($course['progress']) ? '' : 'mt-auto'; ?>">
                                        <?php echo ($course['progress'] < 100 && $course['progress'] > 0) ? 'Lanjutkan Belajar' : (($course['progress'] == 100) ? 'Ulas Kembali' : 'Mulai Belajar'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                         <div class="col-12 text-center">
                            <img src="../asset/empty_course_placeholder.png" alt="Belum ada kursus" style="max-width: 250px; margin-bottom: 20px;">
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