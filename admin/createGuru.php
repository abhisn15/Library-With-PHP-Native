<?php
require '../dbController.php';

// mengecek tombol submit sudah ditekan atau belum
if (isset($_POST["submit"])) {
    if (tambahGuru($_POST) > 0) {
        echo "
        <script>
            alert('data berhasil ditambahkan!');
            document.location.href = 'guru.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('data gagal ditambahkan!');
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
    <title>Tambah Guru Baru</title>
</head>

<body>
    <h2 class="m-4">Menambahkan Guru</h2>
    <form method="POST" class="ms-3" id="Tambah" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nama Guru</label>
            <input type="text" class="form-control w-50" id="nama" aria-describedby="textHelp" name="nama">
            <label class="form-label">NIP</label>
            <input type="number" class="form-control w-50" id="nip" aria-describedby="textHelp" name="nip" oninput="maximumInputNisn(this)" maxlength="10">
            <label class="form-label">No HP</label>
            <input type="number" class="form-control w-50" id="hp" aria-describedby="textHelp" name="hp">
            <label class="form-label">Alamat</label>
            <input type="text" class="form-control w-50" id="alamat" aria-describedby="textHelp" name="alamat">
            <br>
            <label class="form-label">Gambar</label>
            <img id="previewImg" src="#" alt="Preview Gambar" width="50" style="display: none;"> <br>
            <input type="file" class="form-control w-50" id="gambar" aria-describedby="textHelp" name="gambar" onchange="previewImage(event)">
            <button type="submit" class="btn btn-primary mt-3" name="submit">Submit</button>
            <p style="color: red; margin-top: 10px;" id="warning"></p>
        </div>
    </form>

    <script>
        function maximumInputNisn(input) {
            var sanitizedInput = input.value.replace(/\D/g, '');
            var maxLength = 10;
            if (sanitizedInput.length > maxLength) {
                sanitizedInput = sanitizedInput.substring(0, maxLength);
            }
            input.value = sanitizedInput;
        }

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
            var nama = document.getElementById("nama").value.trim();
            var nip = document.getElementById("nip").value.trim();
            var hp = document.getElementById("hp").value.trim();
            var alamat = document.getElementById("alamat").value.trim();

            if (nama === "" || nip === "" || hp === "" || alamat === "") {
                event.preventDefault();
                document.getElementById("warning").innerHTML = "Mohon isi semua kolom!";
            } else if (nip.length !== 10) {
                event.preventDefault();
                document.getElementById("warning").innerHTML = "NIP harus terdiri dari 10 angka!";
            } else if (isNaN(nip)) {
                event.preventDefault();
                document.getElementById("warning").innerHTML = "NIP harus berupa angka!";
            }
        });
    </script>
</body>

</html>