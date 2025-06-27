<?php
session_start();
require_once '../includes/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $db = Database::getInstance();
    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $db->query($sql, [$name, $email, $phone, $user_id]);
        
        header('Location: user_profile.php');
        exit();
    }

    // Get current user data
    $sql = "SELECT * FROM users WHERE id = ?";
    $result = $db->query($sql, [$user_id]);
    $user = $result->fetch_assoc();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles/user_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Edit Profile</h1>
            <a href="user_profile.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Profile</a>
        </div>

        <div class="edit-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>