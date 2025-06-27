<?php
function getUserDetails($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT 
            id,
            fullname,
            email,
            phone,
            room_number,
            created_at,
            username,
            role
        FROM users 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserOrders($conn, $user_id) {
    // Get user phone number first
    $stmt = $conn->prepare("SELECT phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        return [];
    }

    // Get orders using phone number
    $stmt = $conn->prepare("
        SELECT 
            order_id,
            location_type,
            location_number,
            customer_name,
            items,
            quantity,
            total_amount,
            status,
            order_date
        FROM bills 
        WHERE customer_phone = ?
        ORDER BY order_date DESC 
        LIMIT 5
    ");
    
    $stmt->bind_param("s", $user['phone']);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function formatOrderItems($items) {
    // Items are stored as "Item1 x1, Item2 x2" format
    return explode(', ', $items);
}

function getOrderStatusClass($status) {
    switch(strtolower($status)) {
        case 'pending':
            return 'pending';
        case 'completed':
            return 'completed';
        case 'cancelled':
            return 'cancelled';
        default:
            return 'pending';
    }
}