<?php
session_start();
require '../komponen/koneksi.php'; //

// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../log in or register/login.php"); //
    exit();
}
$id_mentor_saat_ini = $_SESSION['user_id'];

$list_kursus = [];
$stmt = $conn->prepare("SELECT id_kursus, judul_kursus, kategori_materi, jenis_kursus, harga_kursus, thumbnail_kursus, tanggal_upload FROM kursus WHERE id_mentor = ? ORDER BY tanggal_upload DESC");
if ($stmt) {
    $stmt->bind_param("i", $id_mentor_saat_ini);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $list_kursus[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; // ?>
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .thumbnail-preview {
        max-width: 100px;
        max-height: 75px;
        border-radius: .25rem;
        object-fit: cover;
    }
</style>
<body>
    <?php require '../komponen/sidebar.php'; // ?>
    <?php require '../komponen/nav.php'; // ?>

    <main class="mt-5 pt-4">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Kelola Kursus Anda</h2>
                <a href="tambah_kursus.php" class="btn btn-success"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Kursus Baru</a>
            </div>
            <hr class="mb-4">

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if(!empty($list_kursus)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Thumbnail</th>
                                    <th>Judul Kursus</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Harga (Rp)</th>
                                    <th>Tgl Upload</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($list_kursus as $kursus): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php if(!empty($kursus['thumbnail_kursus']) && file_exists($kursus['thumbnail_kursus'])): ?>
                                            <img src="<?php echo htmlspecialchars($kursus['thumbnail_kursus']); ?>" alt="Thumbnail" class="thumbnail-preview">
                                        <?php else: ?>
                                            <small class="text-muted">N/A</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($kursus['judul_kursus']); ?></td>
                                    <td><?php echo htmlspecialchars($kursus['kategori_materi']); ?></td>
                                    <td><span class="badge bg-<?php echo ($kursus['jenis_kursus'] == 'Berbayar') ? 'warning text-dark' : 'info'; ?>"><?php echo htmlspecialchars($kursus['jenis_kursus']); ?></span></td>
                                    <td><?php echo ($kursus['jenis_kursus'] == 'Berbayar' ? number_format($kursus['harga_kursus'], 0, ',', '.') : '-'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($kursus['tanggal_upload'])); ?></td>
                                    <td class="text-center">
                                        <a href="edit_kursus.php?id=<?php echo $kursus['id_kursus']; ?>" class="btn btn-outline-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <form action="proses_kursus.php" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus \'<?php echo htmlspecialchars(addslashes($kursus['judul_kursus'])); ?>\'? Tindakan ini tidak dapat diurungkan.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_kursus" value="<?php echo $kursus['id_kursus']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash-fill"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">Anda belum memiliki kursus. Silakan <a href="tambah_kursus.php" class="alert-link">tambahkan kursus baru</a>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div class="mt-5">
     <?php require '../komponen/footer.php'; // ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>