    <?php
    require '../dbController.php';

    $id = $_GET["id"];
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION['Admin'])) {
        header("Location: ../login.php");
        exit;
    }


    if (hapus($id) > 0) {
        echo "
    <script>
        alert('data berhasil dihapus!');
        document.location.href = 'Dashboard.php';
    </script>

    ";
    } else {
        echo "
    <script>
        alert('data gagal dihapus!');
        document.location.href = 'Dashboard.php';
    </script>

    ";
    }
