<?php
$conn = mysqli_connect("localhost", "root", "", "rplsmkmy_abhi_sekolah");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function query($query, $params = [])
{
    global $conn;
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Query Error: " . mysqli_error($conn));
    }

    if ($params) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
        die("Execute Error: " . mysqli_stmt_error($stmt));
    }

    if (stripos($query, 'SELECT') === 0) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    } else {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected_rows;
    }
}

function upload()
{
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        echo "<script>
            alert('pilih gambar terlebih dahulu!');
            </script>";
        return false;
    }

    // cek apakah yang diupload adalah gambar
    $mime = getimagesize($tmpName);
    if ($mime === false && $_FILES['gambar']['type'] !== 'image/heic') {
        echo "<script>
            alert('file yang Anda upload bukan gambar!');
            </script>";
        var_dump($_FILES['gambar']); // Tambahkan var_dump untuk debugging
        return false;
    }

    // cek jika ukurannya terlalu besar
    if ($ukuranFile > 5000000) {
        echo "<script>
               alert('ukuran gambar terlalu besar!');
               </script>";
        return false;
    }

    // generate nama gambar baru
    $ekstensiGambar = pathinfo($namaFile, PATHINFO_EXTENSION);
    $namaFileBaru = uniqid() . '.' . $ekstensiGambar;

    // tentukan alamat penyimpanan file secara absolut
    $alamatSimpan = '../img/' . $namaFileBaru;

    if (move_uploaded_file($tmpName, $alamatSimpan)) {
        return $namaFileBaru; // mengembalikan nama file baru
    } else {
        echo "<script>
               alert('gagal mengunggah gambar!');
               </script>";
        error_log("Failed to move uploaded file. Check permissions or path. tmpName: $tmpName, alamatSimpan: $alamatSimpan");
        return false;
    }
}

function tambah($data)
{
    global $conn;
    $nama_siswa = htmlspecialchars($data["nama_siswa"]);
    $nisn = htmlspecialchars($data["nisn"]);
    $nama_jurusan = htmlspecialchars($data["nama_jurusan"]);
    $kelas = htmlspecialchars($data["kelas"]);
    $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan nama file

    if (!$gambar) {
        return false;
    }

    $query = "INSERT INTO siswa (nama_siswa, nisn, nama_jurusan, kelas, gambar) 
              VALUES ('$nama_siswa', '$nisn', '$nama_jurusan', '$kelas', '$gambar')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function tambahGuru($data)
{
    global $conn;
    $nip = htmlspecialchars($data["nip"]);
    $nama = htmlspecialchars($data["nama"]);
    $hp = htmlspecialchars($data["hp"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan nama file

    if (!$gambar) {
        return false;
    }

    $query = "INSERT INTO guru (nip, nama, hp, alamat, gambar) 
              VALUES ('$nip', '$nama', '$hp', '$alamat', '$gambar')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function tambahBuku($data)
{
    global $conn;
    $judul = htmlspecialchars($data["judul"]);
    $penerbit = htmlspecialchars($data["penerbit"]);
    $tahun_terbit = htmlspecialchars($data["tahun_terbit"]);
    $stok_buku = htmlspecialchars($data["stok_buku"]);
    $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan penerbit file

    if (!$gambar) {
        return false;
    }

    $query = "INSERT INTO buku (judul, penerbit, tahun_terbit, stok_buku, gambar) 
            VALUES ('$judul', '$penerbit', '$tahun_terbit', '$stok_buku', '$gambar')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function pinjam($data, $admin_id)
{
    global $conn;
    $id_buku = htmlspecialchars($data["id_buku"]);
    $tanggal_pinjam = htmlspecialchars($data["tanggal_pinjam"]);
    $id_peminjam = htmlspecialchars($data["id_peminjam"]);

    // Calculate the return date
    $masa_pinjam = date('Y-m-d', strtotime($tanggal_pinjam . ' +7 days'));

    $query = "INSERT INTO transaksi (id_buku, id_peminjam, tanggal_pinjam, masa_pinjam, tanggal_pengembalian, id_admin) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssss', $id_buku, $id_peminjam, $tanggal_pinjam, $masa_pinjam, $tanggal_pengembalian, $admin_id);

    if ($stmt->execute()) {
        return $stmt->affected_rows;
    } else {
        return -1;
    }
}


function hapus($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM siswa WHERE id = $id");

    return mysqli_affected_rows($conn);
}

function hapusGuru($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM guru WHERE id = $id");

    return mysqli_affected_rows($conn);
}

function hapusBuku($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM buku WHERE id = $id");

    return mysqli_affected_rows($conn);
}

function ubah($data)
{
    global $conn;

    $nama_siswa = htmlspecialchars($data["nama_siswa"]);
    $nisn = htmlspecialchars($data["nisn"]);
    $nama_jurusan = htmlspecialchars($data["nama_jurusan"]);
    $kelas = htmlspecialchars($data["kelas"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]); // Nama gambar lama dari form input hidden
    $id = intval($data["id"]); // Mendapatkan nilai id dari $data dan pastikan tipe datanya integer

    // Cek apakah user memilih gambar baru atau tidak
    if (!empty($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
        $gambar = upload();
        if ($gambar === false) {
            // Upload gagal, handle error atau return false
            return false;
        }
    } else {
        $gambar = $gambarLama;
    }

    // Menggunakan prepared statements untuk mencegah SQL injection
    $stmt = $conn->prepare("UPDATE siswa SET nama_siswa = ?, nisn = ?, nama_jurusan = ?, kelas = ?, gambar = ? WHERE id = ?");
    if (!$stmt) {
        return false;
    }
    // Menggunakan tipe data yang sesuai untuk bind_param
    $stmt->bind_param("sssssi", $nama_siswa, $nisn, $nama_jurusan, $kelas, $gambar, $id);

    $stmt->execute();
    $affected_rows = $stmt->affected_rows;

    $stmt->close();

    return $affected_rows;
}

function ubahGuru($data)
{
    global $conn;

    $nip = htmlspecialchars($data["nip"]);
    $nama = htmlspecialchars($data["nama"]);
    $hp = htmlspecialchars($data["hp"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]); // Nama gambar lama dari form input hidden
    $id = intval($data["id"]); // Mendapatkan nilai id dari $data dan pastikan tipe datanya integer

    // Cek apakah user memilih gambar baru atau tidak
    if (!empty($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
        $gambar = upload();
        if ($gambar === false) {
            // Upload gagal, handle error atau return false
            return false;
        }
    } else {
        $gambar = $gambarLama;
    }

    // Menggunakan prepared statements untuk mencegah SQL injection
    $stmt = $conn->prepare("UPDATE guru SET nip = ?, nama = ?, hp = ?, alamat = ?, gambar = ? WHERE id = ?");
    if (!$stmt) {
        return false;
    }
    // Menggunakan tipe data yang sesuai untuk bind_param
    $stmt->bind_param("sssssi", $nip, $nama, $hp, $alamat, $gambar, $id);

    $stmt->execute();
    $affected_rows = $stmt->affected_rows;

    $stmt->close();

    return $affected_rows;
}

function ubahBuku($data)
{
    global $conn;

    $judul = htmlspecialchars($data["judul"]);
    $penerbit = htmlspecialchars($data["penerbit"]);
    $tahun_terbit = htmlspecialchars($data["tahun_terbit"]);
    $stok_buku = htmlspecialchars($data["stok_buku"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]); // Nama gambar lama dari form input hidden
    $id = intval($data["id"]); // Mendapatkan nilai id dari $data dan pastikan tipe datanya integer

    // Cek apakah user memilih gambar baru atau tidak
    if (!empty($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
        $gambar = upload();
        if ($gambar === false) {
            // Upload gagal, handle error atau return false
            return false;
        }
    } else {
        $gambar = $gambarLama;
    }

    // Menggunakan prepared statements untuk mencegah SQL injection
    $stmt = $conn->prepare("UPDATE buku SET judul = ?, penerbit = ?, tahun_terbit = ?, stok_buku = ?, gambar = ? WHERE id = ?");
    if (!$stmt) {
        return false;
    }
    // Menggunakan tipe data yang sesuai untuk bind_param
    $stmt->bind_param("sssssi", $judul, $penerbit, $tahun_terbit, $stok_buku, $gambar, $id);

    $stmt->execute();
    $affected_rows = $stmt->affected_rows;

    $stmt->close();

    return $affected_rows;
}

function cari($keyword)
{
    $query = "SELECT * FROM siswa
                WHERE 
            nama_siswa LIKE '%$keyword%' OR
            kelas LIKE '%$keyword%' OR
            nama_jurusan LIKE '%$keyword%' 
            
    ";
    return query($query);
}

function cariGuru($keyword)
{
    $query = "SELECT * FROM guru
                WHERE 
            nama LIKE '%$keyword%'
    ";
    return query($query);
}

function cariBuku($keyword)
{
    $query = "SELECT * FROM buku
                WHERE 
            judul LIKE '%$keyword%' OR
            penerbit LIKE '%$keyword%'
    ";
    return query($query);
}

function cariTransaksi($keyword, $id_user)
{
    global $conn;
    $keyword = "%" . $keyword . "%"; // Tambahkan wildcard untuk pencarian LIKE
    $query = "SELECT transaksi.*, buku.judul AS judul_buku
                FROM transaksi
                INNER JOIN buku ON transaksi.id_buku = buku.id
                WHERE transaksi.id_peminjam = ? AND buku.judul LIKE ?";
    return query($query, [$id_user, $keyword]);
}

function cariHistoryPeminjaman($keyword, $id_user)
{
    $query = "SELECT transaksi.*, buku.judul AS judul_buku
              FROM transaksi 
              INNER JOIN buku ON transaksi.id_buku = buku.id
              WHERE transaksi.id_peminjam = ? AND buku.judul LIKE ?";

    // Tambahkan wildcard (%) ke keyword untuk pencarian LIKE
    $keyword = "%$keyword%";

    return query($query, [$id_user, $keyword]);
}

function input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function register($conn)
{
    $username = $password = $confirm_password = "";
    $username_err = $password_err = $confirm_password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validasi username
        if (empty(input($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            $username = input($_POST["username"]);
            // Cek jika user sudah digunakan di database
            $sql = "SELECT id FROM users WHERE username = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $username_err = "Nama ini sudah ada yang punya.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }

        // Validasi password
        if (empty(input($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(input($_POST["password"])) < 6) {
            $password_err = "Password harus berjumlah 6 karakter.";
        } else {
            $password = input($_POST["password"]);
        }

        // Validasi confirm password
        if (empty(input($_POST["confirm_password"]))) {
            $confirm_password_err = "Tolong konfirmasi password.";
        } else {
            $confirm_password = input($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Konfirmasi password belum sesuai.";
            }
        }

        if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $username, $param_password);
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

                if (mysqli_stmt_execute($stmt)) {
                    echo
                    "<script>
                        alert('User berhasil ditambahkan');
                        window.location.href = 'login.php';
                    </script>";
                } else {
                    echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    return [
        'username' => $username,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'username_err' => $username_err,
        'password_err' => $password_err,
        'confirm_password_err' => $confirm_password_err
    ];
}

function login($conn)
{
    session_start();

    $username = $password = "";
    $username_err = $password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST["username"]))) {
            $username_err = "Tolong isi username.";
        } else {
            $username = trim($_POST["username"]);
        }

        if (empty(trim($_POST["password"]))) {
            $password_err = "Tolong isi password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty($username_err) && empty($password_err)) {
            $sql = "SELECT id, username, password, f_role FROM users WHERE username = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $f_role);
                        if (mysqli_stmt_fetch($stmt)) {
                            if (password_verify($password, $hashed_password)) {
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["f_role"] = $f_role;
                                setcookie("username", $username, time() + (86400 * 2), "/"); // Cookie berlaku selama 2 hari

                                if ($f_role == 'Admin') {
                                    header("Location: ../library-php-native/admin/Dashboard.php");
                                } elseif ($f_role == 'Anggota') {
                                    header("Location: ../library-php-native/user/Dashboard.php");
                                } else {
                                    header("Location: ../index.php");
                                }
                                exit();
                            } else {
                                $password_err = "Password yang kamu isi tidak valid.";
                            }
                        }
                    } else {
                        $username_err = "Tidak menemukan akun dengan username $username.";
                    }
                } else {
                    echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    return [
        'username' => $username,
        'password' => $password,
        'username_err' => $username_err,
        'password_err' => $password_err
    ];
}