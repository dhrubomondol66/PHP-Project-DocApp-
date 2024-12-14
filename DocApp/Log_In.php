<?php 
$is_invalid = false;
session_start();  // Start session at the beginning
if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $mysqli = require __DIR__ . "/db.php";
  $sql = sprintf("SELECT * FROM users WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
  $result = $mysqli->query($sql);
  if (!$result) {
    die("Query error: " . $mysqli->error);
  }
  $user = $result->fetch_assoc();
  if ($user) {
    if (password_verify($_POST["password"], $user["pwd"])) {
      session_regenerate_id();
      $_SESSION["user_id"] = $user["id"];
      $_SESSION["username"] = $user["username"];
      header("Location: index2.php");
      exit;
    } else {
      $is_invalid = true;  // Set flag for invalid login
    }
  } else {
    $is_invalid = true;  // Also set flag if user not found
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <style>
    body {
      background-color: #3498db;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      background: -webkit-linear-gradient(left, #003366, #004080, #0059b3, #0073e6);
    }

    .login-container {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: center;
    }

    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      box-sizing: border-box;
    }

    button {
      background-color: #2980b9;
      color: #ffffff;
      padding: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #216ba5;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($is_invalid): ?>
      <em style="color: red;">Invalid email or password</em>
    <?php endif; ?>
    <form action="Log_in.php" method="POST">
      <input type="text" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
