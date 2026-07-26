<?php
require_once "connection.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("dashboard.php");
}

$bookingId = (int)($_POST["booking_id"] ?? 0);
$uid = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT b.*, v.name AS v_name, v.brand, v.image FROM bookings b
    JOIN vehicles v ON v.id = b.vehicle_id
    WHERE b.id = ? AND b.user_id = ? LIMIT 1");
$stmt->bind_param("ii", $bookingId, $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    redirect("dashboard.php");
}
$b = $res->fetch_assoc();

if ($b["status"] === "pending") {
    $status = "confirmed";
    $upd = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $upd->bind_param("si", $status, $bookingId);
    $upd->execute();
    $b["status"] = "confirmed";
}

unset($_SESSION["pending_booking"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page_title = "Payment Successful"; include "includes/head_assets.php"; ?>
    <style>
        .success-ring { width: 92px; height: 92px; border-radius: 50%; margin: 0 auto 22px; display: grid; place-items: center;
            background: rgba(62,207,142,.12); border: 2px solid var(--success); color: var(--success); font-size: 2.4rem;
            animation: pop .5s cubic-bezier(.2,1.4,.4,1); }
        @keyframes pop { from { transform: scale(.4); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>
<?php include "includes/nav.php"; ?>

<main class="auth-wrap">
    <div style="width:min(520px,94%)">
        <div class="panel" style="text-align:center">
            <div class="success-ring"><i class="fa-solid fa-check"></i></div>
            <h1 style="font-size:2rem">Payment successful!</h1>
            <p style="color:var(--muted);margin:8px 0 22px">Your booking is confirmed. Keys are waiting at the counter.</p>

            <div style="background:var(--bg-2);border:1px dashed var(--line);border-radius:12px;padding:16px;text-align:left">
                <div class="price-line"><span>Booking reference</span><b style="color:var(--accent-2)"><?php echo e($b["booking_ref"]); ?></b></div>
                <div class="price-line"><span>Vehicle</span><span><?php echo e($b["brand"]); ?> <?php echo e($b["v_name"]); ?></span></div>
                <div class="price-line"><span>Pickup</span><span><?php echo date("d M Y", strtotime($b["start_date"])); ?></span></div>
                <div class="price-line"><span>Return</span><span><?php echo date("d M Y", strtotime($b["end_date"])); ?></span></div>
                <div class="price-line total"><span>Amount paid</span><span class="amt"><?php echo money($b["total_price"]); ?></span></div>
            </div>

            <div style="display:flex;gap:12px;margin-top:24px">
                <a href="dashboard.php" class="btn btn-solid" style="flex:1"><i class="fa-solid fa-calendar-check"></i> My bookings</a>
                <a href="vehicle_list.php" class="btn btn-ghost" style="flex:1"><i class="fa-solid fa-car"></i> Keep browsing</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
