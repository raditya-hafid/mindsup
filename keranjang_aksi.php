<?php
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_kursus = $_POST['id_kursus'] ?? null;

    if ($action == 'tambah' && $id_kursus) {
        $nama_kursus = $_POST['nama_kursus'] ?? 'Nama Tidak Diketahui';
        $harga_kursus = isset($_POST['harga_kursus']) ? floatval($_POST['harga_kursus']) : 0;
        $gambar_kursus = $_POST['gambar_kursus'] ?? ''; // Path relatif dari root (mis: uploads/folder/gambar.jpg)

        // Cek apakah kursus sudah ada di keranjang
        if (!isset($_SESSION['keranjang'][$id_kursus])) {
            $_SESSION['keranjang'][$id_kursus] = [
                'id' => $id_kursus,
                'nama' => $nama_kursus,
                'harga' => $harga_kursus,
                'gambar' => $gambar_kursus, // Simpan path gambar dari DB
                'kuantitas' => 1 // Untuk kursus, kuantitas biasanya 1
            ];
            $_SESSION['pesan_keranjang'] = "Kursus \"".htmlspecialchars($nama_kursus)."\" berhasil ditambahkan ke keranjang!";
        } else {
            // Jika sudah ada, mungkin Anda ingin menampilkan pesan atau tidak melakukan apa-apa
            // Untuk kursus, biasanya tidak menambah kuantitas, tapi bisa juga jika model bisnisnya berbeda
            // $_SESSION['keranjang'][$id_kursus]['kuantitas']++; // Contoh jika ingin tambah kuantitas
            $_SESSION['pesan_keranjang'] = "Kursus \"".htmlspecialchars($nama_kursus)."\" sudah ada di keranjang Anda.";
        }
    } elseif ($action == 'hapus' && $id_kursus) {
        if (isset($_SESSION['keranjang'][$id_kursus])) {
            $nama_kursus_dihapus = $_SESSION['keranjang'][$id_kursus]['nama'];
            unset($_SESSION['keranjang'][$id_kursus]);
            $_SESSION['pesan_keranjang'] = "Kursus \"".htmlspecialchars($nama_kursus_dihapus)."\" berhasil dihapus dari keranjang.";
        }
    } elseif ($action == 'update_kuantitas' && $id_kursus && isset($_POST['kuantitas'])) {
        $kuantitas = intval($_POST['kuantitas']);
        if (isset($_SESSION['keranjang'][$id_kursus])) {
            if ($kuantitas > 0) {
                $_SESSION['keranjang'][$id_kursus]['kuantitas'] = $kuantitas;
                $_SESSION['pesan_keranjang'] = "Kuantitas kursus berhasil diperbarui.";
            } else {
                // Jika kuantitas 0 atau kurang, hapus item
                $nama_kursus_dihapus = $_SESSION['keranjang'][$id_kursus]['nama'];
                unset($_SESSION['keranjang'][$id_kursus]);
                $_SESSION['pesan_keranjang'] = "Kursus \"".htmlspecialchars($nama_kursus_dihapus)."\" berhasil dihapus dari keranjang karena kuantitas 0.";
            }
        }
    } elseif ($action == 'kosongkan') {
        $_SESSION['keranjang'] = [];
        $_SESSION['pesan_keranjang'] = "Keranjang berhasil dikosongkan.";
    }
}

// Redirect kembali ke halaman keranjang atau halaman sebelumnya
// Jika ada referer dan bukan halaman aksi itu sendiri, kembali ke referer
$referer = $_SERVER['HTTP_REFERER'] ?? '../courses/index.php'; // Default kembali ke halaman kursus
if (strpos($referer, 'keranjang_aksi.php') !== false) {
    $referer = '../courses/index.php'; // Hindari loop redirect
}

// Jika aksi dari halaman keranjang, redirect ke keranjang
if (isset($_POST['dari_keranjang'])) {
    header("Location: ./simulasi/keranjang.php"); // Nantinya akan kita buat keranjang.php
} else {
    header("Location: " . $referer);
}
exit();
?>