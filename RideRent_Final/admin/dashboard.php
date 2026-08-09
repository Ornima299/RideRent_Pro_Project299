<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

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
$bookingQuery = mysqli_query($conn, "SELECT * FROM booking");
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
    <title>Admin Dashboard - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-car-side"></i> RideRent Pro</h2>
        </div>
        <div class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="vehicle_approvals.php"><i class="fas fa-check-circle"></i> Vehicle Approvals</a></li>
                <li><a href="driver_assignment.php"><i class="fas fa-user-plus"></i> Driver Assignment</a></li>
                <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <button class="theme-toggle" onclick="toggleTheme()" style="width: 100%; justify-content: center;">
                <i class="fas fa-moon"></i>
                <span>Dark Mode</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Welcome back, Admin!</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Vehicles</h3>
                    <p><?php echo $totalVehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Drivers</h3>
                    <p><?php echo $totalDrivers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E84393, #D6336C);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Bookings</h3>
                    <p><?php echo $totalBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Reviews</h3>
                    <p><?php echo $totalReviews; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #00C9A7);">
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Avg Rating</h3>
                    <p><?php echo $totalRatings; ?> ⭐</p>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> Recent Bookings</h3>
                <p class="card-subtitle">Latest 5 bookings</p>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bookingQuery = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, v.vehicle_name 
                                FROM booking b 
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
                                    $date = date('M d, Y', strtotime($row['booking_date']));
                            ?>
                                <tr>
                                    <td><?php echo $row['booking_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo $row['booking_status']; ?></span></td>
                                    <td><?php echo $date; ?></td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No recent bookings found</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>