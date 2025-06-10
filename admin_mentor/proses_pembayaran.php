<?php
require '../komponen/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id_pembelian'];

    $stmt_count = $conn->prepare("UPDATE `pembelian` SET `status` = 'Sukses' WHERE `pembelian`.`id_pembelian` = ?");
    $stmt_count->bind_param("i", $id);
    $stmt_count->execute();

    header("Location: dashboard_admin.php");
    exit();
}
?>