<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../forms/loginform.html");
    exit();
}

// Database connection
require_once '../profile/includes/db_connect.php';

try {
    // Get user data using PDO
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Get user's recent orders
    $stmt = $conn->prepare("
        SELECT order_id, location_number, items, total_amount, status, order_date 
        FROM bills 
        WHERE user_email = :email 
        ORDER BY order_date DESC 
        LIMIT 5
    ");
    $stmt->bindParam(':email', $user['email'], PDO::PARAM_STR);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Chellappa Hotel</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <!-- Header -->
        <header class="profile-header">
            <h1>My Profile</h1>
            <a href="../../forms/logout.php" class="logout-btn">Logout</a>
        </header>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="avatar">
                <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
            </div>
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Room No:</strong> <?php echo htmlspecialchars($user['room_number']); ?></p>
            </div>
        </div>

        <!-- Update Form -->
        <form class="update-form" action="update_profile.php" method="POST">
            <h3>Update Information</h3>
            
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($user['room_number']); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="current_password">Current Password (for changes)</label>
                <input type="password" id="current_password" name="current_password">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
            </div>
            
            <button type="submit" class="update-btn">Update Profile</button>
        </form>

        <!-- Order History -->
        <div class="order-history">
            <h3>Recent Orders</h3>
            
            <?php if (!empty($orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Room/Table</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['location_number']; ?></td>
                            <td><?php echo htmlspecialchars(substr($order['items'], 0, 30)); ?>...</td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders">No recent orders found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>