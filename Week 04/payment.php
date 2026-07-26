<?php
require_once "connection.php";
require_login();

$bookingId = (int)($_GET["id"] ?? $_SESSION["pending_booking"] ?? 0);

$stmt = $conn->prepare("SELECT b.*, v.name AS v_name, v.brand, v.image FROM bookings b
    JOIN vehicles v ON v.id = b.vehicle_id
    WHERE b.id = ? AND b.user_id = ? AND b.status = 'pending' LIMIT 1");
$uid = (int)$_SESSION["user_id"];
$stmt->bind_param("ii", $bookingId, $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    redirect("dashboard.php");
}
$b = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page_title = "Checkout"; include "includes/head_assets.php"; ?>
</head>
<body>
<?php include "includes/nav.php"; ?>

<main class="auth-wrap">
    <div style="width:min(520px,94%)">
        <div class="panel">
            <span class="eyebrow">Secure checkout</span>
            <h1 style="font-size:1.9rem">Confirm &amp; pay</h1>

            <div style="display:flex;gap:16px;align-items:center;background:var(--bg-2);border:1px solid var(--line);border-radius:12px;padding:14px;margin:20px 0">
                <img src="<?php echo e($b["image"]); ?>" alt="" style="width:96px;height:64px;object-fit:cover;border-radius:8px">
                <div style="flex:1">
                    <div style="font-weight:700"><?php echo e($b["brand"]); ?> <?php echo e($b["v_name"]); ?></div>
                    <div style="color:var(--muted);font-size:.85rem"><?php echo date("d M Y", strtotime($b["start_date"])); ?> &rarr; <?php echo date("d M Y", strtotime($b["end_date"])); ?> &middot; <?php echo (int)$b["total_days"]; ?> day(s)</div>
                </div>
                <div style="font-family:'Barlow Condensed';font-size:1.5rem;font-weight:800;color:var(--accent-2)"><?php echo money($b["total_price"]); ?></div>
            </div>

            <form action="payment_success.php" method="POST">
                <input type="hidden" name="booking_id" value="<?php echo (int)$b["id"]; ?>">
                <div class="field">
                    <label>Cardholder name</label>
                    <input type="text" placeholder="Name on card" value="<?php echo e($_SESSION["user_name"]); ?>" required>
                </div>
                <div class="field">
                    <label>Card number</label>
                    <input type="text" inputmode="numeric" placeholder="4242 4242 4242 4242" maxlength="19" required
                        oninput="this.value=this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim()">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label>Expiry</label>
                        <input type="text" placeholder="MM/YY" maxlength="5" required
                            oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{2})(\d)/,'$1/$2')">
                    </div>
                    <div class="field">
                        <label>CVC</label>
                        <input type="text" inputmode="numeric" placeholder="123" maxlength="4" required
                            oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>
                <button class="btn btn-solid btn-block" type="submit"><i class="fa-solid fa-lock"></i> Pay <?php echo money($b["total_price"]); ?></button>
                <p style="text-align:center;color:var(--muted);font-size:.8rem;margin-top:12px"><i class="fa-solid fa-flask"></i> Demo checkout &mdash; no real charge is made.</p>
            </form>
        </div>
    </div>
</main>
</body>
</html>
