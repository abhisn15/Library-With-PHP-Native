<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../dbController.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
  header("Location: ../login.php");
  exit;
}

$id_admin = $_SESSION['id'];
$user = query("SELECT * FROM users WHERE id = ?", [$id_admin])[0];

// Ambil semua data buku
$bukuList = query("SELECT * FROM buku");

// Ambil semua data pengguna
$users = query("SELECT * FROM users WHERE f_role = 'Anggota'");

// Mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
  $id_buku = intval($_POST["id_buku"]);
  $id_peminjam = intval($_POST["id_peminjam"]);
  $tanggal_pinjam = htmlspecialchars($_POST["tanggal_pinjam"]);

  // Query data buku berdasarkan ID
  $buku = query("SELECT * FROM buku WHERE id = ?", [$id_buku]);

  if (empty($buku)) {
    $_SESSION['message'] = "Buku dengan ID $id_buku tidak ditemukan!";
    header("Location: Dashboard.php");
    exit;
  }

  $buku = $buku[0];

  // Mengurangi stok buku
  if ($buku['stok_buku'] <= 0) {
    $_SESSION['message'] = "Yahh, stok buku sudah habis, tunggu di lain waktu ya :)";
    header("Location: Dashboard.php");
    exit;
  }

  $stok_buku = $buku['stok_buku'] - 1;
  $query = "UPDATE buku SET stok_buku = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param('ii', $stok_buku, $id_buku);

  if (!$stmt->execute()) {
    throw new Exception("Execute failed: " . $stmt->error);
  }

  $result = pinjam($_POST, $id_admin);

  if ($result > 0) {
    echo "
        <script>
        alert('Peminjaman sukses dikirim, segera konfirmasi kepada admin!');
        document.location.href = 'transaksi.php';
        </script>
        ";
  } else {
    echo "
        <script>
        alert('Peminjaman gagal dikirim!!');
        document.location.href = 'transaksi.php';
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
      <label for="id_buku" class="form-label">ID Buku</label>
      <select class="form-select w-50" id="id_buku" name="id_buku" required>
        <?php foreach ($bukuList as $buku) : ?>
          <option value="<?php echo $buku['id']; ?>"><?php echo htmlspecialchars($buku['judul']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="id_peminjam" class="form-label mt-3">Peminjam</label>
      <select class="form-select w-50" id="id_peminjam" name="id_peminjam" required>
        <?php foreach ($users as $user) : ?>
          <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="tanggal_pinjam" class="form-label mt-3">Tanggal Peminjaman</label>
      <input type="date" class="form-control w-50" id="tanggal_pinjam" name="tanggal_pinjam" required>

      <button type="submit" name="submit" class="btn btn-primary mt-3">Pinjam</button>
    </div>
  </form>
  <a href="Dashboard.php" class="m-4">Kembali</a>
</body>

</html>