<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../dbController.php';
session_start();

$id_user = $_SESSION['id'];
$perPage = 5; // Jumlah item per halaman

// Inisialisasi variabel untuk pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $perPage;

// Proses pengembalian buku
if (isset($_GET['kembalikan']) && isset($_GET['id'])) {
  $id_transaksi = intval($_GET['id']);
  $tanggal_pengembalian = date('Y-m-d H:i:s');

  // Ambil id buku dari transaksi
  $query = "SELECT id_buku FROM transaksi WHERE id = ? AND id_peminjam = ?";
  $result = query($query, [$id_transaksi, $id_user]);
  $id_buku = $result[0]['id_buku'] ?? null;

  if ($id_buku) {
    // Update tanggal pengembalian di transaksi
    $query = "UPDATE transaksi SET tanggal_pengembalian = ? WHERE id = ? AND id_peminjam = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
      throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('sii', $tanggal_pengembalian, $id_transaksi, $id_user);

    if ($stmt->execute()) {
      // Tambah stok buku
      $query = "UPDATE buku SET stok_buku = stok_buku + 1 WHERE id = ?";
      $stmt = $conn->prepare($query);
      if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
      }
      $stmt->bind_param('i', $id_buku);
      $stmt->execute();

      $_SESSION['message'] = "Buku berhasil dikembalikan dan stok diperbarui!";
    } else {
      $_SESSION['message'] = "Gagal mengembalikan buku!";
    }

    header("Location: transaksi.php");
    exit;
  }
}

if (isset($_POST["cariRiwayatPeminjaman"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku
                            FROM transaksi 
                            INNER JOIN buku ON transaksi.id_buku = buku.id
                            WHERE transaksi.id_peminjam = ?
                            LIMIT ?, ?", [$id_user, $start, $perPage]);
    $total = count($transaksi);
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cariHistoryPeminjaman($keyword, $id_user);
    $total = count($transaksi);
    $page = 1; // Reset ke halaman pertama setelah pencarian
    $start = 0;
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku
                        FROM transaksi 
                        INNER JOIN buku ON transaksi.id_buku = buku.id
                        WHERE transaksi.id_peminjam = ?
                        LIMIT ?, ?", [$id_user, $start, $perPage]);
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM transaksi WHERE id_peminjam = ?", [$id_user])[0]['total'];
}
$pages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <title>Transaksi Buku</title>
  <script src="../js/jquery.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="Dashboard.php">Library 40</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="Dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="guru.php">Guru</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="siswa.php">Siswa</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="transaksi.php" aria-disabled="true">History Pinjaman</a>
          </li>
        </ul>
        <form class="d-flex" role="search" action="" method="post">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
          <button class="btn btn-outline-success me-2" name='cari' type="submit" name="cariTransaksi">Search</button>
        </form>
        <button class="btn btn-outline-danger" onclick="location.href = '../logout.php'" type="submit">
          <span>Logout</span>
        </button>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
    <h2 class="mb-4">Riwayat Peminjaman Buku</h2>

    <?php
    if (isset($_SESSION['message'])) {
      echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
      unset($_SESSION['message']);
    }
    ?>

    <table class="table table-bordered text-center">
      <thead>
        <tr>
          <th>ID Transaksi</th>
          <th>Judul Buku</th>
          <th>Tanggal Pinjam</th>
          <th>Masa Pinjam</th>
          <th>Tanggal Pengembalian</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transaksi as $row) : ?>
          <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['judul_buku']); ?></td>
            <td><?php echo htmlspecialchars($row['tanggal_pinjam']); ?></td>
            <td class="text-danger"><?php echo htmlspecialchars($row['masa_pinjam']); ?></td>
            <td><?php echo $row['tanggal_pengembalian'] ?? 'Belum Dikembalikan!'; ?></td>
            <td>
              <?php if (is_null($row['tanggal_pengembalian'])) : ?>
                <a href="transaksi.php?kembalikan=1&id=<?php echo $row['id']; ?>" class="btn btn-primary">Kembalikan</a>
              <?php else : ?>
                Sudah Dikembalikan
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $pages; $i++) : ?>
        <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <!-- Tampilkan pesan dari session -->
  <?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['message']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div><?php unset($_SESSION['message']); ?> <?php endif; ?>
</body>
</html>