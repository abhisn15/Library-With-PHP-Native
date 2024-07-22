<?php
require '../../dbController.php';

$keyword = $_GET['keyword'];

$query = "SELECT * FROM guru
                WHERE 
            nama LIKE '%$keyword%' OR
            nip LIKE '%$keyword%'
    ";
$guru = query($query);
?>
<table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
  <tr style="text-align: center;">
    <th>No.</th>
    <th>Aksi</th>
    <th>Gambar</th>
    <th>Nama</th>
    <th>Nip</th>
    <th>No Hp</th>
    <th>Alamat</th>
  </tr>
  <?php $i = $start + 1; ?>
  <?php foreach ($guru as $row) : ?>
    <tr style="text-align: center;">
      <td class="center-align"><?= $i; ?></td>
      <td class="center-align">
        <a href="edit.php?id=<?= $row["id"]; ?>">ubah</a>
        <a href="delete.php?id=<?= $row["id"]; ?>" onclick="return confirm('serius?');" style="color: red;">hapus</a>
      </td>
      <td class="center-align"><img src="../img/<?= $row["gambar"]; ?>" width="100"></td>
      <td class="center-align"><?= $row["nama"]; ?></td>
      <td class="center-align"><?= $row["nip"]; ?></td>
      <td class="center-align"><?= $row["hp"]; ?></td>
      <td class="center-align"><?= $row["alamat"]; ?></td>
    </tr>
    <?php $i++; ?>
  <?php endforeach; ?>
</table>