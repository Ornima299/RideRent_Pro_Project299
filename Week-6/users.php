<?php
// users.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("database/connection.php");

// Get all users from different tables
$allUsers = [];

// 1. Get Admins
$adminQuery = mysqli_query($conn, "SELECT admin_id as id, full_name, username, email, phone, 'Admin' as role, status, created_at FROM admin");
if ($adminQuery) {
    while ($row = mysqli_fetch_assoc($adminQuery)) {
        $allUsers[] = $row;
    }
}

// 2. Get Customers
$customerQuery = mysqli_query($conn, "SELECT customer_id as id, full_name, username, email, phone_1 as phone, 'Customer' as role, status, created_at FROM customer");
if ($customerQuery) {
    while ($row = mysqli_fetch_assoc($customerQuery)) {
        $allUsers[] = $row;
    }
}

// 3. Get Drivers
$driverQuery = mysqli_query($conn, "SELECT driver_id as id, full_name, username, email, phone, 'Driver' as role, status, created_at FROM driver");
if ($driverQuery) {
    while ($row = mysqli_fetch_assoc($driverQuery)) {
        $allUsers[] = $row;
    }
}

// 4. Get Vehicle Owners
$ownerQuery = mysqli_query($conn, "SELECT owner_id as id, full_name, username, email, phone, 'Vehicle Owner' as role, status, created_at FROM vehicle_owner");
if ($ownerQuery) {
    while ($row = mysqli_fetch_assoc($ownerQuery)) {
        $allUsers[] = $row;
    }
}

// Statistics
$totalUsers = count($allUsers);
$totalAdmins = count(array_filter($allUsers, function($u) { return $u['role'] == 'Admin'; }));
$totalCustomers = count(array_filter($allUsers, function($u) { return $u['role'] == 'Customer'; }));
$totalDrivers = count(array_filter($allUsers, function($u) { return $u['role'] == 'Driver'; }));
$totalOwners = count(array_filter($allUsers, function($u) { return $u['role'] == 'Vehicle Owner'; }));

// Handle Status Update
if (isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $role = $_POST['role'];
    
    $table = '';
    $id_field = '';
    
    switch($role) {
        case 'Admin': $table = 'admin'; $id_field = 'admin_id'; break;
        case 'Customer': $table = 'customer'; $id_field = 'customer_id'; break;
        case 'Driver': $table = 'driver'; $id_field = 'driver_id'; break;
        case 'Vehicle Owner': $table = 'vehicle_owner'; $id_field = 'owner_id'; break;
    }
    
    if ($table && $id_field) {
        $update = "UPDATE $table SET status='$status' WHERE $id_field='$user_id'";
        if (mysqli_query($conn, $update)) {
            $success = "User status updated successfully!";
            header("Location: users.php");
            exit();
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}

// Handle Delete
if (isset($_GET['delete']) && isset($_GET['role'])) {
    $user_id = $_GET['delete'];
    $role = $_GET['role'];
    
    $table = '';
    $id_field = '';
    
    switch($role) {
        case 'Admin': $table = 'admin'; $id_field = 'admin_id'; break;
        case 'Customer': $table = 'customer'; $id_field = 'customer_id'; break;
        case 'Driver': $table = 'driver'; $id_field = 'driver_id'; break;
        case 'Vehicle Owner': $table = 'vehicle_owner'; $id_field = 'owner_id'; break;
    }
    
    if ($table && $id_field) {
        $delete = "DELETE FROM $table WHERE $id_field='$user_id'";
        if (mysqli_query($conn, $delete)) {
            $success = "User deleted successfully!";
            header("Location: users.php");
            exit();
        } else {
            $error = "Delete failed: " . mysqli_error($conn);
        }
    }
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $allUsers = array_filter($allUsers, function($user) use ($search) {
        return stripos($user['full_name'], $search) !== false || 
               stripos($user['email'], $search) !== false ||
               stripos($user['username'], $search) !== false;
    });
}

// Check if any users exist
$hasUsers = count($allUsers) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - RideRent Pro</title>
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
        .stat-card h3 { font-size: 28px; font-weight: 700; color: #0d6efd; }
        .stat-card p { color: #666; margin: 0; }
        .table-wrapper { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .search-box { margin-bottom: 20px; }
        .search-box input { padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 8px; width: 300px; }
        .search-box button { padding: 10px 25px; background: #0d6efd; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .badge-active { background: #48bb78; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-inactive { background: #fc8181; color: white; padding: 5px 12px; border-radius: 20px; }
        .badge-pending { background: #ecc94b; color: #2d3748; padding: 5px 12px; border-radius: 20px; }
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
        <li class="active"><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="vehicle_list.php"><i class="fas fa-car"></i> Vehicles</a></li>
        <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
        <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
        <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="ratings.php"><i class="fas fa-star-half-alt"></i> Ratings</a></li>
        <li><a href="dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1><i class="fas fa-users"></i> Users Management</h1>
        <p>Manage all users from all roles</p>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-card"><h3><?php echo $totalUsers; ?></h3><p>Total Users</p></div>
        <div class="stat-card"><h3><?php echo $totalAdmins; ?></h3><p>Admins</p></div>
        <div class="stat-card"><h3><?php echo $totalCustomers; ?></h3><p>Customers</p></div>
        <div class="stat-card"><h3><?php echo $totalDrivers; ?></h3><p>Drivers</p></div>
        <div class="stat-card"><h3><?php echo $totalOwners; ?></h3><p>Vehicle Owners</p></div>
    </div>

    <?php if(isset($success)) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    <?php if(isset($error)) { ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <!-- Search -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by name, email or username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
            <?php if(!empty($search)) { ?>
                <a href="users.php" class="btn btn-secondary ms-2">Clear</a>
            <?php } ?>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-wrapper">
        <h4>All Users</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasUsers) {
                        foreach ($allUsers as $user) { ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td><span class="badge bg-info"><?php echo $user['role']; ?></span></td>
                                <td>
                                    <span class="badge <?php 
                                        if ($user['status'] == 'Active') echo 'badge-active';
                                        elseif ($user['status'] == 'Pending') echo 'badge-pending';
                                        else echo 'badge-inactive';
                                    ?>">
                                        <?php echo $user['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $user['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $user['id']; ?>&role=<?php echo urlencode($user['role']); ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Status Update Modal -->
                            <div class="modal fade" id="statusModal<?php echo $user['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update User Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                                <p>User: <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></p>
                                                <p>Current Status: <strong><?php echo $user['status']; ?></strong></p>
                                                <div class="form-group">
                                                    <label>New Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="Active">Active</option>
                                                        <option value="Inactive">Inactive</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Suspended">Suspended</option>
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
                        <tr><td colspan="9" class="text-center">No users found. Please add some users first.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>