    <?php
    require '../dbController.php';

    $id = $_GET["id"];

    if (hapusBuku($id) > 0) {
      echo "
    <script>
        alert('data berhasil dihapus!');
        document.location.href = 'buku.php';
    </script>

    ";
    } else {
      echo "
    <script>
        alert('data gagal dihapus!');
        document.location.href = 'buku.php';
    </script>

    ";
    }
