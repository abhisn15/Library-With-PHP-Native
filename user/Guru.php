<?php
require '../dbController.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Anggota'])) {
  header("Location: ../login.php");
  exit;
}

$perPage = 14; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Ambil cookie
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Anggota | Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            <a class="nav-link active" href="guru.php">Guru</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="siswa.php">Siswa</a>
          </li>
        </ul>
        <form class="d-flex" role="search" action="" method="post">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
          <button class="btn btn-outline-success me-2" type="submit" name="cariGuru">Search</button>
        </form>
        <button class="btn btn-outline-danger" onclick="location.href = '../logout.php'" type="submit">
          <span>Logout</span>
        </button>
      </div>
    </div>
  </nav>
  <section class="d-flex flex-row align-items-center justify-content-center">
    <div class="container mt-4">
      <h4>Daftar Guru</h4>
      <div class="row">
        <?php foreach ($guru as $row) : ?>
          <div class="col-md-3">
            <div class="card mb-4 py-3 d-flex flex-column justify-content-between">
              <div class="d-flex justify-content-center">
                <img src="../img/<?= $row['gambar']; ?>" alt="<?= $row['nama'] ?>" width="200">
              </div>
              <div class="card-body">
                <div class="d-flex flex-row justify-content-between">
                  <p class="card-text"> <strong><?= $row['nama']; ?></strong></p>
                  <p class="card-text"> <?= $row['alamat']; ?></p>
                </div>
                <div class="d-flex flex-row align-items-center">
                  <div class="d-flex flex-column">
                    <span class="card-text">NIP </span>
                    <span class="card-text">No Hp</span>
                  </div>
                  <div class="d-flex flex-column mx-4">
                    <span>: <?= $row['nip']; ?></span>
                    <span>: <?= $row['hp']; ?></span>
                  </div>
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
  </section>
</body>

</html>