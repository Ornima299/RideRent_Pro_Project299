<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle approval/rejection
if(isset($_GET['action']) && isset($_GET['id'])) {
    $vehicle_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if($action == 'approve') {
        $update = "UPDATE vehicle SET approval_status = 'Approved' WHERE vehicle_id = '$vehicle_id'";
        mysqli_query($conn, $update);
        header("Location: vehicle_approvals.php?msg=approved");
        exit();
    } elseif($action == 'reject') {
        $update = "UPDATE vehicle SET approval_status = 'Rejected' WHERE vehicle_id = '$vehicle_id'";
        mysqli_query($conn, $update);
        header("Location: vehicle_approvals.php?msg=rejected");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Approvals - RideRent Pro</title>
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
                <li><a href="vehicle_approvals.php" class="active"><i class="fas fa-check-circle"></i> Vehicle Approvals</a></li>
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
            <h1><i class="fas fa-check-circle"></i> Vehicle Approvals</h1>
            <p>Review and approve vehicle owner requests</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php 
                if($_GET['msg'] == 'approved') echo "Vehicle approved successfully!";
                if($_GET['msg'] == 'rejected') echo "Vehicle rejected successfully!";
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Approvals</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle Name</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Price/Day</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.*, o.full_name as owner_name FROM vehicle v 
                                    LEFT JOIN vehicle_owner o ON v.owner_id = o.owner_id 
                                    WHERE v.approval_status = 'Pending' 
                                    ORDER BY v.created_at DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td><?php echo $row['vehicle_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td>৳<?php echo number_format($row['price_per_day'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><span class='badge badge-warning'>Pending</span></td>
                                        <td>
                                            <a href="vehicle_approvals.php?action=approve&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this vehicle?')"><i class="fas fa-check"></i> Approve</a>
                                            <a href="vehicle_approvals.php?action=reject&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this vehicle?')"><i class="fas fa-times"></i> Reject</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='10' style='text-align: center; padding: 30px;'>No pending vehicle approvals.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Vehicles</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle Name</th>
                                <th>Brand</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Price/Day</th>
                                <th>Approval Status</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.*, o.full_name as owner_name FROM vehicle v 
                                    LEFT JOIN vehicle_owner o ON v.owner_id = o.owner_id 
                                    ORDER BY v.created_at DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $approvalBadge = '';
                                    if($row['approval_status'] == 'Approved') $approvalBadge = 'badge-success';
                                    elseif($row['approval_status'] == 'Rejected') $approvalBadge = 'badge-danger';
                                    else $approvalBadge = 'badge-warning';
                                    
                                    $availabilityBadge = '';
                                    if($row['availability'] == 'Available') $availabilityBadge = 'badge-success';
                                    elseif($row['availability'] == 'Booked') $availabilityBadge = 'badge-danger';
                                    else $availabilityBadge = 'badge-warning';
                            ?>
                                    <tr>
                                        <td><?php echo $row['vehicle_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td>৳<?php echo number_format($row['price_per_day'], 2); ?></td>
                                        <td><span class='badge <?php echo $approvalBadge; ?>'><?php echo $row['approval_status']; ?></span></td>
                                        <td><span class='badge <?php echo $availabilityBadge; ?>'><?php echo $row['availability']; ?></span></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center; padding: 30px;'>No vehicles found.</td></tr>";
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