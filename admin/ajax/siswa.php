<?php
require '../../dbController.php';

$keyword = $_GET['keyword'];

$query = "SELECT * FROM siswa
                WHERE 
            nama_siswa LIKE '%$keyword%' OR
            kelas LIKE '%$keyword%' OR
            nama_jurusan LIKE '%$keyword%' 
    ";
$siswa = query($query);
?>
<table border="1" cellpadding="10" cellspacing="0" class="table table-bordered">
  <tr style="text-align: center;">
    <th>No.</th>
    <th>Aksi</th>
    <th>Gambar</th>
    <th>Nama</th>
    <th>Nisn</th>
    <th>Kelas</th>
    <th>Jurusan</th>
  </tr>
  <?php $i = $start + 1; ?>
  <?php foreach ($siswa as $row) : ?>
    <tr style="text-align: center;">
      <td class="center-align"><?= $i; ?></td>
      <td class="center-align">
        <a href="edit.php?id=<?= $row["id"]; ?>">ubah</a>
        <a href="delete.php?id=<?= $row["id"]; ?>" onclick="return confirm('serius?');" style="color: red;">hapus</a>
      </td>
      <td class="center-align"><img src="../img/<?= $row["gambar"]; ?>" width="100"></td>
      <td class="center-align"><?= $row["nama_siswa"]; ?></td>
      <td class="center-align"><?= $row["nisn"]; ?></td>
      <td class="center-align"><?= $row["kelas"]; ?></td>
      <td class="center-align"><?= $row["nama_jurusan"]; ?></td>
    </tr>
    <?php $i++; ?>
  <?php endforeach; ?>
</table>