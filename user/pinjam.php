<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../dbController.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Anggota'])) {
  header("Location: ../login.php");
  exit;
}

$id_user = $_SESSION['id'];
$user = query("SELECT * FROM users WHERE id = ?", [$id_user])[0];

// Ambil data di URL
$id = intval($_GET["id"]);
error_log("Book ID from URL: $id");

// Query data buku berdasarkan ID
$buku = query("SELECT * FROM buku WHERE id = ?", [$id]);

if (empty($buku)) {
  $_SESSION['message'] = "Buku dengan ID $id tidak ditemukan!";
  header("Location: buku.php");
  exit;
}

$buku = $buku[0];
error_log("Book data: " . print_r($buku, true));

// Mengecek stok buku
if ($buku['stok_buku'] <= 0) {
  $_SESSION['message'] = "Yahh, stok buku sudah habis, tunggu dilain waktu ya:)";
  header("Location: Dashboard.php");
  exit;
}

// Mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
  $result = pinjam($_POST, $_SESSION['id'], $id);

  if ($result > 0) {
    echo "
            <script>
            alert('Peminjaman sukses dikirim, segera konfirmasi kepada admin!');
            document.location.href = 'Dashboard.php';
            </script>
            ";
  } else {
    echo "
            <script>
            alert('Peminjaman gagal dikirim!!');
            document.location.href = 'Dashboard.php';
            </script>
            ";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <title>Pinjam Buku</title>
</head>

<body>
  <h2 class="m-4">Pinjam Buku</h2>
  <form method="POST" class="ms-3">
    <div class="mb-3">
      <label class="form-label">Judul Buku</label>
      <h3><?php echo htmlspecialchars($buku['judul']); ?></h3>
      <input type="hidden" name="id_buku" value="<?php echo $buku['id']; ?>">

      <label class="form-label">Peminjam</label>
      <h3><?php echo htmlspecialchars($user['username']); ?></h3>
      <input type="hidden" name="id_peminjam" value="<?php echo $user['id']; ?>">

      <label class="form-label">Tanggal Peminjaman</label>
      <input type="date" class="form-control w-50" name="tanggal_pinjam" required>
      <button type="submit" name="submit" class="btn btn-primary mt-3">Pinjam</button>
    </div>
  </form>

  <a href="Dashboard.php" class="m-4">Kembali</a>
</body>

</html>