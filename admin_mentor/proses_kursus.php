<?php
session_start();
require '../komponen/koneksi.php'; //

// Cek apakah pengguna sudah login dan adalah mentor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    $_SESSION['error_message'] = "Akses ditolak. Silakan login sebagai mentor.";
    header("Location: ../log in or register/login.php"); //
    exit();
}

$id_mentor_saat_ini = $_SESSION['user_id'];

// Fungsi untuk upload gambar thumbnail
function uploadThumbnail($fileInputName, $uploadDir = "../uploads/thumbnails/") {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == UPLOAD_ERR_OK) {
        $targetDir = rtrim($uploadDir, '/') . '/'; // Pastikan ada trailing slash
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $_SESSION['error_message'] = "Gagal membuat direktori upload: " . $targetDir;
                return false;
            }
        }

        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileName = uniqid('thumb_', true) . '_' . basename(preg_replace("/[^a-zA-Z0-9\.\-\_]/", "", $_FILES[$fileInputName]["name"]));
        $targetFilePath = $targetDir . $fileName;
        $imageFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Cek ukuran file (misal maks 2MB)
        if ($_FILES[$fileInputName]['size'] > 2 * 1024 * 1024) {
            $_SESSION['error_message'] = "Maaf, ukuran file thumbnail terlalu besar (maks 2MB).";
            return false;
        }
        
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
            // Cek apakah benar-benar gambar
            if (getimagesize($fileTmpPath)) {
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    return $targetFilePath;
                } else {
                    $_SESSION['error_message'] = "Maaf, terjadi kesalahan saat memindahkan file thumbnail Anda.";
                    return false;
                }
            } else {
                 $_SESSION['error_message'] = "File yang diupload bukan gambar yang valid.";
                 return false;
            }
        } else {
            $_SESSION['error_message'] = "Maaf, hanya file JPG, JPEG, & PNG yang diizinkan untuk thumbnail.";
            return false;
        }
    } elseif (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] != UPLOAD_ERR_NO_FILE) {
        // Ada error lain selain 'tidak ada file yang diupload'
        $_SESSION['error_message'] = "Error saat upload thumbnail: " . $_FILES[$fileInputName]['error'];
        return false;
    }
    return null; // Tidak ada file baru yang diupload atau tidak ada error (UPLOAD_ERR_NO_FILE)
}

function uploadMateri($fileInputName, $uploadDir = "../uploads/materi/") {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == UPLOAD_ERR_OK) {
        $targetDir = rtrim($uploadDir, '/') . '/'; // Pastikan ada trailing slash
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $_SESSION['error_message'] = "Gagal membuat direktori upload: " . $targetDir;
                return false;
            }
        }

        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileName = uniqid('materi_', true) . '_' . basename(preg_replace("/[^a-zA-Z0-9\.\-\_]/", "", $_FILES[$fileInputName]["name"]));
        $targetFilePath = $targetDir . $fileName;
        $imageFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Cek ukuran file (misal maks 10MB)
        if ($_FILES[$fileInputName]['size'] > 10485760) {
            $_SESSION['error_message'] = "Maaf, ukuran file thumbnail terlalu besar (maks 2MB).";
            return false;
        }
        
        $allowTypes = array('mp4', 'pdf', 'txt');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                return $targetFilePath;
            } else {
                $_SESSION['error_message'] = "Maaf, terjadi kesalahan saat memindahkan file materi Anda.";
                return false;
            }
        } else {
            $_SESSION['error_message'] = "Maaf, hanya file MP4, PDF, & TXT yang diizinkan untuk materi.";
            return false;
        }
    } elseif (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] != UPLOAD_ERR_NO_FILE) {
        // Ada error lain selain 'tidak ada file yang diupload'
        $_SESSION['error_message'] = "Error saat upload file materi: " . $_FILES[$fileInputName]['error'];
        return false;
    }
    return null; // Tidak ada file baru yang diupload atau tidak ada error (UPLOAD_ERR_NO_FILE)
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // CREATE
    if ($action == 'create') {
        // Validasi dasar (tambahkan lebih banyak jika perlu)
        if (empty(trim($_POST['judul_kursus'])) || empty(trim($_POST['deskripsi_kursus'])) || empty(trim($_POST['kategori_materi'])) || empty($_POST['jenis_kursus'])) {
            $_SESSION['error_message'] = "Semua field yang wajib diisi tidak boleh kosong.";
            header("Location: tambah_kursus.php");
            exit();
        }

        $judul_kursus = trim($_POST['judul_kursus']);
        $deskripsi_kursus = trim($_POST['deskripsi_kursus']);
        $kategori_materi = $_POST['kategori_materi'];
        $metode_pembelajaran_arr = $_POST['metode_pembelajaran'] ?? [];
        $metode_pembelajaran = !empty($metode_pembelajaran_arr) ? implode(", ", $metode_pembelajaran_arr) : null;
        $jenis_kursus = $_POST['jenis_kursus'];
        $harga_kursus = ($jenis_kursus == 'Berbayar' && isset($_POST['harga_kursus']) && is_numeric($_POST['harga_kursus'])) ? (float)$_POST['harga_kursus'] : 0;
        
        $thumbnail_path = uploadThumbnail('thumbnail_kursus');
        $materi_path = uploadMateri('materi_kursus');

        if ($thumbnail_path === false) { // Ada error saat upload atau validasi gagal
            header("Location: tambah_kursus.php");
            exit();
        } elseif ($thumbnail_path === null) { // Wajib ada thumbnail saat create
             $_SESSION['error_message'] = "Thumbnail kursus wajib diupload.";
             header("Location: tambah_kursus.php");
             exit();
        }

        if ($materi_path === false) { // Ada error saat upload atau validasi gagal
            header("Location: tambah_kursus.php");
            exit();
        } elseif ($materi_path === null) { // Wajib ada thumbnail saat create
             $_SESSION['error_message'] = "Materi kursus wajib diupload.";
             header("Location: tambah_kursus.php");
             exit();
        }
        
        $id_admin = 1;
        $stmt = $conn->prepare("INSERT INTO `kursus`(`id_mentor`, `judul`, `kategori`, `harga`, `deskripsi`, `id_admin`, `jenis_kursus`, `gambar`, `file_materi`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("issisisss", $id_mentor_saat_ini, $judul_kursus, $kategori_materi, $harga_kursus, $deskripsi_kursus, $id_admin, $jenis_kursus, $thumbnail_path, $materi_path);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Kursus \"".htmlspecialchars($judul_kursus)."\" berhasil ditambahkan!";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan kursus: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Gagal menyiapkan statement: " . $conn->error;
        }
        header("Location: kelola_kursus.php");
        exit();
    }

    // UPDATE
    elseif ($action == 'update') {
        if (empty(trim($_POST['id_kursus'])) || !is_numeric($_POST['id_kursus']) || empty(trim($_POST['judul_kursus'])) || empty(trim($_POST['deskripsi_kursus'])) || empty(trim($_POST['kategori_materi'])) || empty($_POST['jenis_kursus'])) {
            $_SESSION['error_message'] = "ID atau field wajib lainnya tidak vaaa. <br> = ". empty(trim($_POST['kategori_materi'])) ;
            header("Location: kelola_kursus.php"); // Redirect ke kelola jika ID bermasalah
            exit();
        }

        $id_kursus = (int)$_POST['id_kursus'];
        $judul_kursus = trim($_POST['judul_kursus']);
        $deskripsi_kursus = trim($_POST['deskripsi_kursus']);
        $kategori_materi = $_POST['kategori_materi'];
        $metode_pembelajaran_arr = $_POST['metode_pembelajaran'] ?? [];
        $metode_pembelajaran = !empty($metode_pembelajaran_arr) ? implode(", ", $metode_pembelajaran_arr) : null;
        $jenis_kursus = $_POST['jenis_kursus'];
        $harga_kursus = ($jenis_kursus == 'Berbayar' && isset($_POST['harga_kursus']) && is_numeric($_POST['harga_kursus'])) ? (float)$_POST['harga_kursus'] : 0;
        $existing_thumbnail = $_POST['existing_thumbnail'];
        $existing_materi = $_POST['existing_materi'];
        
        $new_thumbnail_path = uploadThumbnail('thumbnail_kursus');
        if ($new_thumbnail_path === false) { // Ada error saat upload atau validasi gagal
            header("Location: edit_kursus.php?id=" . $id_kursus);
            exit();
        }

        $new_materi_path = uploadMateri('materi_kursus');
        if ($new_materi_path === false) { // Ada error saat upload atau validasi gagal
            header("Location: edit_kursus.php?id=" . $id_kursus);
            exit();
        }
        
        $final_thumbnail_path = $existing_thumbnail; // Default ke thumbnail lama
        // Hapus thumbnail lama jika ada dan path baru berhasil didapatkan dan berbeda
        if ($new_thumbnail_path !== null && $new_thumbnail_path !== $existing_thumbnail) {
            @unlink($existing_thumbnail); // @ untuk menekan error jika file tidak ada (meskipun sudah dicek)
            $final_thumbnail_path = $new_thumbnail_path; // Gunakan path baru
        }

        $final_materi_path = $existing_materi; // Default ke thumbnail lama
        // Hapus thumbnail lama jika ada dan path baru berhasil didapatkan dan berbeda
        if ($new_materi_path !== null && $new_materi_path !== $existing_materi) {
            @unlink($existing_materi); // @ untuk menekan error jika file tidak ada (meskipun sudah dicek)
            $final_materi_path = $new_materi_path; // Gunakan path baru
        }
        


        $stmt = $conn->prepare("UPDATE kursus SET id_kursus=?, id_mentor=?, judul=?, kategori=?, harga=?, deskripsi=?, id_admin=?, jenis_kursus=?, gambar=?, file_materi=? WHERE id_kursus=? AND id_mentor=?");
        if ($stmt) {
            $stmt->bind_param("iissisisssii", $id_kursus, $id_mentor_saat_ini, $judul_kursus, $kategori_materi, $harga_kursus, $deskripsi_kursus, $id_admin, $jenis_kursus, $final_thumbnail_path, $final_materi_path, $id_kursus, $id_mentor_saat_ini);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Kursus \"".htmlspecialchars($judul_kursus)."\" berhasil diperbarui!";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui kursus: " . $stmt->error;
            }
            $stmt->close();
        } else {
             $_SESSION['error_message'] = "Gagal menyiapkan statement update: " . $conn->error;
        }
        header("Location: kelola_kursus.php");
        exit();
    }

    // DELETE
    elseif ($action == 'delete') {
        if (empty(trim($_POST['id_kursus'])) || !is_numeric($_POST['id_kursus'])) {
            $_SESSION['error_message'] = "ID Kursus tidak valid untuk dihapus.";
            header("Location: kelola_kursus.php");
            exit();
        }
        $id_kursus = (int)$_POST['id_kursus'];
        $thumbnail_to_delete = null;

        // Ambil path thumbnail untuk dihapus dari server
        $stmt_get_thumb = $conn->prepare("SELECT gambar FROM kursus WHERE id_kursus = ? AND id_mentor = ?");
        if($stmt_get_thumb){
            $stmt_get_thumb->bind_param("ii", $id_kursus, $id_mentor_saat_ini);
            $stmt_get_thumb->execute();
            $result_thumb = $stmt_get_thumb->get_result();
            if($row_thumb = $result_thumb->fetch_assoc()){
                $thumbnail_to_delete = $row_thumb['thumbnail_kursus'];
            }
            $stmt_get_thumb->close();
        }

        $stmt = $conn->prepare("DELETE FROM kursus WHERE id_kursus = ? AND id_mentor = ?");
        if($stmt){
            $stmt->bind_param("ii", $id_kursus, $id_mentor_saat_ini);
            if ($stmt->execute()) {
                if (mysqli_affected_rows($conn) > 0 && !empty($thumbnail_to_delete) && file_exists($thumbnail_to_delete)) {
                    @unlink($thumbnail_to_delete); // Hapus file thumbnail
                }
                $_SESSION['success_message'] = "Kursus berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus kursus: " . $stmt->error;
            }
            $stmt->close();
        } else {
             $_SESSION['error_message'] = "Gagal menyiapkan statement delete: " . $conn->error;
        }
        header("Location: kelola_kursus.php");
        exit();
    }
    else {
        $_SESSION['error_message'] = "Tindakan tidak valid.";
        header("Location: kelola_kursus.php");
        exit();
    }

} else {
    // Jika bukan POST atau tidak ada action, redirect
    $_SESSION['error_message'] = "Permintaan tidak valid.";
    header("Location: dashboard_mentor.php");
    exit();
}
?>