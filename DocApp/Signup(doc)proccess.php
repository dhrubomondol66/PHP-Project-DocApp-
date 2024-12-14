<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(empty($_POST["username"])){
    die("Username is required");
}

if(empty($_POST["password"])){
    die("Password is required");
}

if(empty($_POST["phone"])){
    die("Phone number is required");
}

if(empty($_POST["specialist"])){
    die("Mention your specialization");
}

if(empty($_POST["address"])){
    die("Mention your Address");
}

if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    die("Valid email is required");
}

if(strlen($_POST["password"])<8){
    die("Password must be at least 8 charachters");
}

if(!preg_match("/[a-z]/i", $_POST["password"])){
    die("Password must contein at least one letter");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mtsql = require __DIR__ . "/db.php";

$sql = "INSERT INTO doctors (username, phone, email, password, specialist, address) VALUES(?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if(!$stmt->prepare($sql)){
    die("SQL error:" . $mysqli->error);
}

$stmt->bind_param("ssssss", $_POST["username"], $_POST["phone"], $_POST["email"], $password_hash, $_POST["specialist"], $_POST["address"]);

if($stmt->execute()){
    header("Location: index3.php");
    exit;
}else{
    if($mysqli->errno === 1062){
        header("Location: Signup(doc).php?error=email_taken");
        exit;
    }
    header("Location: Signup(doc).php?error=query_failed");
    exit;
}