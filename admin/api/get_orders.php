<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$status = $_GET['status'] ?? 'all';

try {
    $queries = [];

    // Build base queries
    $baseQuery = function($table) use ($status) {
        $sql = "SELECT 
                    order_id AS id,
                    customer_name,
                    customer_phone,
                    location_number,
                    items,
                    quantity,
                    total_amount,
                    status,
                    order_date,
                    '$table' AS service_type
                FROM $table";

        if ($status !== 'all') {
            $sql .= " WHERE status = :status";
        }

        return $sql;
    };

    // Prepare and execute each service query
    $tables = ['room_service', 'table_service', 'takeaway_service'];
    $results = [];

    foreach ($tables as $table) {
        $sql = $baseQuery($table);

        if ($status === 'all') {
            $stmt = $pdo->query($sql);  // ← $pdo is undefined here!

        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['status' => $status]);
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['location_type'] = str_replace('_service', '', $table);
        }

        $results = array_merge($results, $rows);
    }

    // Sort by order_date DESC
    usort($results, function ($a, $b) {
        return strtotime($b['order_date']) - strtotime($a['order_date']);
    });

    echo json_encode([
        'success' => true,
        'data' => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
