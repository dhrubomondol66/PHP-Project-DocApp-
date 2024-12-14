<?php 
    $is_invalid = false;
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $mysqli = require __DIR__ . "/db.php";
        if(!$mysqli){
            die("Database connection failed: " . $mysqli->connect_error);
        }
        $email = $mysqli->real_escape_string($_POST["email"]);
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $mysqli->query($sql);
        if(!$result){
            die("Query error: " . $mysqli->error);
        }
        $user = $result->fetch_assoc();
        if($user){
            if(password_verify($_POST["password"], $user["pwd"])){
                session_start();
                session_regenerate_id();
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                header("Location: Signup-process.php");
                exit;
            }else{
                echo "user not found";
                $is_invalid = true;
            }
        }else{
            $is_invalid = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .signup-form h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .signup-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .signup-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .signup-form button {
            background-color: #3498db;
            color: #fff;
            padding: 12px;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .signup-form button:hover {
            background-color: #2980b9;
        }

        .signup-form button:focus {
            outline: none;
        }
    </style>
</head>
<body>
    <div class="container">
    
        <form class="signup-form" method="POST" action="Signup-process.php">
            <h1>Sign Up</h1>
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
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="pwd" required>
            
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
