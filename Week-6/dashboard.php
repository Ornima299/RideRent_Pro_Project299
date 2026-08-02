<?php
// dashboard.php - Reports
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("database/connection.php");

// Get all statistics
$totalUsers = 0;
$totalCustomers = 0;
$totalDrivers = 0;
$totalOwners = 0;
$totalAdmins = 0;

$adminCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM admin");
if ($adminCount) { $row = mysqli_fetch_assoc($adminCount); $totalAdmins = $row['total']; }

$customerCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer");
if ($customerCount) { $row = mysqli_fetch_assoc($customerCount); $totalCustomers = $row['total']; }

$driverCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM driver");
if ($driverCount) { $row = mysqli_fetch_assoc($driverCount); $totalDrivers = $row['total']; }

$ownerCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicle_owner");
if ($ownerCount) { $row = mysqli_fetch_assoc($ownerCount); $totalOwners = $row['total']; }

$totalUsers = $totalAdmins + $totalCustomers + $totalDrivers + $totalOwners;

$totalVehicles = 0;
$vehicleCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicle");
if ($vehicleCount) { $row = mysqli_fetch_assoc($vehicleCount); $totalVehicles = $row['total']; }

$totalBookings = 0;
$bookingCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking");
if ($bookingCount) { $row = mysqli_fetch_assoc($bookingCount); $totalBookings = $row['total']; }

$totalReviews = 0;
$reviewCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews");
if ($reviewCount) { $row = mysqli_fetch_assoc($reviewCount); $totalReviews = $row['total']; }

// Get revenue
$totalRevenue = 0;
$revenueQuery = mysqli_query($conn, "SELECT SUM(total_price) as total_revenue FROM booking WHERE payment_status='Paid'");
if ($revenueQuery) { $row = mysqli_fetch_assoc($revenueQuery); $totalRevenue = $row['total_revenue'] ?? 0; }

// Vehicle status
$availableVehicles = 0;
$bookedVehicles = 0;
$maintenanceVehicles = 0;

$availQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicle WHERE availability='Available'");
if ($availQuery) { $row = mysqli_fetch_assoc($availQuery); $availableVehicles = $row['total']; }

$bookedQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicle WHERE availability='Booked'");
if ($bookedQuery) { $row = mysqli_fetch_assoc($bookedQuery); $bookedVehicles = $row['total']; }

$mainQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicle WHERE availability='Maintenance'");
if ($mainQuery) { $row = mysqli_fetch_assoc($mainQuery); $maintenanceVehicles = $row['total']; }

// Monthly bookings
$monthlyResult = mysqli_query($conn, "SELECT 
                                      DATE_FORMAT(booking_date, '%Y-%m') as month,
                                      COUNT(*) as count,
                                      SUM(total_price) as revenue
                                      FROM booking 
                                      WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                      GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
                                      ORDER BY month ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - RideRent Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f4f6f9; }
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
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h3 { font-size: 28px; font-weight: 700; }
        .stat-card p { color: #666; margin: 0; }
        .text-primary { color: #0d6efd !important; }
        .text-success { color: #1cc88a !important; }
        .text-warning { color: #f6c23e !important; }
        .text-danger { color: #e74a3b !important; }
        .text-info { color: #36b9cc !important; }
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px; }
        .progress { height: 25px; border-radius: 10px; }
        .table th { background: #f7fafc; font-weight: 600; }
        @media (max-width: 768px) { .sidebar { width: 200px; } .main { margin-left: 200px; } }
        @media (max-width: 576px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fas fa-car"></i> RideRent</h2>
    <ul>
        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="vehicle_list.php"><i class="fas fa-car"></i> Vehicles</a></li>
        <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
        <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
        <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li class="active"><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
        <p>Complete platform statistics</p>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-card"><h3 class="text-primary"><?php echo $totalUsers; ?></h3><p>Total Users</p></div>
        <div class="stat-card"><h3 class="text-success"><?php echo $totalVehicles; ?></h3><p>Total Vehicles</p></div>
        <div class="stat-card"><h3 class="text-warning"><?php echo $totalBookings; ?></h3><p>Total Bookings</p></div>
        <div class="stat-card"><h3 class="text-info">$<?php echo number_format($totalRevenue, 2); ?></h3><p>Total Revenue</p></div>
        <div class="stat-card"><h3 class="text-primary"><?php echo $totalDrivers; ?></h3><p>Total Drivers</p></div>
        <div class="stat-card"><h3 class="text-warning"><?php echo $totalReviews; ?></h3><p>Total Reviews</p></div>
    </div>

    <!-- Vehicle Status -->
    <div class="table-wrapper">
        <h4>Vehicle Status Distribution</h4>
        <div class="row mt-3">
            <div class="col-md-8">
                <?php
                $total = $availableVehicles + $bookedVehicles + $maintenanceVehicles;
                $statuses = [
                    'Available' => ['count' => $availableVehicles, 'color' => '#1cc88a'],
                    'Booked' => ['count' => $bookedVehicles, 'color' => '#f6c23e'],
                    'Maintenance' => ['count' => $maintenanceVehicles, 'color' => '#e74a3b']
                ];
                foreach ($statuses as $name => $data) {
                    $percentage = $total > 0 ? ($data['count'] / $total * 100) : 0;
                ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong><?php echo $name; ?></strong></span>
                            <span><?php echo $data['count']; ?> (<?php echo number_format($percentage, 1); ?>%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo $data['color']; ?>;">
                                <?php echo number_format($percentage, 1); ?>%
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Quick Stats</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success"></i> Available: <strong><?php echo $availableVehicles; ?></strong></li>
                            <li><i class="fas fa-clock text-warning"></i> Booked: <strong><?php echo $bookedVehicles; ?></strong></li>
                            <li><i class="fas fa-tools text-danger"></i> Maintenance: <strong><?php echo $maintenanceVehicles; ?></strong></li>
                            <hr>
                            <li><i class="fas fa-car text-primary"></i> Total: <strong><?php echo $total; ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Bookings -->
    <div class="table-wrapper mt-4">
        <h4>Monthly Bookings (Last 6 Months)</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>Month</th><th>Bookings</th><th>Revenue</th></tr>
                </thead>
                <tbody>
                    <?php if ($monthlyResult && mysqli_num_rows($monthlyResult) > 0) {
                        while ($row = mysqli_fetch_assoc($monthlyResult)) {
                            $monthName = date('F Y', strtotime($row['month'] . '-01'));
                    ?>
                            <tr>
                                <td><strong><?php echo $monthName; ?></strong></td>
                                <td><?php echo $row['count']; ?></td>
                                <td><strong>$<?php echo number_format($row['revenue'] ?? 0, 2); ?></strong></td>
                            </tr>
                    <?php }
                    } else { ?>
                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>