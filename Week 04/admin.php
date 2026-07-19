<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}
include("database/connection.php");

// Data Count with Error Handling
$totalUsers = 0;
$userQuery = mysqli_query($conn, "SELECT * FROM admin");
if ($userQuery) { $totalUsers = mysqli_num_rows($userQuery); }

$totalVehicles = 0;
$vehicleQuery = mysqli_query($conn, "SELECT * FROM vehicle");
if ($vehicleQuery) { $totalVehicles = mysqli_num_rows($vehicleQuery); }

$totalDrivers = 0;
$driverQuery = mysqli_query($conn, "SELECT * FROM driver");
if ($driverQuery) { $totalDrivers = mysqli_num_rows($driverQuery); }

$totalBookings = 0;
$bookingQuery = mysqli_query($conn, "SELECT * FROM bookings");
if ($bookingQuery) { $totalBookings = mysqli_num_rows($bookingQuery); }

// Reviews & Ratings (ডামি ডেটা)
$totalReviews = 24;
$totalRatings = 4.5;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RideRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f4f6f9; }
        .sidebar { position: fixed; width: 260px; height: 100%; background: #0d6efd; color: white; padding-top: 20px; transition: 0.3s; z-index: 1000; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-weight: 700; letter-spacing: 1px; }
        .sidebar h2 i { margin-right: 10px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 12px 25px; transition: 0.3s; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar ul li:hover { background: #0b5ed7; padding-left: 30px; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; font-size: 15px; }
        .sidebar ul li a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar ul li.active { background: #0b5ed7; border-left: 4px solid #ffc107; }
        .sidebar .logout-btn { margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 15px; }
        .main { margin-left: 260px; padding: 20px; }
        .header { background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .header h1 { font-size: 28px; font-weight: 700; color: #2d3748; }
        .header p { color: #718096; margin: 0; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 25px; }
        .card { background: white; padding: 25px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; border: none; transition: transform 0.2s; cursor: pointer; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card h3 { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #a0aec0; margin-bottom: 10px; }
        .card p { font-size: 36px; font-weight: 700; color: #0d6efd; margin: 0; }
        .card .text-success { color: #48bb78 !important; }
        .card .text-warning { color: #ecc94b !important; }
        .card .text-danger { color: #fc8181 !important; }
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 30px; }
        .table-wrapper h4 { font-weight: 600; color: #2d3748; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f7fafc; color: #4a5568; font-weight: 600; padding: 12px 15px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #48bb78; color: white; }
        .badge-danger { background: #fc8181; color: white; }
        .badge-warning { background: #ecc94b; color: #2d3748; }
        .badge-info { background: #63b3ed; color: white; }
        .footer { margin-top: 50px; text-align: center; color: #a0aec0; font-size: 14px; padding: 20px; border-top: 1px solid #e2e8f0; }
        @media (max-width: 768px) { .sidebar { width: 200px; } .main { margin-left: 200px; } }
        @media (max-width: 576px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } .cards { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fas fa-car"></i> RideRent</h2>
    <ul>
        <li class="active"><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="vehicle_list.php"><i class="fas fa-car"></i> Vehicles</a></li>
        <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
        <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
        <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li class="logout-btn"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <p>Welcome back, Admin!</p>
    </div>

    <!-- Cards -->
    <div class="cards">
        <div class="card"><h3><i class="fas fa-users"></i> Total Users</h3><p><?php echo $totalUsers; ?></p></div>
        <div class="card"><h3><i class="fas fa-car"></i> Total Vehicles</h3><p><?php echo $totalVehicles; ?></p></div>
        <div class="card"><h3><i class="fas fa-id-card"></i> Total Drivers</h3><p><?php echo $totalDrivers; ?></p></div>
        <div class="card"><h3><i class="fas fa-calendar-check"></i> Total Bookings</h3><p><?php echo $totalBookings; ?></p></div>
        <div class="card"><h3><i class="fas fa-star"></i> Total Reviews</h3><p><?php echo $totalReviews; ?></p></div>
        <div class="card"><h3><i class="fas fa-star-half-alt"></i> Avg Rating</h3><p><?php echo $totalRatings; ?> ⭐</p></div>
    </div>

    <!-- Recent Bookings -->
    <div class="table-wrapper">
        <h4><i class="fas fa-clock"></i> Recent Bookings</h4>
        <table>
            <thead><tr><th>#Booking ID</th><th>Customer</th><th>Vehicle</th><th>Status</th></tr></thead>
            <tbody>
                <?php
                $bookingQuery = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, v.vehicle_name 
                    FROM bookings b 
                    LEFT JOIN customer c ON b.customer_id = c.customer_id 
                    LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                    ORDER BY b.booking_id DESC LIMIT 5");
                if ($bookingQuery && mysqli_num_rows($bookingQuery) > 0) {
                    while ($row = mysqli_fetch_assoc($bookingQuery)) {
                        $statusClass = '';
                        if ($row['booking_status'] == 'Confirmed') $statusClass = 'badge-success';
                        elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                        elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                        else $statusClass = 'badge-danger';
                        echo "<tr><td>#{$row['booking_id']}</td><td>{$row['customer_name']}</td><td>{$row['vehicle_name']}</td>
                              <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No recent bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        &copy; 2026 RideRent Pro. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>