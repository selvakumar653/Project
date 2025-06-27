<?php
require_once '../includes/auth_functions.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Add User Handler
if (isset($_POST['add_user'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room = $_POST['room_number'];
    $role = 'guest'; // Force guest role

    try {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, room_number, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$fullname, $email, $phone, $room, $role]);
        header("Location: users.php");
        exit;
    } catch (PDOException $e) {
        echo "Add User Failed: " . $e->getMessage();
    }
}

// Delete User Handler
if (isset($_POST['delete_user'])) {
    $id = $_POST['delete_user_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php");
        exit;
    } catch (PDOException $e) {
        echo "Delete Failed: " . $e->getMessage();
    }
}

// Search Handler
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT id, fullname, email, phone, room_number, role, created_at FROM users";
$params = [];

if (!empty($search)) {
    $sql .= " WHERE fullname LIKE ? OR email LIKE ? OR phone LIKE ? OR room_number LIKE ?";
    $term = "%$search%";
    $params = [$term, $term, $term, $term];
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="../assets/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head>
<body>

<section class="user-management">
    <div class="section-header">
        <h2><i class="fas fa-users"></i> User Management</h2>
        <!-- Dashboard Button -->
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboard.php'">🏠 Dashboard</button>
    </div>

    <!-- Search Form -->
    <form method="GET" action="users.php" class="search-form">
        <input type="text" name="search" placeholder="Search by name, email, phone, or room" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <button type="button" onclick="location.href='users.php'" class="btn btn-secondary">🔄 Refresh</button>
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboard.php'">🏠 Dashboard</button>
    </form>

<!-- Add User Button -->
<button type="button" class="btn btn-primary" onclick="toggleAddUserForm()">Add User</button>

<!-- Add User Form (Initially Hidden) -->
<div id="add-user-form" style="display: none;">
    <form method="POST" action="users.php" class="add-user-form">
        <span class="input-container">
            <input type="text" name="fullname" placeholder="Full Name" required>
        </span>
        <span class="input-container">
            <input type="email" name="email" placeholder="Email" required>
        </span>
        <span class="input-container">
            <input type="text" name="phone" placeholder="Phone" required>
        </span>
        <span class="input-container">
            <input type="text" name="room_number" placeholder="Room Number">
        </span>
        <input type="hidden" name="role" value="guest">
        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
    </form>
</div>


    <!-- Users Table -->
    <div class="users-table-container">
        <?php if (count($users) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room No.</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['fullname']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td><?= htmlspecialchars($user['room_number']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <form method="POST" action="users.php" onsubmit="return confirm('Delete this user?');" style="display:inline;">
                            <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No users found for your search.</p>
        <?php endif; ?>
    </div>
</section>
<script>
function toggleAddUserForm() {
    var form = document.getElementById("add-user-form");
    // Toggle visibility of the form
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block"; // Show the form
    } else {
        form.style.display = "none"; // Hide the form
    }
}
</script>

</body>
</html>