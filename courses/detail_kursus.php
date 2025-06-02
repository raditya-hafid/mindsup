<!DOCTYPE html>
<html lang="en">
<?php
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
            

            if (mysqli_num_rows($result_kursus) === 1) {
                $row = mysqli_fetch_assoc($result_kursus);

                $final_image_src = '../asset/placeholder_image.png';

                if (!empty($row['gambar']) && file_exists($row['gambar'])) {
                    $final_image_src = $row['gambar'];
                }

                echo "<div class=\"row\" style='margin-top: 70px;'>";
                echo "<div class=\"col-3\"><img src='" . $final_image_src . "' alt='Uploaded Image' style='max-width: 100%;'></div>";
                echo "<div class=\"col\"><table class=\"table\">";
                echo "<tr><td width=\"200\"><p><b>Nama Course :</b></p></td><td><p>" . $row['judul'] . "</p></td></tr>";
                echo "<tr><td class=\"teks-tabel\"><p><b>Deskripsi :</b></p></td><td><p>" . $row['deskripsi'] . "</p></td></tr>";
                echo "<tr><td class=\"teks-tabel\"><p><b>Kategori :</b></p></td><td><p>" . $row['kategori'] . "</p></td></tr>";
                echo "<tr><td class=\"teks-tabel\"><p><b>Jenis Kursus :</b></p></td><td><p>" . $row['jenis_kursus'] . "</p></td></tr>";
                echo "<tr><td class=\"teks-tabel\"><p><b>Harga :</b></p></td><td><p>" . $row['harga'] . "</p></td></tr>";
                echo "</table></div></div>";
            } else {
                echo "Course tidak ditemukan";
            }

        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>