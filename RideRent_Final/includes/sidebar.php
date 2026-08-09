<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Define APP_URL if not already defined
if (!defined('APP_URL')) {
    // Determine base URL dynamically
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname(dirname($_SERVER['SCRIPT_NAME']));
    define('APP_URL', $protocol . '://' . $host . $path);
}

$role = get_user_role();
$sidebar_items = [];

switch($role) {
    case 'admin':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => APP_URL . '/admin/dashboard.php'],
            ['icon' => 'fas fa-users', 'text' => 'Users', 'link' => APP_URL . '/admin/users.php'],
            ['icon' => 'fas fa-check-circle', 'text' => 'Vehicle Approvals', 'link' => APP_URL . '/admin/vehicle_approvals.php'],
            ['icon' => 'fas fa-user-plus', 'text' => 'Driver Assignment', 'link' => APP_URL . '/admin/driver_assignment.php'],
            ['icon' => 'fas fa-id-card', 'text' => 'Drivers', 'link' => APP_URL . '/admin/drivers.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'Bookings', 'link' => APP_URL . '/admin/bookings.php'],
            ['icon' => 'fas fa-star', 'text' => 'Reviews', 'link' => APP_URL . '/admin/reviews.php'],
            ['icon' => 'fas fa-star-half-alt', 'text' => 'Ratings', 'link' => APP_URL . '/admin/ratings.php'],
            ['icon' => 'fas fa-chart-bar', 'text' => 'Reports', 'link' => APP_URL . '/admin/reports.php'],
        ];
        break;
    case 'owner':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => APP_URL . '/owner/dashboard.php'],
            ['icon' => 'fas fa-car', 'text' => 'My Vehicles', 'link' => APP_URL . '/owner/vehicles/vehicle_list.php'],
            ['icon' => 'fas fa-plus-circle', 'text' => 'Add Vehicle', 'link' => APP_URL . '/owner/vehicles/add_vehicle.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'Bookings', 'link' => APP_URL . '/owner/bookings.php'],
            ['icon' => 'fas fa-id-card', 'text' => 'Drivers', 'link' => APP_URL . '/owner/drivers.php'],
        ];
        break;
    case 'driver':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => APP_URL . '/driver/dashboard.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'My Bookings', 'link' => APP_URL . '/driver/bookings.php'],
            ['icon' => 'fas fa-dollar-sign', 'text' => 'My Earnings', 'link' => APP_URL . '/driver/earnings.php'],
            ['icon' => 'fas fa-user', 'text' => 'My Profile', 'link' => APP_URL . '/driver/profile.php'],
        ];
        break;
    case 'customer':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => APP_URL . '/customer/dashboard.php'],
            ['icon' => 'fas fa-car', 'text' => 'Browse Vehicles', 'link' => APP_URL . '/customer/vehicles.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'My Bookings', 'link' => APP_URL . '/customer/bookings.php'],
            ['icon' => 'fas fa-user', 'text' => 'My Profile', 'link' => APP_URL . '/customer/profile.php'],
        ];
        break;
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-car-side"></i> RideRent Pro</h2>
    </div>
    <div class="sidebar-nav">
        <ul>
            <?php foreach ($sidebar_items as $index => $item): ?>
                <li>
                    <a href="<?php echo $item['link']; ?>" class="<?php echo (isset($active_page) && $active_page === $item['text']) ? 'active' : ''; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['text']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="<?php echo APP_URL; ?>/auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <button class="theme-toggle" onclick="toggleTheme()" style="width: 100%; justify-content: center;">
            <i class="fas fa-moon"></i>
            <span>Dark Mode</span>
        </button>
    </div>
</div>