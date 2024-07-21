<?php
require '../dbController.php';

$keyword = $_GET['keyword'];

$query = "SELECT * FROM buku
                WHERE 
            judul LIKE '%$keyword%' OR
            penerbit LIKE '%$keyword%'
    ";
$buku = query($query);
?>
<table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
  <tr style="text-align: center;">
    <th>No.</th>
    <th>Aksi</th>
    <th>Gambar</th>
    <th>Judul</th>
    <th>Penerbit</th>
    <th>Tanggal Terbit</th>
    <th>Jumlah Buku</th>
  </tr>
  <?php $i = $start + 1; ?>
  <?php foreach ($buku as $row) : ?>
    <tr style="text-align: center;">
      <td class="center-align"><?= $i; ?></td>
      <td class="center-align">
        <a href="edit.php?id=<?= $row["id"]; ?>">ubah</a>
        <a href="delete.php?id=<?= $row["id"]; ?>" onclick="return confirm('serius?');" style="color: red;">hapus</a>
      </td>
      <td class="center-align"><img src="../img/<?= $row["gambar"]; ?>" width="100"></td>
      <td class="center-align"><?= $row["judul"]; ?></td>
      <td class="center-align"><?= $row["penerbit"]; ?></td>
      <td class="center-align"><?= $row["tahun_terbit"]; ?></td>
      <td class="center-align"><?= $row["stok_buku"]; ?></td>
    </tr>
    <?php $i++; ?>
  <?php endforeach; ?>
</table>