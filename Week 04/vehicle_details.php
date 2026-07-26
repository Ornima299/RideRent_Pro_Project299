<?php
require_once "connection.php";

$id = (int)($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    redirect("vehicle_list.php");
}
$v = $res->fetch_assoc();
$today = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page_title = $v["name"]; $page_css = "vehicle.css"; include "includes/head_assets.php"; ?>
</head>
<body>
<?php include "includes/nav.php"; ?>

<main class="container">
    <div class="page-head">
        <div>
            <a href="vehicle_list.php" style="color:var(--muted);font-size:.9rem"><i class="fa-solid fa-arrow-left"></i> Back to fleet</a>
            <h1 style="margin-top:8px"><?php echo e($v["brand"]); ?> <span><?php echo e($v["name"]); ?></span></h1>
        </div>
        <?php if ($v["status"] === "available"): ?>
            <span class="badge badge-available"><i class="fa-solid fa-circle" style="font-size:.45rem"></i> Available now</span>
        <?php else: ?>
            <span class="badge badge-unavailable"><i class="fa-solid fa-circle" style="font-size:.45rem"></i> Currently unavailable</span>
        <?php endif; ?>
    </div>

    <div class="detail-grid" style="margin-bottom:60px">
        <div>
            <div class="detail-img">
                <img src="<?php echo e($v["image"]); ?>" alt="<?php echo e($v["name"]); ?>">
            </div>
            <div class="spec-grid">
                <div class="spec"><div class="k">Type</div><div class="v"><i class="fa-solid fa-car"></i> <?php echo e($v["type"]); ?></div></div>
                <div class="spec"><div class="k">Seats</div><div class="v"><i class="fa-solid fa-user-group"></i> <?php echo (int)$v["seats"]; ?> passengers</div></div>
                <div class="spec"><div class="k">Transmission</div><div class="v"><i class="fa-solid fa-gears"></i> <?php echo e($v["transmission"]); ?></div></div>
                <div class="spec"><div class="k">Fuel</div><div class="v"><i class="fa-solid fa-gas-pump"></i> <?php echo e($v["fuel"]); ?></div></div>
            </div>
            <div class="panel">
                <h2><i class="fa-solid fa-circle-info" style="color:var(--accent)"></i> About this car</h2>
                <p style="color:var(--muted);line-height:1.7">
                    <?php echo $v["description"] ? e($v["description"]) : "A well-maintained " . e(strtolower($v["type"])) . " ready for your next trip. Unlimited mileage within the city, 24/7 roadside assistance and full insurance included in every rental."; ?>
                </p>
            </div>
        </div>

        <!-- BOOKING CARD -->
        <div class="panel book-card" data-price="<?php echo (float)$v["price_per_day"]; ?>">
            <h2><i class="fa-solid fa-key" style="color:var(--accent)"></i> Book this car</h2>

            <?php if ($v["status"] !== "available"): ?>
                <div class="alert alert-error"><i class="fa-solid fa-ban"></i> This vehicle is currently unavailable for booking.</div>
            <?php elseif (!is_logged_in()): ?>
                <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Log in to book this vehicle.</div>
                <a href="login.php" class="btn btn-solid btn-block"><i class="fa-solid fa-right-to-bracket"></i> Login to book</a>
            <?php elseif (is_admin()): ?>
                <div class="alert alert-info"><i class="fa-solid fa-user-shield"></i> You are logged in as admin. Switch to a user account to book.</div>
            <?php else: ?>
                <form action="book.php" method="POST">
                    <input type="hidden" name="vehicle_id" value="<?php echo (int)$v["id"]; ?>">
                    <div class="field">
                        <label>Pickup date</label>
                        <input type="date" name="start_date" id="start_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                    </div>
                    <div class="field">
                        <label>Return date</label>
                        <input type="date" name="end_date" id="end_date" min="<?php echo $today; ?>" value="<?php echo date("Y-m-d", strtotime("+1 day")); ?>" required>
                    </div>

                    <div style="margin:18px 0 6px">
                        <div class="price-line"><span><?php echo money($v["price_per_day"]); ?> &times; <b id="days_out">1</b> day(s)</span><span id="subtotal_out"><?php echo money($v["price_per_day"]); ?></span></div>
                        <div class="price-line"><span>Insurance &amp; fees</span><span>Included</span></div>
                        <div class="price-line total"><span>Total</span><span class="amt" id="total_out"><?php echo money($v["price_per_day"]); ?></span></div>
                    </div>

                    <button class="btn btn-solid btn-block" type="submit"><i class="fa-solid fa-credit-card"></i> Proceed to payment</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include "includes/footer.php"; ?>

<script>
(function () {
    var card  = document.querySelector(".book-card");
    if (!card) return;
    var price = parseFloat(card.dataset.price) || 0;
    var s = document.getElementById("start_date");
    var e = document.getElementById("end_date");

    function recalc() {
        if (!s || !e || !s.value || !e.value) return;
        var d1 = new Date(s.value), d2 = new Date(e.value);
        var days = Math.round((d2 - d1) / 86400000);
        if (isNaN(days) || days < 1) days = 1;
        if (e && e.value && s.value && days < 1) { e.setCustomValidity("Return date must be after pickup date"); } else if (e) { e.setCustomValidity(""); }
        var total = days * price;
        document.getElementById("days_out").textContent = days;
        document.getElementById("subtotal_out").textContent = "$" + total.toFixed(2);
        document.getElementById("total_out").textContent = "$" + total.toFixed(2);
        if (s.value) e.min = s.value;
    }
    if (s) s.addEventListener("change", recalc);
    if (e) e.addEventListener("change", recalc);
    recalc();
})();
</script>
</body>
</html>
