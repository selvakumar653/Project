<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "hotel_management");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$search = $_GET['search'] ?? '';
$date_filter = $_GET['date'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM chef_tasks WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (order_id LIKE '%$search%' OR food_items LIKE '%$search%' OR source LIKE '%$search%' OR status LIKE '%$search%')";
}

if (!empty($date_filter)) {
    $date_filter = $conn->real_escape_string($date_filter);
    $sql .= " AND completion_date = '$date_filter'";
}

if (!empty($status_filter)) {
    $status_filter = $conn->real_escape_string($status_filter);
    $sql .= " AND status = '$status_filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chef Task Dashboard</title>
    <style>
        .input-icon {
    position: relative;
    display: inline-block;
}

.input-icon input {
    padding-left: 30px;
}

.input-icon .fa {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #888;
}
body {
    font-family: 'Segoe UI', sans-serif;
    background: #fff5f5; /* Light red background */
    padding: 30px;
    color: #333;
}

h2 {
    text-align: center;
    color: #c0392b;
    animation: fadeIn 1s ease-in-out;
}

form {
    text-align: center;
    margin-bottom: 30px;
    animation: fadeIn 1.5s ease-in-out;
}

input[type="text"],
input[type="date"],
select {
    padding: 10px;
    margin: 0 8px;
    border: 1px solid #e6bcbc;
    border-radius: 8px;
    font-size: 14px;
    transition: box-shadow 0.3s ease;
    background-color: #fff0f0;
}

input:focus, select:focus {
    box-shadow: 0 0 5px #e74c3c;
    outline: none;
}

button {
    padding: 10px 18px;
    background-color: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #c0392b;
}

.refresh-btn {
    background-color: #bdc3c7;
}

.refresh-btn:hover {
    background-color: #95a5a6;
}

table {
    width: 100%;
    border-collapse: collapse;
    animation: fadeInUp 1s ease-in-out;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

th, td {
    padding: 12px 16px;
    border-bottom: 1px solid #f2caca;
    text-align: center;
}

th {
    background-color: #e74c3c;
    color: white;
}

tr:hover {
    background-color: #ffe5e5;
}

.badge {
    padding: 6px 10px;
    border-radius: 6px;
    font-weight: bold;
    text-transform: capitalize;
    display: inline-block;
}

.pending {
    background-color: #f39c12;
    color: #fff;
}

.completed {
    background-color: #27ae60;
    color: #fff;
}

.in-progress {
    background-color: #2980b9;
    color: #fff;
}

@keyframes fadeIn {
    from { opacity: 0 }
    to { opacity: 1 }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

p.no-results {
    text-align: center;
    font-size: 18px;
    color: #b03a2e;
    animation: fadeIn 1s ease;
}
        .dashboard-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .dashboard-btn:hover {
            background-color: #0056b3;
        }
        .dashboard-btn i {
            margin-right: 8px;
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<h2>Chef Task Dashboard</h2>

<a href="dashboard.php" class="btn btn-primary">
    <button type="button" class="dashboard-btn">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </button>
</a>
<form method="get">
    <div class="input-icon">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </div>
    
    <!-- Date Input with Icon -->
<div class="input-icon">
        <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">
</div>
    <select name="status">
        <option value="">All Status</option>
        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="in progress" <?= $status_filter == 'in progress' ? 'selected' : '' ?>>In Progress</option>
    </select>
    <button type="submit" class="btn btn-primary">
    <i class="fas fa-filter"></i> Filter
</button>
    <a href="view_task_chief.php"><button type="button" class="refresh-btn"><i class="fas fa-sync-alt"></i> Refresh</button></a>
</form>


<?php
if ($result && $result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Task ID</th>
                <th>Order ID</th>
                <th>Food Items</th>
                <th>Location</th>
                <th>Completion Date</th>
                <th>Status</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $statusClass = strtolower(str_replace(' ', '-', $row['status']));
        echo "<tr>
                <td>" . htmlspecialchars($row['task_id']) . "</td>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . htmlspecialchars($row['food_items']) . "</td>
                <td>" . htmlspecialchars($row['source']) . "</td>
                <td>" . htmlspecialchars($row['completion_date']) . "</td>
                <td><span class='badge $statusClass'>" . htmlspecialchars($row['status']) . "</span></td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='no-results'>No tasks found.</p>";
}
$conn->close();
?>

</body>
</html>
