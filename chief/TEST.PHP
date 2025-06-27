<?php
require_once __DIR__ . '/../includes/Database.php';

$db = new Database();
$conn = $db->getConnection();
if ($conn) {
    echo "✅ Connected to database!";
} else {
    echo "❌ Connection failed.";
}
?>
