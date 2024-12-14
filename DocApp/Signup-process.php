<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(empty($_POST["username"])){
    die("Username is required");
}

if(empty($_POST["pwd"])){
    die("Password is required");
}

if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    die("Valid email is required");
}

if(strlen($_POST["pwd"])<8){
    die("Password must be at least 8 charachters");
}

if(!preg_match("/[a-z]/i", $_POST["pwd"])){
    die("Password must contein at least one letter");
}

$password_hash = password_hash($_POST["pwd"], PASSWORD_DEFAULT);

$mtsql = require __DIR__ . "/db.php";

$sql = "INSERT INTO users (username, email, pwd) VALUES(?, ?, ?)";

$stmt = $mysqli->stmt_init();

if(!$stmt->prepare($sql)){
    die("SQL error:" . $mysqli->error);
}

$stmt->bind_param("sss", $_POST["username"], $_POST["email"], $password_hash);

if($stmt->execute()){
    header("Location: index4.php");
    exit;
}else{
    if ($mysqli->errno === 1062) {
        // Redirect back to signup.php with an error message
        header("Location: Sign_up.php?error=email_taken");
        exit;
    }
    // Handle other errors
    header("Location: Sign_up.php?error=query_failed");
    exit;
}