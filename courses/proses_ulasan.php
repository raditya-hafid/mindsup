<?php
session_start();
require '../komponen/koneksi.php';

// Pastikan hanya siswa yang login yang bisa memproses
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input dari form
    if (!isset($_POST['id_detail']) || !is_numeric($_POST['id_detail']) || empty(trim($_POST['ulasan']))) {
        die("Data yang dikirim tidak lengkap.");
    }

    $id_detail = (int)$_POST['id_detail'];
    $ulasan = trim($_POST['ulasan']);
    $id_siswa = $_SESSION['user_id'];

    // Query UPDATE untuk menyimpan ulasan ke kolom 'ulasan'
    // Pastikan hanya pemilik ulasan yang bisa mengubahnya
    $sql = "UPDATE detail_pembelian dp JOIN pembelian p ON dp.id_pembelian = p.id_pembelian SET dp.ulasan = ? WHERE dp.id_detail = ? AND p.id_siswa = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $ulasan, $id_detail, $id_siswa);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, redirect kembali ke halaman detail kursus dengan pesan sukses

        // Ambil id_kursus untuk redirect kembali
        $stmt_get_kursus = mysqli_prepare($conn, "SELECT id_kursus FROM detail_pembelian WHERE id_detail = ?");
        mysqli_stmt_bind_param($stmt_get_kursus, "i", $id_detail);
        mysqli_stmt_execute($stmt_get_kursus);
        $result_kursus = mysqli_stmt_get_result($stmt_get_kursus);
        $data_kursus = mysqli_fetch_assoc($result_kursus);

        $_SESSION['success_message'] = "Ulasan berhasil disimpan!";
        header("Location: detail_kursus.php?id=" . $data_kursus['id_kursus']);
        exit();
    } else {
        die("Gagal menyimpan ulasan. Silakan coba lagi.");
    }
} else {
    // Jika akses bukan melalui metode POST, redirect ke halaman utama
    header("Location: ../index.php");
    exit();
}
?>