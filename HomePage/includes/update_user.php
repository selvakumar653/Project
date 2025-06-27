<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null; // Get from session
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }

    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $room_number = $_POST['room_number'] ?? '';
    
    // Validate inputs here
    
    $stmt = $conn->prepare("
        UPDATE users 
        SET fullname = ?, email = ?, phone = ?, room_number = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param("ssssi", $fullname, $email, $phone, $room_number, $user_id);
    
    if ($stmt->execute()) {
        // Handle password update if provided
        if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
            // Add password update logic here
            // Make sure to use password_hash() for the new password
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile']);
    }
    
    $stmt->close();
}