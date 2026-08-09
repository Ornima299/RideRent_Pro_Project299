<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Get ratings statistics
$avg_driver_rating = 0;
$driver_result = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM driver");
if($driver_result) {
    $row = mysqli_fetch_assoc($driver_result);
    $avg_driver_rating = $row['avg_rating'] ? round($row['avg_rating'], 2) : 0;
}

$total_drivers = 0;
$driver_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM driver");
if($driver_count) {
    $row = mysqli_fetch_assoc($driver_count);
    $total_drivers = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings Overview - RideRent Pro</title>
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="vehicle_approvals.php"><i class="fas fa-check-circle"></i> Vehicle Approvals</a></li>
                <li><a href="driver_assignment.php"><i class="fas fa-user-plus"></i> Driver Assignment</a></li>
                <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                <li><a href="ratings.php" class="active"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
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
            <h1><i class="fas fa-star-half-alt"></i> Ratings Overview</h1>
            <p>Driver and vehicle ratings statistics</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Drivers</h3>
                    <p><?php echo $total_drivers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Avg Driver Rating</h3>
                    <p><?php echo $avg_driver_rating; ?> ⭐</p>
                </div>
            </div>
        </div>

        <!-- Driver Ratings Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-card"></i> Driver Ratings</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Experience</th>
                                <th>Rating</th>
                                <th>Rating Count</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM driver ORDER BY rating DESC");
                            while($row = mysqli_fetch_assoc($result)) {
                                $availClass = $row['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                echo "<tr>
                                    <td>{$row['driver_id']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['experience_years']} years</td>
                                    <td><span style='color: #FD7E14;'>{$row['rating']} ⭐</span></td>
                                    <td>{$row['rating_count']}</td>
                                    <td><span class='badge {$availClass}'>{$row['availability']}</span></td>
                                </tr>";
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