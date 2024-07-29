<?php
require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
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
    <h3 class="text-center">Peminjaman Terbanyak</h3>
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
  <hr>
  <a href="laporan.php">Kembali</a>
</body>

</html>