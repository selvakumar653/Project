<?php
session_start();
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../forms/loginform.html';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginform.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get order history
$stmt = $conn->prepare("SELECT * FROM bills WHERE user_email = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Include the HTML view
include 'profile.php';
?>