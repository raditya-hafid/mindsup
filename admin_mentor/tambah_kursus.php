<?php
session_start();
// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../log in or register/login.php"); //
    exit();
}
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
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">Tambah Kursus Baru</h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if(isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                            <?php endif; ?>

                            <form action="proses_kursus.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="create">
                                
                                <div class="mb-3">
                                    <label for="judul_kursus" class="form-label fw-bold">Nama Course</label>
                                    <input type="text" class="form-control" id="judul_kursus" name="judul_kursus" required>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi_kursus" class="form-label fw-bold">Deskripsi</label>
                                    <textarea name="deskripsi_kursus" id="deskripsi_kursus" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="kategori_materi" class="form-label fw-bold">Kategori Materi</label>
                                    <select name="kategori_materi" id="kategori_materi" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Matematika">Matematika</option>
                                        <option value="IPA">IPA</option>
                                        <option value="IPS">IPS</option>
                                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                                        <option value="Pemrograman">Pemrograman</option>
                                        <option value="Desain">Desain</option>
                                        {/* Tambahkan kategori lain jika perlu */}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Metode Pembelajaran</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="metode_pembelajaran[]" value="Video" id="metodeVideo">
                                        <label class="form-check-label" for="metodeVideo">Video</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="metode_pembelajaran[]" value="Teks" id="metodeTeks">
                                        <label class="form-check-label" for="metodeTeks">Teks/Deskripsi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="metode_pembelajaran[]" value="Kuis" id="metodeKuis">
                                        <label class="form-check-label" for="metodeKuis">Kuis Interaktif</label>
                                    </div>
                                     <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="metode_pembelajaran[]" value="Proyek" id="metodeProyek">
                                        <label class="form-check-label" for="metodeProyek">Proyek Praktis</label>
                                    </div>
                                </div>
                                 <div class="mb-3">
                                    <label class="form-label fw-bold">Jenis Kursus</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kursus" id="gratis" value="Gratis" required checked>
                                        <label class="form-check-label" for="gratis">Gratis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kursus" id="berbayar" value="Berbayar" required>
                                        <label class="form-check-label" for="berbayar">Berbayar</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="harga_kursus" class="form-label fw-bold">Harga Kursus (Rp)</label>
                                    <input type="number" class="form-control" id="harga_kursus" name="harga_kursus" min="0" placeholder="Isi jika jenis kursus berbayar. Contoh: 150000">
                                </div>
                                <div class="mb-3">
                                    <label for="thumbnail_kursus" class="form-label fw-bold">Upload Thumbnail Kursus</label>
                                    <input type="file" class="form-control" id="thumbnail_kursus" name="thumbnail_kursus" accept="image/png, image/jpeg, image/jpg" required>
                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Ukuran maks: 2MB.</small>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="dashboard_mentor.php" class="btn btn-outline-secondary me-md-2">Batal</a>
                                    <button type="submit" class="btn btn-success">Simpan Kursus</button>
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