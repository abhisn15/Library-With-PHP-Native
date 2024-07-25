<?php
require '../../dbController.php';

$keyword = $_GET['keyword'];

$query =
  $query = "SELECT transaksi.*, buku.judul AS judul_buku, buku.gambar AS cover, peminjam.username AS nama_peminjam, admin.username AS nama_admin
          FROM transaksi
          INNER JOIN buku ON transaksi.id_buku = buku.id
          INNER JOIN users AS peminjam ON transaksi.id_peminjam = peminjam.id
          INNER JOIN users AS admin ON transaksi.id_admin = admin.id
          WHERE peminjam.username LIKE '%$keyword%' 
          ";
$transaksi = query($query);
?>
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
  <?php $i = $start + 1; ?>
  <?php foreach ($transaksi as $row) : ?>
    <tr style="text-align: center;">
      <td class="center-align"><?= $i; ?></td>
      <td class="center-align"><?= $row["nama_peminjam"]; ?></td>
      <td class="center-align d-flex flex-column justify-center align-items-center gap-3">
        <img src="../img/<?= $row["cover"] ?>" alt="cover" width="100">
        <?= $row["judul_buku"]; ?>
      </td>
      <td class="center-align"><?= $row["tanggal_pinjam"]; ?></td>
      <td class="center-align"><?= $row["f_masa_pinjam"]; ?></td>
      <td class="center-align"><?= $row["tanggal_pengembalian"] ?? 'Belum Dikembalikan!'; ?></td>
      <td class="center-align"><?= $row["nama_admin"]; ?></td>
    </tr>
    <?php $i++; ?>
  <?php endforeach; ?>
</table>