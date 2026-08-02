<?php
// forgot_password.php
session_start();
include("database/connection.php");

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    
    if (!empty($email) && !empty($role)) {
        $table = "";
        switch($role) {
            case 'Admin': $table = 'admin'; break;
            case 'Customer': $table = 'customer'; break;
            case 'Driver': $table = 'driver'; break;
            case 'Vehicle Owner': $table = 'vehicle_owner'; break;
            default: $table = '';
        }
        
        if (!empty($table)) {
            $check = mysqli_query($conn, "SELECT * FROM $table WHERE email='$email'");
            
            if (mysqli_num_rows($check) > 0) {
                $message = "Password reset link has been sent to your email.";
                $message_type = "success";
            } else {
                $message = "No account found with this email and role!";
                $message_type = "danger";
            }
        } else {
            $message = "Please select a valid role!";
            $message_type = "danger";
        }
    } else {
        $message = "Please fill all fields!";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - RideRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>RideRent</h3>
                    <p class="mb-0">Reset your password</p>
                </div>
                <div class="card-body">
                    <?php if($message) { ?>
                        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                    <?php } ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label>Account Type</label>
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="Customer">Customer</option>
                                <option value="Driver">Driver</option>
                                <option value="Vehicle Owner">Vehicle Owner</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                    <div class="text-center mt-3">
                        <p><a href="login.php">Back to Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>