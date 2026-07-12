<?php


$host = "localhost";
$user = "root";
$password = "";
$database = "riderent_pro299";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

echo "Database Connected Successfully!";

?>