<?php
// This function records the user's history in the database
function record_user_history($file_path, $action) {
    // Ensure the user is logged in (assuming check.php verifies the session)

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }    
    $user_id = $_SESSION['userid'];
    // Database connection (Modify with your actual DB credentials)
    $host = "127.0.0.1";  // Database host
    $db = "s2667265_user";      // Database name
    $user = "s2667265";       // Database username
    $pass = "7C5v7C6LwI@12345678";           // Database password (for localhost)

    // Create a new database connection
    try {
        // Create a PDO connection
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exception
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
    }

    // Check if the file_path and action are valid before proceeding
    if (empty($file_path) || empty($action) || empty($user_id)) {
        // Exit if any required data is missing
        return ["error" => "Missing required parameters."];
    }

    // Insert the history record into the database
    $sql = "INSERT INTO history (user_id, action, file_path) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $user_id, PDO::PARAM_STR);
    $stmt->bindParam(2, $action, PDO::PARAM_STR);
    $stmt->bindParam(3, $file_path, PDO::PARAM_STR);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
    } else {
        return ["error" => "Error recording history: " . $stmt->errorInfo()[2]];
    }
}
?>
