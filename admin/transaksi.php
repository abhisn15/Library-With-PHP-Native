<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../dbController.php';

$perPage = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Ambil cookie
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION["Admin"])) {
  header("Location: ../login.php");
  exit;
}

// Fungsi untuk mengembalikan buku
if (isset($_GET['kembalikan']) && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $tanggal_pengembalian = date('Y-m-d');

  $query = "UPDATE transaksi SET tanggal_pengembalian = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param('si', $tanggal_pengembalian, $id);

  if ($stmt->execute()) {
    $_SESSION['message'] = "Buku berhasil dikembalikan!";
    header("Location: transaksi.php");
    exit;
  } else {
    die("Execute failed: " . $stmt->error);
  }
}

if (isset($_POST["cariListTransaksi"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku, buku.gambar AS cover, peminjam.username AS nama_peminjam, admin.username AS nama_admin 
                          FROM transaksi 
                          INNER JOIN buku ON transaksi.id_buku = buku.id 
                          INNER JOIN users AS peminjam ON transaksi.id_peminjam = peminjam.id 
                          INNER JOIN users AS admin ON transaksi.id_admin = admin.id 
                          LIMIT $start, $perPage");
    $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cari($keyword);
    $total = count($transaksi);
    $page = 1; // Reset ke halaman pertama setelah pencarian
    $start = 0;
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku, buku.gambar AS cover, peminjam.username AS nama_peminjam, admin.username AS nama_admin 
                        FROM transaksi 
                        INNER JOIN buku ON transaksi.id_buku = buku.id 
                        INNER JOIN users AS peminjam ON transaksi.id_peminjam = peminjam.id 
                        INNER JOIN users AS admin ON transaksi.id_admin = admin.id 
                        LIMIT $start, $perPage");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
}

$pages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Admin | Transaksi</title>
  <style>
    /* Menyelaraskan teks di tengah secara horizontal dan vertikal */
    .center-align {
      text-align: center;
      vertical-align: middle;
    }

    /* Menyelaraskan gambar di tengah secara horizontal dan vertikal */
    .center-align img {
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .loader {
      width: 100px;
      position: absolute;
      bottom: -18px;
      right: 60px;
      z-index: -1;
      display: none;
    }

    @media print {

      .logout,
      .tambah,
      .form-cari,
      .aksi {
        display: none;
      }
    }
  </style>
  <script src="../js/jquery.js"></script>
  <script src="../js/scriptTransaksi.js"></script>
</head>

<body class="m-4">
  <h3>Halo, <?= htmlspecialchars($username); ?>!</h3>
  <h1>List Transaksi</h1>

  <div class="row">
    <div class="col-lg-6">
      <button type="button" class="btn btn-primary" onclick="location.href = 'pinjam.php'" data-bs-toggle="modal" data-bs-target="#formModal">
        Pinjam Buku
      </button>
      <a href="../logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
      <a href="../cetakTransaksi.php" class="btn btn-success" target="_blank">Cetak</a>
      <a href="Dashboard.php" class="btn btn-info">Siswa</a>
      <a href="buku.php" class="btn btn-info">Buku</a>
      <a href="guru.php" class="btn btn-info">Guru</a>
      <a href="laporan.php" class="btn btn-info">Laporan</a>
    </div>
  </div>
  <br><br>
  <form action="" method="post" class="col-lg-6">
    <div class="input-group">
      <input type="text" name="keyword" size="40" autofocus placeholder="masukkan keyword pencarian..." autocomplete="off" class="ps-2" id="keyword">
      <button class="btn btn-primary" type="submit" name="cari" id="tombolCari">Cari</button>
      <img src="../img/loader.gif" class="loader">
    </div>
  </form>
  <br>
  <div id="container">
    <table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
      <tr style="text-align: center;">
        <th>No.</th>
        <th>Nama Peminjam</th>
        <th>Buku</th>
        <th>Tanggal Peminjaman</th>
        <th>Masa Pinjam</th>
        <th>Tanggal Pengembalian</th>
        <th>Pemberi</th>
        <th>Aksi</th>
      </tr>
      <?php $i = $start + 1; ?>
      <?php foreach ($transaksi as $row) : ?>
        <tr style="text-align: center;">
          <td class="center-align"><?= $i; ?></td>
          <td class="center-align"><?= $row["nama_peminjam"]; ?></td>
          <td class="center-align d-flex flex-column justify-center align-items-center gap-3">
            <?= $row["judul_buku"]; ?>
          </td>
          <td class="center-align"><?= $row["tanggal_pinjam"]; ?></td>
          <td class="center-align"><?= $row["masa_pinjam"]; ?></td>
          <td class="center-align"><?= $row["tanggal_pengembalian"] ?? 'Belum Dikembalikan!'; ?></td>
          <td class="center-align"><?= $row["nama_admin"]; ?></td>
          <td>
            <?php if (is_null($row['tanggal_pengembalian'])) : ?>
              <a href="ubahMasaPinjam.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Ubah Masa Pinjam</a>
              <a href="transaksi.php?kembalikan=1&id=<?php echo $row['id']; ?>" class="btn btn-primary">Kembalikan</a>
            <?php else : ?>
              Sudah Dikembalikan
            <?php endif; ?>
          </td>
        </tr>
        <?php $i++; ?>
      <?php endforeach; ?>
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
</body>

</html>