<?php
// driver.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'driver') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { position: fixed; width: 260px; height: 100%; background: #0d6efd; color: white; padding-top: 20px; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-weight: 700; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 12px 25px; transition: 0.3s; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar ul li:hover { background: #0b5ed7; padding-left: 30px; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; }
        .sidebar ul li a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar ul li.active { background: #0b5ed7; border-left: 4px solid #ffc107; }
        .main { margin-left: 260px; padding: 20px; }
        .header { background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .header h1 { font-size: 28px; font-weight: 700; color: #2d3748; }
        .header p { color: #718096; margin: 0; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: 0.3s; }
        .footer { margin-top: 50px; text-align: center; color: #a0aec0; padding: 20px; border-top: 1px solid #e2e8f0; }
        @media (max-width: 768px) { .sidebar { width: 200px; } .main { margin-left: 200px; } }
        @media (max-width: 576px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-car"></i> RideRent</h2>
    <ul>
        <li class="active"><a href="driver.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#"><i class="fas fa-calendar-check"></i> My Bookings</a></li>
        <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> Driver Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION['user_name']; ?>!</p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <h3><i class="fas fa-calendar-check text-primary"></i></h3>
                <h2>0</h2>
                <p>Total Bookings</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <h3><i class="fas fa-star text-warning"></i></h3>
                <h2>0.0</h2>
                <p>Average Rating</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <h3><i class="fas fa-clock text-success"></i></h3>
                <h2>Available</h2>
                <p>Status</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-user"></i> Profile Information</h4>
            <hr>
            <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
            <p><strong>User ID:</strong> #<?php echo $_SESSION['user_id']; ?></p>
            <p><strong>Role:</strong> Driver</p>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 RideRent Pro. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>