<?php
require_once __DIR__ . '/../includes/+auth_functions.php';

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../forms/loginform.html");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../forms/loginform.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: ../profile/profile.php");
            exit();
        } else {
            header("Location: ../forms/loginform.html?error=invalid_credentials");
            exit();
        }
    }
    
    if ($action === 'update_profile') {
        check_auth();
        
        $user_id = $_SESSION['user_id'];
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $room_number = $_POST['room_number'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        // Validate inputs
        if (empty($fullname)) $errors[] = "Full name is required";
        if (empty($phone)) $errors[] = "Phone number is required";
        if (empty($room_number)) $errors[] = "Room number is required";
        
        // Password change validation
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $errors[] = "Current password is required to change password";
            } else {
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if (!password_verify($current_password, $user['password'])) {
                    $errors[] = "Current password is incorrect";
                } elseif ($new_password !== $confirm_password) {
                    $errors[] = "New passwords do not match";
                }
            }
        }
        
        if (empty($errors)) {
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ?, room_number = ?, password = ? WHERE id = ?");
                $stmt->execute([$fullname, $phone, $room_number, $hashed_password, $user_id]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ?, room_number = ? WHERE id = ?");
                $stmt->execute([$fullname, $phone, $room_number, $user_id]);
            }
            
            $_SESSION['success'] = "Profile updated successfully";
        } else {
            $_SESSION['errors'] = $errors;
        }
        
        header("Location: ../profile/profile.php");
        exit();
    }
}
?>