<?php
require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("Location: ../login.php");
  exit;
}

// Query for Peminjaman Terbanyak
$peminjamanTerbanyakQuery = "SELECT buku.judul AS judul_buku, COUNT(transaksi.id_buku) AS jumlah_peminjaman
  FROM transaksi
  INNER JOIN buku ON transaksi.id_buku = buku.id
  GROUP BY buku.id, buku.judul
  ORDER BY jumlah_peminjaman DESC
  LIMIT 10";
$peminjamanTerbanyak = query($peminjamanTerbanyakQuery);

// Query for Anggota Paling Aktif
$anggotaPalingAktifQuery = "SELECT users.username AS nama_anggota, COUNT(transaksi.id_peminjam) AS jumlah_peminjaman
  FROM transaksi
  INNER JOIN users ON transaksi.id_peminjam = users.id
  GROUP BY users.id, users.username
  ORDER BY jumlah_peminjaman DESC
  LIMIT 10";
$anggotaPalingAktif = query($anggotaPalingAktifQuery);

// Query for Buku yang Belum Dikembalikan
$bukuBelumDikembalikanQuery = "SELECT buku.judul AS judul_buku, transaksi.tanggal_pinjam, transaksi.masa_pinjam
  FROM transaksi
  INNER JOIN buku ON transaksi.id_buku = buku.id
  WHERE transaksi.tanggal_pengembalian IS NULL";
$bukuBelumDikembalikan = query($bukuBelumDikembalikanQuery);

// Query for Judul Buku yang Laris Dipinjam
$bukuLarisQuery = "SELECT buku.judul AS judul_buku, COUNT(transaksi.id_buku) AS jumlah_peminjaman
  FROM transaksi
  INNER JOIN buku ON transaksi.id_buku = buku.id
  GROUP BY buku.id, buku.judul
  ORDER BY jumlah_peminjaman DESC
  LIMIT 10";
$bukuLaris = query($bukuLarisQuery);

// Query for Admin Teraktif
$adminTeraktifQuery = "SELECT users.username AS nama_admin, users.f_role, COUNT(transaksi.id_admin) AS jumlah_transaksi
  FROM transaksi
  INNER JOIN users ON transaksi.id_admin = users.id
  WHERE users.f_role = 'Admin'
  GROUP BY users.username, users.f_role
  ORDER BY jumlah_transaksi DESC";
$adminTeraktif = query($adminTeraktifQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link href="../css/navbar.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/custom.css">
  <title>Halaman Laporan</title>
</head>

<body>
  <div class="container m-4">
    <h2>Halo, <?= $username; ?>!</h2>
    <h4 class="text-start">Laporan Perpustakaan</h4>
    <div class="row">
      <div class="col-lg-6">
        <a href="../logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
        <a href="../cetakGuru.php" class="btn btn-success" target="_blank">Cetak</a>
        <a href="Dashboard.php" class="btn btn-info">Dashboard</a>
        <a href="buku.php" class="btn btn-info">Buku</a>
        <a href="guru.php" class="btn btn-info">Guru</a>
        <a href="transaksi.php" class="btn btn-info">Transaksi</a>
      </div>
    </div>
    <br><br>

    <div class="row">
      <div class="col-12">
        <h3>Peminjaman Terbanyak</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Judul Buku</th>
              <th>Jumlah Peminjaman</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($peminjamanTerbanyak as $index => $row) : ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['judul_buku']; ?></td>
                <td><?= $row['jumlah_peminjaman']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="col-12">
        <h3>Anggota Paling Aktif</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Nama Anggota</th>
              <th>Jumlah Peminjaman</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($anggotaPalingAktif as $index => $row) : ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['nama_anggota']; ?></td>
                <td><?= $row['jumlah_peminjaman']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="col-12">
        <h3>Buku yang Belum Dikembalikan</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Judul Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Masa Pinjam</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bukuBelumDikembalikan as $index => $row) : ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['judul_buku']; ?></td>
                <td><?= $row['tanggal_pinjam']; ?></td>
                <td><?= $row['masa_pinjam']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="col-12">
        <h3>Judul Buku yang Laris Dipinjam</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Judul Buku</th>
              <th>Jumlah Peminjaman</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bukuLaris as $index => $row) : ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['judul_buku']; ?></td>
                <td><?= $row['jumlah_peminjaman']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="col-12">
        <h3>Admin Teraktif</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Nama Admin</th>
              <th>Jumlah Transaksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($adminTeraktif as $index => $row) : ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['nama_admin']; ?></td>
                <td><?= $row['jumlah_transaksi']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>