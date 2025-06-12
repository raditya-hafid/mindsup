<!DOCTYPE html>
<html lang="en">
<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../log in or register/login.php"); //
    exit();
}

require '../head/head.php';
require '../komponen/koneksi.php';

$id = $_GET["id"];

$stmt_kursus = mysqli_prepare($conn, "SELECT * FROM kursus WHERE id_kursus = ?");
mysqli_stmt_bind_param($stmt_kursus, "i", $id);
mysqli_stmt_execute($stmt_kursus);
$result_kursus = mysqli_stmt_get_result($stmt_kursus);
?>

    <body>
        <?php
            require '../komponen/sidebar.php';
            require '../komponen/nav.php';
            

            if (mysqli_num_rows($result_kursus) === 1): 
                $row = mysqli_fetch_assoc($result_kursus);

                $final_image_src = '../asset/placeholder_image.png';

                if (!empty($row['gambar']) && file_exists($row['gambar'])) {
                    $final_image_src = $row['gambar'];
                }
            ?>

            <div class="row" style='margin-top: 70px;'>
                <div class="col-3">
                    <img src="<?php echo $final_image_src; ?>" alt='Uploaded Image' style='max-width: 100%;'>
                </div>
                <div class="col">
                    <table class="table">
                        <tr>
                            <td width="200">
                                <p><b>Nama Course :</b></p>
                            </td>

                            <td>
                                <p><?php echo $row['judul']; ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td class="teks-tabel">
                                <p><b>Deskripsi :</b></p>
                            </td>

                            <td>
                                <p><?php $row['deskripsi']; ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="teks-tabel">
                                <p><b>Kategori :</b></p>
                            </td>
                            
                            <td>
                                <p><?php echo $row['kategori']; ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="teks-tabel">
                                <p><b>Jenis Kursus :</b></p>
                            </td>
                            
                            <td>
                                <p><?php echo $row['jenis_kursus']; ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="teks-tabel">
                                <p><b>Harga :</b></p>
                            </td>
                            
                            <td>
                                <p><?php echo $row['harga']; ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php
                $stmt = $conn->prepare("SELECT 1 FROM pembelian p JOIN detail_pembelian dp ON p.id_pembelian = dp.id_pembelian WHERE p.id_siswa = ? AND dp.id_kursus = ? LIMIT 1;");
                $stmt->bind_param("ii", $_SESSION['user_id'], $id);
                $stmt->execute();

                if (($_SESSION['role'] === "siswa" && $stmt->fetch()) || $_SESSION['role'] !== "siswa"):
                    
            ?>

            <h2>Materi Kursus :</h2>

            <?php
                if (!empty($row['file_materi']) && file_exists($row['file_materi'])) {
                    echo "<a href='" . $row['file_materi'] . "'>File Materi</a>";
                } else {
                    echo "<p>Belum ada file materi untuk ditampilkan</a>";
                }

            else:
            ?>

            <h3>Maaf, anda belum punya izin untuk membuka materi</h3>
            <p>Silahkan beli kursusnya terlebih dahulu</p>

            <?php
                endif;
                else: 
            ?>

            <h1>Course Tidak Ditemukan</h1>

            <?php endif ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>