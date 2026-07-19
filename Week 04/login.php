<?php
session_start();
include("database/connection.php");

if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // ============================
    // Admin Login
    // ============================
    if($role == 'Admin'){
        $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $_SESSION['admin'] = $email;
            $_SESSION['role'] = 'admin';
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid Email or Password for Admin!";
        }
    }

    // ============================
    // Vehicle Owner Login
    // ============================
    elseif($role == 'Vehicle Owner'){
        $sql = "SELECT * FROM vehicle_owner WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['owner_id'] = $row['owner_id'];
            $_SESSION['owner_name'] = $row['full_name'];
            $_SESSION['role'] = 'owner';
            header("Location: owner_dashboard.php");
            exit();
        } else {
            $error = "Invalid Email or Password for Vehicle Owner!";
        }
    }

    // ============================
    // Driver Login
    // ============================
    elseif($role == 'Driver'){
        $sql = "SELECT * FROM driver WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['driver_id'] = $row['driver_id'];
            $_SESSION['driver_name'] = $row['full_name'];
            $_SESSION['role'] = 'driver';
            header("Location: driver_dashboard.php");
            exit();
        } else {
            $error = "Invalid Email or Password for Driver!";
        }
    }

    // ============================
    // Customer Login
    // ============================
    elseif($role == 'Customer'){
        $sql = "SELECT * FROM customer WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['customer_id'] = $row['customer_id'];
            $_SESSION['customer_name'] = $row['full_name'];
            $_SESSION['role'] = 'customer';
            header("Location: customer_dashboard.php");
            exit();
        } else {
            $error = "Invalid Email or Password for Customer!";
        }
    }

    // ============================
    // If Role Not Selected
    // ============================
    else {
        $error = "Please select a valid role!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0d6efd, #4e9cff);
        }
        .login-container {
            background: white;
            width: 400px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .login-container h1 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .login-container p {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #0d6efd;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #0b5ed7;
        }
        .alert {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .links {
            text-align: center;
            margin-top: 15px;
        }
        .links a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>RideRent</h1>
    <p>Vehicle Rental Management System</p>

    <?php if(isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="form-group">
            <label>Login As</label>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Vehicle Owner">Vehicle Owner</option>
                <option value="Driver">Driver</option>
                <option value="Customer">Customer</option>
            </select>
        </div>

        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="links">
        <a href="#">Forgot Password?</a>
        <p>Don't have an account? <a href="#">Register</a></p>
    </div>
</div>

</body>
</html>