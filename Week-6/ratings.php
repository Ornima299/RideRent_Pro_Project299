<?php
// ratings.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("database/connection.php");

// Get all ratings
$ratingsQuery = "SELECT r.*, 
                 c.full_name as customer_name,
                 v.vehicle_name
                 FROM reviews r
                 LEFT JOIN customer c ON r.customer_id = c.customer_id
                 LEFT JOIN vehicle v ON r.vehicle_id = v.vehicle_id
                 ORDER BY r.rating DESC";

$ratingsResult = mysqli_query($conn, $ratingsQuery);

// Statistics
$avgRating = 0;
$totalRatings = 0;
$ratingCounts = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];

$ratingStats = mysqli_query($conn, "SELECT rating, COUNT(*) as count FROM reviews GROUP BY rating");
if ($ratingStats) {
    while ($row = mysqli_fetch_assoc($ratingStats)) {
        $ratingCounts[(string)$row['rating']] = $row['count'];
        $totalRatings += $row['count'];
        $avgRating += $row['rating'] * $row['count'];
    }
}

if ($totalRatings > 0) {
    $avgRating = $avgRating / $totalRatings;
}

$hasRatings = $ratingsResult && mysqli_num_rows($ratingsResult) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings Management</title>
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
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px; }
        .table-wrapper h4 { font-weight: 600; color: #2d3748; margin-bottom: 15px; }
        .table th { background: #f7fafc; font-weight: 600; padding: 12px 15px; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; }
        .badge-approved { background: #1cc88a; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-pending { background: #f6c23e; color: #2d3748; padding: 5px 12px; border-radius: 20px; }
        .rating-stars { color: #f6c23e; }
        .rating-bar { height: 8px; background: #edf2f7; border-radius: 10px; overflow: hidden; }
        .rating-bar-fill { height: 100%; border-radius: 10px; }
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
        <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li class="active"><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1><i class="fas fa-star-half-alt"></i> Ratings Management</h1>
        <p>View all ratings</p>
    </div>

    <div class="stats">
        <div class="stat-card"><h3 class="text-warning"><?php echo number_format($avgRating, 1); ?> ⭐</h3><p>Average Rating</p></div>
        <div class="stat-card"><h3 class="text-primary"><?php echo $totalRatings; ?></h3><p>Total Ratings</p></div>
        <div class="stat-card"><h3 class="text-success"><?php echo ($ratingCounts['5'] + $ratingCounts['4']); ?></h3><p>Positive (4-5⭐)</p></div>
        <div class="stat-card"><h3 class="text-danger"><?php echo ($ratingCounts['1'] + $ratingCounts['2']); ?></h3><p>Negative (1-2⭐)</p></div>
    </div>

    <div class="table-wrapper">
        <h4>Rating Distribution</h4>
        <?php
        $maxCount = max($ratingCounts);
        $ratings = [
            '5' => ['label' => '5 Stars', 'color' => '#1cc88a'],
            '4' => ['label' => '4 Stars', 'color' => '#36b9cc'],
            '3' => ['label' => '3 Stars', 'color' => '#f6c23e'],
            '2' => ['label' => '2 Stars', 'color' => '#f6c23e'],
            '1' => ['label' => '1 Star', 'color' => '#e74a3b']
        ];
        ?>
        <div class="row mt-3">
            <div class="col-md-6">
                <?php foreach ($ratings as $key => $info) { 
                    $count = $ratingCounts[$key] ?? 0;
                    $percentage = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><?php echo $info['label']; ?></span>
                            <span><?php echo $count; ?> reviews</span>
                        </div>
                        <div class="rating-bar">
                            <div class="rating-bar-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $info['color']; ?>;"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="display-1 text-warning"><?php echo number_format($avgRating, 1); ?></h2>
                        <div class="rating-stars" style="font-size: 28px;">
                            <?php 
                            $fullStars = floor($avgRating);
                            $halfStar = $avgRating - $fullStars >= 0.5;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) echo '<i class="fas fa-star"></i>';
                                elseif ($i == $fullStars + 1 && $halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <p class="text-muted">Based on <?php echo $totalRatings; ?> reviews</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-wrapper mt-4">
        <h4>All Customer Ratings</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th><th>Customer</th><th>Vehicle</th>
                        <th>Rating</th><th>Comment</th><th>Date</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasRatings) {
                        while ($rating = mysqli_fetch_assoc($ratingsResult)) { ?>
                            <tr>
                                <td>#<?php echo $rating['review_id']; ?></td>
                                <td><?php echo htmlspecialchars($rating['customer_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($rating['vehicle_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="rating-stars">
                                        <?php 
                                        $r = $rating['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $r) echo '<i class="fas fa-star"></i>';
                                            elseif ($i - 0.5 <= $r) echo '<i class="fas fa-star-half-alt"></i>';
                                            else echo '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                        <span class="ms-1"><?php echo number_format($r, 1); ?></span>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($rating['comment'] ?? '', 0, 50)); ?></td>
                                <td><?php echo date('M d, Y', strtotime($rating['review_date'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $rating['status'] == 'Approved' ? 'badge-approved' : 'badge-pending'; ?>">
                                        <?php echo $rating['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="7" class="text-center">No ratings found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>