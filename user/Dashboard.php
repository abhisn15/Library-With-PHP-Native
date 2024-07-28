<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../dbController.php';

session_start();

$perPage = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Ambil cookie
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Anggota'])) {
  header("Location: ../login.php");
  exit;
}

if (isset($_POST["cariBuku"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $buku = query("SELECT * FROM buku LIMIT ?, ?", [$start, $perPage]);
    $total = query("SELECT COUNT(*) AS total FROM buku")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $buku = cariBuku($keyword);
    $total = count($buku);
    $page = 1; // Reset ke halaman pertama setelah pencarian
    $start = 0;
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $buku = query("SELECT * FROM buku LIMIT ?, ?", [$start, $perPage]);
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM buku")[0]['total'];
}
$pages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <title>Halaman Dashboard | Anggota</title>
  <script src="../js/jquery.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library 40</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="guru.php">Guru</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="siswa.php">Siswa</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="transaksi.php" aria-disabled="true">History Pinjaman</a>
          </li>
        </ul>
        <form class="d-flex" role="search" action="" method="post">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
          <button class="btn btn-outline-success me-2" type="submit" name="cariBuku">Search</button>
        </form>
        <button class="btn btn-outline-danger" onclick="location.href = '../logout.php'" type="submit">
          <span>Logout</span>
        </button>
      </div>
    </div>
  </nav>

  <?php
  // Tampilkan pesan jika ada
  if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success" role="alert">';
    echo $_SESSION['message'];
    echo '</div>';
    // Hapus pesan setelah ditampilkan
    unset($_SESSION['message']);
  }
  ?>
  <div class="container mt-4">
    <h3>Selamat Datang, <?= $username ?></h3>
    <hr>
    <h4>Daftar Buku</h4>

    <?php
    if (isset($_SESSION['message'])) {
      echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
      unset($_SESSION['message']);
    }
    ?>

    <?php if (empty($buku)) : ?>
      <div class="alert alert-warning">
        Buku tidak ditemukan dalam pencarian.
      </div>
    <?php else : ?>
      <div class="container mt-4">
        <div class="row">
          <?php foreach ($buku as $row) : ?>
            <div class="col-md-3">
              <div class="card mb-4 py-3 d-flex flex-column justify-content-between" style="height: 540px;">
                  <div class="d-flex justify-content-center">
                    <img src="../img/<?= htmlspecialchars($row['gambar']); ?>" width="200" alt="<?= htmlspecialchars($row['judul']); ?>">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['judul']); ?></h5>
                    <p class="card-text">Penerbit: <?= htmlspecialchars($row['penerbit']); ?></p>
                    <div class="d-flex flex-row justify-content-between">
                      <p class="card-text">Tahun Terbit: <?= htmlspecialchars($row['tahun_terbit']); ?></p>
                      <p class="card-text">Stok Buku: <?= htmlspecialchars($row['stok_buku']); ?></p>
                    </div>
                    <div>
                      <a href="pinjam.php?id=<?= htmlspecialchars($row['id']); ?>" class="d-grid btn btn-primary ">Pinjam</a>
                    </div>
                  </div>
              </div>
            </div>

          <?php endforeach; ?>
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
      </div>
    <?php endif; ?>
  </div>


  <!-- Tampilkan pesan dari session -->
  <?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['message']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
</body>

</html>