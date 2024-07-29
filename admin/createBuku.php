<?php
require '../dbController.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
  header("Location: ../login.php");
  exit;
}


// mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
  if (tambahBuku($_POST) > 0) {
    echo "
        <script>
            alert('data berhasil ditambahkan!');
            document.location.href = 'buku.php';
        </script>
        ";
  } else {
    echo "
        <script>
            alert('data gagal ditambahkan!');
            document.location.href = 'buku.php';
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
  <title>Tambah Buku Baru</title>
</head>

<body>
  <h2 class="m-4">Menambahkan Buku</h2>
  <form method="POST" class="ms-3" id="Tambah" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Judul Buku</label>
      <input type="text" class="form-control w-50" id="judul" aria-describedby="textHelp" name="judul">
      <label class="form-label">Penerbit</label>
      <input type="text" class="form-control w-50" id="penerbit" aria-describedby="textHelp" name="penerbit">
      <label class="form-label">Tahun Terbit</label>
      <input type="number" class="form-control w-50" id="tahun_terbit" aria-describedby="textHelp" name="tahun_terbit">
      <label class="form-label">Jumlah Buku</label>
      <input type="number" class="form-control w-50" id="stok_buku" aria-describedby="textHelp" name="stok_buku">
      <br>
      <label class="form-label">Cover</label>
      <img id="previewImg" src="#" alt="Preview Gambar" width="50" style="display: none;"> <br>
      <input type="file" class="form-control w-50" id="gambar" aria-describedby="textHelp" name="gambar" onchange="previewImage(event)">
      <button type="submit" class="btn btn-primary mt-3" name="submit">Submit</button>
      <p style="color: red; margin-top: 10px;" id="warning"></p>
    </div>
  </form>

  <script>
    function previewImage(event) {
      var reader = new FileReader();
      reader.onload = function() {
        var output = document.getElementById('previewImg');
        output.src = reader.result;
        output.style.display = 'block';
      }
      reader.readAsDataURL(event.target.files[0]);
    }

    document.getElementById("Tambah").addEventListener("submit", function(event) {
      var judul = document.getElementById("judul").value.trim();
      var penerbit = document.getElementById("penerbit").value.trim();
      var tahun_terbit = document.getElementById("tahun_terbit").value.trim();
      var stok_buku = document.getElementById("stok_buku").value.trim();

      if (judul === "" || penerbit === "" || tahun_terbit === "" || stok_buku === "") {
        event.preventDefault();
        document.getElementById("warning").innerHTML = "Mohon isi semua kolom!";
      }
    });
  </script>
</body>

</html>