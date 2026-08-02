<?php
// reviews.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("database/connection.php");

// Get all reviews
$reviewsQuery = "SELECT r.*, 
                 c.full_name as customer_name,
                 v.vehicle_name
                 FROM reviews r
                 LEFT JOIN customer c ON r.customer_id = c.customer_id
                 LEFT JOIN vehicle v ON r.vehicle_id = v.vehicle_id
                 ORDER BY r.review_id DESC";

$reviewsResult = mysqli_query($conn, $reviewsQuery);

// Statistics
$totalReviews = 0;
$pendingReviews = 0;
$approvedReviews = 0;
$rejectedReviews = 0;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews");
if ($totalQuery) { 
    $row = mysqli_fetch_assoc($totalQuery); 
    $totalReviews = $row['total']; 
}

$pendingQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews WHERE status='Pending'");
if ($pendingQuery) { 
    $row = mysqli_fetch_assoc($pendingQuery); 
    $pendingReviews = $row['total']; 
}

$approvedQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews WHERE status='Approved'");
if ($approvedQuery) { 
    $row = mysqli_fetch_assoc($approvedQuery); 
    $approvedReviews = $row['total']; 
}

$rejectedQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews WHERE status='Rejected'");
if ($rejectedQuery) { 
    $row = mysqli_fetch_assoc($rejectedQuery); 
    $rejectedReviews = $row['total']; 
}

// Average rating
$avgRating = 0;
$avgQuery = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM reviews");
if ($avgQuery) { 
    $row = mysqli_fetch_assoc($avgQuery); 
    $avgRating = $row['avg_rating'] ?? 0; 
}

// Handle Update
if (isset($_POST['update_status'])) {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    
    $update = "UPDATE reviews SET status='$status' WHERE review_id='$review_id'";
    if (mysqli_query($conn, $update)) {
        header("Location: reviews.php");
        exit();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $review_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM reviews WHERE review_id='$review_id'");
    header("Location: reviews.php");
    exit();
}

$hasReviews = $reviewsResult && mysqli_num_rows($reviewsResult) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management</title>
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
        .header h1 { font-size: 28px; font-weight: 700; color: #2d3748; }
        .header p { color: #718096; margin: 0; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h3 { font-size: 28px; font-weight: 700; }
        .stat-card p { color: #666; margin: 0; }
        .text-primary { color: #0d6efd !important; }
        .text-warning { color: #f6c23e !important; }
        .text-success { color: #1cc88a !important; }
        .text-danger { color: #e74a3b !important; }
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-wrapper h4 { font-weight: 600; color: #2d3748; margin-bottom: 15px; }
        .table th { background: #f7fafc; font-weight: 600; padding: 12px 15px; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; }
        .badge-pending { background: #f6c23e; color: #2d3748; padding: 5px 12px; border-radius: 20px; }
        .badge-approved { background: #1cc88a; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-rejected { background: #e74a3b; color: white; padding: 5px 12px; border-radius: 20px; }
        .rating-stars { color: #f6c23e; }
        @media (max-width: 768px) { .sidebar { width: 200px; } .main { margin-left: 200px; } }
        @media (max-width: 576px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-car"></i> RideRent</h2>
    <ul>
        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="vehicle_list.php"><i class="fas fa-car"></i> Vehicles</a></li>
        <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
        <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
        <li><a href="booking.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li class="active"><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1><i class="fas fa-star"></i> Reviews Management</h1>
        <p>Manage customer reviews</p>
    </div>

    <div class="stats">
        <div class="stat-card"><h3 class="text-primary"><?php echo $totalReviews; ?></h3><p>Total Reviews</p></div>
        <div class="stat-card"><h3 class="text-warning"><?php echo $pendingReviews; ?></h3><p>Pending</p></div>
        <div class="stat-card"><h3 class="text-success"><?php echo $approvedReviews; ?></h3><p>Approved</p></div>
        <div class="stat-card"><h3 class="text-danger"><?php echo $rejectedReviews; ?></h3><p>Rejected</p></div>
        <div class="stat-card"><h3 class="text-primary"><?php echo number_format($avgRating, 1); ?> ⭐</h3><p>Average Rating</p></div>
    </div>

    <div class="table-wrapper">
        <h4>All Reviews</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th><th>Customer</th><th>Vehicle</th><th>Rating</th>
                        <th>Comment</th><th>Date</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasReviews) {
                        while ($review = mysqli_fetch_assoc($reviewsResult)) { ?>
                            <tr>
                                <td>#<?php echo $review['review_id']; ?></td>
                                <td><?php echo htmlspecialchars($review['customer_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($review['vehicle_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="rating-stars">
                                        <?php 
                                        $rating = $review['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) echo '<i class="fas fa-star"></i>';
                                            elseif ($i - 0.5 <= $rating) echo '<i class="fas fa-star-half-alt"></i>';
                                            else echo '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                        <span class="ms-1"><?php echo number_format($rating, 1); ?></span>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($review['comment'] ?? '', 0, 50)); ?></td>
                                <td><?php echo date('M d, Y', strtotime($review['review_date'])); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        if ($review['status'] == 'Pending') echo 'badge-pending';
                                        elseif ($review['status'] == 'Approved') echo 'badge-approved';
                                        else echo 'badge-rejected';
                                    ?>">
                                        <?php echo $review['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $review['review_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $review['review_id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Delete this review?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="8" class="text-center">No reviews found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>