<?php
include("../database/connection.php");

// Check Vehicle ID
if (!isset($_GET['id'])) {
    header("Location: vehicles.php");
    exit();
}

$id = $_GET['id'];

// Get Vehicle Data
$query = "SELECT * FROM vehicle WHERE vehicle_id='$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Vehicle not found!");
}

$row = mysqli_fetch_assoc($result);

// Update Vehicle
if (isset($_POST['update'])) {

    $vehicle_name = mysqli_real_escape_string($conn, $_POST['vehicle_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $fuel_type = mysqli_real_escape_string($conn, $_POST['fuel_type']);
    $transmission = mysqli_real_escape_string($conn, $_POST['transmission']);
    $seat_capacity = mysqli_real_escape_string($conn, $_POST['seat_capacity']);
    $price = mysqli_real_escape_string($conn, $_POST['price_per_day']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Image Upload
    if ($_FILES['image']['name'] != "") {

        $image = time() . "_" . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp, "../uploads/" . $image);

    } else {

        $image = $row['image'];
    }

    $update = "UPDATE vehicle SET

        vehicle_name='$vehicle_name',
        brand='$brand',
        model='$model',
        year='$year',
        vehicle_type='$vehicle_type',
        fuel_type='$fuel_type',
        transmission='$transmission',
        seat_capacity='$seat_capacity',
        price_per_day='$price',
        location='$location',
        availability='$availability',
        description='$description',
        image='$image'

        WHERE vehicle_id='$id'";

    if (mysqli_query($conn, $update)) {

        echo "<script>
        alert('Vehicle Updated Successfully!');
        window.location='vehicle_details.php?id=$id';
        </script>";

    } else {

        echo "<script>alert('Update Failed');</script>";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Edit Vehicle</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>Edit Vehicle</h3>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label>Vehicle Name</label>

<input type="text"
class="form-control"
name="vehicle_name"
value="<?php echo $row['vehicle_name']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Brand</label>

<input type="text"
class="form-control"
name="brand"
value="<?php echo $row['brand']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Model</label>

<input type="text"
class="form-control"
name="model"
value="<?php echo $row['model']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Year</label>

<input type="number"
class="form-control"
name="year"
value="<?php echo $row['year']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Vehicle Type</label>

<input type="text"
class="form-control"
name="vehicle_type"
value="<?php echo $row['vehicle_type']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Fuel Type</label>

<input type="text"
class="form-control"
name="fuel_type"
value="<?php echo $row['fuel_type']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Transmission</label>

<input type="text"
class="form-control"
name="transmission"
value="<?php echo $row['transmission']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Seat Capacity</label>

<input type="number"
class="form-control"
name="seat_capacity"
value="<?php echo $row['seat_capacity']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Price Per Day</label>

<input type="number"
class="form-control"
name="price_per_day"
value="<?php echo $row['price_per_day']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Location</label>

<input type="text"
class="form-control"
name="location"
value="<?php echo $row['location']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Availability</label>

<select name="availability" class="form-control">

<option <?php if($row['availability']=="Available") echo "selected"; ?>>Available</option>

<option <?php if($row['availability']=="Booked") echo "selected"; ?>>Booked</option>

<option <?php if($row['availability']=="Maintenance") echo "selected"; ?>>Maintenance</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Vehicle Image</label>

<input type="file" class="form-control" name="image">

<br>

<img src="../uploads/<?php echo $row['image']; ?>" width="150">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
class="form-control"
rows="5"
name="description"><?php echo $row['description']; ?></textarea>

</div>

<div class="text-center">

<button type="submit"
name="update"
class="btn btn-success">

Update Vehicle

</button>

<a href="vehicle_details.php?id=<?php echo $id; ?>"
class="btn btn-secondary">

Cancel

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</body>

</html>
