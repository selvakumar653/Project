<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "hotel_management");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get query parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10000;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Calculate offset
$offset = ($page - 1) * $limit;

// Base query
$query = "SELECT a.id, a.date, a.type, a.description, a.status 
          FROM activities a 
          WHERE 1=1";

// Add search condition
if ($search) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (description LIKE '%$search%' OR type LIKE '%$search%')";
}

// Add filter condition
if ($filter !== 'all') {
    $filter = $conn->real_escape_string($filter);
    $query .= " AND type = '$filter'";
}

// Get total count for pagination
$countQuery = str_replace("SELECT a.id, a.date, a.type, a.description, a.status", "SELECT COUNT(*) as count", $query);
$totalResult = $conn->query($countQuery);
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['count'];

// Add pagination
$query .= " ORDER BY date DESC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);

// Prepare response
$data = [
    'total' => (int)$total,
    'page' => $page,
    'limit' => $limit,
    'data' => []
];

// Fetch results
while ($row = $result->fetch_assoc()) {
    $data['data'][] = [
        'id' => $row['id'],
        'date' => $row['date'],
        'type' => $row['type'],
        'description' => $row['description'],
        'status' => $row['status']
    ];
}

// Close connection
$conn->close();

// Send response
echo json_encode($data);