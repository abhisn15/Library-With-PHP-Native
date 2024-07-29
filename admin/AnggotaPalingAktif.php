<?php
require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("Location: ../login.php");
  exit;
}

// Query for Anggota Paling Aktif
$anggotaPalingAktifQuery = "SELECT users.username AS nama_anggota, COUNT(transaksi.id_peminjam) AS jumlah_peminjaman
  FROM transaksi
  INNER JOIN users ON transaksi.id_peminjam = users.id
  GROUP BY users.id, users.username
  ORDER BY jumlah_peminjaman DESC
  LIMIT 10";
$anggotaPalingAktif = query($anggotaPalingAktifQuery);
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
    <h3 class="text-center">Anggota Paling Aktif</h3>
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
  <hr>
  <a href="laporan.php">Kembali</a>
</body>

</html>