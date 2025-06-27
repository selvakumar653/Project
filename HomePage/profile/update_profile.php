<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../forms/loginform.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$room_number = $_POST['room_number'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
$errors = [];

if (empty($fullname)) $errors[] = "Full name is required";
if (empty($phone)) $errors[] = "Phone number is required";
if (empty($room_number)) $errors[] = "Room number is required";

try {
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($current_password, $user['password'])) {
                $errors[] = "Current password is incorrect";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "New passwords do not match";
            }
        }
    }

    if (empty($errors)) {
        $conn->beginTransaction();
        
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET fullname = :fullname, phone = :phone, room_number = :room_number, password = :password WHERE id = :id");
            $stmt->bindParam(':password', $hashed_password);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = :fullname, phone = :phone, room_number = :room_number WHERE id = :id");
        }
        
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success'] = "Profile updated successfully";
    } else {
        $_SESSION['errors'] = $errors;
    }
} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: profile.php");
exit();