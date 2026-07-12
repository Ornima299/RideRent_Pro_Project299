<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}

include "database/connection.php";

$sql = "SELECT COUNT(*) AS total_vehicle FROM vehicle";  
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

$total_vehicle = $row['total_vehicle'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - DriveEase</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f4f6f9;
}

/* Sidebar */

.sidebar{
    position:fixed;
    width:250px;
    height:100%;
    background:#0d6efd;
    color:white;
    padding-top:20px;
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
}

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    padding:15px 25px;
    cursor:pointer;
}

.sidebar ul li a{
    color:white;
    text-decoration:none;
    display:block;
    width:100%;
}


/* Main Content */

.main{
    margin-left:250px;
    padding:20px;
}

.header{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

.header h1{
    color:#333;
}

/* Dashboard Cards */

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-top:25px;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
    text-align:center;
}

.card h3{
    color:#555;
}

.card p{
    font-size:32px;
    margin-top:10px;
    color:#0d6efd;
    font-weight:bold;
}

/* Tables */

.section{
    margin-top:30px;
}

.section h2{
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

table th{
    background:#0d6efd;
    color:white;
    padding:12px;
}

table td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

.btn{
    padding:6px 12px;
    border:none;
    border-radius:5px;
    color:white;
    cursor:pointer;
}

.approve{
    background:green;
}

.reject{
    background:red;
}

</style>
</head>
<body>

<div class="sidebar">

    <h2>RideRent_Pro299</h2>

    <ul>
        <li>🏠 Dashboard</li>
        <li>👤 Users</li>
        <li>🚗 Vehicles</li>
        <li>🪪 Drivers</li>
        <li>📖 Bookings</li>
        <li>⭐ Reviews</li>
        <li>📊 Reports</li>
        <li>⚙ Settings</li>
       
    <li>
    <a href="logout.php">TEST LOGOUT</a>


</li>
    </ul>

</div>

<div class="main">

    <div class="header">
        <h1>Admin Dashboard</h1>
        <p>Welcome Admin</p>
    </div>

    <!-- Statistics -->

    <div class="cards">

        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $total_vehicle; ?></p>
        </div>

        <div class="card">
            <h3>Total Vehicles</h3>
            <p>45</p>
        </div>

        <div class="card">
            <h3>Total Drivers</h3>
            <p>30</p>
        </div>

        <div class="card">
            <h3>Total Bookings</h3>
            <p>210</p>
        </div>

    </div>

    <!-- Driver Verification -->

    <div class="section">

        <h2>Pending Driver Verification</h2>

        <table>

            <tr>
                <th>Name</th>
                <th>License No</th>
                <th>Experience</th>
                <th>Action</th>
            </tr>

            <tr>
                <td>Rahim Ahmed</td>
                <td>DL123456</td>
                <td>5 Years</td>
                <td>
                    <button class="btn approve">Approve</button>
                    <button class="btn reject">Reject</button>
                </td>
            </tr>

            <tr>
                <td>Karim Hasan</td>
                <td>DL987654</td>
                <td>3 Years</td>
                <td>
                    <button class="btn approve">Approve</button>
                    <button class="btn reject">Reject</button>
                </td>
            </tr>

        </table>

    </div>

    <!-- Recent Bookings -->

    <div class="section">

        <h2>Recent Bookings</h2>

        <table>

            <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Status</th>
            </tr>

            <tr>
                <td>B001</td>
                <td>Ornima</td>
                <td>Toyota Premio</td>
                <td>Confirmed</td>
            </tr>

            <tr>
                <td>B002</td>
                <td>Hasan</td>
                <td>Honda Civic</td>
                <td>Pending</td>
            </tr>

        </table>

    </div>

</div>

</body>
</html>