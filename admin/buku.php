<?php
require '../dbController.php';

$perPage = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Ambil cookie
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
  header("Location: ../login.php");
  exit;
}

if (isset($_POST["cariBuku"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $buku = query("SELECT * FROM buku LIMIT $start, $perPage");
    $total = query("SELECT COUNT(*) AS total FROM buku")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $buku = cari($keyword);
    $total = count($buku);
    $page = 1; // Reset ke halaman pertama setelah pencarian
    $start = 0;
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $buku = query("SELECT * FROM buku LIMIT $start, $perPage");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM buku")[0]['total'];
}

$pages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Admin</title>
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

    .card {
      width: 190px;
      background: white;
      padding: .4em;
      border-radius: 6px;
    }

    .card-image {
      background-color: rgb(236, 236, 236);
      width: 100%;
      height: 130px;
      border-radius: 6px 6px 0 0;
    }

    .card-image:hover {
      transform: scale(0.98);
    }

    .category {
      text-transform: uppercase;
      font-size: 0.7em;
      font-weight: 600;
      color: rgb(63, 121, 230);
      padding: 10px 7px 0;
    }

    .category:hover {
      cursor: pointer;
    }

    .heading {
      font-weight: 600;
      color: rgb(88, 87, 87);
      padding: 7px;
    }

    .heading:hover {
      cursor: pointer;
    }

    .author {
      color: gray;
      font-weight: 400;
      font-size: 11px;
      padding-top: 20px;
    }

    .name {
      font-weight: 600;
    }

    .name:hover {
      cursor: pointer;
    }
  </style>
  <script src="../js/jquery.js"></script>
  <script src="../js/scriptBuku.js"></script>
</head>

<body class="m-4">
  <h3>Halo, <?= htmlspecialchars($username); ?>!</h3>

  <h1>Daftar Buku</h1>


  <div class="row">
    <div class="col-lg-6">
      <button type="button" class="btn btn-primary" onclick="location.href = 'createBuku.php'" data-bs-toggle="modal" data-bs-target="#formModal">
        Tambah Buku
      </button>
      <a href="../logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
      <a href="../cetakBuku.php" class="btn btn-success" target="_blank">Cetak</a>
      <a href="Dashboard.php" class="btn btn-info">Siswa</a>
      <a href="guru.php" class="btn btn-info">Guru</a>
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
    <!-- <div class="card">
      <div class="card-image"></div>
      <div class="category"> Illustration </div>
      <div class="heading"> Judul
        <div class="author"> By <span class="name">Abi</span> 4 days ago</div>
      </div>
    </div> -->
      <table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
        <tr style="text-align: center;">
          <th>No.</th>
          <th>Aksi</th>
          <th>Cover</th>
          <th>Judul</th>
          <th>Penerbit</th>
          <th>Tahun Terbit</th>
          <th>Jumlah Buku</th>
        </tr>
        <?php $i = $start + 1; ?>
        <?php foreach ($buku as $row) : ?>
          <tr style="text-align: center;">
            <td class="center-align"><?= $i; ?></td>
            <td class="center-align">
              <a href="editBuku.php?id=<?= $row["id"]; ?>">ubah</a>
              <a href="deleteBuku.php?id=<?= $row["id"]; ?>" onclick="return confirm('serius?');" style="color: red;">hapus</a>
            </td>
            <td class="center-align"><img src="../img/<?= $row["gambar"]; ?>" width="100"></td>
            <td class="center-align"><?= $row["judul"]; ?></td>
            <td class="center-align"><?= $row["penerbit"]; ?></td>
            <td class="center-align"><?= $row["tahun_terbit"]; ?></td>
            <td class="center-align"><?= $row["stok_buku"]; ?></td>
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