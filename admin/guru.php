<?php
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

if (isset($_POST["cariGuru"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $guru = query("SELECT * FROM guru LIMIT $start, $perPage");
    $total = query("SELECT COUNT(*) AS total FROM guru")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $guru = cari($keyword);
    $total = count($guru);
    $page = 1; // Reset ke halaman pertama setelah pencarian
    $start = 0;
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $guru = query("SELECT * FROM guru LIMIT $start, $perPage");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM guru")[0]['total'];
}

$pages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Admin | Guru</title>
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
  <script src="../js/scriptGuru.js"></script>
</head>

<body class="m-4">
  <h3>Halo, <?= htmlspecialchars($username); ?>!</h3>
  <h1>Daftar Guru</h1>

  <div class="row">
    <div class="col-lg-6">
      <button type="button" class="btn btn-primary" onclick="location.href = 'createGuru.php'" data-bs-toggle="modal" data-bs-target="#formModal">
        Tambah Guru
      </button>
      <a href="../logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
      <a href="../cetakGuru.php" class="btn btn-success" target="_blank">Cetak</a>
      <a href="Dashboard.php" class="btn btn-info">Siswa</a>
      <a href="buku.php" class="btn btn-info">Buku</a>
      <a href="transaksi.php" class="btn btn-info">List Transaksi</a>
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
        <th>Aksi</th>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Nip</th>
        <th>No Hp</th>
        <th>Alamat</th>
      </tr>
      <?php $i = $start + 1; ?>
      <?php foreach ($guru as $row) : ?>
        <tr style="text-align: center;">
          <td class="center-align"><?= $i; ?></td>
          <td class="center-align">
            <a href="editGuru.php?id=<?= $row["id"]; ?>">ubah</a>
            <a href="deleteGuru.php?id=<?= $row["id"]; ?>" onclick="return confirm('serius?');" style="color: red;">hapus</a>
          </td>
          <td class="center-align"><img src="../img/<?= $row["gambar"]; ?>" width="100"></td>
          <td class="center-align"><?= $row["nama"]; ?></td>
          <td class="center-align"><?= $row["nip"]; ?></td>
          <td class="center-align"><?= $row["hp"]; ?></td>
          <td class="center-align"><?= $row["alamat"]; ?></td>
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