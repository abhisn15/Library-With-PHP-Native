<?php
require "dbController.php";

$register_data = register($conn);
$username = $register_data['username'];
$password = $register_data['password'];
$confirm_password = $register_data['confirm_password'];
$username_err = $register_data['username_err'];
$password_err = $register_data['password_err'];
$confirm_password_err = $register_data['confirm_password_err'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Register</h2>
    <form action="register.php" method="post">
      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
        <span class="invalid-feedback"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($password); ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($confirm_password); ?>">
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
  </div>
</body>

</html>