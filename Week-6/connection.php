<?php
// database/connection.php
$host = "localhost";
$user = "root";
$password = "";
$database = "riderent_pro299";

// MySQLi Connection
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Helper function to get count
function getCount($table, $condition = "") {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM $table";
    if ($condition) {
        $sql .= " WHERE $condition";
    }
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

// Check if table exists
function tableExists($table) {
    global $conn;
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return mysqli_num_rows($check) > 0;
}

// Debug function
function debug($data) {
    echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ddd;'>";
    print_r($data);
    echo "</pre>";
}

// Get single record
function getRecord($table, $id, $idField = 'id') {
    global $conn;
    $sql = "SELECT * FROM $table WHERE $idField = '$id'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Get all records
function getAllRecords($table, $orderBy = 'id', $order = 'DESC') {
    global $conn;
    $sql = "SELECT * FROM $table ORDER BY $orderBy $order";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}
?>