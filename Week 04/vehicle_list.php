<?php
require_once "connection.php";

$q        = trim($_GET["q"] ?? "");
$type     = trim($_GET["type"] ?? "");
$maxPrice = $_GET["max_price"] ?? "";

$sql = "SELECT * FROM vehicles WHERE 1=1";
$params = [];
$types  = "";

if ($q !== "") {
    $sql .= " AND (name LIKE ? OR brand LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like;
    $types .= "ss";
}
if ($type !== "") {
    $sql .= " AND type = ?";
    $params[] = $type; $types .= "s";
}
if ($maxPrice !== "" && is_numeric($maxPrice)) {
    $sql .= " AND price_per_day <= ?";
    $params[] = (float)$maxPrice; $types .= "d";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page_title = "Browse Cars"; $page_css = "vehicle.css"; include "includes/head_assets.php"; ?>
</head>
<body>
<?php include "includes/nav.php"; ?>

<main class="container">
    <div class="page-head">
        <div>
            <span class="eyebrow">The fleet</span>
            <h1>Browse <span>cars</span></h1>
            <p><?php echo $result->num_rows; ?> vehicle<?php echo $result->num_rows === 1 ? "" : "s"; ?> match your search.</p>
        </div>
    </div>

    <form class="filter-bar" method="GET" action="">
        <div class="field">
            <label>Search</label>
            <input type="text" name="q" placeholder="Brand or model" value="<?php echo e($q); ?>">
        </div>
        <div class="field">
            <label>Type</label>
            <select name="type">
                <option value="">Any type</option>
                <?php foreach (["Sedan","SUV","Hatchback","Luxury","Sports","MPV"] as $t): ?>
                    <option <?php echo $type === $t ? "selected" : ""; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Max price / day</label>
            <select name="max_price">
                <option value="">Any budget</option>
                <option value="50"  <?php echo $maxPrice === "50"  ? "selected" : ""; ?>>Under $50</option>
                <option value="100" <?php echo $maxPrice === "100" ? "selected" : ""; ?>>Under $100</option>
                <option value="200" <?php echo $maxPrice === "200" ? "selected" : ""; ?>>Under $200</option>
            </select>
        </div>
        <button class="btn btn-solid" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
        <a class="btn btn-ghost" href="vehicle_list.php"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="fleet-grid" style="margin-bottom:60px">
            <?php while ($v = $result->fetch_assoc()): ?>
                <article class="v-card">
                    <div class="v-img">
                        <img src="<?php echo e($v["image"]); ?>" alt="<?php echo e($v["name"]); ?>">
                        <?php if ($v["status"] === "available"): ?>
                            <span class="badge badge-available"><i class="fa-solid fa-circle" style="font-size:.45rem"></i> Available</span>
                        <?php else: ?>
                            <span class="badge badge-unavailable"><i class="fa-solid fa-circle" style="font-size:.45rem"></i> Unavailable</span>
                        <?php endif; ?>
                    </div>
                    <div class="v-body">
                        <div class="v-top">
                            <div>
                                <div class="v-brand"><?php echo e($v["brand"]); ?></div>
                                <h3><?php echo e($v["name"]); ?></h3>
                            </div>
                            <div class="v-price">
                                <span class="amt"><?php echo money($v["price_per_day"]); ?></span>
                                <span class="per">/ day</span>
                            </div>
                        </div>
                        <div class="v-specs">
                            <span><i class="fa-solid fa-tag"></i> <?php echo e($v["type"]); ?></span>
                            <span><i class="fa-solid fa-user-group"></i> <?php echo (int)$v["seats"]; ?></span>
                            <span><i class="fa-solid fa-gears"></i> <?php echo e($v["transmission"]); ?></span>
                            <span><i class="fa-solid fa-gas-pump"></i> <?php echo e($v["fuel"]); ?></span>
                        </div>
                        <a href="vehicle_details.php?id=<?php echo (int)$v["id"]; ?>" class="btn btn-solid btn-block">
                            <?php echo $v["status"] === "available" ? "View & book" : "View details"; ?>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty panel" style="margin-bottom:60px">
            <i class="fa-solid fa-car-on"></i>
            <p>No vehicles match those filters.<br>Try widening your search.</p>
        </div>
    <?php endif; ?>
</main>

<?php include "includes/footer.php"; ?>
</body>
</html>
