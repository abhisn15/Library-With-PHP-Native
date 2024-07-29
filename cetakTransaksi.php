<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once 'dbController.php';

if (isset($_POST["cariListTransaksi"])) {
  $keyword = $_POST["keyword"];
  if (trim($keyword) == "") {
    // Jika keyword kosong, ambil semua data dengan pagination
    $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku, buku.gambar AS cover, peminjam.username AS nama_peminjam, admin.username AS nama_admin 
                          FROM transaksi 
                          INNER JOIN buku ON transaksi.id_buku = buku.id 
                          INNER JOIN users AS peminjam ON transaksi.id_peminjam = peminjam.id 
                          INNER JOIN users AS admin ON transaksi.id_admin = admin.id ");
    $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cari($keyword);
    $total = count($transaksi);
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $transaksi = query("SELECT transaksi.*, buku.judul AS judul_buku, buku.gambar AS cover, peminjam.username AS nama_peminjam, admin.username AS nama_admin 
                        FROM transaksi 
                        INNER JOIN buku ON transaksi.id_buku = buku.id 
                        INNER JOIN users AS peminjam ON transaksi.id_peminjam = peminjam.id 
                        INNER JOIN users AS admin ON transaksi.id_admin = admin.id ");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
}

// Specify the temporary directory
$temporaryDir = __DIR__ . '/mpdf_tmp';

// Check if the temporary directory exists and is writable
if (!is_dir($temporaryDir) || !is_writable($temporaryDir)) {
  die('Temporary directory is not writable.');
}

// Configure mPDF to use the temporary directory
$mpdfConfig = [
  'tempDir' => $temporaryDir
];

$mpdf = new \Mpdf\Mpdf($mpdfConfig);

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="./css/print.css">
</head>
<body>
   <h1>Daftar Buku</h1>
  <table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
      <tr style="text-align: center;">
        <th>No.</th>
        <th>Nama Peminjam</th>
        <th>Buku</th>
        <th>Tanggal Peminjaman</th>
        <th>Masa Pinjam</th>
        <th>Tanggal Pengembalian</th>
        <th>Pemberi</th>
      </tr>
      ';

$i = 1;
foreach ($transaksi as $row) {
  $html .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . htmlspecialchars($row["nama_peminjam"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["judul_buku"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["tanggal_pinjam"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["masa_pinjam"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["tanggal_pengembalian"], ENT_QUOTES, 'UTF-8') ?? 'Belum dikembalikan!' . '</td>
                <td>' . htmlspecialchars($row["nama_admin"], ENT_QUOTES, 'UTF-8') . '</td>
            </tr>';
}
$html .= '</table>    
</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('daftar-buku.pdf', 'I');
