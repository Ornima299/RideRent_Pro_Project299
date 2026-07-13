<?php
include("db_connect.php");


$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

if ($search != "") {
    $sql = "SELECT * FROM vehicle WHERE vehicle_name LIKE '%$search%' 
            OR brand LIKE '%$search%' 
            OR model LIKE '%$search%' 
            OR location LIKE '%$search%'
            ORDER BY vehicle_id ASC";
} else {
    $sql = "SELECT * FROM vehicle ORDER BY vehicle_id ASC";
}

$result = mysqli_query($conn, $sql);


$msg = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $msg = "Vehicle deleted successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vehicle List - RideRent Pro</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 20px;
    }
    h2 {
        color: #333;
    }
    .search-box {
        margin-bottom: 15px;
    }
    .search-box input[type="text"] {
        padding: 6px;
        width: 250px;
    }
    .search-box input[type="submit"] {
        padding: 6px 12px;
        background-color: #2b6cb0;
        color: white;
        border: none;
        cursor: pointer;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background-color: white;
    }
    table, th, td {
        border: 1px solid #ccc;
    }
    th, td {
        padding: 8px;
        text-align: left;
        font-size: 14px;
    }
    th {
        background-color: #2b6cb0;
        color: white;
    }
    .available {
        color: green;
        font-weight: bold;
    }
    .booked {
        color: orange;
        font-weight: bold;
    }
    .maintenance {
        color: red;
        font-weight: bold;
    }
    .btn {
        padding: 4px 8px;
        text-decoration: none;
        font-size: 13px;
        border-radius: 3px;
    }
    .edit-btn {
        background-color: #38a169;
        color: white;
        margin-right: 5px;
    }
    .delete-btn {
        background-color: #e53e3e;
        color: white;
    }
    .msg {
        color: green;
        font-weight: bold;
    }
</style>
</head>
<body>

<h2>Vehicle List</h2>

<?php if ($msg != "") { ?>
    <p class="msg"><?php echo $msg; ?></p>
<?php } ?>

<form class="search-box" method="GET" action="vehicle_list.php">
    <input type="text" name="search" placeholder="Search by name, brand, model or location" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Vehicle Name</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Year</th>
        <th>Type</th>
        <th>Fuel</th>
        <th>Transmission</th>
        <th>Seats</th>
        <th>Price/Day</th>
        <th>Location</th>
        <th>Availability</th>
        <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['vehicle_id']; ?></td>
                <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                <td><?php echo htmlspecialchars($row['brand']); ?></td>
                <td><?php echo htmlspecialchars($row['model']); ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                <td><?php echo htmlspecialchars($row['fuel_type']); ?></td>
                <td><?php echo htmlspecialchars($row['transmission']); ?></td>
                <td><?php echo $row['seat_capacity']; ?></td>
                <td><?php echo number_format($row['price_per_day'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td>
                    <?php
                        $status = $row['availability'];
                        if ($status == "Available") {
                            echo "<span class='available'>$status</span>";
                        } elseif ($status == "Booked") {
                            echo "<span class='booked'>$status</span>";
                        } else {
                            echo "<span class='maintenance'>$status</span>";
                        }
                    ?>
                </td>
                <td>
                    <a class="btn edit-btn" href="edit_vehicle.php?id=<?php echo $row['vehicle_id']; ?>">Edit</a>
                    <a class="btn delete-btn" href="delete_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="13">No vehicle found.</td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
<?php mysqli_close($conn); ?>
