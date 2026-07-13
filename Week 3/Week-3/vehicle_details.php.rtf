<?php
include("../database/connection.php");

if (!isset($_GET['id'])) {
    header("Location: vehicles.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM vehicle WHERE vehicle_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Vehicle not found!";
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vehicle Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/vehicle.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow-lg border-0">

<div class="row g-0">

<div class="col-md-5">

<?php if(!empty($row['image'])){ ?>

<img src="../uploads/<?php echo $row['image']; ?>" class="img-fluid rounded-start w-100" style="height:100%; object-fit:cover;">

<?php } else { ?>

<img src="../assets/images/no-image.png" class="img-fluid rounded-start w-100">

<?php } ?>

</div>

<div class="col-md-7">

<div class="card-body">

<h2 class="fw-bold">

<?php echo $row['vehicle_name']; ?>

</h2>

<hr>

<table class="table">

<tr>

<th>Brand</th>

<td><?php echo $row['brand']; ?></td>

</tr>

<tr>

<th>Model</th>

<td><?php echo $row['model']; ?></td>

</tr>

<tr>

<th>Year</th>

<td><?php echo $row['year']; ?></td>

</tr>

<tr>

<th>Vehicle Type</th>

<td><?php echo $row['vehicle_type']; ?></td>

</tr>

<tr>

<th>Fuel Type</th>

<td><?php echo $row['fuel_type']; ?></td>

</tr>

<tr>

<th>Transmission</th>

<td><?php echo $row['transmission']; ?></td>

</tr>

<tr>

<th>Seat Capacity</th>

<td><?php echo $row['seat_capacity']; ?></td>

</tr>

<tr>

<th>Location</th>

<td><?php echo $row['location']; ?></td>

</tr>

<tr>

<th>Price / Day</th>

<td>$<?php echo $row['price_per_day']; ?></td>

</tr>

<tr>

<th>Availability</th>

<td>

<?php

if($row['availability']=="Available"){

echo "<span class='badge bg-success'>Available</span>";

}elseif($row['availability']=="Booked"){

echo "<span class='badge bg-danger'>Booked</span>";

}else{

echo "<span class='badge bg-warning text-dark'>Maintenance</span>";

}

?>

</td>

</tr>

</table>

<h5>Description</h5>

<p>

<?php echo nl2br($row['description']); ?>

</p>

<div class="mt-4">

<a href="edit_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-primary">

Edit Vehicle

</a>

<a href="delete_vehicle.php?id=<?php echo $row['vehicle_id']; ?>"

class="btn btn-danger"

onclick="return confirm('Are you sure you want to delete this vehicle?');">

Delete

</a>

<a href="vehicles.php" class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>
