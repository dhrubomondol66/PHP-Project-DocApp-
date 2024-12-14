<?php 

$host = 'localhost';
$dbname = 'docapp'; // Ensure this database exists
$dbusername = 'root'; // Default XAMPP username
$dbpassword = ''; // Default XAMPP password (usually empty)

$mysqli = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>
