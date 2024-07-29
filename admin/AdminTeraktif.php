<?php
require '../dbController.php';

session_start();

$username = $_SESSION['username'];

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
  header("Location: ../login.php");
  exit;
}


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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Admin | Peminjaman Terbanyak</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

</head>

<body style="height: 100vh; display:flex; flex-direction:column; justify-content: center; ">
  <div class="col-12">
    <h3 class="text-center">Admin Teraktif</h3>
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
  <hr>
  <a href="laporan.php">Kembali</a>
</body>

</html>