<?php
// register.php
session_start();
include("database/connection.php");

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: admin.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    
    $errors = [];
    
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (empty($role)) $errors[] = "Please select a role";
    
    if (empty($errors)) {
        $tables = ['admin', 'customer', 'driver', 'vehicle_owner'];
        $email_exists = false;
        
        foreach ($tables as $table) {
            $check = mysqli_query($conn, "SELECT email FROM $table WHERE email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $email_exists = true;
                break;
            }
        }
        
        if ($email_exists) {
            $errors[] = "Email already registered!";
        }
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $table = "";
        switch($role) {
            case 'Admin':
                $table = 'admin';
                $sql = "INSERT INTO $table (full_name, username, email, phone, password, role, status) 
                        VALUES ('$full_name', '$username', '$email', '$phone', '$hashed_password', 'Admin', 'Active')";
                break;
            case 'Customer':
                $table = 'customer';
                $sql = "INSERT INTO $table (full_name, username, email, phone_1, password, status) 
                        VALUES ('$full_name', '$username', '$email', '$phone', '$hashed_password', 'Active')";
                break;
            case 'Driver':
                $table = 'driver';
                $sql = "INSERT INTO $table (full_name, username, email, phone, password, status) 
                        VALUES ('$full_name', '$username', '$email', '$phone', '$hashed_password', 'Pending')";
                break;
            case 'Vehicle Owner':
                $table = 'vehicle_owner';
                $sql = "INSERT INTO $table (full_name, username, email, phone, password, status) 
                        VALUES ('$full_name', '$username', '$email', '$phone', '$hashed_password', 'Pending')";
                break;
            default:
                $error = "Please select a valid role!";
                break;
        }
        
        if (empty($error) && isset($sql)) {
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RideRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>RideRent</h3>
                    <p class="mb-0">Create your account</p>
                </div>
                <div class="card-body">
                    <?php if($error) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>
                    <?php if($success) { ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php } ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Choose username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Create password" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Register As</label>
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="Customer">Customer</option>
                                <option value="Vehicle Owner">Vehicle Owner</option>
                                <option value="Driver">Driver</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>