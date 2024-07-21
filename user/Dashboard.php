<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || isset($_SESSION["Anggota"])) {
  header("Location: ../login.php");
  exit;
}

echo "Welcome to User Dashboard, " . $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Dashboard | Anggota</title>
</head>

<body>
  <a href="../logout.php">Logout</a>

</body>

</html>