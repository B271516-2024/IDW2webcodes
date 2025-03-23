<?php
// Start the session to store any session data (if needed later)
session_start();
ob_start();

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

$error_message = null;

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize the input data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $baseDir = "/home/s2667265/public_html/userdata/";
    
    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
   // $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute([$username]);

    try{
        if ($stmt->rowCount() > 0) {
            // If the username already exists
            $error_message = "Username already exists. Please choose another.";
        } else {
            // Insert the new user into the database
            $insert_sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $insert_stmt->bindParam(':password', $hashed_password);

            if ($insert_stmt->execute()) {
                $userfolder = $baseDir . $username;
                exec("python3 /home/s2667265/public_html/python/permission.py " . escapeshellarg($userfolder));
                // If user registration is successful
                $_SESSION['username'] = $username;        // Store the username in the session
                header("Location: ../html/index.html");        // Redirect to the dashboard page (if any)
                exit();
            } else {
                $error_message = "Error: " . $insert_stmt->errorInfo()[2];
            }
        }
    }catch (PDOException $e) {
        // Handle database errors
        $error_message = "Database error: " . $e->getMessage();
    }
}
ob_end_flush();
?>
