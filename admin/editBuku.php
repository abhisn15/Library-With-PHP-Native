<?php
require '../dbController.php';

// Ambil data di URL
$id = intval($_GET["id"]);

// Query data buku berdasarkan ID
$buku = query("SELECT * FROM buku WHERE id = $id")[0];

// Mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
  $result = ubahBuku($_POST);

  if ($result > 0) {
    echo "
        <script>
        alert('Data berhasil diubah!');
        document.location.href = 'buku.php';
        </script>
        ";
  } else {
    echo "
        <script>
        alert('Data gagal diubah!');
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
  <title>Edit buku</title>
</head>

<body>
  <h2 class="m-4">Update buku</h2>
  <form method="POST" class="ms-3" enctype="multipart/form-data">
    <div class="mb-3">
      <input type="hidden" class="form-control w-50" value="<?php echo $buku['id']; ?>" name="id">
      <label class="form-label">Judul Buku</label>
      <input type="text" class="form-control w-50" name="judul" value="<?php echo $buku['judul']; ?>">
      <label class="form-label">Penerbit</label>
      <input type="text" class="form-control w-50" name="penerbit" value="<?php echo $buku['penerbit']; ?>" maxlength="10">
      <label class="form-label">Tahun Terbit</label>
      <input type="number" class="form-control w-50" name="tahun_terbit" value="<?php echo $buku['tahun_terbit']; ?>">
      <label class="form-label">Jumlah Buku</label>
      <input type="number" class="form-control w-50" name="stok_buku" value="<?php echo $buku['stok_buku']; ?>">
      <label class="form-label">Cover</label> <br>
      <img src="../img/<?php echo $buku['gambar']; ?>" width="50" id="previewImg" class="mb-2"> <br>
      <input type="file" class="form-control w-50" id="gambar" name="gambar" onchange="previewImage(event)">
      <input type="hidden" name="gambarLama" value="<?php echo $buku['gambar']; ?>">
      <button type="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
      <p style="color: red; margin-top: 10px;" id="warning"></p>
    </div>
  </form>

  <a href="buku.php" class="m-4">Kembali</a>

  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function() {
        const output = document.getElementById('previewImg');
        output.src = reader.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>

</html>