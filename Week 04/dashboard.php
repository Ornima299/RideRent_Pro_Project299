<?php
include("database/connection.php");

// Count Vehicles
$vehicleQuery = mysqli_query($conn, "SELECT * FROM vehicle");
$totalVehicles = mysqli_num_rows($vehicleQuery);

// Available Vehicles
$availableQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Available'");
$availableVehicles = mysqli_num_rows($availableQuery);

// Booked Vehicles
$bookedQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Booked'");
$bookedVehicles = mysqli_num_rows($bookedQuery);

// Maintenance
$maintenanceQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Maintenance'");
$maintenanceVehicles = mysqli_num_rows($maintenanceQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .dashboard-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); text-align: center; }
        .dashboard-card h3 { font-size: 30px; }
        .dashboard-card p { margin: 0; color: #555; }
        .vehicle-card { transition: 0.3s; border: none; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .vehicle-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .vehicle-card img { width: 100%; height: 220px; object-fit: cover; }
        .badge { padding: 8px 15px; font-size: 14px; }
        footer { margin-top: 50px; text-align: center; color: #777; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">DriveEase Admin</a>
        <div>
            <a href="vehicle_list.php" class="btn btn-light">Vehicle List</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <h3><?php echo $totalVehicles; ?></h3>
                <p>Total Vehicles</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <h3><?php echo $availableVehicles; ?></h3>
                <p>Available</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <h3><?php echo $bookedVehicles; ?></h3>
                <p>Booked</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <h3><?php echo $maintenanceVehicles; ?></h3>
                <p>Maintenance</p>
            </div>
        </div>
    </div>

    <hr>

    <h3 class="mt-5 mb-4">Latest Vehicles</h3>

    <div class="row">
        <?php
        $list = mysqli_query($conn, "SELECT * FROM vehicle ORDER BY vehicle_id DESC LIMIT 6");
        while($row = mysqli_fetch_assoc($list)) {
        ?>
        <div class="col-lg-4 mb-4">
            <div class="card vehicle-card">
                <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['vehicle_name']; ?>">
                <div class="card-body">
                    <h4><?php echo $row['vehicle_name']; ?></h4>
                    <p>Brand: <b><?php echo $row['brand']; ?></b></p>
                    <p>Price: <b>$<?php echo $row['price_per_day']; ?></b></p>
                    <?php
                    if($row['availability'] == "Available") {
                        echo "<span class='badge bg-success'>Available</span>";
                    } elseif($row['availability'] == "Booked") {
                        echo "<span class='badge bg-danger'>Booked</span>";
                    } else {
                        echo "<span class='badge bg-warning text-dark'>Maintenance</span>";
                    }
                    ?>
                    <br><br>
                    <a href="vehicle_details.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<footer>
    <p>© 2026 DriveEase Vehicle Rental System</p>
</footer>

</body>
</html>