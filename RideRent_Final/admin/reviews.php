<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle approval/rejection
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'approved' : 'rejected';
    mysqli_query($conn, "UPDATE reviews SET status = '$status' WHERE review_id = '$id'");
    header("Location: reviews.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '$id'");
    header("Location: reviews.php");
    exit();
}

// Fetch all reviews
$reviews_query = mysqli_query($conn, "SELECT r.*, c.full_name as customer_name FROM reviews r LEFT JOIN customer c ON r.user_id = c.customer_id ORDER BY r.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management - RideRent Pro</title>
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
                <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
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
            <h1><i class="fas fa-star"></i> Reviews Management</h1>
            <p>Manage customer reviews and feedback</p>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Target Type</th>
                                <th>Target ID</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($reviews_query && mysqli_num_rows($reviews_query) > 0) {
                                while($row = mysqli_fetch_assoc($reviews_query)) {
                                    $statusClass = '';
                                    if($row['status'] == 'approved') $statusClass = 'badge-success';
                                    elseif($row['status'] == 'pending') $statusClass = 'badge-warning';
                                    else $statusClass = 'badge-danger';
                                    
                                    $stars = '';
                                    for($i = 1; $i <= 5; $i++) {
                                        $stars .= $i <= $row['rating'] ? '⭐' : '☆';
                                    }
                            ?>
                                <tr>
                                    <td><?php echo $row['review_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo ucfirst($row['target_type']); ?></td>
                                    <td><?php echo $row['target_id']; ?></td>
                                    <td><?php echo $stars; ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['comment'], 0, 50)) . (strlen($row['comment']) > 50 ? '...' : ''); ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php if($row['status'] == 'pending') { ?>
                                            <a href="reviews.php?action=approve&id=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                                            <a href="reviews.php?action=reject&id=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                        <?php } ?>
                                        <a href="reviews.php?delete=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No reviews found</td>
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