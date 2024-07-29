<?php

require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("Location: ../login.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

  <title>Halaman Admin | Laporan</title>
</head>

<body>
  <div class="container m-4">
    <h2>Halo, <?= $username; ?>!</h2>
    <h4 class="text-start">Laporan Perpustakaan</h4>
    <div class="row">
      <div class="col-lg-6">
        <a href="../logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
        <a href="Dashboard.php" class="btn btn-info">Dashboard</a>
        <a href="buku.php" class="btn btn-info">Buku</a>
        <a href="guru.php" class="btn btn-info">Guru</a>
        <a href="transaksi.php" class="btn btn-info">Transaksi</a>
      </div>
    </div>
    <br><br>
    <h4 class="text-center" style="width: 120%;">Hasil Laporan Perpustakaan</h4>
    <div class="d-flex flex-row align-items-center justify-content-center gap-2" style="width: 120%;">

      <a href="PeminjamanTerbanyak.php" class="btn btn-info">Peminjaman Terbanyak</a>
      <a href="BukuBelumDikembalikan.php" class="btn btn-info">Buku yang Belum Dikembalikan</a>
      <a href="AnggotaPalingAktif.php" class="btn btn-info">Anggota Paling Aktif</a>
      <a href="AdminTeraktif.php" class="btn btn-info">Admin Teraktif</a>
    </div>
  </div>
</body>

</html>