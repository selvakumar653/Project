<?php
session_start();
include 'db_connection.php';

$conn = OpenCon();

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Use $user_email in your order insert query
$sql = "INSERT INTO orders (user_email, ...) VALUES (?, ...)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s...", $user_email, ...);

// Execute the statement
if ($stmt->execute()) {
    echo "Order placed successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
CloseCon($conn);
?>