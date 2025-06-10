<?php
session_start();
require '../komponen/koneksi.php'; //

// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../log in or register/login.php"); //
    exit();
}

// $id_mentor_saat_ini = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.id_pembelian, s.username, s.email_siswa, p.pay_method, p.total FROM pembelian p JOIN siswa s ON p.id_siswa = s.id_siswa WHERE p.status = 'Pending';"); // Tambahkan 'harga'

$list_payment = [];
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $list_payment[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; // ?>
<style>
    .dashboard-hero {
        background: linear-gradient(to right, #007bff, #0056b3); /* Warna hijau untuk mentor */
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

    <main class="mt-3 pt-2">
        <div class="container-fluid px-4">
            <div class="dashboard-hero text-center">
                <h1>Dashboard Admin</h1>
                <p>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['username']); ?>! Kelola Proses Pembayaran dari sini.</p>
            </div>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Email</th>
                        <th>Metode Pembayaran</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; foreach($list_payment as $payment): ?>
                    
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $payment["username"]; ?></td>
                        <td><?php echo $payment["email_siswa"]; ?></td>
                        <td><?php echo $payment["pay_method"]; ?></td>
                        <td><?php echo $payment["total"]; ?></td>
                        <td>
                            <form action="proses_pembayaran.php" method="POST">
                                <input type="hidden" name="id_pembelian" <?php echo 'value="' . $payment["id_pembelian"] . '"' ?>>
                                <button type="submit" class="btn btn-success btn-sm">Verifikasi</button>
                            </form>
                        </td>
                    </tr>
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        </div>
    </main>

    <div class="mt-5">
      <?php require '../komponen/footer.php'; // ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>