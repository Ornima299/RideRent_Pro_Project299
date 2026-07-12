<?php
session_start();

include "database/connection.php";

if(isset($_POST['email']) && isset($_POST['password'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $_SESSION['admin'] = $email;

       header("Location: /RideRent_Pro299/admin.php");
exit();

    } else {

        echo "Invalid Email or Password";

    }

}
?>