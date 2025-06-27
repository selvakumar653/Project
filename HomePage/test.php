<?php
session_start();
// Prevent any output before headers
ob_start();

// Error handling
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Clear any previous output
if (ob_get_length()) ob_clean();

// Set proper JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Get POST data
    $json = file_get_contents('php://input');
    
    // Log raw input for debugging
    error_log("Received data: " . $json);
    
    if (!$json) {
        throw new Exception('No data received');
    }

    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    if (!isset($data['items']) || !isset($data['totalAmount']) || !isset($data['dining'])) {
        throw new Exception('Missing required fields');
    }

    // Connect to database
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli('localhost', 'root', '', 'hotel_management');
    
    // Set charset to ensure proper JSON encoding
    $conn->set_charset('utf8mb4');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    // Format items as string
    $itemsString = '';
    foreach ($data['items'] as $item) {
        $itemsString .= $item['name'] . ' x' . $item['quantity'] . ', ';
    }
    $itemsString = rtrim($itemsString, ', ');

    $missingFoods = [];
    $foodIds = [];
    foreach ($data['items'] as $item) {
        $foodName = $item['name'];
        $foodIdStmt = $conn->prepare("SELECT id FROM menu_items WHERE name = ?");
        $foodIdStmt->bind_param("s", $foodName);
        $foodIdStmt->execute();
        $foodIdStmt->bind_result($foodId);
        $found = false;
        while ($foodIdStmt->fetch()) {
            $foodIds[] = $foodId;
            $found = true;
            error_log("Added foodId: $foodId");
        }
        if (!$found) {
            $missingFoods[] = $foodName;
            error_log("Missing food: $foodName");
        }
        $foodIdStmt->close();
    }
    $itemIdsString = implode(',', $foodIds);
    error_log("Final itemIdsString: $itemIdsString");

    if (!empty($missingFoods)) {
        throw new Exception('The following food items were not found: ' . implode(', ', $missingFoods));
    }

    // Set variables based on dining type
    $userEmail = $_SESSION['email'];
    $userName = $_SESSION['fullname'];
    $locationType = $data['dining']['type'];
    $locationNumber = $data['dining']['location'];

    // Set customerName logic
    if ($locationType === 'room' || $locationType === 'table') {
        $customerName = $userName;
    } else if (isset($data['dining']['customerInfo']['name']) && !empty($data['dining']['customerInfo']['name'])) {
        $customerName = $data['dining']['customerInfo']['name'];
    } else {
        $customerName = $userName;
    }

    $customerPhone = $data['dining']['customerInfo']['phone'] ?? null;
    $arrivalTime = $data['dining']['customerInfo']['arrivalTime'] ?? null;

    // Prepare SQL statement
    $stmt = $conn->prepare("
        INSERT INTO bills (
            user_email,
            location_type,
            location_number,
            customer_name,
            customer_phone,
            arrival_time,
            items,
            item_ids,
            quantity,
            total_amount,
            order_date,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')
    ");

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssisd",
        $userEmail,
        $locationType,
        $locationNumber,
        $customerName,
        $customerPhone,
        $arrivalTime,
        $itemsString,
        $itemIdsString,
        $data['quantity'],
        $data['totalAmount']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }

    $orderId = $stmt->insert_id;
    $waitingId = null;
    $showWaitingId = false;

    // Generate waiting ID only for takeaway orders
    if ($locationType === 'takeaway') {
        $waitingId = date('Ymd') . str_pad($orderId, 4, '0', STR_PAD_LEFT);
        $showWaitingId = true;
        
        // Update the order with waiting ID
        $updateStmt = $conn->prepare("UPDATE bills SET waiting_id = ? WHERE order_id = ?");
        if (!$updateStmt) {
            throw new Exception('Failed to prepare update statement: ' . $conn->error);
        }
        
        $updateStmt->bind_param("si", $waitingId, $orderId);
        if (!$updateStmt->execute()) {
            throw new Exception('Failed to update waiting ID: ' . $updateStmt->error);
        }
        $updateStmt->close();
    }

    // Prepare response based on order type
    $response = [
        'success' => true,
        'message' => 'Order processed successfully',
        'orderId' => $orderId,
        'orderType' => $locationType,
        'showWaitingId' => $showWaitingId
    ];

    // Add waiting ID to response only for takeaway orders
    if ($showWaitingId) {
        $response['waitingId'] = $waitingId;
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>