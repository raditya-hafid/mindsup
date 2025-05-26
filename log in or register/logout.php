<?php 
session_start(); 
unset($_SESSION['username']);
unset($_SESSION['id_siswa']);
header("Location: ../landing page/pertama.php");
exit();
?>