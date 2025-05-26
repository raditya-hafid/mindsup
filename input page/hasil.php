<!DOCTYPE html>
<html lang="en">

<?php require '../head/head.php'; ?>

<body>
    <!-- CSS-only Sidebar implementation -->
    <div class="sidebar-container">
        <input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">
        
        <div class="sidebar">
            <label for="sidebar-toggle" class="sidebar-close">&times;</label>
            <div class="sidebar-content">
                <div class="sidebar-heading">Menu</div>
                <a href="#" class="sidebar-link"><i class="bi bi-house-door me-2"></i> Home</a>
                <a href="#" class="sidebar-link"><i class="bi bi-book me-2"></i> All Courses</a>
                <a href="#" class="sidebar-link"><i class="bi bi-star me-2"></i> Featured Courses</a>
                <a href="#" class="sidebar-link"><i class="bi bi-person me-2"></i> My Account</a>
                
                <div class="sidebar-heading">Categories</div>
                <a href="#" class="sidebar-link">Programming</a>
                <a href="#" class="sidebar-link">Data Science</a>
                <a href="#" class="sidebar-link">Web Development</a>
                <a href="#" class="sidebar-link">Digital Marketing</a>
                <a href="#" class="sidebar-link">Design</a>
                
                <div class="sidebar-heading">Help</div>
                <a href="#" class="sidebar-link"><i class="bi bi-question-circle me-2"></i> FAQs</a>
                <a href="#" class="sidebar-link"><i class="bi bi-headset me-2"></i> Support</a>
                <a href="#" class="sidebar-link"><i class="bi bi-info-circle me-2"></i> About Us</a>
            </div>
        </div>
        
        <label for="sidebar-toggle" class="sidebar-overlay"></label>
    </div>

    <!-- Navigation Bar -->
    <?php
        require '../komponen/nav.php';
        require '../komponen/koneksi.php';
    ?>

    <main class="mt-5">
        <div class="main-content">
            <br>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    $nc = $_POST['namacourse'];
                    $deskripsi = $_POST['deskripsi'];
                    $kategori = $_POST["kategori_materi"];
                    $metode = isset($_POST["metode"]) ? $_POST["metode"] : [];
                    $jenis = $_POST["jenis_kursus"];
                    $tanggal = date("Y-m-d H:i:s");
                    $harga = $_POST["harga"];
                    // Upload Image
                    if(isset($_FILES['image'])) {
                        $target_dir = "uploads/"; // Directory dimana gambar akan dikirimkan
                        
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        
                        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                        $check = getimagesize($_FILES["image"]["tmp_name"]);

                        $target_file = $target_dir . $nc . "_" . time() . "." . $file_extension;
                        
                        if($check !== false) {
                            
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                                echo "<div class=\"row\">";
                                echo "<div class=\"col-3\"><a href='$target_file' target='_blank'><img src='$target_file' alt='Uploaded Image' style='max-width: 300px;'></a></div>";
                                echo "<div class=\"col\"><table class=\"table\">";
                                echo "<tr><td width=\"200\"><p><b>Nama Course :</b></p></td><td><p>$nc</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Deskripsi :</b></p></td><td><p>$deskripsi</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Kategori :</b></p></td><td><p>$kategori</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Metode Pembelajaran :</b></p></td><td><p>" . implode(", ", $metode) . "</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Jenis Kursus :</b></p></td><td><p>$jenis</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Harga :</b></p></td><td><p>$harga</p></td></tr>";
                                echo "<tr><td class=\"teks-tabel\"><p><b>Tanggal Upload :</b></p></td><td><p>$tanggal</p></td></tr>"; 
                                echo "</table></div>";
                                echo "</div>";

                                $sql = "INSERT INTO `kursus`(`id_mentor`, `judul`, `kategori`, `harga`, `deskripsi`, `id_admin`, `jenis_kursus`, `gambar`) VALUES ('1','$nc','$kategori','$harga','$deskripsi','1','$jenis','$target_file')";

                                if (!mysqli_query($conn, $sql)) {
                                    echo "Input gagal: " . mysqli_error($conn);
                                }
                            } else {
                                echo "<p>Maaf, terjadi kesalahan saat mengupload gambar.</p>";
                            }
                        } else {
                            echo "<p>File yang diupload bukan gambar.</p>";
                        }
                    }
                }
        ?>
        </div>

 
    </main>
    <?php
        require '../komponen/footer.php';
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>