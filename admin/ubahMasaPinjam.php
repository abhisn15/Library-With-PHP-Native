<?php
require '../dbController.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION["Admin"])) {
  header("Location: ../login.php");
  exit;
}

$id_transaksi = intval($_GET["id"]);
$query = "SELECT transaksi.*, buku.judul AS judul_buku, users.username AS nama_peminjam 
  FROM transaksi 
  INNER JOIN buku ON transaksi.id_buku = buku.id 
  INNER JOIN users ON transaksi.id_peminjam = users.id 
  WHERE transaksi.id = ?
";
$transaksi = query($query, [$id_transaksi]);

if (empty($transaksi)) {
  $_SESSION['message'] = "Transaksi dengan ID $id_transaksi tidak ditemukan!";
  header("Location: transaksi.php");
  exit;
}

$transaksi = $transaksi[0];

// Mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
  $tambahan_hari = intval($_POST["tambahan_hari"]);
  $tanggal_kembali_baru = date('Y-m-d', strtotime($transaksi["masa_pinjam"] . " +$tambahan_hari days"));

  $query = "UPDATE transaksi SET masa_pinjam = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param('si', $tanggal_kembali_baru, $id_transaksi);

  if ($stmt->execute()) {
    $_SESSION['message'] = "Masa pinjam berhasil diperpanjang!";
    header("Location: transaksi.php");
    exit;
  } else {
    throw new Exception("Execute failed: " . $stmt->error);
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <title>Ubah Masa Pinjam</title>
</head>

<body>
  <h2 class="m-4">Ubah Masa Pinjam</h2>
  <form method="POST" class="ms-3">
    <div class="mb-3">
      <label class="form-label">Judul Buku</label>
      <h3><?php echo htmlspecialchars($transaksi['judul_buku']); ?></h3>

      <label class="form-label">Peminjam</label>
      <h3><?php echo htmlspecialchars($transaksi['nama_peminjam']); ?></h3>

      <label class="form-label">Tanggal Pinjam</label>
      <h3><?php echo htmlspecialchars($transaksi['tanggal_pinjam']); ?></h3>

      <label class="form-label">Tanggal Kembali Sekarang</label>
      <h3><?php echo htmlspecialchars($transaksi['masa_pinjam']); ?></h3>

      <label class="form-label">Tambahan Hari</label>
      <input type="number" class="form-control w-50" name="tambahan_hari" min="1" required>

      <button type="submit" name="submit" class="btn btn-primary mt-3">Perpanjang</button>
    </div>
  </form>
  <a href="transaksi.php" class="m-4">Kembali</a>
</body>

</html>