<?php
include("db_connect.php");

$message = "";
$message_type = "";

if(isset($_POST['save_vehicle']))
{
    // ============================
    // Get Form Data
    // ============================

    $vehicle_name = trim($_POST['vehicle_name']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $year = $_POST['year'];
    $vehicle_type = $_POST['vehicle_type'];
    $fuel_type = $_POST['fuel_type'];
    $transmission = $_POST['transmission'];
    $seat_capacity = $_POST['seat_capacity'];
    $price_per_day = $_POST['price_per_day'];
    $location = trim($_POST['location']);
    $availability = $_POST['availability'];
    $description = trim($_POST['description']);

    // ============================
    // Server Side Validation
    // ============================

    if(
        empty($vehicle_name) ||
        empty($brand) ||
        empty($model) ||
        empty($year) ||
        empty($vehicle_type) ||
        empty($fuel_type) ||
        empty($transmission) ||
        empty($seat_capacity) ||
        empty($price_per_day) ||
        empty($location)
    )
    {
        $message="Please fill up all required fields.";
        $message_type="danger";
    }

    elseif($price_per_day<=0)
    {
        $message="Price must be greater than zero.";
        $message_type="danger";
    }

    elseif($year<2015 || $year>2026)
    {
        $message="Vehicle year must be between 2015 and 2026.";
        $message_type="danger";
    }

    else
    {

        //==============================
        // Image Upload
        //==============================

        $image_name=$_FILES['vehicle_image']['name'];
        $tmp_name=$_FILES['vehicle_image']['tmp_name'];
        $image_size=$_FILES['vehicle_image']['size'];

        $extension=strtolower(pathinfo($image_name,PATHINFO_EXTENSION));

        $allowed=array("jpg","jpeg","png");

        if(!in_array($extension,$allowed))
        {
            $message="Only JPG, JPEG and PNG files are allowed.";
            $message_type="danger";
        }

        elseif($image_size>2097152)
        {
            $message="Image size must be below 2MB.";
            $message_type="danger";
        }

        else
        {

            // Unique Image Name

            $new_image=time()."_".$image_name;

            move_uploaded_file(
                $tmp_name,
                "uploads/".$new_image
            );

            //==============================
            // Insert Into Database
            //==============================

            $sql="INSERT INTO vehicle
            (
                vehicle_name,
                brand,
                model,
                year,
                vehicle_type,
                fuel_type,
                transmission,
                seat_capacity,
                price_per_day,
                location,
                availability,
                image,
                description
            )

            VALUES

            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )";

            $stmt=mysqli_prepare($conn,$sql);

            mysqli_stmt_bind_param(

                $stmt,

                "sssssssisssss",

                $vehicle_name,
                $brand,
                $model,
                $year,
                $vehicle_type,
                $fuel_type,
                $transmission,
                $seat_capacity,
                $price_per_day,
                $location,
                $availability,
                $new_image,
                $description

            );

            if(mysqli_stmt_execute($stmt))
            {
                $message="Vehicle Added Successfully!";
                $message_type="success";
            }
            else
            {
                $message="Database Error : ".mysqli_error($conn);
                $message_type="danger";
            }

        }

    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Vehicle | RideRent Pro</title>

<link rel="stylesheet" href="css/add_vehicle.css">

<script src="js/validation.js" defer></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="container mt-5 mb-5">

<div class="card shadow-lg">

<div class="card-header bg-primary text-white">

<h2 class="text-center">

<i class="fa-solid fa-car-side"></i>

Add New Vehicle

</h2>

</div>

<div class="card-body">

<?php

if($message!="")
{

echo "<div class='alert alert-$message_type'>$message</div>";

}

?>

<form

method="POST"

action=""

enctype="multipart/form-data"

id="vehicleForm"

>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Vehicle Name

</label>

<input

type="text"

class="form-control"

name="vehicle_name"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Brand

</label>

<input

type="text"

class="form-control"

name="brand"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Model

</label>

<input

type="text"

class="form-control"

name="model"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Year

</label>

<input

type="number"

class="form-control"

name="year"

min="2015"

max="2026"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Vehicle Type

</label>

<select

class="form-select"

name="vehicle_type"

required

>

<option value="">Select</option>

<option>Sedan</option>

<option>SUV</option>

<option>Microbus</option>

<option>Van</option>

<option>Truck</option>

<option>Bike</option>

<option>Hatchback</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Fuel Type

</label>

<select

class="form-select"

name="fuel_type"

required

>

<option value="">Select</option>

<option>Petrol</option>

<option>Diesel</option>

<option>Hybrid</option>

<option>Electric</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Transmission

</label>

<select

class="form-select"

name="transmission"

required

>

<option value="">Select</option>

<option>Automatic</option>

<option>Manual</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Seat Capacity

</label>

<input

type="number"

class="form-control"

name="seat_capacity"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Price Per Day (BDT)

</label>

<input

type="number"

class="form-control"

name="price_per_day"

step="0.01"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Location

</label>

<input

type="text"

class="form-control"

name="location"

required

>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Availability

</label>

<select

class="form-select"

name="availability"

required

>

<option>Available</option>

<option>Booked</option>

<option>Maintenance</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Vehicle Image

</label>

<input

type="file"

class="form-control"

name="vehicle_image"

accept=".jpg,.jpeg,.png"

required

>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Description

</label>

<textarea

class="form-control"

rows="5"

maxlength="500"

name="description"

placeholder="Write vehicle description..."

></textarea>

</div>

<div class="col-md-12 text-center mt-4">

<button

type="submit"

name="save_vehicle"

class="btn btn-success btn-lg"

>

<i class="fa-solid fa-floppy-disk"></i>

Save Vehicle

</button>

&nbsp;&nbsp;

<button

type="reset"

class="btn btn-secondary btn-lg"

>

Reset

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</body>

</html>
