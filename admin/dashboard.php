<?php
require_once '../includes/auth_functions.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Get stats
try {
    $totalBills = $conn->query("SELECT COUNT(*) FROM bills")->fetchColumn();
    $todayRevenue = $conn->query("SELECT SUM(total_amount) FROM bills WHERE DATE(order_date) = CURDATE() AND status = 'completed'")->fetchColumn();
    $lowStockItems = $conn->query("SELECT COUNT(*) FROM menu_items WHERE stock_quantity < alert_threshold")->fetchColumn();
    $totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Chellappa Hotel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Chellappa Hotel</h2>
                <p>Admin Panel</p>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="bills.php"><i class="fas fa-receipt"></i> Bill Counter</a></li>
                    <li><a href="inventory.php"><i class="fas fa-boxes"></i> Stock Management</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> User Management</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <a href="view_task_chief.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Chef Task Dashboard
                    </a>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="user-menu">
                    <span>Welcome, <?= htmlspecialchars($_SESSION['user_fullname']) ?></span>
                    <a href="../includes/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #4e73df;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Bills</h3>
                        <p><?= number_format($totalBills) ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #1cc88a;">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Revenue</h3>
                        <p>₹<?= number_format($todayRevenue ?: 0, 2) ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #f6c23e;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Low Stock Items</h3>
                        <p><?= number_format($lowStockItems) ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #e74a3b;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Users</h3>
                        <p><?= number_format($totalUsers) ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <section class="recent-activity">
                <h2><i class="fas fa-history"></i> Recent Activity</h2>
                <div class="activity-list">
                    <?php
                    $activities = $conn->query("
                        SELECT * FROM activities 
                        ORDER BY date DESC 
                        LIMIT 5
                    ")->fetchAll();
                    
                    foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <?php switch($activity['type']) {
                                case 'users': echo '<i class="fas fa-user"></i>'; break;
                                case 'orders': echo '<i class="fas fa-utensils"></i>'; break;
                                case 'inventory': echo '<i class="fas fa-box"></i>'; break;
                                default: echo '<i class="fas fa-info-circle"></i>';
                            } ?>
                        </div>
                        <div class="activity-details">
                            <p><?= htmlspecialchars($activity['description']) ?></p>
                            <small><?= date('M j, Y g:i A', strtotime($activity['date'])) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>