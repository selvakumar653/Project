<?php
require_once __DIR__ . '/../includes/Database.php';
$conn = new mysqli('localhost', 'root', '', 'hotel_management');

// Setup filters
$services = [
    'room_service'    => 'Room Service',
    'table_service'   => 'Table Service',
    'takeaway_service'=> 'Takeaway Service'
];

$selectedSource = $_GET['source'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';
function isActive($value, $current) {
    return $value === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chef Dashboard</title>
    <style>
    /* General body styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #ffe5e5 0%, #fff5f5 100%);
    color: #2d2d2d;
    overflow-x: hidden;
}

/* Header Section for Shop Name and Dashboard */
.header {
    background: #f87171;
    padding: 20px 0;
    text-align: center;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.shop-name {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 10px;
    letter-spacing: 1px;
    animation: slideDown 1s ease-out both;
}

.dashboard-title {
    font-size: 28px;
    font-weight: 500;
    letter-spacing: 0.5px;
    margin: 0;
    animation: slideDown 1.5s ease-out both;
}

/* Filter section styles */
.filter-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px;
    padding: 12px 20px;
    background-color: #ffffffdd;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    animation: fadeIn 1s ease-out both;
}

.filter-buttons {
    display: flex;
    gap: 12px;
}

.filter-btn {
    padding: 10px 18px;
    border-radius: 6px;
    background: #ef4444;
    color: #fff;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s ease;
}

.filter-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

.filter-btn.active {
    background: #3730a3;
    box-shadow: 0 4px 10px rgba(55, 48, 163, 0.4);
}

.status-filter select {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background-color: #fff;
    color: #2d2d2d;
    font-size: 15px;
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.status-filter select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 6px rgba(59, 130, 246, 0.4);
}

/* Table styles */
table {
    width: 100%;
    margin: 20px auto;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    animation: fadeInUp 1s ease-out both;
}

th, td {
    padding: 14px 18px;
    text-align: center;
}

th {
    background: #f3f4f6;
    color: #1f2937;
    font-weight: 600;
    letter-spacing: 0.5px;
}

tr {
    transition: background 0.3s ease, transform 0.2s ease;
}

tr:hover {
    background: #f9fafb;
    transform: translateX(4px);
}

td {
    border-bottom: 1px solid #e5e7eb;
}

td select {
    padding: 8px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

td select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 4px rgba(59, 130, 246, 0.4);
}

/* Keyframe animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-group {
        flex-direction: column;
        gap: 12px;
    }
    .filter-buttons {
        justify-content: center;
    }
    .status-filter select {
        width: 100%;
    }
}
    </style>
</head>
<body>

<div class="header">
    <h1 class="shop-name">The Chellappa Hotel Kitchen</h1>
    <h2 class="dashboard-title">Chef Dashboard</h2>
</div>

<div class="filter-group">
    <!-- Filter Buttons -->
    <div class="filter-buttons">
        <form method="get" style="display: flex; gap: 12px;">
            <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <button class="filter-btn <?= isActive('all', $selectedSource) ?>" name="source" value="all">All Sources</button>
            <button class="filter-btn <?= isActive('room_service', $selectedSource) ?>" name="source" value="room_service">Room</button>
            <button class="filter-btn <?= isActive('table_service', $selectedSource) ?>" name="source" value="table_service">Table</button>
            <button class="filter-btn <?= isActive('takeaway_service', $selectedSource) ?>" name="source" value="takeaway_service">Takeaway</button>
        </form>
    </div>

    <!-- Status Filter -->
    <div class="status-filter">
        <form method="get">
            <input type="hidden" name="source" value="<?= htmlspecialchars($selectedSource) ?>">
            <select id="status-filter" name="status" onchange="this.form.submit()">
                <option value="all" <?= ($statusFilter === 'all' ? 'selected' : '') ?>>All Statuses</option>
                <option value="Pending" <?= ($statusFilter === 'Pending' ? 'selected' : '') ?>>Pending</option>
                <option value="In Progress" <?= ($statusFilter === 'In Progress' ? 'selected' : '') ?>>In Progress</option>
                <option value="Completed" <?= ($statusFilter === 'Completed' ? 'selected' : '') ?>>Completed</option>
            </select>
        </form>
    </div>
</div>

<table class="orders-table" border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Source</th>
            <th>Items</th>
            <th>Status</th>
            <th>Update Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($services as $table => $label) {
        if ($selectedSource !== 'all' && $selectedSource !== $table) {
            continue;
        }

        $query = "SELECT id, items, status FROM $table";
        if ($statusFilter !== 'all') {
            $safeStatus = mysqli_real_escape_string($conn, $statusFilter);
            $query .= " WHERE status = '$safeStatus'";
        }

        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr data-type='{$table}' data-status='{$row['status']}'>";
            echo "  <td>{$row['id']}</td>";
            echo "  <td>{$label}</td>";
            echo "  <td>{$row['items']}</td>";
            echo "  <td>{$row['status']}</td>";
            echo "  <td>
                        <select class='status-dropdown' data-id='{$row['id']}' data-type='{$table}'>
                            <option value='Pending'"    . ($row['status']=='Pending'    ? ' selected':'') . ">Pending</option>
                            <option value='In Progress'" . ($row['status']=='In Progress' ? ' selected':'') . ">In Progress</option>
                            <option value='Completed'"   . ($row['status']=='Completed'   ? ' selected':'') . ">Completed</option>
                        </select>
                    </td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>
</body>
</html>

