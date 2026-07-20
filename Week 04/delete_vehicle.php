<?php
include("../database/connection.php");

// Check ID
if(!isset($_GET['id'])){
    header("Location: vehicles.php");
    exit();
}

$id = $_GET['id'];

// Get Vehicle Image
$sql = "SELECT image FROM vehicle WHERE vehicle_id='$id'";
$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Vehicle not found.");
}

$row = mysqli_fetch_assoc($result);

// Delete image from uploads folder
if(!empty($row['image'])){

    $imagePath = "../uploads/".$row['image'];

    if(file_exists($imagePath)){
        unlink($imagePath);
    }

}

// Delete Vehicle
$delete = "DELETE FROM vehicle WHERE vehicle_id='$id'";

if(mysqli_query($conn,$delete)){

    echo "<script>
            alert('Vehicle Deleted Successfully');
            window.location='vehicles.php';
          </script>";

}else{

    echo "<script>
            alert('Delete Failed');
            history.back();
          </script>";

}
?>
