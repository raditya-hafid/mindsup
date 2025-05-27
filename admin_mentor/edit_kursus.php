<?php
session_start();
require '../komponen/koneksi.php'; //

// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../log in or register/login.php"); //
    exit();
}

if (!isset($_GET['id']) || empty(trim($_GET['id'])) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Kursus tidak valid atau tidak ditemukan.";
    header("Location: kelola_kursus.php");
    exit();
}

$id_kursus_edit = (int)$_GET['id'];
$id_mentor_saat_ini = $_SESSION['user_id'];
$kursus = null;
$id_kursus_CRUD = 0;

$stmt = $conn->prepare("SELECT * FROM kursus WHERE id_kursus = ? AND id_mentor = ?");
if ($stmt) {
    $stmt->bind_param("ii", $id_kursus_edit, $id_mentor_saat_ini);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $kursus = $result->fetch_assoc();
        $id_kursus_CRUD = $id_kursus_edit;
    } else {
        $_SESSION['error_message'] = "Kursus tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.";
        header("Location: kelola_kursus.php");
        exit();
    }
    $stmt->close();
} else {
    // Error saat prepare statement
    $_SESSION['error_message'] = "Terjadi kesalahan pada database.";
    header("Location: kelola_kursus.php");
    exit();
}

if ($kursus === null) {
    // Untuk keamanan tambahan jika $kursus tidak terisi
    $_SESSION['error_message'] = "Data kursus tidak dapat dimuat.";
    header("Location: kelola_kursus.php");
    exit();
}

$metode_pembelajaran_tersimpan = !empty($kursus['metode_pembelajaran']) ? explode(", ", $kursus['metode_pembelajaran']) : [];
?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; // ?>
<body>
    <?php require '../komponen/sidebar.php'; // ?>
    <?php require '../komponen/nav.php'; // ?>

    <main class="mt-5 pt-4">
        <div class="container">
             <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">Edit Kursus: <?php echo htmlspecialchars($kursus['judul']); ?></h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if(isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                            <?php endif; ?>

                            <form action="proses_kursus.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id_kursus" value="<?php echo $id_kursus_CRUD; ?>">
                                <input type="hidden" name="existing_thumbnail" value="<?php echo htmlspecialchars($kursus['gambar']); ?>">
                                
                                <div class="mb-3">
                                    <label for="judul_kursus" class="form-label fw-bold">Nama Course</label>
                                    <input type="text" class="form-control" id="judul_kursus" name="judul_kursus" value="<?php echo htmlspecialchars($kursus['judul']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi_kursus" class="form-label fw-bold">Deskripsi</label>
                                    <textarea name="deskripsi_kursus" id="deskripsi_kursus" class="form-control" rows="5" required><?php echo htmlspecialchars($kursus['deskripsi']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="kategori_materi" class="form-label fw-bold">Kategori Materi</label>
                                    <select name="kategori_materi" id="kategori_materi" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php 
                                        $kategori_options = ["Matematika", "IPA", "IPS", "Bahasa Indonesia", "Bahasa Inggris", "Pemrograman", "Desain"];
                                        foreach($kategori_options as $kat_opt){
                                            $selected = ($kursus['kategori_materi'] == $kat_opt) ? 'selected' : '';
                                            echo "<option value=\"$kat_opt\" $selected>$kat_opt</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Metode Pembelajaran</label>
                                    <?php 
                                    $metode_options = ["Video", "Teks", "Kuis Interaktif", "Proyek Praktis"];
                                    foreach($metode_options as $met_opt): 
                                        $checked = in_array($met_opt, $metode_pembelajaran_tersimpan) ? 'checked' : '';
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="metode_pembelajaran[]" value="<?php echo $met_opt; ?>" id="metodeEdit<?php echo str_replace(' ', '', $met_opt); ?>" <?php echo $checked; ?>>
                                        <label class="form-check-label" for="metodeEdit<?php echo str_replace(' ', '', $met_opt); ?>"><?php echo $met_opt; ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                 <div class="mb-3">
                                    <label class="form-label fw-bold">Jenis Kursus</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kursus" id="gratisEdit" value="Gratis" <?php echo ($kursus['jenis_kursus'] == 'Gratis') ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="gratisEdit">Gratis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kursus" id="berbayarEdit" value="Berbayar" <?php echo ($kursus['jenis_kursus'] == 'Berbayar') ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="berbayarEdit">Berbayar</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="harga_kursus" class="form-label fw-bold">Harga Kursus (Rp)</label>
                                    <input type="number" class="form-control" id="harga_kursus" name="harga_kursus" min="0" placeholder="Isi jika jenis kursus berbayar" value="<?php echo htmlspecialchars($kursus['harga']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="thumbnail_kursus" class="form-label fw-bold">Upload Thumbnail Baru (Kosongkan jika tidak ingin ganti)</label>
                                    <input type="file" class="form-control" id="thumbnail_kursus" name="thumbnail_kursus" accept="image/png, image/jpeg, image/jpg">
                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Ukuran maks: 2MB.</small>
                                    <?php if(!empty($kursus['thumbnail_kursus']) && file_exists($kursus['thumbnail_kursus'])): ?>
                                        <p class="mt-2">Thumbnail saat ini: <br><img src="<?php echo htmlspecialchars($kursus['thumbnail_kursus']); ?>" alt="Thumbnail Saat Ini" style="max-width: 200px; max-height: 150px; border-radius: .25rem; margin-top: 5px;"></p>
                                    <?php elseif(!empty($kursus['thumbnail_kursus'])): ?>
                                        <p class="mt-2 text-danger">Thumbnail saat ini tidak dapat ditemukan: <?php echo htmlspecialchars($kursus['thumbnail_kursus']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                   <a href="kelola_kursus.php" class="btn btn-outline-secondary me-md-2">Batal</a>
                                   <button type="submit" class="btn btn-warning">Update Kursus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="mt-5">
     <?php require '../komponen/footer.php'; // ?>
    </div>
</body>
</html>