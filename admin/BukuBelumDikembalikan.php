<?php
require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("Location: ../login.php");
  exit;
}

// Query for Buku yang Belum Dikembalikan
$bukuBelumDikembalikanQuery = "SELECT buku.judul AS judul_buku, transaksi.tanggal_pinjam, transaksi.masa_pinjam
  FROM transaksi
  INNER JOIN buku ON transaksi.id_buku = buku.id
  WHERE transaksi.tanggal_pengembalian IS NULL";
$bukuBelumDikembalikan = query($bukuBelumDikembalikanQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Admin | Peminjaman Terbanyak</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

</head>

<body style="height: 100vh; display:flex; flex-direction:column; justify-content: center; ">
  <div class="col-12">
    <h3 class="text-center">Buku yang Belum Dikembalikan</h3>
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
  <hr>
  <a href="laporan.php">Kembali</a>
</body>

</html>