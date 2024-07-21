<?php
require "dbController.php";

$login_data = login($conn);
$username = $login_data['username'];
$password = $login_data['password'];
$username_err = $login_data['username_err'];
$password_err = $login_data['password_err'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Login</h2>
    <form action="login.php" method="post">
      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
        <span class="invalid-feedback"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="text-center mt-3">Belum punya akun? <a href="register.php">Register</a></p>
  </div>
</body>

</html>