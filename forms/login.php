<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "hotel_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user inputs
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    
    // Prepare and execute SQL statement to find user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['room_number'] = $user['room_number'];
            
            // Redirect to homepage with success
            header("Location: /Project/Homepage/New.php");
            exit();
        } else {
            // Invalid password 
            echo "<script>
                    alert('Invalid email or password. Please try again.');
                    window.location.href = 'loginform.html';
                  </script>";
        }
    } else {
        // User not found
        echo "<script>
                alert('Invalid email or password. Please try again.');
                window.location.href = 'loginform.html';
              </script>";
    }
    
    $stmt->close();
}
?>