<?php
    require_once __DIR__ . '/../includes/Database.php';
    $conn = new mysqli('localhost', 'root', '', 'hotel_management');
if (isset($_POST['id'], $_POST['status'], $_POST['type'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $type = $_POST['type'];

    $valid_tables = ['room_service', 'table_service', 'takeaway_service'];
    if (!in_array($type, $valid_tables)) {
        echo json_encode(['success' => false, 'message' => 'Invalid service type']);
        exit;
    }

    // Update the order status
    $stmt = $conn->prepare("UPDATE $type SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        // If status is 'Completed', log into chef_tasks
        if ($status === 'Completed') {
            $fetch = $conn->prepare("SELECT items FROM $type WHERE id = ?");
            $fetch->bind_param("i", $id);
            $fetch->execute();
            $result = $fetch->get_result();

            if ($row = $result->fetch_assoc()) {
                $items = $row['items'];

                $insert = $conn->prepare("INSERT INTO chef_tasks (order_id, food_items, source, status) VALUES (?, ?, ?, ?)");
                $insert->bind_param("isss", $id, $items, $type, $status);
                $insert->execute();
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
}
?>
