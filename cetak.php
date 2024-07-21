<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once 'dbController.php';

$siswa = query("SELECT * FROM siswa");

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
    <title>Daftar Siswa</title>
    <link rel="stylesheet" href="./css/print.css">
</head>
<body>
   <h1>Daftar Siswa</h1>
   <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No.</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Nisn</th>
            <th>Kelas</th>
            <th>Jurusan</th>
        </tr>';

$i = 1;
foreach ($siswa as $row) {
  $html .= '<tr>
                <td>' . $i++ . '</td>
                <td><img src="img/' . htmlspecialchars($row["gambar"], ENT_QUOTES, 'UTF-8') . '" width="50"></td>
                <td>' . htmlspecialchars($row["nisn"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["nama_siswa"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["kelas"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["nama_jurusan"], ENT_QUOTES, 'UTF-8') . '</td>
            </tr>';
}

$html .= '</table>    
</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('daftar-siswa.pdf', 'I');
