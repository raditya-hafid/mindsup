<?php
session_start();
require '../komponen/koneksi.php';

// Periksa jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: ../log in or register/login.php");
    exit();
}

// Ambil semua kursus yang telah dibeli oleh siswa yang sedang login
$stmt = mysqli_prepare($conn, "SELECT k.id_kursus, k.judul, k.deskripsi, k.gambar FROM siswa s JOIN pembelian p ON s.id_siswa = p.id_siswa JOIN detail_pembelian dp ON p.id_pembelian = dp.id_pembelian JOIN kursus k ON dp.id_kursus = k.id_kursus WHERE s.id_siswa = ? AND p.status = 'Sukses'");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$courses = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; ?>
<style>

    .course-link{color:inherit;text-decoration:none;display:block;height:100%;}
    .course-link .card{transition:transform .2s ease-in-out,box-shadow .2s ease-in-out;}
    .course-link:hover .card{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.1);}

    /* CSS Kustom untuk Halaman Dashboard */
    .bigcard{
      border-radius: 5px;
      padding: 1em;
      margin-top: -0.4em;
      margin-bottom: 1.5em;
      box-shadow: 0 4px 10px rgba(26, 106, 255, 0.3);
    }
    .dashboard-hero {
        background: linear-gradient(to right, #007bff, #0056b3);
        /* Warna biru yang lebih soft */
        color: white;
        padding: 30px 20px;
        /* Padding sedikit dikurangi */
        margin-bottom: 30px;
        border-radius: 8px;
    }

    .dashboard-hero h1 {
      margin-top: 1.2em;
        font-size: 2.2rem;
        /* Ukuran font judul disesuaikan */
        
    }

    .dashboard-hero p {
        font-size: 1rem;
        text-align: center;
        margin-bottom: -0.5em;
        /* Ukuran font paragraf disesuaikan */
    }

    .course-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e9ecef;
        /* Border lebih halus */
        border-radius: .50rem;
        /* Radius border lebih besar */
        overflow: hidden;
        /* Agar gambar tidak keluar dari card */
        background-color: #fff;
        cursor: pointer;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .course-card img {
        width: 100%;
        height: 180px;
        /* Tinggi gambar disamakan */
        object-fit: cover;
        /* Agar gambar terpotong dengan baik */
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
        color: #6c757d;
        /* Warna teks deskripsi */
        min-height: 60px;
        /* Agar tinggi deskripsi konsisten */
    }

    .progress {
        height: 10px;
        /* Tinggi progress bar dikecilkan */
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

    .imagesvg{
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: -10em;
      /* margin-bottom: 5em; */
    }

    

</style>
<body>
    <?php require '../komponen/sidebar.php'; ?>
    <?php require '../komponen/nav.php'; ?>
    <main class="mt-3 pt-2">
        <div class="container-fluid">
            <div class="dashboard-hero text-center" style="background:linear-gradient(to right,#007bff,#0056b3);color:white;padding:30px 20px;margin-bottom:30px;border-radius:8px;">
                <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Semoga aktivitas belajarmu menyenangkan.</p>
            </div>
            <div class="bigcard">
              <h2 class=" text-center display-5 text-primary" style="font-weight: bold;">Kursus Saya</h2>
              <div class="d-flex align-items-center justify-content-center">
                <div style="height: 3px; width: 20%; background-color: #007bff; text-align: center; margin-top: -7px;"></div>
              </div>
            <div class="row">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <a href="../courses/detail_kursus.php?id=<?php echo $course['id_kursus']; ?>&source=dashboard" class="course-link">
                                <div class="card h-100">
                                    <?php
                                    $final_image_src = '../asset/placeholder_image.png';
                                    $path_dari_db = $course['gambar'];
                                    if (!empty($path_dari_db)) {
                                        $path_bersih = str_replace('../','',$path_dari_db);
                                        $path_untuk_html = '../' . $path_bersih;
                                        $path_untuk_cek = realpath(__DIR__ . '/..') . '/' . $path_bersih;
                                        if (file_exists($path_untuk_cek)) {
                                            $final_image_src = $path_untuk_html;
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($final_image_src); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['judul']); ?>" style="height:180px;object-fit:cover;">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['judul']); ?></h5>
                                        <p class="card-text small text-muted"><?php echo substr(htmlspecialchars($course['deskripsi']), 0, 100) . '...'; ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                  <div class="col-12">
                          <div class="col-12 text-center lead" style="margin-bottom: 9em;">
                            <div class="text-muted" style="font-size: 1rem;">
                              <p style="font-weight: 400; margin-top: 1em; margin-bottom: -0.1em;">Anda Belum Memiliki Kursus</p>
                              <p>Sepertinya Anda belum mendaftar di kursus manapun. Jangan khawatir!</p>
                            </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 3em;">
                              <div class="imagesvg">
                                <img src="../asset/learning.svg" class="img-fluid" width="180px" alt="">
                              </div>
                              <div style="text-align: center; width: auto;" >
                                <a href="../courses/Halaman.php#courses" class="btn btn-lg btn-primary" style="border-radius: 20px;">
                                    <i class="bi bi-search me-2"></i>Jelajahi Kursus Sekarang
                                </a>
                              </div>
                            </div>
                      </div>
                <?php endif; ?>
            </div>
          </div>
        </div>
    </main>
    <div class="mt-5"><?php require '../komponen/footer.php'; ?></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>