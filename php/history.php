<?php

session_start();
$user_id = $_SESSION['userid'];
header('Content-Type: application/json');

// Database connection (Modify with your actual DB credentials)
$host = "127.0.0.1";  // Database host
$db = "s2667265_user";      // Database name
$user = "s2667265";       // Database username
$pass = "7C5v7C6LwI@12345678";           // Database password (for localhost)

// Create a new database connection
try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exception
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$sql = "SELECT * FROM history WHERE user_id = ? ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);

// Assuming $user_id is defined somewhere before executing this query
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);

// Close the connection
$conn = null;
?>
