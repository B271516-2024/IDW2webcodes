<?php
session_start(); // Start the session

// Database connection details
$host = "127.0.0.1";  // Database host
$db = "s2667265_user";      // Database name
$user = "s2667265";       // Database username
$pass = "7C5v7C6LwI@12345678";           // Database password (for localhost)

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exception
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Simulate a login process (replace this with actual database validation)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect form inputs
    $username = $_POST['userid'];
    $password = $_POST['password'];

    // Query the database to get the user data
    $sql = "SELECT username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    // Execute the query with the username
    $stmt->execute([$username]);

    // Check if a user exists
    if ($stmt->rowCount() > 0) {
        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if the password matches the hash in the database
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session
            $_SESSION['userid'] = $user['username'];

            // Redirect to a protected page (e.g., dashboard)
            header("Location: ../html/index_loged.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that user id.";
    }

    // Close the prepared statement (optional with PDO as it auto-closes)
    $stmt = null;
}

// Close the database connection (optional with PDO)
$conn = null;
?>
