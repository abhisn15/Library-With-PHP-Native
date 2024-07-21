<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once 'dbController.php';

$guru = query("SELECT * FROM guru");

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
    <title>Daftar Guru</title>
    <link rel="stylesheet" href="./css/print.css">
</head>
<body>
   <h1>Daftar Guru</h1>
   <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No.</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>No HP</th>
            <th>Alamat</th>
        </tr>';

$i = 1;
foreach ($guru as $row) {
  $html .= '<tr>
                <td>' . $i++ . '</td>
                <td><img src="img/' . htmlspecialchars($row["gambar"], ENT_QUOTES, 'UTF-8') . '" width="50"></td>
                <td>' . htmlspecialchars($row["nama"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["nip"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["hp"], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row["alamat"], ENT_QUOTES, 'UTF-8') . '</td>
            </tr>';
}

$html .= '</table>    
</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('daftar-guru.pdf', 'I');
