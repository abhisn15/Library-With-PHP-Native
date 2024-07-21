<?php
session_start();

// Menghapus semua sesi
$_SESSION = array();

// Menghancurkan sesi
session_destroy();

// Menghapus cookie username
setcookie("username", "", time() - 3600, "/");

// Mengarahkan pengguna ke halaman login
header("Location: login.php");
exit;
