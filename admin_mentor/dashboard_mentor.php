<?php
session_start();
require '../komponen/koneksi.php'; //

// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../log in or register/login.php"); //
    exit();
}

$id_mentor_saat_ini = $_SESSION['user_id'];

// Ambil data ringkasan, misalnya jumlah kursus yang dimiliki mentor
$total_kursus = 0;
$stmt_count = $conn->prepare("SELECT COUNT(*) AS total_kursus FROM kursus WHERE id_mentor = ?");
if ($stmt_count) {
    $stmt_count->bind_param("i", $id_mentor_saat_ini);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    if ($result_count) {
        $data_count = $result_count->fetch_assoc();
        $total_kursus = $data_count['total_kursus'] ?? 0;
    }
    $stmt_count->close();
}

?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; // ?>
<style>
    .dashboard-hero {
        background: linear-gradient(to right, #28a745, #218838); /* Warna hijau untuk mentor */
        color: white;
        padding: 40px 20px;
        margin-bottom: 30px;
        border-radius: 8px;
    }
    .dashboard-hero h1 {
        font-size: 2.5rem;
        font-weight: bold;
    }
    .dashboard-stat-card {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: .75rem;
        padding: 25px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .dashboard-stat-card h3 {
        font-size: 3rem;
        font-weight: 700;
        color: #28a745; /* Warna hijau */
    }
    .dashboard-stat-card p {
        font-size: 1.1rem;
        color: #555;
        margin-top: 5px;
    }
    .action-buttons .btn {
        padding: 12px 25px;
        font-size: 1.1rem;
    }
</style>
<body>
    <?php require '../komponen/sidebar.php'; // Gunakan sidebar umum atau buat sidebar_mentor.php ?>
    <?php require '../komponen/nav.php'; // Gunakan nav.php yang sudah disesuaikan ?>

    <main class="mt-5 pt-4">
        <div class="container-fluid px-4"> {/* Tambahkan padding horizontal */}
            <div class="dashboard-hero text-center"> {/* Pusatkan teks di hero */}
                <h1>Dashboard Mentor</h1>
                <p>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['username']); ?>! Kelola kursus Anda dari sini.</p>
            </div>

            <div class="row justify-content-center"> {/* Pusatkan kartu statistik */}
                <div class="col-md-5 col-lg-4">
                    <div class="dashboard-stat-card">
                        <h3><?php echo $total_kursus; ?></h3>
                        <p>Total Kursus Anda</p>
                    </div>
                </div>
                {/* Tambahkan statistik lain jika perlu, contoh:
                <div class="col-md-5 col-lg-4">
                    <div class="dashboard-stat-card">
                        <h3>125</h3>
                        <p>Total Siswa Terdaftar</p>
                    </div>
                </div>
                */}
            </div>

            <div class="mt-4 text-center action-buttons"> {/* Pusatkan tombol aksi */}
                <a href="tambah_kursus.php" class="btn btn-success btn-lg me-2"><i class="bi bi-plus-circle-fill"></i> Tambah Kursus Baru</a>
                <a href="kelola_kursus.php" class="btn btn-primary btn-lg"><i class="bi bi-pencil-square"></i> Kelola Kursus</a>
            </div>
            
        </div>
    </main>

    <div class="mt-5"> {/* Beri jarak sebelum footer */}
      <?php require '../komponen/footer.php'; // ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>