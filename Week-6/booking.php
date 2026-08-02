<?php
// booking.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("database/connection.php");

// Get all bookings
$bookingsQuery = "SELECT b.*, 
                  c.full_name as customer_name,
                  v.vehicle_name,
                  d.full_name as driver_name
                  FROM booking b
                  LEFT JOIN customer c ON b.customer_id = c.customer_id
                  LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
                  LEFT JOIN driver d ON b.driver_id = d.driver_id
                  ORDER BY b.booking_id DESC";

$bookingsResult = mysqli_query($conn, $bookingsQuery);

// Statistics
$totalBookings = 0;
$pendingBookings = 0;
$confirmedBookings = 0;
$completedBookings = 0;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking");
if ($totalQuery) { 
    $row = mysqli_fetch_assoc($totalQuery); 
    $totalBookings = $row['total']; 
}

$pendingQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE booking_status='Pending'");
if ($pendingQuery) { 
    $row = mysqli_fetch_assoc($pendingQuery); 
    $pendingBookings = $row['total']; 
}

$confirmedQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE booking_status='Confirmed'");
if ($confirmedQuery) { 
    $row = mysqli_fetch_assoc($confirmedQuery); 
    $confirmedBookings = $row['total']; 
}

$completedQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE booking_status='Completed'");
if ($completedQuery) { 
    $row = mysqli_fetch_assoc($completedQuery); 
    $completedBookings = $row['total']; 
}

// Handle Update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['booking_status'];
    $payment_status = $_POST['payment_status'];
    
    $update = "UPDATE booking SET booking_status='$status', payment_status='$payment_status' WHERE booking_id='$booking_id'";
    if (mysqli_query($conn, $update)) {
        header("Location: booking.php");
        exit();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM booking WHERE booking_id='$booking_id'");
    header("Location: booking.php");
    exit();
}

$hasBookings = $bookingsResult && mysqli_num_rows($bookingsResult) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
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
        .text-info { color: #36b9cc !important; }
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-wrapper h4 { font-weight: 600; color: #2d3748; margin-bottom: 15px; }
        .table th { background: #f7fafc; font-weight: 600; padding: 12px 15px; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; }
        .badge-pending { background: #f6c23e; color: #2d3748; padding: 5px 12px; border-radius: 20px; }
        .badge-confirmed { background: #36b9cc; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-completed { background: #1cc88a; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-cancelled { background: #e74a3b; color: white; padding: 5px 12px; border-radius: 20px; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
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
        <li class="active"><a href="booking.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1><i class="fas fa-calendar-check"></i> Booking Management</h1>
        <p>Manage all vehicle bookings</p>
    </div>

    <div class="stats">
        <div class="stat-card"><h3 class="text-primary"><?php echo $totalBookings; ?></h3><p>Total</p></div>
        <div class="stat-card"><h3 class="text-warning"><?php echo $pendingBookings; ?></h3><p>Pending</p></div>
        <div class="stat-card"><h3 class="text-info"><?php echo $confirmedBookings; ?></h3><p>Confirmed</p></div>
        <div class="stat-card"><h3 class="text-success"><?php echo $completedBookings; ?></h3><p>Completed</p></div>
    </div>

    <div class="table-wrapper">
        <h4>All Bookings</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasBookings) {
                        while ($booking = mysqli_fetch_assoc($bookingsResult)) { ?>
                            <tr>
                                <td><strong>#<?php echo $booking['booking_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($booking['vehicle_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($booking['driver_name'] ?? 'No Driver'); ?></td>
                                <td><?php echo date('M d', strtotime($booking['start_date'])); ?></td>
                                <td><?php echo date('M d', strtotime($booking['end_date'])); ?></td>
                                <td><strong>$<?php echo number_format($booking['total_price'], 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php 
                                        if ($booking['booking_status'] == 'Pending') echo 'badge-pending';
                                        elseif ($booking['booking_status'] == 'Confirmed') echo 'badge-confirmed';
                                        elseif ($booking['booking_status'] == 'Completed') echo 'badge-completed';
                                        else echo 'badge-cancelled';
                                    ?>">
                                        <?php echo $booking['booking_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $booking['payment_status'] == 'Paid' ? 'badge-completed' : 'badge-pending'; ?>">
                                        <?php echo $booking['payment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $booking['booking_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $booking['booking_id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Delete this booking?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Status Update Modal -->
                            <div class="modal fade" id="statusModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Booking</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                <div class="mb-3">
                                                    <label>Booking Status</label>
                                                    <select name="booking_status" class="form-control">
                                                        <option value="Pending" <?php echo $booking['booking_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Confirmed" <?php echo $booking['booking_status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="Completed" <?php echo $booking['booking_status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                        <option value="Cancelled" <?php echo $booking['booking_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Payment Status</label>
                                                    <select name="payment_status" class="form-control">
                                                        <option value="Pending" <?php echo $booking['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Paid" <?php echo $booking['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                                        <option value="Refunded" <?php echo $booking['payment_status'] == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <tr><td colspan="10" class="text-center">No bookings found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>