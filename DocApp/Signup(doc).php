<?php
session_start();

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Include DB connection
    $mysqli = require __DIR__ . "/db.php";

    if (!$mysqli) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Sanitize user input
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Prepare SQL query to fetch the user
    $stmt = $mysqli->prepare("SELECT * FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();  // Fetch user data

        if ($user && password_verify($password, $user["password"])) {
            // Regenerate session and store user data
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["specialist"] = $user["specialist"];  // Add specialist to session
            $_SESSION["address"] = $user["address"];  // Add address to session

            // Redirect after successful login
            header("Location: index3.php");
            exit;
        } else {
            $is_invalid = true;
        }
    } else {
        die("Query error: " . $stmt->error);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login and Signup Form</title>
  <style>
    @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    html, body {
      display: grid;
      height: 100%;
      width: 100%;
      place-items: center;
      background: -webkit-linear-gradient(left, #003366, #004080, #0059b3, #0073e6);
    }
    ::selection {
      background: #1a75ff;
      color: #fff;
    }
    .wrapper {
      overflow: hidden;
      max-width: 390px;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 15px 20px rgba(0,0,0,0.1);
    }
    .wrapper .title-text {
      display: flex;
      width: 200%;
    }
    .wrapper .title {
      width: 50%;
      font-size: 35px;
      font-weight: 600;
      text-align: center;
      transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
    }
    .wrapper .slide-controls {
      position: relative;
      display: flex;
      height: 50px;
      width: 100%;
      overflow: hidden;
      margin: 30px 0 10px 0;
      justify-content: space-between;
      border: 1px solid lightgrey;
      border-radius: 15px;
    }
    .slide-controls .slide {
      height: 100%;
      width: 100%;
      color: #fff;
      font-size: 18px;
      font-weight: 500;
      text-align: center;
      line-height: 48px;
      cursor: pointer;
      z-index: 1;
      transition: all 0.6s ease;
    }
    .slide-controls label.signup {
      color: #000;
    }
    .slide-controls .slider-tab {
      position: absolute;
      height: 100%;
      width: 50%;
      left: 0;
      z-index: 0;
      border-radius: 15px;
      background: -webkit-linear-gradient(left,#003366,#004080,#0059b3, #0073e6);
      transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
    }
    input[type="radio"] {
      display: none;
    }
    #signup:checked ~ .slider-tab {
      left: 50%;
    }
    #signup:checked ~ label.signup {
      color: #fff;
      cursor: default;
      user-select: none;
    }
    #signup:checked ~ label.login {
      color: #000;
    }
    #login:checked ~ label.signup {
      color: #000;
    }
    #login:checked ~ label.login {
      cursor: default;
      user-select: none;
    }
    .wrapper .form-container {
      width: 100%;
      overflow: hidden;
    }
    .form-container .form-inner {
      display: flex;
      width: 200%;
    }
    .form-container .form-inner form {
      width: 50%;
      transition: all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55);
    }
    .form-inner form .field {
      height: 50px;
      width: 100%;
      margin-top: 20px;
    }
    .form-inner form .field input {
      height: 100%;
      width: 100%;
      outline: none;
      padding-left: 15px;
      border-radius: 15px;
      border: 1px solid lightgrey;
      border-bottom-width: 2px;
      font-size: 17px;
      transition: all 0.3s ease;
    }
    .form-inner form .field input:focus {
      border-color: #1a75ff;
    }
    .form-inner form .field input::placeholder {
      color: #999;
      transition: all 0.3s ease;
    }
    form .field input:focus::placeholder {
      color: #1a75ff;
    }
    .form-inner form .pass-link {
      margin-top: 5px;
    }
    .form-inner form .signup-link {
      text-align: center;
      margin-top: 30px;
    }
    .form-inner form .pass-link a,
    .form-inner form .signup-link a {
      color: #1a75ff;
      text-decoration: none;
    }
    .form-inner form .pass-link a:hover,
    .form-inner form .signup-link a:hover {
      text-decoration: underline;
    }
    form .btn {
      height: 50px;
      width: 100%;
      border-radius: 15px;
      position: relative;
      overflow: hidden;
    }
    form .btn .btn-layer {
      height: 100%;
      width: 300%;
      position: absolute;
      left: -100%;
      background: -webkit-linear-gradient(right,#003366,#004080,#0059b3, #0073e6);
      border-radius: 15px;
      transition: all 0.4s ease;
    }
    form .btn:hover .btn-layer {
      left: 0;
    }
    form .btn input[type="submit"] {
      height: 100%;
      width: 100%;
      z-index: 1;
      position: relative;
      background: none;
      border: none;
      color: #fff;
      padding-left: 0;
      border-radius: 15px;
      font-size: 20px;
      font-weight: 500;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="wrapper">
    <div class="title-text">
      <div class="title login">Login Form</div>
      <div class="title signup">Signup Form</div>
    </div>
    <?php if ($is_invalid): ?>
        <div style="color: red; text-align: center; margin-bottom: 10px;">
            Invalid email or password
        </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
            <div style="color: red; text-align: center; margin-bottom: 10px;">
                <?php 
                    if ($_GET['error'] === 'email_taken') {
                        echo "Email is already taken";
                    } elseif ($_GET['error'] === 'query_failed') {
                        echo "An error occurred while processing your request. Please try again.";
                    }
                ?>
            </div>
        <?php endif; ?>
    <div class="form-container">
      <div class="slide-controls">
        <input type="radio" name="slide" id="login" checked>
        <input type="radio" name="slide" id="signup">
        <label for="login" class="slide login">Login</label>
        <label for="signup" class="slide signup">Signup</label>
        <div class="slider-tab"></div>
      </div>
      
      <div class="form-inner">
        <form action="Signup(doc).php" method="post" class="login">
            <div class="field">
                <input type="text" placeholder="Email Address" required name="email">
            </div>
            <div class="field">
                <input type="password" placeholder="Password" required name="password">
            </div>
            <div class="pass-link"><a href="#">Forgot password?</a></div>
            <div class="field btn">
            <div class="btn-layer"></div>
                <input type="submit" value="Login">
            </div>
            <div class="signup-link">Not a member? <a href="">Signup now</a></div>
        </form>
        
        <form action="Signup(doc)proccess.php" method="post" class="signup">
            <div class="field">
                <input type="text" placeholder="Username" required name="username">
            </div>
            <div class="field">
                <input type="text" placeholder="Phone" required name="phone">
            </div>
            <div class="field">
                <input type="text" placeholder="Email Address" required name="email">
            </div>
            <div class="field">
                <input type="password" placeholder="Password" required name="password">
            </div>
            <div class="field">
                <input type="text" placeholder="Specialist", required name="specialist">
            </div>
            <div class="field">
                <input type="text" placeholder="Address" required name="address">
            </div>
            <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" value="Signup">
            </div>
        </form>

      </div>
    </div>
  </div>

  <script>
    const loginText = document.querySelector(".title-text .login");
    const loginForm = document.querySelector("form.login");
    const loginBtn = document.querySelector("label.login");
    const signupBtn = document.querySelector("label.signup");
    const signupLink = document.querySelector("form .signup-link a");

    signupBtn.onclick = () => {
      loginForm.style.marginLeft = "-50%";
      loginText.style.marginLeft = "-50%";
    };
    loginBtn.onclick = () => {
      loginForm.style.marginLeft = "0%";
      loginText.style.marginLeft = "0%";
    };
    signupLink.onclick = () => {
      signupBtn.click();
      return false;
    };
  </script>

</body>
</html>
