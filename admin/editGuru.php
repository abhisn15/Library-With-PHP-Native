<?php
require '../dbController.php';

// Ambil data di URL
$id = intval($_GET["id"]);
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
    header("Location: ../login.php");
    exit;
}


// Query data guru berdasarkan ID
$guru = query("SELECT * FROM guru WHERE id = $id")[0];

// Mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
    $result = ubahGuru($_POST);

    if ($result > 0) {
        echo "
        <script>
        alert('Data berhasil diubah!');
        document.location.href = 'guru.php';
        </script>
        ";
    } else {
        echo "
        <script>
        alert('Data gagal diubah!');
        document.location.href = 'guru.php';
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
    <title>Edit Guru</title>
</head>

<body>
    <h2 class="m-4">Update Guru</h2>
    <form method="POST" class="ms-3" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="hidden" class="form-control w-50" value="<?php echo $guru['id']; ?>" name="id">
            <label class="form-label">Nama Guru</label>
            <input type="text" class="form-control w-50" name="nama" value="<?php echo $guru['nama']; ?>">
            <label class="form-label">NIP</label>
            <input type="number" class="form-control w-50" name="nip" value="<?php echo $guru['nip']; ?>" maxlength="10">
            <label class="form-label">No HP</label>
            <input type="number" class="form-control w-50" name="hp" value="<?php echo $guru['hp']; ?>">
            <label class="form-label">Alamat</label>
            <input type="text" class="form-control w-50" name="alamat" value="<?php echo $guru['alamat']; ?>">
            <label class="form-label">Gambar</label> <br>
            <img src="img/<?php echo $guru['gambar']; ?>" width="50" id="previewImg" class="mb-2"> <br>
            <input type="file" class="form-control w-50" id="gambar" name="gambar" onchange="previewImage(event)">
            <input type="hidden" name="gambarLama" value="<?php echo $guru['gambar']; ?>">
            <button type="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
            <p style="color: red; margin-top: 10px;" id="warning"></p>
        </div>
    </form>

    <a href="guru.php" class="m-4">Kembali</a>

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