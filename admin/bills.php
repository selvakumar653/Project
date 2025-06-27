<?php
require_once '../includes/auth_functions.php';
require_once __DIR__ . '/../includes/db_connect.php';

$serviceType = $_GET['service'] ?? 'room';
$statusFilter = $_GET['status'] ?? '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['bill_id'])) {
    $newStatus = $_POST['update_status'];
    $billId = (int)$_POST['bill_id'];

    switch ($serviceType) {
        case 'room':
            $updateQuery = "UPDATE room_service SET status = ? WHERE id = ?";
            break;
        case 'table':
            $updateQuery = "UPDATE table_service SET status = ? WHERE id = ?";
            break;
        case 'takeaway':
            $updateQuery = "UPDATE takeaway_service SET status = ? WHERE id = ?";
            break;
        default:
            $updateQuery = "UPDATE room_service SET status = ? WHERE id = ?";
    }

    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([$newStatus, $billId]);
}

// Determine query based on service and status filter
switch ($serviceType) {
    case 'room':
        $query = "SELECT * FROM room_service";
        $title = "Room Service Bills";
        break;
    case 'table':
        $query = "SELECT * FROM table_service";
        $title = "Table Service Bills";
        break;
    case 'takeaway':
        $query = "SELECT * FROM takeaway_service";
        $title = "Takeaway Service Bills";
        break;
    default:
        $query = "SELECT * FROM room_service";
        $title = "Room Service Bills";
}

if ($statusFilter) {
    $query .= " WHERE status = " . $conn->quote($statusFilter);
}
$query .= " ORDER BY order_date DESC";

// Get bills data
try {
    $bills = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<html>
<head>
    <link rel="stylesheet" href="../assets/css/bill.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<!-- Similar header/sidebar as dashboard.php -->
<section class="bill-counter">
    <div class="section-header">
        <h2><i class="fas fa-receipt"></i> <?= $title ?></h2>
        <div class="actions">
            <form method="get" style="display:inline;">
                <button type="submit" name="service" value="room" class="btn btn-primary">Room Service</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="service" value="table" class="btn btn-success">Table Service</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="service" value="takeaway" class="btn btn-warning">Takeaway</button>
            </form>
            <form action="dashboard.php" method="get" style="display:inline;">
                <button type="submit" class="btn btn-secondary" style="background-color:#6c757d; color:white;">Go to Dashboard</button>
            </form>
        </div>
        <div class="status-filter">
            <form method="get" class="status-form">
                <input type="hidden" name="service" value="<?= $serviceType ?>">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" class="status-select" onchange="this.form.submit()">
                    <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All</option>
                    <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>completed</option>
                    <option value="Canceled" <?= $statusFilter === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                </select>
            </form>
        </div>
    </div>

    <div class="bill-list">
        <?php if (!empty($bills)): ?>
            <table>
                <thead>
                    <tr>
                        <?php foreach (array_keys($bills[0]) as $column): ?>
                            <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $column))) ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bills as $bill): ?>
                        <tr>
                            <?php foreach ($bill as $value): ?>
                                <td><?= htmlspecialchars($value) ?></td>
                            <?php endforeach; ?>
                            <td>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                    <label><?= htmlspecialchars($bill['status']) ?></label><br>
                                    <select name="update_status" onchange="this.form.submit()">
                                        <option disabled selected>Change status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">completed</option>
                                        <option value="canceled">Canceled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bills found for this service.</p>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
