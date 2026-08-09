<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle Delete
if(isset($_GET['delete']) && isset($_GET['type'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];
    
    $table = "";
    $id_field = "";
    
    switch($type) {
        case 'admin':
            $table = "admin";
            $id_field = "admin_id";
            break;
        case 'customer':
            $table = "customer";
            $id_field = "customer_id";
            break;
        case 'driver':
            $table = "driver";
            $id_field = "driver_id";
            break;
        case 'owner':
            $table = "vehicle_owner";
            $id_field = "owner_id";
            break;
    }
    
    if($table && $id_field) {
        $sql = "DELETE FROM $table WHERE $id_field = '$id'";
        mysqli_query($conn, $sql);
    }
    
    header("Location: users.php");
    exit();
}

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['type']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    $status = $_GET['status'];
    
    $table = "";
    $id_field = "";
    
    switch($type) {
        case 'admin':
            $table = "admin";
            $id_field = "admin_id";
            break;
        case 'customer':
            $table = "customer";
            $id_field = "customer_id";
            break;
        case 'driver':
            $table = "driver";
            $id_field = "driver_id";
            break;
        case 'owner':
            $table = "vehicle_owner";
            $id_field = "owner_id";
            break;
    }
    
    if($table && $id_field) {
        $sql = "UPDATE $table SET status = '$status' WHERE $id_field = '$id'";
        mysqli_query($conn, $sql);
    }
    
    header("Location: users.php");
    exit();
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - RideRent Pro</title>
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
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
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
            <h1><i class="fas fa-users"></i> Users Management</h1>
            <p>Manage all system users</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Users</h3>
            </div>
            <div class="card-body">
                <div class="filter-tabs">
                    <a href="users.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All Users</a>
                    <a href="users.php?filter=admin" class="btn <?php echo $filter == 'admin' ? 'btn-primary' : 'btn-secondary'; ?>">Admins</a>
                    <a href="users.php?filter=customer" class="btn <?php echo $filter == 'customer' ? 'btn-primary' : 'btn-secondary'; ?>">Customers</a>
                    <a href="users.php?filter=driver" class="btn <?php echo $filter == 'driver' ? 'btn-primary' : 'btn-secondary'; ?>">Drivers</a>
                    <a href="users.php?filter=owner" class="btn <?php echo $filter == 'owner' ? 'btn-primary' : 'btn-secondary'; ?>">Vehicle Owners</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users List</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($filter == 'all' || $filter == 'admin') {
                                $result = mysqli_query($conn, "SELECT * FROM admin");
                                while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['admin_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-primary'>Admin</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['admin_id']}&type=admin&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['admin_id']}&type=admin&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['admin_id']}&type=admin' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'customer') {
                                $result = mysqli_query($conn, "SELECT * FROM customer");
                                while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['customer_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone_1']}</td>
                                        <td><span class='badge badge-info'>Customer</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['customer_id']}&type=customer&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['customer_id']}&type=customer&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['customer_id']}&type=customer' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'driver') {
                                $result = mysqli_query($conn, "SELECT * FROM driver");
                                while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['driver_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-secondary'>Driver</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['driver_id']}&type=driver&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['driver_id']}&type=driver&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['driver_id']}&type=driver' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'owner') {
                                $result = mysqli_query($conn, "SELECT * FROM vehicle_owner");
                                while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['owner_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-warning'>Owner</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['owner_id']}&type=owner&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['owner_id']}&type=owner&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['owner_id']}&type=owner' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
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