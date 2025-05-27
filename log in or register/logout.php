<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['role']);
unset($_SESSION['email']); // Jika ada
// session_destroy(); // Alternatif untuk menghapus semua data sesi

header("Location: ../landing page/pertama.php"); //
exit();
?>