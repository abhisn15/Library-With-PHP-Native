<?php
require '../dbController.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Anggota'])) {
  header("Location: ../login.php");
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Anggota | Siswa</title>
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
            <a class="nav-link" href="guru.php">Guru</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="siswa.php">Siswa</a>
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
  <section class="d-flex flex-row align-items-center justify-content-center" style="height: 100vh;">
    <h1>
      INI HALAMAN UNTUK MENAMPILKAN PARA SISWA
    </h1>
  </section>
</body>

</html>